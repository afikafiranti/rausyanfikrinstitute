<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKajianReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->status === 'active'
            && $this->user()->hasAnyRole(['super_admin', 'admin', 'koorda', 'alumni']);
    }

    protected function prepareForValidation(): void
    {
        if ($this->user() && ! $this->user()->hasAnyRole(['super_admin', 'admin'])) {
            $this->merge(['wilayah_id' => $this->user()->wilayah_id]);
        }
    }

    public function rules(): array
    {
        return [
            'wilayah_id' => ['required', 'integer', 'exists:wilayah,id'],
            'tanggal' => ['required', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'judul' => ['required', 'string', 'max:180'],
            'tempat' => ['nullable', 'string', 'max:150'],
            'pemateri' => ['nullable', 'string', 'max:150'],
            'peserta_l' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'peserta_p' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'durasi_menit' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'catatan' => ['nullable', 'string', 'max:16383'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
