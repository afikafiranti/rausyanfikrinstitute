<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportSummaryRequest;
use App\Models\KajianReport;
use App\Support\ReportExcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class ReportSummaryController extends Controller
{
    public function index(ReportSummaryRequest $request)
    {
        return redirect()->route('laporan.index', array_filter($request->validated(), fn ($value) => $value !== null && $value !== ''));
    }

    public function export(ReportSummaryRequest $request)
    {
        abort_unless(class_exists(ZipArchive::class), 503, 'Ekstensi ZIP PHP diperlukan untuk berkas Excel.');
        $query = $this->filteredReports($request)
            ->select(['id', 'tanggal', 'judul', 'wilayah_id', 'user_id', 'pemateri', 'tempat', 'peserta_l', 'peserta_p', 'durasi_menit', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason'])
            ->with(['wilayah:id,name', 'user:id,name', 'reviewer:id,name']);
        if ((clone $query)->count() > ReportExcel::MAX_EXPORT_ROWS) {
            throw ValidationException::withMessages(['export' => ReportExcel::EXPORT_LIMIT_MESSAGE]);
        }
        $book = ReportExcel::export($query);

        return response()->streamDownload(function () use ($book) {
            try {
                (new Xlsx($book))->save('php://output');
            } finally {
                $book->disconnectWorksheets();
            }
        }, 'laporan-kajian.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function filteredReports(ReportSummaryRequest $request): Builder
    {
        return KajianReport::query()->visibleTo($request->user())
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->matchingFilters($request->validated());
    }
}
