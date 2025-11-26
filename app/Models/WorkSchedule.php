<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'schedule_name',
        'description',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
        'shift_start',
        'shift_end',
        'daily_hours',
        'weekly_hours',
        'break_start',
        'break_end',
        'break_paid',
        'overtime_allowed',
        'overtime_rate_multiplier',
        'grace_period_minutes',
        'is_active',
    ];

    protected $casts = [
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'saturday' => 'boolean',
        'sunday' => 'boolean',
        'break_paid' => 'boolean',
        'overtime_allowed' => 'boolean',
        'is_active' => 'boolean',
        'daily_hours' => 'decimal:2',
        'weekly_hours' => 'decimal:2',
        'overtime_rate_multiplier' => 'decimal:2',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get array of working days
     */
    public function getWorkingDaysAttribute()
    {
        $days = [];
        $dayNames = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($dayNames as $day) {
            if ($this->{$day}) {
                $days[] = ucfirst($day);
            }
        }
        
        return $days;
    }

    /**
     * Get count of working days per week
     */
    public function getWorkingDaysCountAttribute()
    {
        return count($this->working_days);
    }

    /**
     * Check if a specific day is a working day
     */
    public function isWorkingDay($dayName)
    {
        $dayName = strtolower($dayName);
        return $this->{$dayName} ?? false;
    }

    /**
     * Get formatted work hours
     */
    public function getFormattedWorkHoursAttribute()
    {
        return date('h:i A', strtotime($this->shift_start)) . ' - ' . 
               date('h:i A', strtotime($this->shift_end));
    }
}
