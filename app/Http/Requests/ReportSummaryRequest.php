<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->status === 'active' && $user->can('review-report')
            && $user->hasAnyRole(['admin', 'super_admin', 'koorda'])
            && ($user->hasAnyRole(['admin', 'super_admin']) || $user->wilayah_id);
    }

    public function validationData(): array
    {
        $data = $this->query();
        if ($this->user() && ! $this->user()->hasAnyRole(['admin', 'super_admin'])) {
            $data['wilayah_id'] = $this->user()->wilayah_id;
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'wilayah_id' => ['nullable', 'integer', 'exists:wilayah,id'],
            'status' => ['nullable', Rule::in(['submitted', 'approved', 'rejected'])],
            'dari' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'],
            'sampai' => array_merge(['nullable', 'date_format:Y-m-d', 'after_or_equal:1000-01-01', 'before_or_equal:9999-12-31'], $this->filled('dari') ? ['after_or_equal:dari'] : []),
        ];
    }
}
