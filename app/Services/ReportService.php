<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\Loan;
use App\Models\Payslip;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate report based on type and filters
     */
    public function generateReport(
        string $reportType,
        ?string $dateFrom,
        ?string $dateTo,
        ?int $employeeId
    ) {
        // Convert report_type to proper method name (e.g., payroll_detailed -> PayrollDetailed)
        $methodName = str_replace('_', '', ucwords($reportType, '_'));
        $method = 'generate' . $methodName . 'Report';
        
        if (method_exists($this, $method)) {
            return $this->$method($dateFrom, $dateTo, $employeeId);
        }

        return collect();
    }

    /**
     * Generate payroll report
     */
    private function generatePayrollReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Payslip::with(['employee', 'payroll']);
        
        if ($dateFrom && $dateTo) {
            $query->whereHas('payroll', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('period_start', [$dateFrom, $dateTo]);
            });
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        return $query->latest()->get();
    }

    /**
     * Generate detailed payroll report (alias for payroll report)
     */
    private function generatePayrollDetailedReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        return $this->generatePayrollReport($dateFrom, $dateTo, $employeeId);
    }

    /**
     * Generate deductions report
     */
    private function generateDeductionsReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Payslip::with(['employee', 'payroll'])
            ->where(function ($q) {
                $q->where('sss', '>', 0)
                    ->orWhere('philhealth', '>', 0)
                    ->orWhere('pagibig', '>', 0)
                    ->orWhere('tax', '>', 0);
            });
        
        if ($dateFrom && $dateTo) {
            $query->whereHas('payroll', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('period_start', [$dateFrom, $dateTo]);
            });
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        return $query->latest()->get();
    }

    /**
     * Generate overtime report
     */
    private function generateOvertimeReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Attendance::with('employee')->where('overtime_hours', '>', 0);
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        return $query->latest('date')->get();
    }

    /**
     * Generate attendance report
     */
    private function generateAttendanceReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Attendance::with('employee');
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('date', [$dateFrom, $dateTo]);
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $attendanceRecords = $query->latest('date')->get();
        
        // Generate absent records for employees with no attendance on scheduled work days
        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
            
            $employees = $employeeId 
                ? Employee::where('id', $employeeId)->where('status', 'active')->get()
                : Employee::where('status', 'active')->get();
            
            $absentRecords = collect();
            
            foreach ($employees as $employee) {
                if (!$employee->workSchedule) continue;
                
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $dayOfWeek = strtolower($currentDate->format('l'));
                    $isScheduledDay = match($dayOfWeek) {
                        'monday' => $employee->workSchedule->monday,
                        'tuesday' => $employee->workSchedule->tuesday,
                        'wednesday' => $employee->workSchedule->wednesday,
                        'thursday' => $employee->workSchedule->thursday,
                        'friday' => $employee->workSchedule->friday,
                        'saturday' => $employee->workSchedule->saturday,
                        'sunday' => $employee->workSchedule->sunday,
                        default => false,
                    };
                    
                    if ($isScheduledDay) {
                        // Check if attendance record exists (any status)
                        $hasAttendance = $attendanceRecords->first(function ($record) use ($employee, $currentDate) {
                            return $record->employee_id == $employee->id 
                                && $record->date->format('Y-m-d') == $currentDate->format('Y-m-d');
                        });
                        
                        // Only create absent record if NO attendance record exists at all
                        if (!$hasAttendance) {
                            // Check for approved leave
                            $hasLeave = LeaveApplication::where('employee_id', $employee->id)
                                ->where('status', 'approved')
                                ->whereDate('start_date', '<=', $currentDate)
                                ->whereDate('end_date', '>=', $currentDate)
                                ->exists();
                            
                            if (!$hasLeave) {
                                // Create absent record object
                                $absentRecord = new Attendance([
                                    'employee_id' => $employee->id,
                                    'date' => $currentDate->copy(),
                                    'time_in' => null,
                                    'time_out' => null,
                                    'hours_worked' => 0,
                                    'overtime_hours' => 0,
                                    'status' => 'absent',
                                ]);
                                $absentRecord->employee = $employee;
                                $absentRecords->push($absentRecord);
                            }
                        }
                    }
                    
                    $currentDate->addDay();
                }
            }
            
            // Merge and sort by date descending
            return $attendanceRecords->concat($absentRecords)->sortByDesc('date')->values();
        }
        
        return $attendanceRecords;
    }

    /**
     * Generate leave report
     */
    private function generateLeaveReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = LeaveApplication::with('employee');
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('start_date', [$dateFrom, $dateTo]);
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        return $query->latest()->get();
    }

    /**
     * Generate loans report
     */
    private function generateLoansReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Loan::with('employee');
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        return $query->latest()->get();
    }

    /**
     * Generate employee report
     */
    private function generateEmployeeReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Employee::query();
        
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        
        return $query->get();
    }

    /**
     * Generate leave balance report
     */
    private function generateLeaveBalanceReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Employee::query();
        
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        
        return $query->get()->map(function ($emp) use ($dateTo) {
            $year = $dateTo ? Carbon::parse($dateTo)->year : now()->year;
            $totalLeave = 12;
            $usedLeave = LeaveApplication::where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $year)
                ->sum('days_count');
            
            $emp->total_leave = $totalLeave;
            $emp->used_leave = $usedLeave;
            $emp->remaining_leave = $totalLeave - $usedLeave;
            
            return $emp;
        });
    }

    /**
     * Generate employee compensation report
     */
    private function generateEmployeeCompensationReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = Employee::query();
        
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        
        return $query->get();
    }

    /**
     * Calculate summary statistics for report
     */
    public function calculateSummary(string $reportType, $data): array
    {
        $method = 'calculate' . str_replace('_', '', ucwords($reportType, '_')) . 'Summary';
        
        if (method_exists($this, $method)) {
            return $this->$method($data);
        }

        return [];
    }

    private function calculatePayrollSummary($data): array
    {
        return [
            'total_gross' => $data->sum('gross_pay'),
            'total_deductions' => $data->sum('total_deductions'),
            'total_net' => $data->sum('net_pay'),
            'count' => $data->count(),
        ];
    }

    private function calculateDeductionsSummary($data): array
    {
        return [
            'total_sss' => $data->sum('sss'),
            'total_philhealth' => $data->sum('philhealth'),
            'total_pagibig' => $data->sum('pagibig'),
            'total_tax' => $data->sum('tax'),
            'total_deductions' => $data->sum('total_deductions'),
            'count' => $data->count(),
        ];
    }

    private function calculateOvertimeSummary($data): array
    {
        return [
            'total_records' => $data->count(),
            'total_overtime' => $data->sum('overtime_hours'),
        ];
    }

    private function calculateAttendanceSummary($data): array
    {
        return [
            'total_records' => $data->count(),
            'present' => $data->where('status', 'present')->count(),
            'late' => $data->where('status', 'late')->count(),
            'absent' => $data->where('status', 'absent')->count(),
            'total_hours' => $data->sum('hours_worked'),
            'total_overtime' => $data->sum('overtime_hours'),
        ];
    }

    private function calculateLeaveSummary($data): array
    {
        return [
            'total_applications' => $data->count(),
            'approved' => $data->where('status', 'approved')->count(),
            'pending' => $data->where('status', 'pending')->count(),
            'rejected' => $data->where('status', 'rejected')->count(),
            'total_days' => $data->sum('days_count'),
        ];
    }

    private function calculateLoansSummary($data): array
    {
        return [
            'total_loans' => $data->count(),
            'total_amount' => $data->sum('amount'),
            'total_paid' => $data->sum('paid_amount'),
            'total_remaining' => $data->sum('remaining_balance'),
            'approved' => $data->where('status', 'approved')->count(),
            'pending' => $data->where('status', 'pending')->count(),
            'rejected' => $data->where('status', 'rejected')->count(),
            'completed' => $data->where('status', 'completed')->count(),
        ];
    }

    private function calculateEmployeeSummary($data): array
    {
        return [
            'total_employees' => $data->count(),
            'active' => $data->where('status', 'active')->count(),
            'inactive' => $data->where('status', 'inactive')->count(),
            'full_time' => $data->where('employment_type', 'full-time')->count(),
            'part_time' => $data->where('employment_type', 'part-time')->count(),
        ];
    }

    private function calculateLeaveBalanceSummary($data): array
    {
        return [
            'total_employees' => $data->count(),
        ];
    }

    private function calculateEmployeeCompensationSummary($data): array
    {
        return [
            'total_employees' => $data->count(),
            'avg_salary' => $data->avg('basic_salary'),
        ];
    }

    /**
     * Generate activity log report
     */
    private function generateActivityLogReport(?string $dateFrom, ?string $dateTo, ?int $employeeId)
    {
        $query = ActivityLog::with('user');
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
        }
        
        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }
        
        return $query->latest('created_at')->get();
    }

    private function calculateActivityLogSummary($data): array
    {
        return [
            'total_activities' => $data->count(),
            'total_users' => $data->pluck('user_id')->unique()->count(),
            'by_action' => $data->groupBy('action')->map->count()->toArray(),
            'by_module' => $data->groupBy('module')->map->count()->toArray(),
        ];
    }
}
