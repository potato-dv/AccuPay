<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        return [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:employees,email|max:255',
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
            'work_schedule_id' => 'required|exists:work_schedules,id',
            'tax_id_number' => 'nullable|string|max:50|unique:employees,tax_id_number',
            'sss_number' => 'nullable|string|max:50',
            'philhealth_number' => 'nullable|string|max:50',
            'pagibig_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'vacation_leave_credits' => 'nullable|integer|min:0|max:365',
            'sick_leave_credits' => 'nullable|integer|min:0|max:365',
            'emergency_leave_credits' => 'nullable|integer|min:0|max:365',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|regex:/^[0-9]{10,11}$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'first_name.regex' => 'First name must contain only letters and spaces.',
            'middle_name.regex' => 'Middle name must contain only letters and spaces.',
            'last_name.regex' => 'Last name must contain only letters and spaces.',
            'phone.regex' => 'Phone number must be 10 or 11 digits.',
            'emergency_phone.regex' => 'Emergency phone must be 10 or 11 digits.',
            'birthdate.before' => 'Employee must be at least 18 years old.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
        ];
    }
}
