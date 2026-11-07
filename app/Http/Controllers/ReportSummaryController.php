<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportSummaryRequest;
use App\Models\KajianReport;
use Illuminate\Database\Eloquent\Builder;

class ReportSummaryController extends Controller
{
    public function index(ReportSummaryRequest $request)
    {
        return redirect()->route('laporan.index', array_filter($request->validated(), fn ($value) => $value !== null && $value !== ''));
    }

    public function export(ReportSummaryRequest $request)
    {
        $query = $this->filteredReports($request)
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

    private function filteredReports(ReportSummaryRequest $request): Builder
    {
        return KajianReport::query()->visibleTo($request->user())
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->matchingFilters($request->validated());
    }
}
