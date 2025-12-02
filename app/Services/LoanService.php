<?php

namespace App\Services;

use App\Models\ActivityLog;
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

        $oldPaidAmount = $loan->paid_amount;
        $oldRemainingBalance = $loan->remaining_balance;
        $oldStatus = $loan->status;
        $employeeName = $loan->employee->full_name ?? 'Unknown';

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
        $newStatus = $newRemainingBalance <= 0 ? 'completed' : 'approved';
        
        $loan->update([
            'paid_amount' => $newPaidAmount,
            'remaining_balance' => $newRemainingBalance,
            'status' => $newStatus,
        ]);

        // Log the manual payment activity
        $changes = [];
        $changes[] = "Payment Amount: ₱" . number_format($amount, 2);
        $changes[] = "Paid Amount: ₱" . number_format($oldPaidAmount, 2) . " → ₱" . number_format($newPaidAmount, 2);
        $changes[] = "Remaining Balance: ₱" . number_format($oldRemainingBalance, 2) . " → ₱" . number_format($newRemainingBalance, 2);
        
        if ($oldStatus !== $newStatus) {
            $changes[] = "Status: " . ucfirst($oldStatus) . " → " . ucfirst($newStatus);
        }
        
        if ($notes) {
            $changes[] = "Notes: " . $notes;
        }

        ActivityLog::log(
            'loan_payment',
            "Manual payment of ₱" . number_format($amount, 2) . " for loan of {$employeeName}",
            implode(" | ", $changes)
        );
    }

    /**
     * Update loan details (for approved/active loans)
     */
    public function updateLoan(int $loanId, array $data): void
    {
        $loan = Loan::findOrFail($loanId);
        
        // Calculate new monthly deduction if terms or amount changed
        $amount = $data['amount'] ?? $loan->amount;
        $terms = $data['terms'] ?? $loan->terms;
        $paidAmount = $data['paid_amount'] ?? $loan->paid_amount;
        
        $monthlyDeduction = $terms > 0 ? round($amount / $terms, 2) : 0;
        $remainingBalance = $amount - $paidAmount;
        
        // Ensure status is correct
        $status = $loan->status;
        if ($remainingBalance <= 0 && $status === 'approved') {
            $status = 'completed';
        } elseif ($remainingBalance > 0 && $status === 'completed') {
            $status = 'approved';
        }
        
        $loan->update([
            'amount' => $amount,
            'terms' => $terms,
            'monthly_deduction' => $monthlyDeduction,
            'paid_amount' => $paidAmount,
            'remaining_balance' => $remainingBalance,
            'status' => $status,
            'start_date' => $data['start_date'] ?? $loan->start_date,
        ]);
    }
}
