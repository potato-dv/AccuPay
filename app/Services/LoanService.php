<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\Auth;

class LoanService
{
    /**
     * Get filtered loans
     */
    public function getFilteredLoans(?string $status, ?string $search)
    {
        $query = Loan::with(['employee', 'approvedBy', 'payments'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('employee_id', 'like', '%' . $search . '%');
            });
        }

        return $query->get();
    }

    /**
     * Approve loan
     */
    public function approveLoan(int $loanId, string $startDate): void
    {
        $loan = Loan::findOrFail($loanId);
        
        $loan->update([
            'status' => 'approved',
            'start_date' => $startDate,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reason' => null,
        ]);
    }

    /**
     * Reject loan
     */
    public function rejectLoan(int $loanId, string $reason): void
    {
        $loan = Loan::findOrFail($loanId);
        
        $loan->update([
            'status' => 'rejected',
            'reason' => $reason,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Record manual loan payment
     */
    public function recordPayment(int $loanId, float $amount, ?string $notes = null): void
    {
        $loan = Loan::findOrFail($loanId);
        
        if ($amount > $loan->remaining_balance) {
            throw new \Exception('Payment amount cannot exceed remaining balance.');
        }

        LoanPayment::create([
            'loan_id' => $loan->id,
            'payroll_id' => null,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_type' => 'manual',
            'notes' => $notes ?? 'Manual payment recorded by admin',
            'processed_by' => Auth::id(),
        ]);

        $newPaidAmount = $loan->paid_amount + $amount;
        $newRemainingBalance = $loan->remaining_balance - $amount;
        
        $loan->update([
            'paid_amount' => $newPaidAmount,
            'remaining_balance' => $newRemainingBalance,
            'status' => $newRemainingBalance <= 0 ? 'completed' : 'approved',
        ]);
    }
}
