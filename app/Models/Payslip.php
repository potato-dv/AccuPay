<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $fillable = [
        'payroll_id', 'employee_id', 'basic_salary', 'overtime_pay', 'allowances',
        'bonuses', 'gross_pay', 'tax', 'tax_rate', 'sss', 'sss_rate', 'philhealth', 'philhealth_rate', 
        'pagibig', 'pagibig_rate', 'other_deductions', 'loan_deductions', 'late_deduction', 
        'undertime_deduction', 'absence_deduction', 'total_deductions', 'net_pay', 'hours_worked', 'overtime_hours',
        'undertime_hours', 'days_present', 'days_absent', 'days_late'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'sss' => 'decimal:2',
        'sss_rate' => 'decimal:2',
        'philhealth' => 'decimal:2',
        'philhealth_rate' => 'decimal:2',
        'pagibig' => 'decimal:2',
        'pagibig_rate' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'loan_deductions' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'undertime_deduction' => 'decimal:2',
        'absence_deduction' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'undertime_hours' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
