<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AlumniIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'q'          => ['nullable','string','max:100'],
            'angkatan'   => ['nullable','integer','between:0,65535'],
            'wilayah_id' => ['nullable','integer','min:1'],
            'level_id'   => ['nullable','integer','min:1'],
            'ab'         => ['nullable','in:iya,tidak'],
            'sort'       => ['nullable','in:name,email,angkatan,wilayah,level,status,created_at'],
            'dir'        => ['nullable','in:asc,desc'],
            'per_page'   => ['nullable','integer','between:5,100'],
        ];
    }
}
