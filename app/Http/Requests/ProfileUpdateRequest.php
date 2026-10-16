<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $id = $this->user()->id;

        return [
            'name'      => ['required','string','max:255'],
            'email'     => ['required','email','max:255',"unique:users,email,{$id}"],
            'phone'     => ['nullable','string','max:30',"unique:users,phone,{$id}"],
            'angkatan'  => ['nullable','integer','between:0,65535'],
            'pekerjaan' => ['nullable','string','max:100'],
            'wilayah_id'=> ['nullable','integer','exists:wilayah,id'],
            'tempat_lahir' => ['nullable','string','max:100'],
            'tanggal_lahir' => ['nullable','date_format:Y-m-d'],
            'pendidikan_terakhir' => ['nullable','string','max:100'],
            'kampus' => ['nullable','string','max:150'],
            'status_pernikahan' => ['nullable','in:lajang,menikah,duda/janda'],
            'ab' => ['nullable','in:iya,tidak'],
            'photo'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
