<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:100|max:100000',
            'purpose' => 'required|string|max:500',
            'terms' => 'required|integer|min:1|max:24',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Loan amount must be at least ₱100.',
            'amount.max' => 'Loan amount cannot exceed ₱100,000.',
            'terms.min' => 'Loan term must be at least 1 cutoff.',
            'terms.max' => 'Loan term cannot exceed 24 cutoffs.',
        ];
    }
}
