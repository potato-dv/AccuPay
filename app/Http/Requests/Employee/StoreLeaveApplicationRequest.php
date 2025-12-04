<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeaveApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_type' => 'required|string|in:Vacation Leave,Sick Leave,Emergency Leave,Maternity Leave,Paternity Leave',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.after_or_equal' => 'Leave start date must be today or a future date.',
            'end_date.after_or_equal' => 'Leave end date must be on or after the start date.',
        ];
    }
}
