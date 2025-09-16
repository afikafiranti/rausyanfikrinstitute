<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationApproveRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'level_id' => ['required','exists:levels,id'],
            'note'     => ['nullable','string','max:500'],
        ];
    }
}
