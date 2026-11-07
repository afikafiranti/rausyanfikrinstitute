<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportExcel
{
    public const MAX_EXPORT_ROWS = 5000;

    public const EXPORT_LIMIT_MESSAGE = 'Ekspor Excel maksimal 5.000 laporan sesuai wilayah dan filter. Persempit filter lalu coba lagi.';

    private const HEADERS = [
        'ID', 'Tanggal', 'Judul', 'Wilayah', 'Pelapor', 'Pemateri', 'Tempat',
        'Peserta Laki-laki', 'Peserta Perempuan', 'Total Peserta', 'Durasi (menit)',
        'Status', 'Reviewer', 'Waktu Review', 'Wilayah ID', 'Alasan Penolakan',
    ];

    public static function export(Builder $query): Spreadsheet
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Laporan');
        $sheet->fromArray(self::HEADERS);
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:P1');
        foreach (['A' => 10, 'B' => 15, 'C' => 40, 'D' => 24, 'E' => 28, 'F' => 28,
            'G' => 28, 'H' => 19, 'I' => 20, 'J' => 17, 'K' => 18, 'L' => 17,
            'M' => 28, 'N' => 21, 'O' => 14, 'P' => 40] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        try {
            $line = 2;
            foreach ($query->lazyById(500) as $report) {
                if ($line > self::MAX_EXPORT_ROWS + 1) {
                    throw ValidationException::withMessages(['export' => self::EXPORT_LIMIT_MESSAGE]);
                }
                $values = [
                    $report->id, $report->tanggal->format('Y-m-d'), $report->judul,
                    $report->wilayah?->name ?? 'Wilayah belum ditentukan', $report->user?->name,
                    $report->pemateri, $report->tempat, (int) $report->peserta_l,
                    (int) $report->peserta_p, (int) $report->peserta_l + (int) $report->peserta_p,
                    $report->durasi_menit === null ? null : (int) $report->durasi_menit,
                    match ($report->status) {
                        'submitted' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    }, $report->reviewer?->name,
                    $report->reviewed_at?->format('Y-m-d H:i:s'), $report->wilayah_id,
                    $report->reject_reason,
                ];
                foreach ($values as $index => $value) {
                    if ($value === null) {
                        continue;
                    }
                    $type = in_array($index, [0, 7, 8, 9, 10, 14], true) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING;
                    $sheet->setCellValueExplicit([$index + 1, $line], $type === DataType::TYPE_NUMERIC ? $value : (string) $value, $type);
                }
                $line++;
            }

            return $book;
        } catch (\Throwable $exception) {
            $book->disconnectWorksheets();
            throw $exception;
        }
    }
}
