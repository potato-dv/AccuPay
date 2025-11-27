<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'employee_id',
        'amount',
        'purpose',
        'terms',
        'monthly_deduction',
        'paid_amount',
        'remaining_balance',
        'status',
        'reason',
        'approved_by',
        'approved_at',
        'start_date',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'start_date' => 'date',
        'amount' => 'decimal:2',
        'monthly_deduction' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    // Calculate how many payments have been made
    public function getPaymentsMadeAttribute()
    {
        return $this->payments()->count();
    }

    // Calculate remaining payments
    public function getRemainingPaymentsAttribute()
    {
        return max(0, $this->terms - $this->payments_made);
    }

    // Get progress percentage
    public function getProgressPercentageAttribute()
    {
        if ($this->amount <= 0) return 0;
        return min(100, round(($this->paid_amount / $this->amount) * 100, 2));
    }
}
