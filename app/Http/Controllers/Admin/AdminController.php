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
use Illuminate\Support\Facades\Hash;

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

        $attendances = $query->paginate(20);
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
            
            $hoursWorked = abs($timeOut->diffInMinutes($timeIn)) / 60;
            $validated['hours_worked'] = round($hoursWorked, 2);

            // Get employee's work schedule to calculate overtime
            $employee = Employee::find($validated['employee_id']);
            $regularHours = 8; // Default to 8 hours
            
            if ($employee && $employee->workSchedule) {
                $regularHours = $employee->workSchedule->daily_hours ?? 8;
            }

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
            
            $hoursWorked = abs($timeOut->diffInMinutes($timeIn)) / 60;
            $validated['hours_worked'] = round($hoursWorked, 2);

            // Get employee's work schedule to calculate overtime
            $employee = Employee::find($attendance->employee_id);
            $regularHours = 8; // Default to 8 hours
            
            if ($employee && $employee->workSchedule) {
                $regularHours = $employee->workSchedule->daily_hours ?? 8;
            }

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
            // Get attendance for this period
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$validated['period_start'], $validated['period_end']])
                ->where('status', 'present')
                ->get();

            $hoursWorked = $attendances->sum('hours_worked') ?? 0;
            $overtimeHours = $attendances->sum('overtime_hours') ?? 0;

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

            // Calculate deductions (simplified)
            $sss = $grossPay * 0.045; // 4.5% SSS
            $philhealth = $grossPay * 0.02; // 2% PhilHealth
            $pagibig = min($grossPay * 0.02, 100); // 2% Pag-IBIG, max 100
            $tax = $grossPay > 20833 ? ($grossPay - 20833) * 0.20 : 0; // Simplified tax

            $totalDeductions = $sss + $philhealth + $pagibig + $tax;
            $netPay = $grossPay - $totalDeductions;

            // Create payslip
            Payslip::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'allowances' => 0,
                'bonuses' => 0,
                'gross_pay' => $grossPay,
                'tax' => $tax,
                'sss' => $sss,
                'philhealth' => $philhealth,
                'pagibig' => $pagibig,
                'other_deductions' => 0,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'hours_worked' => $hoursWorked,
                'overtime_hours' => $overtimeHours,
            ]);

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
                $otherDeductions = floatval($data['other_deductions'] ?? 0);
                
                // Calculate Total Deductions
                $totalDeductionsEmp = $tax + $sss + $philhealth + $pagibig + $otherDeductions;
                
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
        return view('admin.view_employee_payslip', compact('payslip'));
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
        $query = Payslip::with(['employee', 'payroll'])
            ->whereHas('payroll', function($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc');

        // Filter by payroll
        if ($request->payroll_id) {
            $query->where('payroll_id', $request->payroll_id);
        }

        $payslips = $query->paginate(20);
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
        // Get filter parameters
        $reportType = $request->get('report_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $employeeId = $request->get('employee_id');

        // Summary Statistics
        $totalEmployees = Employee::count();
        $totalPayrolls = Payroll::count();
        $totalAttendance = Attendance::count();
        $totalLeaves = LeaveApplication::count();

        // Build query for detailed report data based on type
        $reportData = [];
        
        if ($reportType === 'payroll') {
            $query = Payslip::with(['employee', 'payroll']);
            
            if ($dateFrom && $dateTo) {
                $query->whereHas('payroll', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('period_start', [$dateFrom, $dateTo]);
                });
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            $reportData = $query->latest()->paginate(20);
            
        } elseif ($reportType === 'attendance') {
            $query = Attendance::with('employee');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('date', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            $reportData = $query->latest('date')->paginate(20);
            
        } elseif ($reportType === 'leave') {
            $query = LeaveApplication::with('employee');
            
            if ($dateFrom && $dateTo) {
                $query->whereBetween('start_date', [$dateFrom, $dateTo]);
            }
            
            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }
            
            $reportData = $query->latest()->paginate(20);
            
        } elseif ($reportType === 'employee') {
            $query = Employee::query();
            
            if ($employeeId) {
                $query->where('id', $employeeId);
            }
            
            $reportData = $query->paginate(20);
        }

        $employees = Employee::all();

        return view('admin.manage_reports', compact(
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

    public function settings()
    {
        return view('admin.settings');
    }
}
