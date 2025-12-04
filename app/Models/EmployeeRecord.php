<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRecord extends Model
{
    protected $fillable = [
        'employee_id', 'record_type', 'record_date',
        'employee_number', 'first_name', 'middle_name', 'last_name', 
        'email', 'phone', 'birthdate', 'sex', 'civil_status', 'address',
        'emergency_contact', 'emergency_phone',
        'department', 'position', 'hire_date', 'employment_type', 'status',
        'basic_salary', 'hourly_rate', 'night_differential_rate', 'holiday_rate_multiplier',
        'tax_id_number', 'sss_number', 'philhealth_number', 'pagibig_number',
        'bank_account_number', 'bank_name',
        'vacation_leave_credits', 'sick_leave_credits', 'emergency_leave_credits',
        'work_schedule_id', 'custom_rest_days',
        'notes', 'created_by', 'is_approved', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'record_date' => 'date',
        'birthdate' => 'date',
        'hire_date' => 'date',
        'basic_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'night_differential_rate' => 'decimal:2',
        'holiday_rate_multiplier' => 'decimal:2',
        'vacation_leave_credits' => 'decimal:2',
        'sick_leave_credits' => 'decimal:2',
        'emergency_leave_credits' => 'decimal:2',
        'custom_rest_days' => 'array',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name . ' ' : ' ';
        return $this->first_name . $middle . $this->last_name;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('record_type', $type);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // Methods
    public function approve($userId)
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Create a snapshot record from current employee data
     */
    public static function createSnapshot(Employee $employee, $recordType = 'snapshot', $notes = null)
    {
        return self::create([
            'employee_id' => $employee->id,
            'record_type' => $recordType,
            'record_date' => now(),
            'employee_number' => $employee->employee_id,
            'first_name' => $employee->first_name,
            'middle_name' => $employee->middle_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'birthdate' => $employee->birthdate,
            'sex' => $employee->sex,
            'civil_status' => $employee->civil_status,
            'address' => $employee->address,
            'emergency_contact' => $employee->emergency_contact,
            'emergency_phone' => $employee->emergency_phone,
            'department' => $employee->department,
            'position' => $employee->position,
            'hire_date' => $employee->hire_date,
            'employment_type' => $employee->employment_type,
            'status' => $employee->status,
            'basic_salary' => $employee->basic_salary,
            'hourly_rate' => $employee->hourly_rate,
            'night_differential_rate' => $employee->night_differential_rate,
            'holiday_rate_multiplier' => $employee->holiday_rate_multiplier,
            'tax_id_number' => $employee->tax_id_number,
            'sss_number' => $employee->sss_number,
            'philhealth_number' => $employee->philhealth_number,
            'pagibig_number' => $employee->pagibig_number,
            'bank_account_number' => $employee->bank_account_number,
            'bank_name' => $employee->bank_name,
            'vacation_leave_credits' => $employee->vacation_leave_credits,
            'sick_leave_credits' => $employee->sick_leave_credits,
            'emergency_leave_credits' => $employee->emergency_leave_credits,
            'work_schedule_id' => $employee->work_schedule_id,
            'custom_rest_days' => $employee->custom_rest_days,
            'notes' => $notes,
            'created_by' => auth()->id(),
            'is_approved' => false,
        ]);
    }
}
