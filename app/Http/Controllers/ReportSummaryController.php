<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportSummaryRequest;
use App\Models\KajianReport;
use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;

class ReportSummaryController extends Controller
{
    public function index(ReportSummaryRequest $request)
    {
        $filters = $request->validated();
        $query = $this->filteredReports($filters);
        $groups = (clone $query)->selectRaw('status, COUNT(*) AS reports, SUM(peserta_l) AS male, SUM(peserta_p) AS female')
            ->groupBy('status')->get();
        $totals = ['reports' => 0, 'participants' => 0, 'male' => 0, 'female' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($groups as $group) {
            $totals[$group->status] = (int) $group->reports;
            $totals['reports'] += (int) $group->reports;
            $totals['male'] += (int) $group->male;
            $totals['female'] += (int) $group->female;
        }
        $totals['participants'] = $totals['male'] + $totals['female'];
        $reports = $query->select(['id', 'tanggal', 'judul', 'wilayah_id', 'user_id', 'status', 'peserta_l', 'peserta_p'])
            ->with(['wilayah:id,name', 'user:id,name'])->orderByDesc('tanggal')->orderByDesc('id')->paginate(12)
            ->appends(array_filter($filters, fn ($value) => $value !== null && $value !== ''));
        $canChooseRegion = $request->user()->hasAnyRole(['admin', 'super_admin']);
        $regions = Wilayah::query()->when(! $canChooseRegion, fn ($query) => $query->whereKey($request->user()->wilayah_id))
            ->orderBy('name')->get(['id', 'name']);

        return response()->view('laporan.summary', compact('reports', 'filters', 'totals', 'regions', 'canChooseRegion'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function export(ReportSummaryRequest $request)
    {
        $query = $this->filteredReports($request->validated())
            ->select(['id', 'tanggal', 'judul', 'wilayah_id', 'user_id', 'pemateri', 'tempat', 'peserta_l', 'peserta_p', 'durasi_menit', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason'])
            ->with(['wilayah:id,name', 'user:id,name', 'reviewer:id,name']);

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            try {
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, ['ID', 'Tanggal', 'Judul', 'Wilayah', 'Pelapor', 'Pemateri', 'Tempat', 'Peserta L', 'Peserta P', 'Total Peserta', 'Durasi Menit', 'Status', 'Reviewer', 'Waktu Review', 'Wilayah ID', 'Alasan Penolakan'], ',', '"', '', "\r\n");
                foreach ($query->lazyById(500) as $report) {
                    $row = [$report->id, $report->tanggal->format('Y-m-d'), $report->judul, $report->wilayah?->name ?? 'Wilayah belum ditentukan', $report->user?->name, $report->pemateri, $report->tempat,
                        $report->peserta_l, $report->peserta_p, (int) $report->peserta_l + (int) $report->peserta_p, $report->durasi_menit, $report->status,
                        $report->reviewer?->name, $report->reviewed_at?->format('Y-m-d H:i:s'), $report->wilayah_id, $report->reject_reason];
                    $row = array_map(function ($value) {
                        if (is_string($value) && preg_match('/\A(?:[\t\r\n]|[\s\p{Z}\x{FEFF}\x00-\x1F]*[=+\-@])/u', $value)) {
                            return "'".$value;
                        }

                        return $value;
                    }, $row);
                    fputcsv($output, $row, ',', '"', '', "\r\n");
                }
            } finally {
                fclose($output);
            }
        }, 'laporan-kajian.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function filteredReports(array $filters): Builder
    {
        return KajianReport::query()->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->when($filters['wilayah_id'] ?? null, fn ($query, $value) => $query->where('wilayah_id', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['dari'] ?? null, fn ($query, $value) => $query->where('tanggal', '>=', $value))
            ->when($filters['sampai'] ?? null, fn ($query, $value) => $query->where('tanggal', '<=', $value));
    }
}
