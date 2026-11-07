<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerificationApproveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            '_return' => ['nullable', 'string', 'max:2048', 'starts_with:'.route('alumni.index', [], false).'?'],
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')->where('is_active', true)],
            'note'     => ['nullable','string','max:500'],
        ];
    }
    public function messages(): array
    {
        return ['level_id.required' => 'Pilih level awal.', 'level_id.exists' => 'Pilih level aktif yang tersedia.',
            'note.max' => 'Catatan maksimal 500 karakter.'];
    }
}
