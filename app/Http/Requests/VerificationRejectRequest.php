<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRejectRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            '_return' => ['nullable', 'string', 'max:2048', 'starts_with:'.route('alumni.index', [], false).'?'],
            'reason' => ['required','string','min:5','max:500'],
        ];
    }
    public function messages(): array
    {
        return ['reason.required' => 'Tulis alasan perbaikan.', 'reason.min' => 'Alasan minimal 5 karakter.',
            'reason.max' => 'Alasan maksimal 500 karakter.'];
    }
}
