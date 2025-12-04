<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\Loan;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeePayslipService
{
    /**
     * Get payslips with related data
     */
    public function getPayslipsData(Employee $employee): array
    {
        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->get();

        $payslips = Payslip::with(['payroll'])
            ->where('employee_id', $employee->id)
            ->whereHas('payroll', function($query) {
                $query->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $attendanceByPayslip = $this->getAttendanceByPayslip($employee, $payslips);

        return [
            'payslips' => $payslips,
            'active_loans' => $activeLoans,
            'total_loan_balance' => $activeLoans->sum('remaining_balance'),
            'total_monthly_deduction' => $activeLoans->sum('monthly_deduction'),
            'attendance_by_payslip' => $attendanceByPayslip,
        ];
    }

    /**
     * Get attendance records grouped by payslip with absent days filled
     */
    protected function getAttendanceByPayslip(Employee $employee, $payslips): array
    {
        $attendanceByPayslip = [];

        foreach ($payslips as $payslip) {
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$payslip->payroll->period_start, $payslip->payroll->period_end])
                ->orderBy('date', 'asc')
                ->get();

            $attendanceByDate = [];
            foreach ($attendanceRecords as $record) {
                $attendanceByDate[$record->date->format('Y-m-d')] = $record;
            }

            $periodStart = Carbon::parse($payslip->payroll->period_start);
            $periodEnd = Carbon::parse($payslip->payroll->period_end);
            $allDates = [];
            $currentDate = $periodStart->copy();

            while ($currentDate->lte($periodEnd)) {
                $dateStr = $currentDate->format('Y-m-d');
                $isWorkingDay = $employee->isScheduledToWork($dateStr);

                if ($isWorkingDay) {
                    if (isset($attendanceByDate[$dateStr])) {
                        $allDates[] = $attendanceByDate[$dateStr];
                    } else {
                        // Create virtual absent record for display
                        $absentRecord = new Attendance();
                        $absentRecord->date = $dateStr;
                        $absentRecord->status = 'absent';
                        $absentRecord->hours_worked = 0;
                        $allDates[] = $absentRecord;
                    }
                }

                $currentDate->addDay();
            }

            $attendanceByPayslip[$payslip->id] = $allDates;
        }

        return $attendanceByPayslip;
    }

    /**
     * Get payslip statistics
     */
    public function getPayslipStatistics(Employee $employee): array
    {
        return [
            'last_payslip' => Payslip::where('employee_id', $employee->id)->latest()->first(),
            'avg_monthly_pay' => Payslip::where('employee_id', $employee->id)->avg('net_pay'),
            'total_payslips' => Payslip::where('employee_id', $employee->id)->count(),
        ];
    }
}
