<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\LeaveApplication;
use App\Models\Payslip;
use App\Models\User;
use App\Models\SupportReport;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'pending_leaves' => LeaveApplication::where('status', 'pending')->count(),
            'payroll_status' => Payroll::latest()->first()?->status ?? 'pending',
            'total_payrolls' => Payroll::count(),
            'recent_employees' => Employee::orderBy('created_at', 'desc')->limit(5)->get(),
            'total_attendance_today' => Attendance::whereDate('date', today())->count(),
        ];
        return view('admin.adminDashboard', $data);
    }

    public function manageAttendance(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('date', 'desc');

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->get();
        $employees = Employee::where('status', 'active')->orderBy('employee_id')->get();

        return view('admin.manage_attendance', compact('attendances', 'employees'));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,on-leave,late',
            'remarks' => 'nullable|string',
        ]);

        // Check if attendance record already exists for this employee on this date
        $existingAttendance = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existingAttendance) {
            return back()->withErrors(['date' => 'Attendance record already exists for this employee on this date.'])->withInput();
        }

        // Calculate hours worked if both time_in and time_out are provided
        if ($request->time_in && $request->time_out) {
            $date = $validated['date'];
            $timeIn = \Carbon\Carbon::parse($date . ' ' . $request->time_in);
            $timeOut = \Carbon\Carbon::parse($date . ' ' . $request->time_out);
            
            // If time_out is earlier than time_in, assume it's the next day
            if ($timeOut->lessThanOrEqualTo($timeIn)) {
                $timeOut->addDay();
            }
            
            // Calculate total time in minutes
            $totalMinutes = abs($timeOut->diffInMinutes($timeIn));
            
            // Get employee's work schedule
            $employee = Employee::find($validated['employee_id']);
            $regularHours = 8; // Default to 8 hours
            $breakMinutes = 0;
            
            if ($employee && $employee->workSchedule) {
                $regularHours = $employee->workSchedule->daily_hours ?? 8;
                
                // Calculate break duration if break times are set and break is unpaid
                if ($employee->workSchedule->break_start && $employee->workSchedule->break_end && !$employee->workSchedule->break_paid) {
                    $breakStart = \Carbon\Carbon::parse($date . ' ' . $employee->workSchedule->break_start);
                    $breakEnd = \Carbon\Carbon::parse($date . ' ' . $employee->workSchedule->break_end);
                    $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                }
            }
            
            // Subtract unpaid break time from total minutes
            $workedMinutes = $totalMinutes - $breakMinutes;
            $hoursWorked = $workedMinutes / 60;
            $validated['hours_worked'] = round($hoursWorked, 2);

            // Calculate overtime (hours worked beyond regular hours)
            if ($hoursWorked > $regularHours) {
                $validated['overtime_hours'] = round($hoursWorked - $regularHours, 2);
            } else {
                $validated['overtime_hours'] = 0;
            }
        } else {
            $validated['hours_worked'] = 0;
            $validated['overtime_hours'] = 0;
        }

        Attendance::create($validated);

        return back()->with('success', 'Attendance record added successfully!');
    }

    public function updateAttendance(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,on-leave,late',
            'remarks' => 'nullable|string',
        ]);

        // Calculate hours worked if both time_in and time_out are provided
        if ($request->time_in && $request->time_out) {
            $date = $validated['date'];
            $timeIn = \Carbon\Carbon::parse($date . ' ' . $request->time_in);
            $timeOut = \Carbon\Carbon::parse($date . ' ' . $request->time_out);
            
            // If time_out is earlier than time_in, assume it's the next day
            if ($timeOut->lessThanOrEqualTo($timeIn)) {
                $timeOut->addDay();
            }
            
            // Calculate total time in minutes
            $totalMinutes = abs($timeOut->diffInMinutes($timeIn));
            
            // Get employee's work schedule
            $employee = Employee::find($attendance->employee_id);
            $regularHours = 8; // Default to 8 hours
            $breakMinutes = 0;
            
            if ($employee && $employee->workSchedule) {
                $regularHours = $employee->workSchedule->daily_hours ?? 8;
                
                // Calculate break duration if break times are set and break is unpaid
                if ($employee->workSchedule->break_start && $employee->workSchedule->break_end && !$employee->workSchedule->break_paid) {
                    $breakStart = \Carbon\Carbon::parse($date . ' ' . $employee->workSchedule->break_start);
                    $breakEnd = \Carbon\Carbon::parse($date . ' ' . $employee->workSchedule->break_end);
                    $breakMinutes = abs($breakEnd->diffInMinutes($breakStart));
                }
            }
            
            // Subtract unpaid break time from total minutes
            $workedMinutes = $totalMinutes - $breakMinutes;
            $hoursWorked = $workedMinutes / 60;
            $validated['hours_worked'] = round($hoursWorked, 2);

            // Calculate overtime (hours worked beyond regular hours)
            if ($hoursWorked > $regularHours) {
                $validated['overtime_hours'] = round($hoursWorked - $regularHours, 2);
            } else {
                $validated['overtime_hours'] = 0;
            }
        } else {
            $validated['hours_worked'] = 0;
            $validated['overtime_hours'] = 0;
        }

        $attendance->update($validated);

        return back()->with('success', 'Attendance record updated successfully!');
    }

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted successfully!');
    }

    public function manageEmployees()
    {
        $employees = Employee::orderBy('created_at', 'desc')->get();
        return view('admin.manage_employees', compact('employees'));
    }

    public function addEmployee()
    {
        return view('admin.add_employee');
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:employees,email|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'birthdate' => 'required|date|before:today|before:-18 years',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date|before_or_equal:today',
            'basic_salary' => 'nullable|numeric|min:0|max:999999999.99',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999.99',
            'employment_type' => 'required|in:full-time,part-time,contract',
            'work_schedule_id' => 'required|exists:work_schedules,id',
            'tax_id_number' => 'nullable|string|max:50|unique:employees,tax_id_number',
            'sss_number' => 'nullable|string|max:50',
            'philhealth_number' => 'nullable|string|max:50',
            'pagibig_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'vacation_leave_credits' => 'nullable|integer|min:0|max:365',
            'sick_leave_credits' => 'nullable|integer|min:0|max:365',
            'emergency_leave_credits' => 'nullable|integer|min:0|max:365',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|regex:/^[0-9]{10,11}$/',
        ], [
            'first_name.regex' => 'First name must contain only letters and spaces.',
            'middle_name.regex' => 'Middle name must contain only letters and spaces.',
            'last_name.regex' => 'Last name must contain only letters and spaces.',
            'phone.regex' => 'Phone number must be 10 or 11 digits.',
            'emergency_phone.regex' => 'Emergency phone must be 10 or 11 digits.',
            'birthdate.before' => 'Employee must be at least 18 years old.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
        ]);

        // Generate employee ID
        $lastEmployee = Employee::latest('id')->first();
        $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
        $validated['employee_id'] = 'EMP' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        Employee::create($validated);

        return redirect()->route('admin.employees')->with('success', 'Employee added successfully!');
    }

    public function viewEmployee($id)
    {
        $employee = Employee::with('workSchedule')->findOrFail($id);
        return view('admin.view_employee', compact('employee'));
    }

    public function editEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.edit_employee', compact('employee'));
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:employees,email,'.$id.'|max:255',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'birthdate' => 'required|date|before:today|before:-18 years',
            'sex' => 'required|in:Male,Female',
            'civil_status' => 'required|in:Single,Married,Widowed,Separated',
            'department' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'hire_date' => 'required|date|before_or_equal:today',
            'basic_salary' => 'nullable|numeric|min:0|max:999999999.99',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999.99',
            'employment_type' => 'required|in:full-time,part-time,contract',
            'status' => 'required|in:active,on-leave,terminated',
            'work_schedule_id' => 'required|exists:work_schedules,id',
            'tax_id_number' => 'nullable|string|max:50|unique:employees,tax_id_number,'.$id,
            'sss_number' => 'nullable|string|max:50',
            'philhealth_number' => 'nullable|string|max:50',
            'pagibig_number' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'vacation_leave_credits' => 'nullable|integer|min:0',
            'sick_leave_credits' => 'nullable|integer|min:0',
            'emergency_leave_credits' => 'nullable|integer|min:0',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully!');
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.delete_employee', compact('employee'));
    }

    public function destroyEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully!');
    }

    public function managePayroll()
    {
        $payrolls = Payroll::orderBy('created_at', 'desc')->get();
        return view('admin.manage_payroll', compact('payrolls'));
    }

    public function generatePayroll(Request $request)
    {
        $validated = $request->validate([
            'payroll_period' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Get all active employees (including Active and On Leave status)
        $employees = Employee::whereIn('status', ['active', 'Active', 'on leave', 'On Leave'])->get();
        $totalAmount = 0;
        $employeeCount = 0;

        // Create payroll record with pending status
        $payroll = Payroll::create([
            'payroll_period' => $validated['payroll_period'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'payment_date' => $validated['payment_date'],
            'total_amount' => 0,
            'total_employees' => 0,
            'status' => 'pending',
            'notes' => $validated['notes'],
        ]);

        // Generate payslips for each employee
        foreach ($employees as $employee) {
            // Get all attendance records for this period
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$validated['period_start'], $validated['period_end']])
                ->get();

            // Calculate working days in period based on employee's work schedule
            $periodStart = \Carbon\Carbon::parse($validated['period_start']);
            $periodEnd = \Carbon\Carbon::parse($validated['period_end']);
            $totalWorkingDays = 0;
            $currentDate = $periodStart->copy();
            
            while ($currentDate->lte($periodEnd)) {
                if ($employee->isScheduledToWork($currentDate->format('Y-m-d'))) {
                    $totalWorkingDays++;
                }
                $currentDate->addDay();
            }

            // Count attendance by status
            // Note: "late" employees are still present, so days_present includes both present and late
            $daysPresent = $attendances->whereIn('status', ['present', 'late'])->count();
            $daysLate = $attendances->where('status', 'late')->count();
            $daysOnLeave = $attendances->where('status', 'on-leave')->count();
            $daysAbsent = max(0, $totalWorkingDays - $daysPresent - $daysOnLeave);

            // Get hours worked and overtime from present/late attendance
            $presentAttendances = $attendances->whereIn('status', ['present', 'late']);
            $hoursWorked = $presentAttendances->sum('hours_worked') ?? 0;
            $overtimeHours = $presentAttendances->sum('overtime_hours') ?? 0;

            // Get employee's work schedule for overtime rate
            $overtimeRate = 1.25; // Default overtime rate
            if ($employee->workSchedule && $employee->workSchedule->overtime_rate_multiplier) {
                $overtimeRate = $employee->workSchedule->overtime_rate_multiplier;
            }

            // Calculate pay based on employment type
            $basicSalary = 0;
            $overtimePay = 0;
            
            if ($employee->employment_type == 'full-time') {
                // Full-time employees only get paid if they have hours worked
                if ($hoursWorked > 0) {
                    $basicSalary = $employee->basic_salary / 2; // Semi-monthly
                    $overtimePay = $overtimeHours * ($employee->hourly_rate ?? 0) * $overtimeRate;
                } else {
                    // Even with no hours, still create payslip but with 0 values for full-time
                    // Skip this employee - they will not get a payslip
                    continue;
                }
            } else {
                // Part-time employees only get paid for hours worked
                if ($hoursWorked > 0) {
                    $basicSalary = $hoursWorked * $employee->hourly_rate;
                    $overtimePay = $overtimeHours * ($employee->hourly_rate * $overtimeRate);
                } else {
                    // No hours worked, skip this employee
                    continue;
                }
            }

            // Skip employees with no basic salary computed (safety check)
            if ($basicSalary == 0 && $hoursWorked == 0) {
                continue;
            }

            $grossPay = $basicSalary + $overtimePay;

            // Calculate late deduction (10 minutes or more late)
            $lateDeduction = 0;
            $lateAttendances = $attendances->where('status', 'late');
            foreach ($lateAttendances as $lateAtt) {
                if ($lateAtt->time_in && $employee->workSchedule) {
                    $attendanceDate = \Carbon\Carbon::parse($lateAtt->date)->format('Y-m-d');
                    $scheduledTime = \Carbon\Carbon::parse($attendanceDate . ' ' . $employee->workSchedule->time_in);
                    $actualTime = \Carbon\Carbon::parse($attendanceDate . ' ' . $lateAtt->time_in);
                    $lateMinutes = $scheduledTime->diffInMinutes($actualTime, false);
                    
                    if ($lateMinutes >= 10) {
                        // Deduct based on hourly rate and minutes late
                        $lateDeduction += ($employee->hourly_rate / 60) * $lateMinutes;
                    }
                }
            }

            // Calculate deductions
            $sssRate = 4.5;
            $philhealthRate = 2.0;
            $pagibigRate = 2.0;
            
            $sss = $grossPay * ($sssRate / 100); // 4.5% SSS
            $philhealth = $grossPay * ($philhealthRate / 100); // 2% PhilHealth
            $pagibig = min($grossPay * ($pagibigRate / 100), 100); // 2% Pag-IBIG, max 100
            
            // Calculate tax rate based on income bracket
            $taxRate = 0;
            if ($grossPay > 20833) {
                $taxRate = 20; // 20%
                $tax = ($grossPay - 20833) * 0.20;
            } else {
                $tax = 0;
            }

            // Calculate loan deductions
            $loanDeduction = 0;
            $activeLoans = Loan::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where('remaining_balance', '>', 0)
                ->whereNotNull('start_date')
                ->where('start_date', '<=', $validated['payment_date'])
                ->get();

            foreach ($activeLoans as $loan) {
                // Deduct the monthly amount or remaining balance (whichever is smaller)
                $deductionAmount = min($loan->monthly_deduction, $loan->remaining_balance);
                $loanDeduction += $deductionAmount;
            }

            $totalDeductions = $sss + $philhealth + $pagibig + $tax + $loanDeduction + $lateDeduction;
            $netPay = $grossPay - $totalDeductions;

            // Create payslip
            $payslip = Payslip::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'allowances' => 0,
                'bonuses' => 0,
                'gross_pay' => $grossPay,
                'tax' => $tax,
                'tax_rate' => $taxRate,
                'sss' => $sss,
                'sss_rate' => $sssRate,
                'philhealth' => $philhealth,
                'philhealth_rate' => $philhealthRate,
                'pagibig' => $pagibig,
                'pagibig_rate' => $pagibigRate,
                'other_deductions' => 0,
                'loan_deductions' => $loanDeduction,
                'late_deduction' => $lateDeduction,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'hours_worked' => $hoursWorked,
                'overtime_hours' => $overtimeHours,
                'days_present' => $daysPresent,
                'days_absent' => $daysAbsent,
                'days_late' => $daysLate,
            ]);

            // Record loan payments for each active loan
            foreach ($activeLoans as $loan) {
                $deductionAmount = min($loan->monthly_deduction, $loan->remaining_balance);
                
                // Create payment record
                LoanPayment::create([
                    'loan_id' => $loan->id,
                    'payroll_id' => $payroll->id,
                    'amount' => $deductionAmount,
                    'payment_date' => $validated['payment_date'],
                    'payment_type' => 'automatic',
                    'notes' => 'Automatic deduction from payroll: ' . $validated['payroll_period'],
                    'processed_by' => Auth::id(),
                ]);

                // Update loan balance
                $newPaidAmount = $loan->paid_amount + $deductionAmount;
                $newRemainingBalance = $loan->remaining_balance - $deductionAmount;
                
                $loan->update([
                    'paid_amount' => $newPaidAmount,
                    'remaining_balance' => $newRemainingBalance,
                    'status' => $newRemainingBalance <= 0 ? 'completed' : 'approved',
                ]);
            }

            $totalAmount += $netPay;
            $employeeCount++;
        }

        // Update payroll totals
        $payroll->update([
            'total_amount' => $totalAmount,
            'total_employees' => $employeeCount,
        ]);

        return redirect()->route('admin.payroll')->with('success', 'Payroll generated successfully for ' . $employeeCount . ' employees!');
    }

    public function approvePayroll($id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()->route('admin.payroll')->with('error', 'Only pending payrolls can be approved.');
        }

        $payroll->update(['status' => 'approved']);

        return redirect()->route('admin.payroll')->with('success', 'Payroll approved! Employees can now view their payslips.');
    }

    public function editPayroll($id)
    {
        $payroll = Payroll::with('payslips.employee')->findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()->route('admin.payroll')->with('error', 'Only pending payrolls can be edited.');
        }

        return view('admin.edit_payroll', compact('payroll'));
    }

    public function updatePayroll(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()->route('admin.payroll')->with('error', 'Only pending payrolls can be edited.');
        }

        // Update payroll details
        $validated = $request->validate([
            'payroll_period' => 'required|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $payroll->update($validated);

        // Update individual payslips if provided
        if ($request->has('payslips')) {
            $totalGrossPay = 0;
            $totalDeductions = 0;
            $totalNetPay = 0;

            foreach ($request->payslips as $payslipId => $data) {
                $payslip = Payslip::findOrFail($payslipId);
                
                // Get hours worked - if 0, skip calculation
                $hoursWorked = floatval($data['hours_worked'] ?? 0);
                
                if ($hoursWorked == 0) {
                    // Set all values to 0 for employees with no hours worked
                    $payslip->update([
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
                    ]);
                    continue;
                }
                
                // Get input values
                $basicSalary = floatval($data['basic_salary'] ?? 0);
                $overtimePay = floatval($data['overtime_pay'] ?? 0);
                $allowances = floatval($data['allowances'] ?? 0);
                $bonuses = floatval($data['bonuses'] ?? 0);
                
                // Calculate Gross Pay
                $grossPay = $basicSalary + $overtimePay + $allowances + $bonuses;
                
                // Get deductions
                $tax = floatval($data['tax'] ?? 0);
                $sss = floatval($data['sss'] ?? 0);
                $philhealth = floatval($data['philhealth'] ?? 0);
                $pagibig = floatval($data['pagibig'] ?? 0);
                $lateDeduction = floatval($data['late_deduction'] ?? 0);
                $loanDeductions = floatval($data['loan_deductions'] ?? 0);
                $otherDeductions = floatval($data['other_deductions'] ?? 0);
                
                // Calculate Total Deductions
                $totalDeductionsEmp = $tax + $sss + $philhealth + $pagibig + $lateDeduction + $loanDeductions + $otherDeductions;
                
                // Calculate Net Pay
                $netPay = $grossPay - $totalDeductionsEmp;
                
                // Update this employee's payslip
                $payslip->update([
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
                    'total_deductions' => $totalDeductionsEmp,
                    'net_pay' => $netPay,
                    'hours_worked' => $hoursWorked,
                    'overtime_hours' => floatval($data['overtime_hours'] ?? 0),
                ]);

                // Add to payroll totals
                $totalGrossPay += $grossPay;
                $totalDeductions += $totalDeductionsEmp;
                $totalNetPay += $netPay;
            }

            // Update payroll summary totals
            $payroll->update([
                'total_amount' => $totalNetPay,
                'total_employees' => $payroll->payslips()->count()
            ]);
        }

        return redirect()->route('admin.payroll.view', $id)->with('success', 'Payroll updated successfully!');
    }

    public function viewPayroll($id)
    {
        $payroll = Payroll::with('payslips.employee')->findOrFail($id);
        return view('admin.view_payroll', compact('payroll'));
    }

    public function viewEmployeePayslip($id)
    {
        $payslip = Payslip::with(['employee', 'payroll'])->findOrFail($id);
        
        // Get active loans for this employee
        $activeLoans = Loan::where('employee_id', $payslip->employee_id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->get();
        
        $totalLoanBalance = $activeLoans->sum('remaining_balance');
        $totalMonthlyDeduction = $activeLoans->sum('monthly_deduction');
        
        // Get detailed attendance records for this payroll period
        $attendanceRecords = Attendance::where('employee_id', $payslip->employee_id)
            ->whereBetween('date', [$payslip->payroll->period_start, $payslip->payroll->period_end])
            ->orderBy('date', 'asc')
            ->get();
        
        // Index by date for easy lookup
        $attendanceByDate = [];
        foreach ($attendanceRecords as $record) {
            $attendanceByDate[$record->date->format('Y-m-d')] = $record;
        }
        
        // Generate all dates in the payroll period
        $periodStart = \Carbon\Carbon::parse($payslip->payroll->period_start);
        $periodEnd = \Carbon\Carbon::parse($payslip->payroll->period_end);
        $allDates = [];
        $currentDate = $periodStart->copy();
        
        while ($currentDate->lte($periodEnd)) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Check if employee should work on this date
            $isWorkingDay = $payslip->employee->isScheduledToWork($dateStr);
            
            if ($isWorkingDay) {
                // Check if attendance record exists
                if (isset($attendanceByDate[$dateStr])) {
                    // Use actual attendance record
                    $allDates[] = $attendanceByDate[$dateStr];
                } else {
                    // Create absent record for display (no attendance logged)
                    $absentRecord = new \App\Models\Attendance();
                    $absentRecord->date = $dateStr;
                    $absentRecord->status = 'absent';
                    $absentRecord->hours_worked = 0;
                    $absentRecord->overtime_hours = 0;
                    $absentRecord->time_in = null;
                    $absentRecord->time_out = null;
                    $absentRecord->remarks = 'No attendance record';
                    $allDates[] = $absentRecord;
                }
            }
            
            $currentDate->addDay();
        }
        
        return view('admin.view_employee_payslip', compact('payslip', 'allDates', 'activeLoans', 'totalLoanBalance', 'totalMonthlyDeduction'));
    }

    public function deletePayroll($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->payslips()->delete();
        $payroll->delete();

        return redirect()->route('admin.payroll')->with('success', 'Payroll deleted successfully!');
    }

    public function managePayslip(Request $request)
    {
        $query = Payslip::with(['employee', 'payroll', 'employee.attendance'])
            ->whereHas('payroll', function($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc');

        // Filter by payroll
        if ($request->payroll_id) {
            $query->where('payroll_id', $request->payroll_id);
        }

        $payslips = $query->get();
        $payrolls = Payroll::where('status', 'approved')->orderBy('created_at', 'desc')->get();

        return view('admin.manage_payslip', compact('payslips', 'payrolls'));
    }

    public function manageLeaveApplications(Request $request)
    {
        $query = LeaveApplication::with('employee')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->leave_type) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->get();
        return view('admin.manage_leave_applications', compact('leaves'));
    }

    public function approveLeave($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request approved successfully!');
    }

    public function rejectLeave(Request $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);

        $validated = $request->validate([
            'admin_remarks' => 'required|string',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_remarks' => $validated['admin_remarks'],
        ]);

        return back()->with('success', 'Leave request rejected.');
    }

    public function manageReports(Request $request)
    {
        $reportType = $request->get('report_type');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $exportFormat = $request->get('export');

        // Summary Statistics
        $totalEmployees = Employee::count();
        $totalPayrolls = Payroll::count();
        $totalAttendance = Attendance::count();
        $totalLeaves = LeaveApplication::count();

        $reportData = collect();
        $summary = [];
        
        if ($reportType === 'payroll' || $reportType === 'payroll_detailed') {
            $query = Payslip::with(['employee', 'payroll']);
            
            if ($dateFrom && $dateTo) {
                $query->whereHas('payroll', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('period_start', [$dateFrom, $dateTo]);
                });
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest()->get();
                $summary = [
                    'total_gross' => $reportData->sum('gross_pay'),
                    'total_deductions' => $reportData->sum('total_deductions'),
                    'total_net' => $reportData->sum('net_pay'),
                    'count' => $reportData->count()
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest()->get();
            
        } elseif ($reportType === 'deductions') {
            $query = Payslip::with(['employee', 'payroll'])
                ->where(function($q) {
                    $q->where('sss', '>', 0)
                      ->orWhere('philhealth', '>', 0)
                      ->orWhere('pagibig', '>', 0)
                      ->orWhere('tax', '>', 0);
                });
            
            if ($dateFrom && $dateTo) {
                $query->whereHas('payroll', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('period_start', [$dateFrom, $dateTo]);
                });
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest()->get();
                $summary = [
                    'total_sss' => $reportData->sum('sss'),
                    'total_philhealth' => $reportData->sum('philhealth'),
                    'total_pagibig' => $reportData->sum('pagibig'),
                    'total_tax' => $reportData->sum('tax'),
                    'total_deductions' => $reportData->sum('total_deductions'),
                    'count' => $reportData->count()
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest()->get();
            
        } elseif ($reportType === 'overtime') {
            $query = Attendance::with('employee')->where('overtime_hours', '>', 0);
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('date', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest('date')->get();
                $summary = [
                    'total_records' => $reportData->count(),
                    'total_overtime' => $reportData->sum('overtime_hours')
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest('date')->get();
            
        } elseif ($reportType === 'leave_balance') {
            $query = Employee::query();
            
            if ($employeeId) {
                $query->where('id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->get();
                $reportData = $reportData->map(function($emp) use ($dateFrom, $dateTo) {
                    $year = $dateTo ? \Carbon\Carbon::parse($dateTo)->year : now()->year;
                    $totalLeave = 12; // Standard annual leave
                    $usedLeave = LeaveApplication::where('employee_id', $emp->id)
                        ->where('status', 'approved')
                        ->whereYear('start_date', $year)
                        ->sum('days_count');
                    $emp->total_leave = $totalLeave;
                    $emp->used_leave = $usedLeave;
                    $emp->remaining_leave = $totalLeave - $usedLeave;
                    return $emp;
                });
                $summary = [
                    'total_employees' => $reportData->count()
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->get()->map(function($emp) use ($dateFrom, $dateTo) {
                $year = $dateTo ? \Carbon\Carbon::parse($dateTo)->year : now()->year;
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
            
        } elseif ($reportType === 'employee_compensation') {
            $query = Employee::query();
            
            if ($employeeId) {
                $query->where('id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->get();
                $summary = [
                    'total_employees' => $reportData->count(),
                    'avg_salary' => $reportData->avg('basic_salary')
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->get();
            
        } elseif ($reportType === 'attendance') {
            $query = Attendance::with('employee');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('date', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest('date')->get();
                $summary = [
                    'total_records' => $reportData->count(),
                    'present' => $reportData->where('status', 'present')->count(),
                    'late' => $reportData->where('status', 'late')->count(),
                    'absent' => $reportData->where('status', 'absent')->count(),
                    'total_hours' => $reportData->sum('hours_worked'),
                    'total_overtime' => $reportData->sum('overtime_hours')
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest('date')->get();
            
        } elseif ($reportType === 'leave') {
            $query = LeaveApplication::with('employee');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('start_date', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest()->get();
                $summary = [
                    'total_applications' => $reportData->count(),
                    'approved' => $reportData->where('status', 'approved')->count(),
                    'pending' => $reportData->where('status', 'pending')->count(),
                    'rejected' => $reportData->where('status', 'rejected')->count(),
                    'total_days' => $reportData->sum('days_count')
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest()->get();
            
        } elseif ($reportType === 'loans') {
            $query = Loan::with('employee');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->latest()->get();
                $summary = [
                    'total_loans' => $reportData->count(),
                    'total_amount' => $reportData->sum('amount'),
                    'total_paid' => $reportData->sum('paid_amount'),
                    'total_remaining' => $reportData->sum('remaining_balance'),
                    'approved' => $reportData->where('status', 'approved')->count(),
                    'pending' => $reportData->where('status', 'pending')->count(),
                    'rejected' => $reportData->where('status', 'rejected')->count(),
                    'completed' => $reportData->where('status', 'completed')->count()
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->latest()->paginate(20);
            
        } elseif ($reportType === 'employee') {
            $query = Employee::query();
            
            if ($employeeId) {
                $query->where('id', $employeeId);
            }
            
            if ($exportFormat) {
                $reportData = $query->get();
                $summary = [
                    'total_employees' => $reportData->count(),
                    'active' => $reportData->where('status', 'active')->count(),
                    'inactive' => $reportData->where('status', 'inactive')->count(),
                    'full_time' => $reportData->where('employment_type', 'full-time')->count(),
                    'part_time' => $reportData->where('employment_type', 'part-time')->count()
                ];
                return $this->exportReport($reportType, $reportData, $summary, $exportFormat, $dateFrom, $dateTo);
            }
            
            $reportData = $query->get();
        }
        
        $employees = Employee::all();        return view('admin.manage_reports', compact(
            'totalEmployees',
            'totalPayrolls',
            'totalAttendance',
            'totalLeaves',
            'reportData',
            'employees',
            'reportType',
            'dateFrom',
            'dateTo',
            'employeeId'
        ));
    }

    private function exportReport($type, $data, $summary, $format, $dateFrom, $dateTo)
    {
        $filename = "{$type}_report_" . now()->format('Y-m-d_His');
        
        if ($format === 'csv') {
            return $this->exportCSV($type, $data, $summary, $filename, $dateFrom, $dateTo);
        }
        
        return redirect()->back()->with('error', 'Invalid export format');
    }

    private function exportCSV($type, $data, $summary, $filename, $dateFrom, $dateTo)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($type, $data, $summary, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Add report header
            fputcsv($file, ['ACCUPAY INC. - ' . strtoupper($type) . ' REPORT']);
            fputcsv($file, ['Period: ' . $dateFrom . ' to ' . $dateTo]);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y h:i A')]);
            fputcsv($file, []);
            
            if ($type === 'payroll') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Payroll Period', 'Period Start', 'Period End', 'Basic Salary', 'Gross Pay', 'Deductions', 'Net Pay']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        $row->payroll->payroll_period,
                        $row->payroll->period_start->format('M d, Y'),
                        $row->payroll->period_end->format('M d, Y'),
                        number_format($row->basic_salary, 2),
                        number_format($row->gross_pay, 2),
                        number_format($row->total_deductions, 2),
                        number_format($row->net_pay, 2)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Records', $summary['count']]);
                fputcsv($file, ['Total Gross Pay', number_format($summary['total_gross'], 2)]);
                fputcsv($file, ['Total Deductions', number_format($summary['total_deductions'], 2)]);
                fputcsv($file, ['Total Net Pay', number_format($summary['total_net'], 2)]);
                
            } elseif ($type === 'payroll_detailed') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Payroll Period', 'Period Start', 'Period End', 'Basic Salary', 'Allowances', 'Overtime Pay', 'Gross Pay', 'Deductions', 'Net Pay']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        $row->payroll->payroll_period,
                        $row->payroll->period_start->format('M d, Y'),
                        $row->payroll->period_end->format('M d, Y'),
                        number_format($row->basic_salary, 2),
                        number_format($row->allowances, 2),
                        number_format($row->overtime_pay, 2),
                        number_format($row->gross_pay, 2),
                        number_format($row->total_deductions, 2),
                        number_format($row->net_pay, 2)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Records', $summary['count']]);
                fputcsv($file, ['Total Gross Pay', number_format($summary['total_gross'], 2)]);
                fputcsv($file, ['Total Deductions', number_format($summary['total_deductions'], 2)]);
                fputcsv($file, ['Total Net Pay', number_format($summary['total_net'], 2)]);
                
            } elseif ($type === 'deductions') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Payroll Period', 'SSS', 'PhilHealth', 'Pag-IBIG', 'Tax', 'Other Deductions', 'Total Deductions']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        $row->payroll->payroll_period,
                        number_format($row->sss, 2),
                        number_format($row->philhealth, 2),
                        number_format($row->pagibig, 2),
                        number_format($row->tax, 2),
                        number_format($row->other_deductions, 2),
                        number_format($row->total_deductions, 2)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Records', $summary['count']]);
                fputcsv($file, ['Total SSS', number_format($summary['total_sss'], 2)]);
                fputcsv($file, ['Total PhilHealth', number_format($summary['total_philhealth'], 2)]);
                fputcsv($file, ['Total Pag-IBIG', number_format($summary['total_pagibig'], 2)]);
                fputcsv($file, ['Total Tax', number_format($summary['total_tax'], 2)]);
                fputcsv($file, ['Total Deductions', number_format($summary['total_deductions'], 2)]);
                
            } elseif ($type === 'overtime') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Date', 'Time In', 'Time Out', 'Regular Hours', 'Overtime Hours', 'Status']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        $row->date->format('M d, Y'),
                        $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '-',
                        $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '-',
                        number_format($row->hours_worked, 2),
                        number_format($row->overtime_hours, 2),
                        ucfirst($row->status)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Records', $summary['total_records']]);
                fputcsv($file, ['Total Overtime Hours', number_format($summary['total_overtime'], 2)]);
                
            } elseif ($type === 'leave_balance') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Department', 'Position', 'Total Leave Credits', 'Used Leave', 'Remaining Leave']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee_id,
                        $row->full_name,
                        $row->department,
                        $row->position,
                        $row->total_leave ?? 12,
                        $row->used_leave ?? 0,
                        $row->remaining_leave ?? 12
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Employees', $summary['total_employees']]);
                
            } elseif ($type === 'employee_compensation') {
                fputcsv($file, ['Employee ID', 'Name', 'Position', 'Department', 'Employment Type', 'Basic Salary', 'Hourly Rate', 'Status']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee_id,
                        $row->full_name,
                        $row->position,
                        $row->department,
                        ucfirst($row->employment_type),
                        number_format($row->basic_salary ?? 0, 2),
                        number_format($row->hourly_rate ?? 0, 2),
                        ucfirst($row->status)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Employees', $summary['total_employees']]);
                fputcsv($file, ['Average Salary', number_format($summary['avg_salary'] ?? 0, 2)]);
                
            } elseif ($type === 'attendance') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Date', 'Time In', 'Time Out', 'Hours Worked', 'Overtime Hours', 'Status']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        $row->date->format('M d, Y'),
                        $row->time_in ? \Carbon\Carbon::parse($row->time_in)->format('h:i A') : '-',
                        $row->time_out ? \Carbon\Carbon::parse($row->time_out)->format('h:i A') : '-',
                        number_format($row->hours_worked, 2),
                        number_format($row->overtime_hours, 2),
                        ucfirst($row->status)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Records', $summary['total_records']]);
                fputcsv($file, ['Present', $summary['present']]);
                fputcsv($file, ['Late', $summary['late']]);
                fputcsv($file, ['Absent', $summary['absent']]);
                fputcsv($file, ['Total Hours Worked', number_format($summary['total_hours'], 2)]);
                fputcsv($file, ['Total Overtime Hours', number_format($summary['total_overtime'], 2)]);
                
            } elseif ($type === 'leave') {
                fputcsv($file, ['Employee ID', 'Employee Name', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Reason', 'Status']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee->employee_id,
                        $row->employee->full_name,
                        ucfirst($row->leave_type),
                        $row->start_date->format('M d, Y'),
                        $row->end_date->format('M d, Y'),
                        $row->days_count,
                        $row->reason,
                        ucfirst($row->status)
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Applications', $summary['total_applications']]);
                fputcsv($file, ['Approved', $summary['approved']]);
                fputcsv($file, ['Pending', $summary['pending']]);
                fputcsv($file, ['Rejected', $summary['rejected']]);
                fputcsv($file, ['Total Leave Days', $summary['total_days']]);
                
            } elseif ($type === 'employee') {
                fputcsv($file, ['Employee ID', 'Name', 'Position', 'Department', 'Employment Type', 'Contact', 'Email', 'Status', 'Hire Date']);
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->employee_id,
                        $row->full_name,
                        $row->position,
                        $row->department,
                        ucfirst($row->employment_type),
                        $row->phone,
                        $row->email,
                        ucfirst($row->status),
                        $row->hire_date->format('M d, Y')
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY']);
                fputcsv($file, ['Total Employees', $summary['total_employees']]);
                fputcsv($file, ['Active', $summary['active']]);
                fputcsv($file, ['Inactive', $summary['inactive']]);
                fputcsv($file, ['Full-time', $summary['full_time']]);
                fputcsv($file, ['Part-time', $summary['part_time']]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function userAccounts()
    {
        $employees = Employee::with('user')->get();
        return view('admin.user_accounts', compact('employees'));
    }

    public function createUserAccount(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        if ($employee->user_id) {
            return back()->with('error', 'This employee already has a user account!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|email|unique:users,email|max:255',
        ]);

        // Create user account with employee_id as password
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($employee->employee_id),
            'role' => 'employee',
        ]);

        // Link user to employee
        $employee->update(['user_id' => $user->id]);

        return back()->with('success', 'User account created successfully for ' . $employee->full_name . '. Default password is: ' . $employee->employee_id);
    }

    public function deleteUserAccount($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        if (!$employee->user_id) {
            return back()->with('error', 'This employee does not have a user account!');
        }

        $user = $employee->user;
        $employee->update(['user_id' => null]);
        $user->delete();

        return back()->with('success', 'User account deleted successfully!');
    }

    public function resetUserPassword($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        if (!$employee->user_id) {
            return back()->with('error', 'This employee does not have a user account!');
        }

        $user = $employee->user;
        // Reset password to employee ID
        $user->update(['password' => Hash::make($employee->employee_id)]);

        return back()->with('success', 'Password reset successfully! New password is: ' . $employee->employee_id);
    }

    public function manageSupportReports(Request $request)
    {
        $query = SupportReport::with(['employee', 'repliedBy'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Search by subject or employee name
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhereHas('employee', function($eq) use ($request) {
                      $eq->where('full_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $reports = $query->get();

        return view('admin.support_reports', compact('reports'));
    }

    public function replySupportReport(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string',
        ]);

        $report = SupportReport::findOrFail($id);
        
        $report->update([
            'admin_reply' => $validated['admin_reply'],
            'replied_by' => Auth::id(),
            'replied_at' => now(),
            'status' => 'in-progress',
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function updateSupportStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in-progress,resolved,closed',
        ]);

        $report = SupportReport::findOrFail($id);
        $report->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function manageLoans(Request $request)
    {
        $query = Loan::with(['employee', 'approvedBy', 'payments'])->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search by employee name
        if ($request->search) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
            });
        }

        $loans = $query->get();

        $employees = Employee::where('status', 'active')->orderBy('first_name')->orderBy('last_name')->get();

        return view('admin.manage_loans', compact('loans', 'employees'));
    }

    public function approveLoan(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'approved',
            'start_date' => $validated['start_date'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'reason' => null,
        ]);

        return redirect()->back()->with('success', 'Loan approved successfully!');
    }

    public function rejectLoan(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $loan = Loan::findOrFail($id);
        $loan->update([
            'status' => 'rejected',
            'reason' => $validated['reason'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Loan rejected.');
    }

    public function updateLoanPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $loan = Loan::findOrFail($id);
        
        if ($validated['payment_amount'] > $loan->remaining_balance) {
            return redirect()->back()->withErrors(['payment_amount' => 'Payment amount cannot exceed remaining balance.']);
        }

        // Create manual payment record
        LoanPayment::create([
            'loan_id' => $loan->id,
            'payroll_id' => null,
            'amount' => $validated['payment_amount'],
            'payment_date' => now(),
            'payment_type' => 'manual',
            'notes' => $validated['notes'] ?? 'Manual payment recorded by admin',
            'processed_by' => Auth::id(),
        ]);

        // Update loan balance
        $newPaidAmount = $loan->paid_amount + $validated['payment_amount'];
        $newRemainingBalance = $loan->remaining_balance - $validated['payment_amount'];
        
        $loan->update([
            'paid_amount' => $newPaidAmount,
            'remaining_balance' => $newRemainingBalance,
            'status' => $newRemainingBalance <= 0 ? 'completed' : 'approved',
        ]);

        return redirect()->back()->with('success', 'Loan payment recorded successfully!');
    }
}