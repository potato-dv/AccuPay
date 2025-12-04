<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|regex:/^[0-9]{10,11}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be 10 or 11 digits.',
            'emergency_phone.regex' => 'Emergency phone must be 10 or 11 digits.',
        ];
    }
}
