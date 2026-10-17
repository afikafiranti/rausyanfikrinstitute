<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKajianReportRequest;
use App\Http\Requests\UpdateKajianReportRequest;
use App\Models\KajianReport;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Throwable;

class ReportController extends Controller
{
    public function listing(Request $request)
    {
        $reports = $this->visibleReports($request)
            ->select(['id', 'user_id', 'wilayah_id', 'tanggal', 'judul', 'status', 'total_peserta'])
            ->with(['user:id,name', 'wilayah:id,name'])
            ->orderByDesc('tanggal')->orderByDesc('id')->paginate(12);

        return view('laporan.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $this->visibleReports($request);
        $canChooseRegion = $request->user()->hasAnyRole(['super_admin', 'admin']);
        $regions = Wilayah::query()->when(! $canChooseRegion, fn ($query) => $query->whereKey($request->user()->wilayah_id))
            ->orderBy('name')->get(['id', 'name']);

        return view('laporan.create', compact('regions', 'canChooseRegion'));
    }

    public function store(StoreKajianReportRequest $request)
    {
        $this->visibleReports($request);
        $data = $request->safe()->except('foto');
        $data['user_id'] = $request->user()->id;
        $data['status'] = KajianReport::STATUS_DRAFT;
        $data['total_peserta'] = (int) $data['peserta_l'] + (int) $data['peserta_p'];
        $path = null;

        try {
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('report-photos', 'local');
                if (! $path) {
                    throw new RuntimeException('Foto laporan gagal disimpan.');
                }
                $data['foto_url'] = $path;
            }
            $report = DB::transaction(function () use ($data) {
                $report = new KajianReport($data);
                if (! $report->save()) {
                    throw new RuntimeException('Draf laporan gagal disimpan.');
                }

                return $report;
            });
        } catch (Throwable $exception) {
            if ($path) {
                try {
                    if (! Storage::disk('local')->delete($path)) {
                        report(new RuntimeException('Foto laporan yang gagal disimpan tidak dapat dibersihkan.'));
                    }
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }
            throw $exception;
        }

        return redirect()->route('laporan.show', $report)->with('success', 'Draf tersimpan. Periksa kembali isian sebelum mengirim laporan.');
    }

    public function edit(Request $request, KajianReport $report)
    {
        $report = $this->ownedDraft($request, $report->id);
        $canChooseRegion = false;
        $regions = Wilayah::query()->whereKey($report->wilayah_id)->get(['id', 'name']);

        return view('laporan.create', compact('report', 'regions', 'canChooseRegion'));
    }

    public function update(UpdateKajianReportRequest $request, KajianReport $report)
    {
        $path = null;
        $oldPath = null;
        try {
            DB::transaction(function () use ($request, $report, &$path, &$oldPath) {
                $draft = $this->ownedDraft($request, $report->id, true);
                $data = $request->safe()->except('foto');
                $data['total_peserta'] = (int) $data['peserta_l'] + (int) $data['peserta_p'];
                if ($request->hasFile('foto')) {
                    $path = $request->file('foto')->store('report-photos', 'local');
                    if (! $path) {
                        throw new RuntimeException('Foto laporan gagal disimpan.');
                    }
                    $oldPath = $draft->foto_url;
                    $data['foto_url'] = $path;
                }
                if (! $draft->fill($data)->save()) {
                    throw new RuntimeException('Draf laporan gagal diperbarui.');
                }
            });
        } catch (Throwable $exception) {
            $this->deletePrivatePhoto($path);
            throw $exception;
        }
        $this->deletePrivatePhoto($oldPath);

        return redirect()->route('laporan.show', $report)->with('success', 'Perubahan draf tersimpan.');
    }

    public function submit(Request $request, KajianReport $report)
    {
        DB::transaction(function () use ($request, $report) {
            $draft = $this->ownedDraft($request, $report->id, true);
            $data = $draft->getAttributes();
            $rules = (new StoreKajianReportRequest)->rules();
            unset($rules['foto']);
            Validator::make($data, $rules)->validate();
            $draft->status = KajianReport::STATUS_SUBMITTED;
            $draft->total_peserta = (int) $draft->peserta_l + (int) $draft->peserta_p;
            $draft->reviewed_by = null;
            $draft->reviewed_at = null;
            $draft->reject_reason = null;
            if (! $draft->save()) {
                throw new RuntimeException('Laporan gagal dikirim.');
            }
        });

        return redirect()->route('laporan.show', $report)->with('success', 'Laporan berhasil dikirim dan menunggu review. Isian laporan kini terkunci.');
    }

    private function ownedDraft(Request $request, int $id, bool $lock = false): KajianReport
    {
        $query = $this->visibleReports($request)->where('user_id', $request->user()->id);
        $draft = $query->when($lock, fn ($query) => $query->lockForUpdate())->findOrFail($id);
        abort_unless($draft->status === KajianReport::STATUS_DRAFT, 409, 'Laporan sudah dikirim dan tidak dapat diubah.');

        return $draft;
    }

    private function deletePrivatePhoto(?string $path): void
    {
        if (! $path || ! preg_match('~\Areport-photos/[a-zA-Z0-9]+\.(?:jpg|jpeg|png)\z~', $path)) {
            return;
        }
        try {
            if (! Storage::disk('local')->delete($path)) {
                report(new RuntimeException('Foto laporan tidak dapat dibersihkan.'));
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function show(Request $request, KajianReport $report)
    {
        $report = $this->visibleReports($request)->with(['user:id,name', 'wilayah:id,name', 'reviewer:id,name'])->findOrFail($report->id);
        $canReview = $request->user()->can('review-report')
            && $report->user_id !== $request->user()->id
            && $report->status === KajianReport::STATUS_SUBMITTED;

        return view('laporan.show', compact('report', 'canReview'));
    }

    public function photo(Request $request, KajianReport $report)
    {
        $report = $this->visibleReports($request)->findOrFail($report->id);
        $path = $report->foto_url;
        abort_unless(is_string($path) && preg_match('~\Areport-photos/[a-zA-Z0-9]+\.(?:jpg|jpeg|png)\z~', $path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function visibleReports(Request $request): Builder
    {
        $user = $request->user();
        abort_unless($user->status === 'active' && $user->hasAnyRole(['super_admin', 'admin', 'koorda', 'alumni']), 403);
        $query = KajianReport::query();
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $query;
        }
        abort_unless($user->wilayah_id, 403);
        $query->where('wilayah_id', $user->wilayah_id);
        if ($user->hasRole('koorda')) {
            return $query->where(fn ($query) => $query->where('status', '!=', KajianReport::STATUS_DRAFT)->orWhere('user_id', $user->id));
        }

        return $query->where('user_id', $user->id);
    }

    public function index(Request $request)
    {
        $query = $this->reviewableReports($request);
        $wilayahId = (int) $request->attributes->get('wilayah_id');
        if ($request->user()->hasAnyRole(['super_admin', 'admin'])) {
            $data = Validator::make($request->query(), ['wilayah_id' => ['nullable', 'integer', 'exists:wilayah,id']])->validate();
            $wilayahId = (int) ($data['wilayah_id'] ?? 0);
            if ($wilayahId) {
                $query->where('wilayah_id', $wilayahId);
            }
        }
        $reports = $query->where('status', KajianReport::STATUS_SUBMITTED)
            ->where('user_id', '!=', $request->user()->id)
            ->select(['id', 'user_id', 'wilayah_id', 'tanggal', 'judul', 'status'])
            ->with(['user:id,name', 'wilayah:id,name'])
            ->orderBy('tanggal')->orderBy('id')->paginate(12)->withQueryString();
        $regionName = $wilayahId ? Wilayah::query()->whereKey($wilayahId)->value('name') : null;

        return view('laporan.review', compact('wilayahId', 'reports', 'regionName'));
    }

    public function approve(Request $request, KajianReport $report)
    {
        return $this->decide($request, $report, KajianReport::STATUS_APPROVED);
    }

    public function reject(Request $request, KajianReport $report)
    {
        return $this->decide($request, $report, KajianReport::STATUS_REJECTED);
    }

    private function reviewableReports(Request $request): Builder
    {
        abort_unless($request->user()->can('review-report'), 403);

        return $this->visibleReports($request);
    }

    private function decide(Request $request, KajianReport $report, string $status)
    {
        DB::transaction(function () use ($request, $report, $status) {
            $record = $this->reviewableReports($request)->lockForUpdate()->findOrFail($report->id);
            abort_if($record->user_id === $request->user()->id, 403, 'Pelapor tidak dapat mereview laporannya sendiri.');
            abort_unless($record->status === KajianReport::STATUS_SUBMITTED, 409, 'Hanya laporan yang menunggu review dapat diputuskan.');
            $reason = null;
            if ($status === KajianReport::STATUS_REJECTED) {
                $data = $request->validate(['reject_reason' => ['required', 'string', 'max:16383']]);
                $reason = $data['reject_reason'];
            }
            $record->status = $status;
            $record->reviewed_by = $request->user()->id;
            $record->reviewed_at = now();
            $record->reject_reason = $reason;
            if (! $record->save()) {
                throw new RuntimeException('Keputusan laporan gagal disimpan.');
            }
        });

        return redirect()->route('laporan.show', $report)->with('success', $status === KajianReport::STATUS_APPROVED ? 'Laporan disetujui.' : 'Laporan ditolak. Alasan penolakan tersimpan.');
    }
}
