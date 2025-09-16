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
            'name'      => ['required','string','max:150'],
            'email'     => ['required','email','max:191',"unique:users,email,{$id}"],
            'phone'     => ['nullable','string','max:30',"unique:users,phone,{$id}"],
            'angkatan'  => ['nullable','string','max:20'],
            'pekerjaan' => ['nullable','string','max:100'],
            'wilayah_id'=> ['nullable','exists:wilayah,id'],
            'photo'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ];
    }
}
