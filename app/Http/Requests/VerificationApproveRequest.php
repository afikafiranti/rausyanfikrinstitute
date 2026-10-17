<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerificationApproveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')->where('is_active', true)],
            'note'     => ['nullable','string','max:500'],
        ];
    }
}
