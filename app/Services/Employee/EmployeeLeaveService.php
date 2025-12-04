<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\ActivityLog;
use Carbon\Carbon;

class EmployeeLeaveService
{
    /**
     * Get all leave applications for an employee
     */
    public function getLeaveApplications(Employee $employee)
    {
        return LeaveApplication::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new leave application
     */
    public function createLeaveApplication(Employee $employee, array $data): LeaveApplication
    {
        $startDate = new \DateTime($data['start_date']);
        $endDate = new \DateTime($data['end_date']);
        $daysCount = $startDate->diff($endDate)->days + 1;

        $leave = LeaveApplication::create([
            'employee_id' => $employee->id,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days_count' => $daysCount,
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        ActivityLog::log(
            'create',
            'leave',
            "Employee {$employee->full_name} submitted a leave application ({$data['leave_type']})",
            $leave->id,
            ['employee' => $employee->full_name, 'type' => $data['leave_type'], 'days' => $daysCount]
        );

        return $leave;
    }

    /**
     * Get leave statistics for the current year
     */
    public function getLeaveStatistics(Employee $employee): array
    {
        $currentYear = now()->year;

        return [
            'total_taken' => LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('days_count'),
            'vacation_leaves' => LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('leave_type', 'Vacation Leave')
                ->whereYear('start_date', $currentYear)
                ->sum('days_count'),
            'sick_leaves' => LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('leave_type', 'Sick Leave')
                ->whereYear('start_date', $currentYear)
                ->sum('days_count'),
            'pending_count' => LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->count(),
        ];
    }
}
