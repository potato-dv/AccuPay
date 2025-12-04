<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class SubmitSupportReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:technical,payroll,leave,attendance,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Please select a support ticket type.',
            'type.in' => 'Invalid support ticket type selected.',
        ];
    }
}
