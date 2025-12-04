<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Payslip;
use App\Models\Loan;
use App\Models\ActivityLog;
use App\Services\Admin\ReportService;
use Carbon\Carbon;

class EmployeeDashboardService
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get dashboard data for an employee
     */
    public function getDashboardData(Employee $employee, int $month, int $year): array
    {
        $attendanceStats = $this->getAttendanceStatistics($employee, $month, $year);
        $leaveStats = $this->getLeaveStatistics($employee, $year);
        $loanStats = $this->getLoanStatistics($employee);
        $payslipData = $this->getLastPayslip($employee);
        $recentActivities = $this->getRecentActivities($employee->id);

        return [
            'employee' => $employee,
            'selected_month' => $month,
            'selected_year' => $year,
            'remaining_leave' => $leaveStats['remaining_leave'],
            'attendance_count' => $attendanceStats['total_count'],
            'present_days' => $attendanceStats['present_days'],
            'late_days' => $attendanceStats['late_days'],
            'absent_days' => $attendanceStats['absent_days'],
            'total_overtime_hours' => $attendanceStats['overtime_hours'],
            'attendance_rate' => $attendanceStats['attendance_rate'],
            'attendance_records' => $attendanceStats['records'],
            'recent_attendance' => $attendanceStats['recent_records'],
            'last_payslip' => $payslipData,
            'active_loans_count' => $loanStats['active_count'],
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * Get attendance statistics for a specific month and year
     */
    protected function getAttendanceStatistics(Employee $employee, int $month, int $year): array
    {
        // Use the admin ReportService to generate attendance data with same logic
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        // Get attendance report using admin's ReportService
        $allRecords = $this->reportService->generateAttendanceReport(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $employee->id
        );
        
        // Records are already filtered by employee_id in the ReportService
        $allRecords = $allRecords->sortByDesc('date')->values();
        
        $totalCount = $allRecords->count();
        $presentDays = $allRecords->where('status', 'present')->count();
        $lateDays = $allRecords->where('status', 'late')->count();
        $absentDays = $allRecords->where('status', 'absent')->count();
        
        // Calculate attendance rate (present + late / total) * 100
        $attendanceRate = $totalCount > 0 
            ? round((($presentDays + $lateDays) / $totalCount) * 100) 
            : 0;

        return [
            'records' => $allRecords,
            'recent_records' => $allRecords->take(5),
            'total_count' => $totalCount,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'overtime_hours' => $allRecords->sum('overtime_hours'),
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Get leave statistics for the year
     */
    protected function getLeaveStatistics(Employee $employee, int $year): array
    {
        $usedLeave = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->sum('days_count');

        return [
            'used_leave' => $usedLeave,
            'remaining_leave' => max(0, 12 - $usedLeave),
        ];
    }

    /**
     * Get loan statistics
     */
    protected function getLoanStatistics(Employee $employee): array
    {
        $activeCount = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->count();

        return [
            'active_count' => $activeCount,
        ];
    }

    /**
     * Get the last approved payslip
     */
    protected function getLastPayslip(Employee $employee): ?Payslip
    {
        return Payslip::where('employee_id', $employee->id)
            ->whereHas('payroll', function($query) {
                $query->where('status', 'approved');
            })
            ->latest()
            ->first();
    }

    /**
     * Get recent activities for an employee
     */
    protected function getRecentActivities(int $employeeId): array
    {
        $activities = [];
        
        // Get recent leave applications
        $recentLeaves = LeaveApplication::where('employee_id', $employeeId)
            ->latest()
            ->limit(3)
            ->get();
        
        foreach ($recentLeaves as $leave) {
            $icon = $leave->status == 'approved' ? 'check-circle' : ($leave->status == 'rejected' ? 'times-circle' : 'clock');
            $activities[] = [
                'type' => 'leave',
                'text' => ucfirst($leave->leave_type) . ' leave application - ' . ucfirst($leave->status),
                'icon' => $icon,
                'date' => $leave->created_at,
            ];
        }
        
        // Get recent payslips
        $recentPayslips = Payslip::where('employee_id', $employeeId)
            ->latest()
            ->limit(2)
            ->get();
        
        foreach ($recentPayslips as $payslip) {
            $activities[] = [
                'type' => 'payslip',
                'text' => 'Payslip generated for ' . ($payslip->payroll->payroll_period ?? 'N/A'),
                'icon' => 'file-invoice-dollar',
                'date' => $payslip->created_at,
            ];
        }
        
        // Sort by date descending
        usort($activities, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        
        return array_slice($activities, 0, 5);
    }
}
