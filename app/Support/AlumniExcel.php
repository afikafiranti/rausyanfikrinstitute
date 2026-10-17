<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ZipArchive;

class AlumniExcel
{
    public const COLUMNS = ['name', 'email', 'wilayah', 'phone', 'angkatan', 'pekerjaan'];
    public const LEGACY_COLUMNS = ['name', 'email', 'wilayah_id', 'phone', 'angkatan', 'pekerjaan'];

    public static function parse(string $path): array
    {
        self::checkArchive($path);
        try {
            $reader = new XlsxReader();
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            $info = $reader->listWorksheetInfo($path);
            if (count($info) !== 1) {
                throw ValidationException::withMessages(['file' => 'Berkas Excel harus memiliki tepat satu sheet.']);
            }
            if ($info[0]['totalRows'] > 201 || $info[0]['totalColumns'] > 6) {
                throw ValidationException::withMessages(['file' => 'Berkas Excel melebihi 200 baris atau enam kolom yang diizinkan.']);
            }
            $book = $reader->load($path);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['file' => 'Berkas Excel tidak dapat dibaca. Gunakan format .xlsx yang valid.']);
        }

        try {
            if ($book->getSheetCount() !== 1) {
                throw ValidationException::withMessages(['file' => 'Berkas Excel harus memiliki tepat satu sheet.']);
            }
            $sheet = $book->getActiveSheet();
            if ($sheet->getHighestDataRow() > 201) {
                throw ValidationException::withMessages(['file' => 'Maksimal 200 baris data per berkas Excel.']);
            }
            $headers = [];
            for ($column = 1; $column <= 6; $column++) {
                $cell = $sheet->getCell([$column, 1]);
                $headers[] = $cell->getDataType() === DataType::TYPE_FORMULA ? null : $cell->getValue();
            }
            if ($headers !== self::COLUMNS && $headers !== self::LEGACY_COLUMNS) {
                throw ValidationException::withMessages(['file' => 'Kolom Excel harus persis: '.implode(', ', self::COLUMNS).'.']);
            }

            $rows = [];
            $failures = [];
            for ($line = 2; $line <= $sheet->getHighestDataRow(); $line++) {
                $values = [];
                $hasFormula = false;
                for ($column = 1; $column <= 6; $column++) {
                    $cell = $sheet->getCell([$column, $line]);
                    $hasFormula = $hasFormula || $cell->getDataType() === DataType::TYPE_FORMULA;
                    $values[] = $cell->getValue();
                }
                if (array_filter($values, fn ($value) => $value !== null && $value !== '') === []) {
                    continue;
                }
                $messages = [];
                if ($hasFormula) {
                    $messages[] = 'Rumus Excel tidak didukung.';
                }
                if ($values[3] !== null && $values[3] !== '' && !is_string($values[3])) {
                    $messages[] = 'Kolom phone harus berformat teks agar nol di depan tidak hilang.';
                }
                if ($messages) {
                    $failures[] = ['line' => $line, 'messages' => $messages];
                    continue;
                }
                $rows[] = ['line' => $line, 'values' => array_combine($headers,
                    array_map(fn ($value) => $value === null ? '' : (string) $value, $values))];
            }

            return ['rows' => $rows, 'failures' => $failures];
        } finally {
            $book->disconnectWorksheets();
        }
    }

    public static function template(): Spreadsheet
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Alumni');
        $sheet->fromArray(self::COLUMNS);
        $sheet->getStyle('D')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:F1');
        foreach (['A' => 28, 'B' => 36, 'C' => 16, 'D' => 20, 'E' => 16, 'F' => 28] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        return $book;
    }

    public static function export(Builder $query): Spreadsheet
    {
        // shortcut: workbook stays in memory; use a streaming XLSX writer when export volume exceeds PHP memory.
        $book = self::template();
        $sheet = $book->getActiveSheet();
        $line = 2;
        foreach ($query->with('wilayah:id,name')->lazy(500) as $user) {
            foreach (self::COLUMNS as $index => $column) {
                $value = $column === 'wilayah' ? $user->wilayah?->name : $user->{$column};
                $sheet->setCellValueExplicit([$index + 1, $line], (string) ($value ?? ''), DataType::TYPE_STRING);
            }
            $line++;
        }

        return $book;
    }

    private static function checkArchive(string $path): void
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['file' => 'Berkas harus berformat .xlsx yang valid.']);
        }
        try {
            if ($zip->numFiles > 1000) {
                throw ValidationException::withMessages(['file' => 'Berkas Excel terlalu kompleks.']);
            }
            $expanded = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if ($stat === false || ($expanded += $stat['size']) > 10 * 1024 * 1024) {
                    throw ValidationException::withMessages(['file' => 'Ukuran data Excel terlalu besar.']);
                }
            }
        } finally {
            $zip->close();
        }
    }
}
