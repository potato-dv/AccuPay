<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $employeeId = $this->route('id');

        return [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => "required|email|unique:employees,email,{$employeeId}|max:255",
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'birthdate' => 'required|date|before:today|before:-18 years',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date|before_or_equal:today',
            'basic_salary' => 'nullable|numeric|min:0|max:999999999.99',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999.99',
            'employment_type' => 'required|in:full-time,part-time,contract',
            'status' => 'required|in:active,on-leave,terminated',
            'work_schedule_id' => 'required|exists:work_schedules,id',
            'tax_id_number' => "nullable|string|max:50|unique:employees,tax_id_number,{$employeeId}",
            'sss_number' => 'nullable|string|max:50',
            'philhealth_number' => 'nullable|string|max:50',
            'pagibig_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'vacation_leave_credits' => 'nullable|integer|min:0',
            'sick_leave_credits' => 'nullable|integer|min:0',
            'emergency_leave_credits' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ];
    }
}
