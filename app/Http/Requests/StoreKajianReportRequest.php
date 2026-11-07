<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKajianReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('review-report') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()?->hasRole('koorda') && ! $this->user()->hasAnyRole(['super_admin', 'admin'])) {
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

    public function attributes(): array
    {
        return [
            'wilayah_id' => 'Wilayah',
            'tanggal' => 'Tanggal kegiatan',
            'judul' => 'Judul kajian',
            'tempat' => 'Tempat',
            'pemateri' => 'Pemateri',
            'peserta_l' => 'Peserta laki-laki',
            'peserta_p' => 'Peserta perempuan',
            'durasi_menit' => 'Durasi',
            'catatan' => 'Catatan kegiatan',
            'foto' => 'Foto dokumentasi',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'exists' => ':attribute yang dipilih tidak tersedia.',
            'date_format' => ':attribute harus berupa tanggal yang valid.',
            'after_or_equal' => ':attribute paling awal :date.',
            'before_or_equal' => ':attribute paling akhir :date.',
            'min' => ['numeric' => ':attribute minimal :min.'],
            'max' => [
                'numeric' => ':attribute maksimal :max.',
                'string' => ':attribute maksimal :max karakter.',
            ],
            'foto.image' => 'Pilih foto dalam format JPG atau PNG.',
            'foto.mimes' => 'Pilih foto dalam format JPG atau PNG.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
