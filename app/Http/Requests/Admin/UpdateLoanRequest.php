<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'terms' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Loan amount is required.',
            'amount.numeric' => 'Loan amount must be a number.',
            'amount.min' => 'Loan amount must be at least 1.',
            'terms.required' => 'Terms (cutoffs) is required.',
            'terms.integer' => 'Terms must be a whole number.',
            'terms.min' => 'Terms must be at least 1 cutoff.',
            'paid_amount.required' => 'Paid amount is required.',
            'paid_amount.numeric' => 'Paid amount must be a number.',
            'paid_amount.min' => 'Paid amount cannot be negative.',
            'start_date.date' => 'Start date must be a valid date.',
        ];
    }
}
