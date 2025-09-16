<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AlumniIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->exists();
    }

    public function rules(): array
    {
        return [
            'q'          => ['nullable','string','max:100'],
            'angkatan'   => ['nullable','string','max:20'],
            'wilayah_id' => ['nullable','integer','min:1'],
            'level_id'   => ['nullable','integer','min:1'],
            'status'     => ['nullable','in:pending,active,suspended'],
            'sort_by'    => ['nullable','in:name,email,angkatan,level_id,wilayah_id,status,created_at'],
            'sort_dir'   => ['nullable','in:asc,desc'],
        ];
    }
}
