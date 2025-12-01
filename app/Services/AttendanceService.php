<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Calculate hours worked and overtime for attendance record
     */
    public function calculateHoursWorked(
        string $date,
        ?string $timeIn,
        ?string $timeOut,
        Employee $employee
    ): array {
        if (!$timeIn || !$timeOut) {
            return [
                'hours_worked' => 0,
                'overtime_hours' => 0,
            ];
        }

        $timeInCarbon = Carbon::parse($date . ' ' . $timeIn);
        $timeOutCarbon = Carbon::parse($date . ' ' . $timeOut);
        
        // If time_out is earlier than time_in, assume it's the next day
        if ($timeOutCarbon->lessThanOrEqualTo($timeInCarbon)) {
            $timeOutCarbon->addDay();
        }
        
        $totalMinutes = abs($timeOutCarbon->diffInMinutes($timeInCarbon));
        $regularHours = 8;
        $breakMinutes = 0;
        
        if ($employee->workSchedule) {
            $regularHours = $employee->workSchedule->daily_hours ?? 8;
            
            // Calculate break duration if break times are set and break is unpaid
            if (
                $employee->workSchedule->break_start &&
                $employee->workSchedule->break_end &&
                !$employee->workSchedule->break_paid
            ) {
                $breakStart = Carbon::parse($date . ' ' . $employee->workSchedule->break_start);
                $breakEnd = Carbon::parse($date . ' ' . $employee->workSchedule->break_end);
                $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
            }
        }
        
        $workedMinutes = $totalMinutes - $breakMinutes;
        $hoursWorked = $workedMinutes / 60;
        $overtimeHours = $hoursWorked > $regularHours ? $hoursWorked - $regularHours : 0;

        return [
            'hours_worked' => round($hoursWorked, 2),
            'overtime_hours' => round($overtimeHours, 2),
        ];
    }

    /**
     * Check if attendance already exists for employee on date
     */
    public function attendanceExists(int $employeeId, string $date): bool
    {
        return Attendance::where('employee_id', $employeeId)
            ->where('date', $date)
            ->exists();
    }

    /**
     * Get filtered attendance records
     */
    public function getFilteredAttendance(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->get();
    }
}
