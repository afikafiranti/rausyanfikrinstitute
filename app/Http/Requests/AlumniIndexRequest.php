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
            'view'       => ['nullable','in:all,verification'],
            'sort'       => ['nullable','in:name,email,angkatan,wilayah,role,level,status,created_at'],
            'dir'        => ['nullable','in:asc,desc'],
            'page'       => ['nullable','integer','min:1'],
            'per_page'   => ['nullable','integer','between:5,100'],
        ];
    }
}
