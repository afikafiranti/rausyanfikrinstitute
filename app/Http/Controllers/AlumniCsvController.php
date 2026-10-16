<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlumniIndexRequest;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use App\Support\AlumniExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class AlumniCsvController extends Controller
{
    private const COLUMNS = AlumniExcel::COLUMNS;

    public function index(Request $request)
    {
        $this->ensureRegion($request->user());
        $wilayah = $this->regions($request->user());

        return view('alumni.csv', ['imported' => null, 'failures' => [], 'wilayah' => $wilayah]);
    }

    public function import(Request $request)
    {
        $actor = $request->user();
        $this->ensureRegion($actor);
        $request->validate(['file' => ['required', 'file', 'max:1024']]);
        $canChooseRegion = $actor->hasAnyRole(['super_admin', 'admin']);
        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'xlsx'], true)) {
            return back()->withErrors(['file' => 'Gunakan berkas .csv atau .xlsx.']);
        }

        if ($extension === 'xlsx') {
            abort_unless(class_exists(ZipArchive::class), 503, 'Ekstensi ZIP PHP diperlukan untuk berkas Excel.');
            ['rows' => $rows, 'failures' => $failures] = AlumniExcel::parse($request->file('file')->getRealPath());
        } else {
            $content = $request->file('file')->getContent();
            if (!mb_check_encoding($content, 'UTF-8') || str_contains($content, "\0")) {
                return back()->withErrors(['file' => 'Berkas harus memakai teks UTF-8 tanpa karakter NUL.']);
            }
            $stream = fopen($request->file('file')->getRealPath(), 'r');
            $rows = [];
            $failures = [];
            $record = 1;
            $dataRows = 0;
            try {
                $header = fgetcsv($stream, 0, ',', '"', '');
                if ($header !== false && is_string($header[0] ?? null)) {
                    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
                }
                if ($header !== self::COLUMNS && $header !== AlumniExcel::LEGACY_COLUMNS) {
                    return back()->withErrors(['file' => 'Kolom CSV harus persis: '.implode(', ', self::COLUMNS).'.']);
                }
                $offset = ftell($stream);
                $fieldPattern = '(?:"(?:[^"]++|"")*+"|[^",\r\n]*+)';
                while (($row = fgetcsv($stream, 0, ',', '"', '')) !== false) {
                    $end = ftell($stream);
                    $raw = substr($content, $offset, $end - $offset);
                    $offset = $end;
                    // fgetcsv accepts broken quotes and can consume later records silently.
                    if (preg_match('/\A'.$fieldPattern.'(?:,'.$fieldPattern.')*+(?:\r\n|\n|\r)?\z/D', $raw) !== 1) {
                        return back()->withErrors(['file' => 'Struktur tanda kutip CSV tidak valid pada record '.($record + 1).'. Tidak ada akun diimpor.']);
                    }
                    $record++;
                    if ($row === [null]) {
                        continue;
                    }
                    $dataRows++;
                    if ($dataRows > 200) {
                        return back()->withErrors(['file' => 'Maksimal 200 baris data per file.']);
                    }
                    if (count($row) !== count(self::COLUMNS)) {
                        $failures[] = ['line' => $record, 'messages' => ['Jumlah kolom tidak sesuai.']];
                        continue;
                    }
                    $rows[] = ['line' => $record, 'values' => array_combine($header, $row)];
                }
            } finally {
                fclose($stream);
            }
        }

        $role = Role::firstOrCreate(['name' => 'alumni']);
        $imported = 0;
        $wilayah = $this->regions($actor);
        $regionsByName = $wilayah->groupBy(fn ($region) => mb_strtolower(trim($region->name)));
        foreach ($rows as $entry) {
            $row = $entry['values'];
            $row = array_map(fn ($value) => trim($value), $row);
            $row['email'] = mb_strtolower($row['email']);
            if (array_key_exists('wilayah', $row)) {
                $regionName = $row['wilayah'];
                $matches = $regionsByName->get(mb_strtolower($regionName), collect());
                if ($regionName === '' && !$canChooseRegion) {
                    $row['wilayah_id'] = (string) $actor->wilayah_id;
                } elseif ($matches->count() !== 1) {
                    $failures[] = ['line' => $entry['line'], 'messages' => [
                        $matches->isEmpty() ? 'Wilayah tidak ditemukan atau di luar izin akun.' : 'Nama wilayah ambigu; gunakan nama wilayah yang unik.',
                    ]];
                    continue;
                } else {
                    $row['wilayah_id'] = (string) $matches->first()->id;
                }
                unset($row['wilayah']);
            }
            if (!$canChooseRegion && $row['wilayah_id'] === '') {
                $row['wilayah_id'] = (string) $actor->wilayah_id;
            }
            $validator = Validator::make($row, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'wilayah_id' => ['required', 'integer', 'exists:wilayah,id'],
                'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')],
                'angkatan' => ['nullable', 'integer', 'between:0,65535'],
                'pekerjaan' => ['nullable', 'string', 'max:100'],
            ]);
            if ($validator->fails() || (!$canChooseRegion && (int) $row['wilayah_id'] !== (int) $actor->wilayah_id)) {
                $messages = $validator->errors()->all();
                if (!$canChooseRegion && (int) $row['wilayah_id'] !== (int) $actor->wilayah_id) {
                    $messages[] = 'Wilayah tidak sesuai dengan akun koorda.';
                }
                $failures[] = ['line' => $entry['line'], 'messages' => $messages];
                continue;
            }

            DB::transaction(function () use ($row, $actor, $role) {
                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'wilayah_id' => (int) $row['wilayah_id'],
                    'phone' => $row['phone'] === '' ? null : $row['phone'],
                    'angkatan' => $row['angkatan'] === '' ? null : (int) $row['angkatan'],
                    'pekerjaan' => $row['pekerjaan'] === '' ? null : $row['pekerjaan'],
                    'password' => Hash::make(Str::random(40)),
                    'status' => User::STATUS_PENDING,
                    'email_verified_at' => null,
                    'level_id' => null,
                ]);
                $user->roles()->attach($role->id);
                AuditLog::create([
                    'actor_id' => $actor->id,
                    'action' => 'alumni.import',
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'payload_json' => ['name' => $user->name, 'email' => $user->email, 'wilayah_id' => $user->wilayah_id],
                    'created_at' => now(),
                ]);
            });
            $imported++;
        }

        return view('alumni.csv', compact('imported', 'failures', 'wilayah'));
    }

    public function template(Request $request)
    {
        $this->ensureRegion($request->user());
        abort_unless(class_exists(ZipArchive::class), 503, 'Ekstensi ZIP PHP diperlukan untuk berkas Excel.');
        $book = AlumniExcel::template();
        return response()->streamDownload(fn () => (new Xlsx($book))->save('php://output'),
            'template-alumni.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function exportExcel(AlumniIndexRequest $request)
    {
        $this->ensureRegion($request->user());
        abort_unless(class_exists(ZipArchive::class), 503, 'Ekstensi ZIP PHP diperlukan untuk berkas Excel.');
        $query = User::alumniListing($request->user(), $request->validated());
        $book = AlumniExcel::export($query);
        return response()->streamDownload(fn () => (new Xlsx($book))->save('php://output'),
            'alumni.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function export(AlumniIndexRequest $request)
    {
        $this->ensureRegion($request->user());
        $query = User::alumniListing($request->user(), $request->validated());

        return response()->streamDownload(function () use ($query) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, self::COLUMNS, ',', '"', '');
            foreach ($query->with('wilayah:id,name')->lazy(500) as $user) {
                $fields = [];
                foreach (self::COLUMNS as $column) {
                    $value = (string) (($column === 'wilayah' ? $user->wilayah?->name : $user->{$column}) ?? '');
                    // Spreadsheet apps can execute formulas in data cells.
                    $fields[] = preg_match('/^(?:\s*[=+\-@]|[\t\r\n])/u', $value) ? "'".$value : $value;
                }
                fputcsv($stream, $fields, ',', '"', '');
            }
            fclose($stream);
        }, 'alumni.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureRegion(User $actor): void
    {
        abort_if(!$actor->hasAnyRole(['super_admin', 'admin']) && !$actor->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');
    }

    private function regions(User $actor)
    {
        return Wilayah::select('id', 'name')
            ->when(!$actor->hasAnyRole(['super_admin', 'admin']), fn ($query) => $query->whereKey($actor->wilayah_id))
            ->orderBy('name')->get();
    }
}
