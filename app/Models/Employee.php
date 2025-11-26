<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'first_name', 'middle_name', 'last_name', 'email', 'phone',
        'birthdate', 'sex', 'civil_status', 'department', 'position', 'hire_date', 
        'basic_salary', 'hourly_rate', 'employment_type', 'status', 'address', 
        'emergency_contact', 'emergency_phone', 'work_schedule_id', 'custom_rest_days', 
        'night_differential_rate', 'holiday_rate_multiplier', 'tax_id_number', 'sss_number', 
        'philhealth_number', 'pagibig_number', 'bank_account_number', 'bank_name',
        'vacation_leave_credits', 'sick_leave_credits', 'emergency_leave_credits'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birthdate' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'night_differential_rate' => 'decimal:2',
        'holiday_rate_multiplier' => 'decimal:2',
        'custom_rest_days' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name . ' ' : ' ';
        return "{$this->first_name}{$middle}{$this->last_name}";
    }

    /**
     * Get age from birthdate
     */
    public function getAgeAttribute()
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }

    /**
     * Check if employee should work on a specific date
     */
    public function isScheduledToWork($date)
    {
        if (!$this->workSchedule) {
            return true; // Default: assume all days are working days if no schedule
        }

        $dayName = strtolower(date('l', strtotime($date)));
        return $this->workSchedule->isWorkingDay($dayName);
    }

    /**
     * Get expected working hours for a date
     */
    public function getExpectedHours($date)
    {
        if (!$this->workSchedule || !$this->isScheduledToWork($date)) {
            return 0;
        }

        return $this->workSchedule->daily_hours;
    }

    /**
     * Calculate remaining leave credits
     */
    public function getRemainingLeaveCredits($leaveType, $year = null)
    {
        $year = $year ?? now()->year;
        
        $usedLeaves = LeaveApplication::where('employee_id', $this->id)
            ->where('status', 'approved')
            ->where('leave_type', $leaveType)
            ->whereYear('start_date', $year)
            ->sum('days_count');

        $fieldMap = [
            'Vacation Leave' => 'vacation_leave_credits',
            'Sick Leave' => 'sick_leave_credits',
            'Emergency Leave' => 'emergency_leave_credits',
        ];

        $totalCredits = $this->{$fieldMap[$leaveType] ?? 'vacation_leave_credits'} ?? 0;
        
        return max(0, $totalCredits - $usedLeaves);
    }

    /**
     * Get work schedule summary
     */
    public function getWorkScheduleSummaryAttribute()
    {
        if (!$this->workSchedule) {
            return 'No schedule assigned';
        }

        return $this->workSchedule->schedule_name . ' (' . 
               implode(', ', array_map(fn($d) => substr($d, 0, 3), $this->workSchedule->working_days)) . ')';
    }
}
