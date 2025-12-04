<?php

namespace App\Services\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Payroll;
use App\Models\Payslip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll for all active employees
     */
    public function generatePayroll(array $data): array
    {
        $employees = Employee::whereIn('status', ['active', 'Active', 'on leave', 'On Leave'])->get();
        $totalAmount = 0;
        $employeeCount = 0;

        $payroll = Payroll::create([
            'payroll_period' => $data['payroll_period'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'payment_date' => $data['payment_date'],
            'total_amount' => 0,
            'total_employees' => 0,
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($employees as $employee) {
            $payslipData = $this->calculateEmployeePayslip(
                $employee,
                $data['period_start'],
                $data['period_end'],
                $data['payment_date']
            );

            if (!$payslipData) {
                continue;
            }

            $payslipData['payroll_id'] = $payroll->id;
            $payslipData['employee_id'] = $employee->id;

            $payslip = Payslip::create($payslipData);

            // Process loan deductions
            $this->processLoanDeductions($employee, $payslipData['loan_deductions'], $payroll->id, $data['payment_date']);

            $totalAmount += $payslipData['net_pay'];
            $employeeCount++;
        }

        $payroll->update([
            'total_amount' => $totalAmount,
            'total_employees' => $employeeCount,
        ]);

        return [
            'payroll' => $payroll,
            'employee_count' => $employeeCount,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Calculate payslip data for an employee
     */
    private function calculateEmployeePayslip(
        Employee $employee,
        string $periodStart,
        string $periodEnd,
        string $paymentDate
    ): ?array {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get();

        $totalWorkingDays = $this->calculateWorkingDays($employee, $periodStart, $periodEnd);
        
        $daysPresent = $attendances->whereIn('status', ['present', 'late'])->count();
        $daysLate = $attendances->where('status', 'late')->count();
        $daysOnLeave = $attendances->where('status', 'on-leave')->count();
        $daysAbsent = max(0, $totalWorkingDays - $daysPresent - $daysOnLeave);

        $presentAttendances = $attendances->whereIn('status', ['present', 'late']);
        $hoursWorked = $presentAttendances->sum('hours_worked') ?? 0;
        
        // Only count overtime if allowed in work schedule
        $overtimeAllowed = $employee->workSchedule && $employee->workSchedule->overtime_allowed;
        $overtimeHours = $overtimeAllowed ? ($presentAttendances->sum('overtime_hours') ?? 0) : 0;

        $overtimeRate = $employee->workSchedule->overtime_rate_multiplier ?? 1.25;

        // Calculate basic salary and overtime pay
        $salaryData = $this->calculateBasicSalary(
            $employee, 
            $hoursWorked, 
            $overtimeHours, 
            $overtimeRate,
            $periodStart,
            $periodEnd,
            $daysPresent,
            $totalWorkingDays,
            $daysAbsent
        );
        
        if (!$salaryData) {
            return null;
        }

        $basicSalary = $salaryData['basic_salary'];
        $absenceDeduction = $salaryData['absence_deduction'];
        $overtimePay = $salaryData['overtime_pay'];
        $grossPay = $basicSalary + $overtimePay;

        // Calculate late deduction
        $lateDeduction = $this->calculateLateDeduction($attendances->where('status', 'late'), $employee);

        // Calculate undertime hours and deduction
        $undertimeData = $this->calculateUndertimeDeduction($presentAttendances, $employee, $periodStart, $periodEnd);
        $undertimeHours = $undertimeData['undertime_hours'];
        $undertimeDeduction = $undertimeData['undertime_deduction'];

        // Calculate statutory deductions
        $deductions = $this->calculateDeductions($grossPay);

        // Calculate loan deductions
        $loanDeduction = $this->calculateLoanDeduction($employee, $paymentDate);

        $totalDeductions = $deductions['sss'] + $deductions['philhealth'] + 
                          $deductions['pagibig'] + $deductions['tax'] + 
                          $loanDeduction + $lateDeduction + $undertimeDeduction + $absenceDeduction;
        
        $netPay = $grossPay - $totalDeductions;

        return [
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'allowances' => 0,
            'bonuses' => 0,
            'gross_pay' => $grossPay,
            'tax' => $deductions['tax'],
            'tax_rate' => $deductions['tax_rate'],
            'sss' => $deductions['sss'],
            'sss_rate' => $deductions['sss_rate'],
            'philhealth' => $deductions['philhealth'],
            'philhealth_rate' => $deductions['philhealth_rate'],
            'pagibig' => $deductions['pagibig'],
            'pagibig_rate' => $deductions['pagibig_rate'],
            'other_deductions' => 0,
            'loan_deductions' => $loanDeduction,
            'late_deduction' => $lateDeduction,
            'undertime_deduction' => $undertimeDeduction,
            'absence_deduction' => $absenceDeduction,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'hours_worked' => $hoursWorked,
            'overtime_hours' => $overtimeHours,
            'undertime_hours' => $undertimeHours,
            'days_present' => $daysPresent,
            'days_absent' => $daysAbsent,
            'days_late' => $daysLate,
        ];
    }

    /**
     * Calculate working days in period
     */
    private function calculateWorkingDays(Employee $employee, string $periodStart, string $periodEnd): int
    {
        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);
        $totalWorkingDays = 0;
        $currentDate = $start->copy();
        
        while ($currentDate->lte($end)) {
            if ($employee->isScheduledToWork($currentDate->format('Y-m-d'))) {
                $totalWorkingDays++;
            }
            $currentDate->addDay();
        }

        return $totalWorkingDays;
    }

    /**
     * Calculate basic salary based on employment type
     * Returns FULL basic salary for the period and calculates absence deduction separately
     * For employees with basic_salary: Uses the basic_salary field directly
     * For employees without basic_salary (part-time): Calculates from hourly_rate × hours worked
     */
    private function calculateBasicSalary(
        Employee $employee,
        float $hoursWorked,
        float $overtimeHours,
        float $overtimeRate,
        string $periodStart,
        string $periodEnd,
        int $daysPresent,
        int $totalWorkingDays,
        int $daysAbsent
    ): ?array {
        $basicSalary = 0;
        $absenceDeduction = 0;
        $overtimePay = 0;
        
        // Check if employee has basic salary defined
        $hasBasicSalary = $employee->basic_salary && $employee->basic_salary > 0;
        
        if ($hasBasicSalary) {
            // Employee has basic salary - use it (full-time employees)
            if ($hoursWorked > 0 || $daysPresent > 0 || $daysAbsent > 0) {
                // Determine if this is semi-monthly (kinsenas) or monthly payroll
                $start = Carbon::parse($periodStart);
                $end = Carbon::parse($periodEnd);
                $periodDays = $start->diffInDays($end) + 1;
                
                // Get employee's monthly basic salary
                $monthlyBasicSalary = $employee->basic_salary;
                
                // Determine FULL base salary for the period (before any deductions)
                if ($periodDays <= 16) {
                    // Kinsenas (Semi-monthly) - Half of monthly salary
                    $periodBaseSalary = $monthlyBasicSalary / 2;
                } else {
                    // Monthly - Full monthly salary
                    $periodBaseSalary = $monthlyBasicSalary;
                }
                
                // Basic salary = FULL period salary (show this to user)
                $basicSalary = $periodBaseSalary;
                
                // Calculate absence deduction separately
                if ($daysAbsent > 0 && $totalWorkingDays > 0) {
                    $dailyRate = $periodBaseSalary / $totalWorkingDays;
                    $absenceDeduction = $dailyRate * $daysAbsent;
                }
                
                // Calculate overtime pay using hourly rate
                if ($overtimeHours > 0) {
                    $hourlyRateForOT = $this->getEmployeeHourlyRate($employee);
                    $overtimePay = $overtimeHours * $hourlyRateForOT * $overtimeRate;
                }
            } else {
                return null;
            }
        } else {
            // No basic salary - use hourly rate (part-time employees)
            if ($hoursWorked > 0) {
                $hourlyRate = $employee->hourly_rate ?? 0;
                
                if ($hourlyRate == 0) {
                    return null; // No hourly rate defined
                }
                
                $basicSalary = $hoursWorked * $hourlyRate;
                
                // Calculate overtime pay for part-time (if overtime is allowed in their schedule)
                if ($overtimeHours > 0) {
                    $overtimePay = $overtimeHours * ($hourlyRate * $overtimeRate);
                }
                
                // Part-time has no absence deduction (they're paid only for hours worked)
                $absenceDeduction = 0;
            } else {
                return null;
            }
        }

        if ($basicSalary == 0 && $hoursWorked == 0) {
            return null;
        }

        return [
            'basic_salary' => round($basicSalary, 2),
            'absence_deduction' => round($absenceDeduction, 2),
            'overtime_pay' => round($overtimePay, 2),
        ];
    }

    /**
     * Calculate late deduction (Proportional Deduction)
     * ONLY for arriving late - separate from undertime
     * 0-5 minutes: No deduction (grace period)
     * 6+ minutes: Deduct for the late minutes only
     */
    private function calculateLateDeduction($lateAttendances, Employee $employee): float
    {
        $lateDeduction = 0;
        
        // Get hourly rate for deduction calculation
        $hourlyRate = $this->getEmployeeHourlyRate($employee);
        
        foreach ($lateAttendances as $lateAtt) {
            if ($lateAtt->time_in && $employee->workSchedule && $employee->workSchedule->shift_start) {
                try {
                    $attendanceDate = Carbon::parse($lateAtt->date)->format('Y-m-d');
                    
                    // Parse times properly - WorkSchedule uses shift_start, not time_in
                    $scheduledTimeStr = $employee->workSchedule->shift_start;
                    $actualTimeStr = $lateAtt->time_in;
                    
                    // Create Carbon instances with the date and time
                    $scheduledTime = Carbon::parse($attendanceDate . ' ' . $scheduledTimeStr);
                    $actualTime = Carbon::parse($attendanceDate . ' ' . $actualTimeStr);
                    
                    // Calculate late minutes
                    // If actualTime > scheduledTime, employee is late
                    if ($actualTime->greaterThan($scheduledTime)) {
                        $lateMinutes = $scheduledTime->diffInMinutes($actualTime);
                        
                        // Grace period: 0-5 minutes = no deduction
                        // 6+ minutes = deduct for late minutes
                        if ($lateMinutes >= 6) {
                            // Calculate late deduction in peso
                            $minutesToDeduct = $lateMinutes - 5; // Minus grace period
                            $lateDeduction += ($hourlyRate / 60) * $minutesToDeduct;
                        }
                    }
                } catch (\Exception $e) {
                    // Skip if there's any parsing error
                    continue;
                }
            }
        }

        return round($lateDeduction, 2);
    }

    /**
     * Calculate undertime hours and deduction
     * ONLY for leaving early - separate from late deduction
     * Undertime = when employee doesn't complete their scheduled hours
     * Only calculates for days the employee was actually present/late (not absent days)
     */
    private function calculateUndertimeDeduction($presentAttendances, Employee $employee, string $periodStart, string $periodEnd): array
    {
        $undertimeHours = 0;
        $undertimeDeduction = 0;
        
        if (!$employee->workSchedule) {
            return [
                'undertime_hours' => 0,
                'undertime_deduction' => 0,
            ];
        }

        // Get shift end time from work schedule
        $shiftEnd = $employee->workSchedule->shift_end;
        
        if (!$shiftEnd) {
            return [
                'undertime_hours' => 0,
                'undertime_deduction' => 0,
            ];
        }
        
        // Calculate undertime based on time_out (leaving early)
        // Undertime = only if employee left before shift_end
        foreach ($presentAttendances as $attendance) {
            $timeOut = $attendance->time_out;
            
            if (!$timeOut) {
                continue;
            }
            
            // Parse times for comparison
            $attendanceDate = Carbon::parse($attendance->date)->format('Y-m-d');
            $actualOut = Carbon::parse($attendanceDate . ' ' . $timeOut);
            $scheduledOut = Carbon::parse($attendanceDate . ' ' . $shiftEnd);
            
            // If employee left before scheduled shift end, calculate undertime
            if ($actualOut->lt($scheduledOut)) {
                // Calculate undertime in hours (how early they left)
                // Use absolute value since we know actualOut is before scheduledOut
                $undertimeMinutes = $actualOut->diffInMinutes($scheduledOut, false);
                $undertimeHours += abs($undertimeMinutes) / 60;
            }
        }
        
        // Deduct based on undertime hours
        $hourlyRate = $this->getEmployeeHourlyRate($employee);
        $undertimeDeduction = $undertimeHours * $hourlyRate;

        return [
            'undertime_hours' => round($undertimeHours, 2),
            'undertime_deduction' => round($undertimeDeduction, 2),
        ];
    }

    /**
     * Get employee's hourly rate
     * For employees with hourly_rate defined, use it
     * For full-time with only basic_salary, calculate hourly rate from monthly salary
     */
    private function getEmployeeHourlyRate(Employee $employee): float
    {
        // If hourly_rate is defined, use it
        if ($employee->hourly_rate && $employee->hourly_rate > 0) {
            return $employee->hourly_rate;
        }
        
        // For full-time employees with basic_salary, calculate hourly rate
        if ($employee->employment_type == 'full-time' && $employee->basic_salary > 0) {
            // Monthly salary ÷ 22 working days ÷ 8 hours per day
            return $employee->basic_salary / 22 / 8;
        }
        
        return 0;
    }

    /**
     * Calculate statutory deductions (SSS, PhilHealth, Pag-IBIG, Tax)
     */
    private function calculateDeductions(float $grossPay): array
    {
        $sssRate = 4.5;
        $philhealthRate = 2.0;
        $pagibigRate = 2.0;
        
        $sss = $grossPay * ($sssRate / 100);
        $philhealth = $grossPay * ($philhealthRate / 100);
        $pagibig = min($grossPay * ($pagibigRate / 100), 100);
        
        $taxRate = 0;
        $tax = 0;
        
        if ($grossPay > 20833) {
            $taxRate = 20;
            $tax = ($grossPay - 20833) * 0.20;
        }

        return [
            'sss' => $sss,
            'sss_rate' => $sssRate,
            'philhealth' => $philhealth,
            'philhealth_rate' => $philhealthRate,
            'pagibig' => $pagibig,
            'pagibig_rate' => $pagibigRate,
            'tax' => $tax,
            'tax_rate' => $taxRate,
        ];
    }

    /**
     * Calculate total loan deduction
     */
    private function calculateLoanDeduction(Employee $employee, string $paymentDate): float
    {
        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->whereNotNull('start_date')
            ->where('start_date', '<=', $paymentDate)
            ->get();

        $loanDeduction = 0;
        
        foreach ($activeLoans as $loan) {
            $loanDeduction += min($loan->monthly_deduction, $loan->remaining_balance);
        }

        return $loanDeduction;
    }

    /**
     * Process loan deductions and create payment records
     */
    private function processLoanDeductions(
        Employee $employee,
        float $totalLoanDeduction,
        int $payrollId,
        string $paymentDate
    ): void {
        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->whereNotNull('start_date')
            ->where('start_date', '<=', $paymentDate)
            ->get();

        foreach ($activeLoans as $loan) {
            $deductionAmount = min($loan->monthly_deduction, $loan->remaining_balance);
            
            LoanPayment::create([
                'loan_id' => $loan->id,
                'payroll_id' => $payrollId,
                'amount' => $deductionAmount,
                'payment_date' => $paymentDate,
                'payment_type' => 'automatic',
                'notes' => 'Automatic deduction from payroll',
                'processed_by' => Auth::id(),
            ]);

            $newPaidAmount = $loan->paid_amount + $deductionAmount;
            $newRemainingBalance = $loan->remaining_balance - $deductionAmount;
            
            $loan->update([
                'paid_amount' => $newPaidAmount,
                'remaining_balance' => $newRemainingBalance,
                'status' => $newRemainingBalance <= 0 ? 'completed' : 'approved',
            ]);
        }
    }

    /**
     * Update payroll and payslips
     */
    public function updatePayroll(Payroll $payroll, array $data, ?array $payslipsData = null): void
    {
        $payroll->update([
            'payroll_period' => $data['payroll_period'],
            'period_start' => $data['period_start'],
            'period_end' => $data['period_end'],
            'payment_date' => $data['payment_date'],
            'notes' => $data['notes'] ?? null,
        ]);

        if ($payslipsData) {
            $totalNetPay = 0;

            foreach ($payslipsData as $payslipId => $payslipData) {
                $payslip = Payslip::findOrFail($payslipId);
                
                $hoursWorked = floatval($payslipData['hours_worked'] ?? 0);
                
                if ($hoursWorked == 0) {
                    $payslip->update($this->getZeroPayslipData());
                    continue;
                }
                
                $calculatedData = $this->calculatePayslipFromInput($payslipData);
                $payslip->update($calculatedData);
                
                $totalNetPay += $calculatedData['net_pay'];
            }

            $payroll->update([
                'total_amount' => $totalNetPay,
                'total_employees' => $payroll->payslips()->count(),
            ]);
        }
    }

    /**
     * Get zero payslip data for employees with no hours
     */
    private function getZeroPayslipData(): array
    {
        return [
            'basic_salary' => 0,
            'overtime_pay' => 0,
            'allowances' => 0,
            'bonuses' => 0,
            'gross_pay' => 0,
            'tax' => 0,
            'sss' => 0,
            'philhealth' => 0,
            'pagibig' => 0,
            'other_deductions' => 0,
            'total_deductions' => 0,
            'net_pay' => 0,
            'hours_worked' => 0,
            'overtime_hours' => 0,
        ];
    }

    /**
     * Calculate payslip from input data
     */
    private function calculatePayslipFromInput(array $data): array
    {
        $basicSalary = floatval($data['basic_salary'] ?? 0);
        $overtimePay = floatval($data['overtime_pay'] ?? 0);
        $allowances = floatval($data['allowances'] ?? 0);
        $bonuses = floatval($data['bonuses'] ?? 0);
        
        $grossPay = $basicSalary + $overtimePay + $allowances + $bonuses;
        
        $tax = floatval($data['tax'] ?? 0);
        $sss = floatval($data['sss'] ?? 0);
        $philhealth = floatval($data['philhealth'] ?? 0);
        $pagibig = floatval($data['pagibig'] ?? 0);
        $lateDeduction = floatval($data['late_deduction'] ?? 0);
        $loanDeductions = floatval($data['loan_deductions'] ?? 0);
        $otherDeductions = floatval($data['other_deductions'] ?? 0);
        
        $totalDeductions = $tax + $sss + $philhealth + $pagibig + 
                          $lateDeduction + $loanDeductions + $otherDeductions;
        
        $netPay = $grossPay - $totalDeductions;
        
        return [
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'allowances' => $allowances,
            'bonuses' => $bonuses,
            'gross_pay' => $grossPay,
            'tax' => $tax,
            'sss' => $sss,
            'philhealth' => $philhealth,
            'pagibig' => $pagibig,
            'late_deduction' => $lateDeduction,
            'loan_deductions' => $loanDeductions,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_pay' => $netPay,
            'hours_worked' => floatval($data['hours_worked'] ?? 0),
            'overtime_hours' => floatval($data['overtime_hours'] ?? 0),
        ];
    }

    /**
     * Send salary to employee via simulated bank transfer
     */
    public function sendSalary(Payslip $payslip): void
    {
        $employee = $payslip->employee;
        
        // Check if employee has bank account
        if ($employee->bank_account_number && $employee->bank_name) {
            // Bank transfer
            $referenceNumber = 'TRX-' . strtoupper(uniqid()) . rand(100, 999);
            $bankName = $employee->bank_name;
            $accountNumber = $employee->bank_account_number;
        } else {
            // Issue check for manual pickup
            $referenceNumber = 'CHK-' . strtoupper(uniqid()) . rand(100, 999);
            $bankName = 'Company Check';
            $accountNumber = 'Pick up at HR Office';
        }
        
        // Update payslip with transfer details
        $payslip->update([
            'transfer_reference_number' => $referenceNumber,
            'transfer_bank_name' => $bankName,
            'transfer_account_number' => $accountNumber,
            'transfer_date' => now(),
            'is_salary_sent' => true,
        ]);
    }
}
