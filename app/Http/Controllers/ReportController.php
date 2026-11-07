<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKajianReportRequest;
use App\Http\Requests\UpdateKajianReportRequest;
use App\Http\Requests\ReportWorkspaceRequest;
use App\Models\KajianReport;
use App\Models\AuditLog;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Throwable;

class ReportController extends Controller
{
    public function listing(ReportWorkspaceRequest $request)
    {
        $filters = $request->validated();
        $query = $this->visibleReports($request)->matchingFilters($filters);
        $groups = (clone $query)->selectRaw('status, COUNT(*) AS reports, SUM(peserta_l) AS male, SUM(peserta_p) AS female')
            ->groupBy('status')->get();
        $totals = ['reports' => 0, 'draft' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0, 'male' => 0, 'female' => 0, 'participants' => 0];
        foreach ($groups as $group) {
            $totals[$group->status] = (int) $group->reports;
            $totals['reports'] += (int) $group->reports;
            if ($group->status !== KajianReport::STATUS_DRAFT) {
                $totals['male'] += (int) $group->male;
                $totals['female'] += (int) $group->female;
            }
        }
        $totals['participants'] = $totals['male'] + $totals['female'];
        $lastPage = max(1, (int) ceil($totals['reports'] / 12));
        if (Paginator::resolveCurrentPage() > $lastPage) {
            $target = array_filter($filters, fn ($value) => $value !== null && $value !== '');
            if ($lastPage > 1) {
                $target['page'] = $lastPage;
            }

            return redirect()->route('laporan.index', $target);
        }
        $direction = ($filters['status'] ?? '') === KajianReport::STATUS_SUBMITTED ? 'asc' : 'desc';
        $reports = $query
            ->select(['id', 'user_id', 'wilayah_id', 'tanggal', 'judul', 'status', 'total_peserta', 'peserta_l', 'peserta_p', 'foto_url'])
            ->with(['user:id,name', 'wilayah:id,name'])
            ->orderBy('tanggal', $direction)->orderBy('id', $direction)->paginate(12)
            ->appends(array_filter($filters, fn ($value) => $value !== null && $value !== ''));
        $canChooseRegion = $request->user()->hasAnyRole(['super_admin', 'admin']);
        $canManageAllReports = $request->user()->hasRole('super_admin');
        $regions = Wilayah::query()->when(! $canChooseRegion, fn (Builder $query) => $query->whereKey($request->user()->wilayah_id))
            ->orderBy('name')->get(['id', 'name']);

        return response()->view('laporan.index', compact('reports', 'filters', 'totals', 'regions', 'canChooseRegion', 'canManageAllReports'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function create(Request $request)
    {
        $this->visibleReports($request);
        $canChooseRegion = $request->user()->hasAnyRole(['super_admin', 'admin']);
        $regions = Wilayah::query()->when(! $canChooseRegion, fn ($query) => $query->whereKey($request->user()->wilayah_id))
            ->orderBy('name')->get(['id', 'name']);

        return $this->reportView($request, 'laporan.create', 'laporan.partials.form', compact('regions', 'canChooseRegion'));
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

        return $this->reportSaved($request, $report, 'Draf tersimpan. Periksa kembali isian sebelum mengirim laporan.');
    }

    public function edit(Request $request, KajianReport $report)
    {
        $report = $this->manageableReport($request, $report->id);
        $canChooseRegion = false;
        $administrativeEdit = $request->user()->hasRole('super_admin')
            && ($report->user_id !== $request->user()->id || $report->status !== KajianReport::STATUS_DRAFT);
        $regions = Wilayah::query()->whereKey($report->wilayah_id)->get(['id', 'name']);

        return $this->reportView($request, 'laporan.create', 'laporan.partials.form', compact('report', 'regions', 'canChooseRegion', 'administrativeEdit'));
    }

    public function update(UpdateKajianReportRequest $request, KajianReport $report)
    {
        $path = null;
        $oldPath = null;
        try {
            DB::transaction(function () use ($request, $report, &$path, &$oldPath) {
                $draft = $this->manageableReport($request, $report->id, true);
                $before = $request->user()->hasRole('super_admin') ? $this->auditSnapshot($draft) : null;
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
                    throw new RuntimeException('Laporan gagal diperbarui.');
                }
                if ($before !== null) {
                    AuditLog::create([
                        'actor_id' => $request->user()->id,
                        'action' => 'report.update',
                        'entity_type' => 'kajian_report',
                        'entity_id' => $draft->id,
                        'payload_json' => ['administrative' => true, 'before' => $before, 'after' => $this->auditSnapshot($draft)],
                        'created_at' => now(),
                    ]);
                }
            });
        } catch (Throwable $exception) {
            $this->deletePrivatePhoto($path);
            throw $exception;
        }
        $this->deletePrivatePhoto($oldPath);

        return $this->reportSaved($request, $report, 'Perubahan laporan tersimpan.');
    }

    public function submit(Request $request, KajianReport $report)
    {
        DB::transaction(function () use ($request, $report) {
            $draft = $this->ownedDraft($request, $report->id, true);
            $data = $draft->getAttributes();
            $validation = new StoreKajianReportRequest;
            $rules = $validation->rules();
            unset($rules['foto']);
            Validator::make($data, $rules, $validation->messages(), $validation->attributes())->validate();
            $draft->status = KajianReport::STATUS_SUBMITTED;
            $draft->total_peserta = (int) $draft->peserta_l + (int) $draft->peserta_p;
            $draft->reviewed_by = null;
            $draft->reviewed_at = null;
            $draft->reject_reason = null;
            if (! $draft->save()) {
                throw new RuntimeException('Laporan gagal dikirim.');
            }
        });

        return $this->reportSaved($request, $report, 'Laporan berhasil dikirim dan menunggu review. Perubahan selanjutnya hanya dapat dilakukan super admin.');
    }

    public function destroy(Request $request, KajianReport $report)
    {
        $path = DB::transaction(function () use ($request, $report) {
            $draft = $this->manageableReport($request, $report->id, true);
            $before = $request->user()->hasRole('super_admin') ? $this->auditSnapshot($draft) : null;
            $path = $draft->foto_url;
            if (! $draft->delete()) {
                throw new RuntimeException('Laporan gagal dihapus.');
            }
            if ($before !== null) {
                AuditLog::create([
                    'actor_id' => $request->user()->id,
                    'action' => 'report.delete',
                    'entity_type' => 'kajian_report',
                    'entity_id' => $draft->id,
                    'payload_json' => ['administrative' => true, 'before' => $before],
                    'created_at' => now(),
                ]);
            }

            return $path;
        });
        $this->deletePrivatePhoto($path);
        $message = 'Laporan dihapus.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'deleted' => true])->header('Cache-Control', 'private, no-store');
        }

        return redirect()->route('laporan.index')->with('success', $message);
    }

    private function ownedDraft(Request $request, int $id, bool $lock = false): KajianReport
    {
        $query = $this->visibleReports($request)->where('user_id', $request->user()->id);
        $draft = $query->when($lock, fn ($query) => $query->lockForUpdate())->findOrFail($id);
        abort_unless($draft->status === KajianReport::STATUS_DRAFT, 409, 'Tindakan ini hanya tersedia untuk draf milik Anda.');

        return $draft;
    }

    private function manageableReport(Request $request, int $id, bool $lock = false): KajianReport
    {
        if (! $request->user()->hasRole('super_admin')) {
            return $this->ownedDraft($request, $id, $lock);
        }

        return $this->visibleReports($request)->when($lock, fn ($query) => $query->lockForUpdate())->findOrFail($id);
    }

    private function auditSnapshot(KajianReport $report): array
    {
        return $report->only([
            'user_id', 'wilayah_id', 'tanggal', 'tempat', 'judul', 'pemateri', 'peserta_l', 'peserta_p',
            'total_peserta', 'durasi_menit', 'catatan', 'foto_url', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason',
        ]);
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
        $canManageReport = $request->user()->hasRole('super_admin')
            || ($report->user_id === $request->user()->id && $report->status === KajianReport::STATUS_DRAFT);
        $lastAdministrativeChange = AuditLog::query()
            ->where('entity_type', 'kajian_report')->where('entity_id', $report->id)->where('action', 'report.update')
            ->with('actor:id,name')->orderByDesc('created_at')->orderByDesc('id')->first();

        return $this->reportView($request, 'laporan.show', 'laporan.partials.detail', compact('report', 'canReview', 'canManageReport', 'lastAdministrativeChange'));
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
        abort_unless($user?->can('review-report'), 403);
        return KajianReport::query()->visibleTo($user);
    }

    public function index(ReportWorkspaceRequest $request)
    {
        $filters = $request->validated();
        $filters['status'] = KajianReport::STATUS_SUBMITTED;

        return redirect()->route('laporan.index', array_filter($filters, fn ($value) => $value !== null && $value !== ''));
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
                $data = $request->validate(['reject_reason' => ['required', 'string', 'max:16383']], [
                    'reject_reason.required' => 'Tulis alasan penolakan.',
                    'reject_reason.max' => 'Alasan penolakan maksimal 16383 karakter.',
                ]);
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

        return $this->reportSaved($request, $report, $status === KajianReport::STATUS_APPROVED ? 'Laporan disetujui.' : 'Laporan ditolak. Alasan penolakan tersimpan.');
    }

    private function reportView(Request $request, string $page, string $fragment, array $data)
    {
        $isModal = $request->header('X-Report-Modal') === '1';
        $response = response()->view($isModal ? $fragment : $page, $data)
            ->header('Cache-Control', 'private, no-store')
            ->header('Vary', 'X-Report-Modal');

        if ($isModal) {
            $response->header('X-Report-Modal', '1');
        }

        return $response;
    }

    private function reportSaved(Request $request, KajianReport $report, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'modal_url' => route('laporan.show', $report),
            ])->header('Cache-Control', 'private, no-store');
        }

        return redirect()->route('laporan.show', $report)->with('success', $message);
    }
}
