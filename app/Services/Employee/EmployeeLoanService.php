<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\Loan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EmployeeLoanService
{
    /**
     * Get all loans for an employee
     */
    public function getEmployeeLoans(Employee $employee)
    {
        return Loan::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new loan request
     */
    public function createLoanRequest(Employee $employee, array $data): Loan
    {
        $validator = Validator::make($data, [
            'amount' => 'required|numeric|min:100|max:100000',
            'purpose' => 'required|string|max:500',
            'terms' => 'required|integer|min:1|max:24',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        // Check if employee already has a pending loan
        $pendingLoan = Loan::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingLoan) {
            throw new \Exception('You already have a pending loan request.');
        }

        // Check if employee has reached maximum active loans
        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->count();

        if ($activeLoans >= 2) {
            throw new \Exception('You have reached the maximum number of active loans (2).');
        }

        $monthlyDeduction = $validated['amount'] / $validated['terms'];

        return Loan::create([
            'employee_id' => $employee->id,
            'amount' => $validated['amount'],
            'remaining_balance' => $validated['amount'],
            'monthly_deduction' => round($monthlyDeduction, 2),
            'terms' => $validated['terms'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);
    }
}
