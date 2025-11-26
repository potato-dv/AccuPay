<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'payroll_period', 'period_start', 'period_end', 'payment_date',
        'total_amount', 'total_employees', 'status', 'notes'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }
}
