<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ReportWorkspaceRequest extends ReportSummaryRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['status'] = ['nullable', Rule::in(['draft', 'submitted', 'approved', 'rejected'])];

        return $rules;
    }
}
