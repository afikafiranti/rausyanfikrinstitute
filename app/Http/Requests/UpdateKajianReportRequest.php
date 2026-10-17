<?php

namespace App\Http\Requests;

class UpdateKajianReportRequest extends StoreKajianReportRequest
{
    public function authorize(): bool
    {
        if (! parent::authorize()) {
            return false;
        }
        $report = $this->route('report');
        abort_unless($report->user_id === $this->user()->id, 404);
        if (! $this->user()->hasAnyRole(['super_admin', 'admin'])) {
            abort_unless($report->wilayah_id === $this->user()->wilayah_id, 404);
        }
        abort_unless($report->status === 'draft', 409, 'Laporan sudah dikirim dan tidak dapat diubah.');

        return true;
    }

    protected function prepareForValidation(): void
    {
        // Wilayah laporan tetap; field ini tidak diterima pada pembaruan.
    }

    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['wilayah_id']);

        return $rules;
    }
}
