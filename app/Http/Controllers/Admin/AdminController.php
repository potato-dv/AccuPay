<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveLoanRequest;
use App\Http\Requests\CreateUserAccountRequest;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Requests\RejectLeaveRequest;
use App\Http\Requests\RejectLoanRequest;
use App\Http\Requests\ReplySupportReportRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UpdateLoanPaymentRequest;
use App\Http\Requests\UpdatePayrollRequest;
use App\Http\Requests\UpdateSupportStatusRequest;
use App\Models\Attendance;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\Loan;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\SupportReport;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\EmployeeService;
use App\Services\ExportService;
use App\Services\LeaveService;
use App\Services\LoanService;
use App\Services\PayrollService;
use App\Services\ReportService;
use App\Services\SupportService;
use App\Services\UserAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService,
        private EmployeeService $employeeService,
        private PayrollService $payrollService,
        private LeaveService $leaveService,
        private LoanService $loanService,
        private ReportService $reportService,
        private ExportService $exportService,
        private SupportService $supportService,
        private UserAccountService $userAccountService
    ) {
    }

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

    // Attendance Management
    public function manageAttendance(Request $request)
    {
        $attendances = $this->attendanceService->getFilteredAttendance(
            $request->date_from,
            $request->date_to,
            $request->employee_id
        );

        $employees = $this->employeeService->getActiveEmployees();

        return view('admin.manage_attendance', compact('attendances', 'employees'));
    }

    public function storeAttendance(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();

        if ($this->attendanceService->attendanceExists($validated['employee_id'], $validated['date'])) {
            return back()
                ->withErrors(['date' => 'Attendance record already exists for this employee on this date.'])
                ->withInput();
        }

        $employee = Employee::findOrFail($validated['employee_id']);
        
        $hoursData = $this->attendanceService->calculateHoursWorked(
            $validated['date'],
            $request->time_in,
            $request->time_out,
            $employee
        );

        $validated = array_merge($validated, $hoursData);
        $attendance = Attendance::create($validated);

        // Log activity
        ActivityLog::log(
            'create',
            'attendance',
            "Added attendance record for {$employee->full_name} on {$validated['date']}",
            $attendance->id,
            ['employee' => $employee->full_name, 'date' => $validated['date'], 'hours' => $hoursData['hours_worked']]
        );

        return back()->with('success', 'Attendance record added successfully!');
    }

    public function updateAttendance(UpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $validated = $request->validated();

        $employee = Employee::find($attendance->employee_id);
        
        $hoursData = $this->attendanceService->calculateHoursWorked(
            $validated['date'],
            $request->time_in,
            $request->time_out,
            $employee
        );

        $validated = array_merge($validated, $hoursData);
        $attendance->update($validated);

        // Log activity
        ActivityLog::log(
            'update',
            'attendance',
            "Updated attendance record for {$employee->full_name} on {$validated['date']}",
            $attendance->id,
            ['employee' => $employee->full_name, 'date' => $validated['date'], 'hours' => $hoursData['hours_worked']]
        );

        return back()->with('success', 'Attendance record updated successfully!');
    }

    public function deleteAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $employeeName = $attendance->employee->full_name ?? 'Unknown';
        $date = $attendance->date->format('Y-m-d');
        
        // Log activity
        ActivityLog::log(
            'delete',
            'attendance',
            "Deleted attendance record for {$employeeName} on {$date}",
            $id,
            ['employee' => $employeeName, 'date' => $date]
        );
        
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted successfully!');
    }

    // Employee Management
    public function manageEmployees()
    {
        $employees = $this->employeeService->getAllEmployees();
        return view('admin.manage_employees', compact('employees'));
    }

    public function addEmployee()
    {
        return view('admin.add_employee');
    }

    public function storeEmployee(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();
        $validated['employee_id'] = $this->employeeService->generateEmployeeId();

        $employee = Employee::create($validated);

        // Log activity
        ActivityLog::log(
            'create',
            'employee',
            "Added new employee: {$employee->full_name} ({$employee->employee_id})",
            $employee->employee_id,
            ['name' => $employee->full_name, 'position' => $employee->position]
        );

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Employee added successfully!');
    }

    public function viewEmployee($id)
    {
        $employee = $this->employeeService->getEmployeeById($id, ['workSchedule']);
        return view('admin.view_employee', compact('employee'));
    }

    public function editEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.edit_employee', compact('employee'));
    }

    public function updateEmployee(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());

        // Log activity
        ActivityLog::log(
            'update',
            'employee',
            "Updated employee information: {$employee->full_name} ({$employee->employee_id})",
            $employee->employee_id,
            ['updated_fields' => array_keys($request->validated())]
        );

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Employee updated successfully!');
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.delete_employee', compact('employee'));
    }

    public function destroyEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employeeName = $employee->full_name;
        $employeeId = $employee->employee_id;
        
        // Log activity
        ActivityLog::log(
            'delete',
            'employee',
            "Deleted employee: {$employeeName} ({$employeeId})",
            $id,
            ['name' => $employeeName, 'employee_id' => $employeeId, 'position' => $employee->position]
        );
        
        $employee->delete();

        return redirect()
            ->route('admin.employees')
            ->with('success', 'Employee deleted successfully!');
    }

    // Payroll Management
    public function managePayroll()
    {
        $payrolls = Payroll::orderBy('created_at', 'desc')->get();
        return view('admin.manage_payroll', compact('payrolls'));
    }

    public function generatePayroll(GeneratePayrollRequest $request)
    {
        $result = $this->payrollService->generatePayroll($request->validated());

        // Log activity
        ActivityLog::log(
            'generate',
            'payroll',
            "Generated payroll for {$result['employee_count']} employees - Period: {$request->period_start} to {$request->period_end}",
            $result['payroll']->id ?? null,
            [
                'period' => $request->payroll_period,
                'employee_count' => $result['employee_count'],
            ]
        );

        return redirect()
            ->route('admin.payroll')
            ->with('success', "Payroll generated successfully for {$result['employee_count']} employees!");
    }

    public function approvePayroll($id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()
                ->route('admin.payroll')
                ->with('error', 'Only pending payrolls can be approved.');
        }

        $payroll->update(['status' => 'approved']);

        // Log activity
        ActivityLog::log(
            'approve',
            'payroll',
            "Approved payroll for period {$payroll->payroll_period}",
            $payroll->id,
            ['period' => $payroll->payroll_period, 'total_employees' => $payroll->total_employees]
        );

        return redirect()
            ->route('admin.payroll')
            ->with('success', 'Payroll approved! Employees can now view their payslips.');
    }

    public function editPayroll($id)
    {
        $payroll = Payroll::with('payslips.employee')->findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()
                ->route('admin.payroll')
                ->with('error', 'Only pending payrolls can be edited.');
        }

        return view('admin.edit_payroll', compact('payroll'));
    }

    public function updatePayroll(UpdatePayrollRequest $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status !== 'pending') {
            return redirect()
                ->route('admin.payroll')
                ->with('error', 'Only pending payrolls can be edited.');
        }

        $this->payrollService->updatePayroll(
            $payroll,
            $request->validated(),
            $request->payslips
        );

        // Log activity
        ActivityLog::log(
            'update',
            'payroll',
            "Updated payroll for period {$payroll->payroll_period}",
            $payroll->id,
            ['period' => $payroll->payroll_period, 'total_employees' => $payroll->total_employees]
        );

        return redirect()
            ->route('admin.payroll.view', $id)
            ->with('success', 'Payroll updated successfully!');
    }

    public function viewPayroll($id)
    {
        $payroll = Payroll::with('payslips.employee')->findOrFail($id);
        return view('admin.view_payroll', compact('payroll'));
    }

    public function viewEmployeePayslip($id)
    {
        $payslip = Payslip::with(['employee', 'payroll'])->findOrFail($id);
        
        $activeLoans = Loan::where('employee_id', $payslip->employee_id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->get();
        
        $totalLoanBalance = $activeLoans->sum('remaining_balance');
        $totalMonthlyDeduction = $activeLoans->sum('monthly_deduction');
        
        $allDates = $this->getPayslipAttendanceDates($payslip);
        
        return view('admin.view_employee_payslip', compact(
            'payslip',
            'allDates',
            'activeLoans',
            'totalLoanBalance',
            'totalMonthlyDeduction'
        ));
    }

    public function deletePayroll($id)
    {
        $payroll = Payroll::findOrFail($id);
        $period = $payroll->payroll_period;
        $employeeCount = $payroll->total_employees;
        
        // Log activity
        ActivityLog::log(
            'delete',
            'payroll',
            "Deleted payroll for period {$period}",
            $id,
            ['period' => $period, 'total_employees' => $employeeCount]
        );
        
        $payroll->payslips()->delete();
        $payroll->delete();

        return redirect()
            ->route('admin.payroll')
            ->with('success', 'Payroll deleted successfully!');
    }

    public function managePayslip(Request $request)
    {
        $query = Payslip::with(['employee', 'payroll', 'employee.attendance'])
            ->whereHas('payroll', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc');

        if ($request->payroll_id) {
            $query->where('payroll_id', $request->payroll_id);
        }

        $payslips = $query->get();
        $payrolls = Payroll::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.manage_payslip', compact('payslips', 'payrolls'));
    }

    // Leave Management
    public function manageLeaveApplications(Request $request)
    {
        $leaves = $this->leaveService->getFilteredLeaves(
            $request->status,
            $request->leave_type
        );

        return view('admin.manage_leave_applications', compact('leaves'));
    }

    public function approveLeave($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $employeeName = $leave->employee->full_name ?? 'Unknown';
        
        $this->leaveService->approveLeave($id);
        
        // Log activity
        ActivityLog::log(
            'approve',
            'leave',
            "Approved leave request for {$employeeName} ({$leave->leave_type})",
            $id,
            ['employee' => $employeeName, 'type' => $leave->leave_type, 'from' => $leave->from_date, 'to' => $leave->to_date]
        );
        
        return back()->with('success', 'Leave request approved successfully!');
    }

    public function rejectLeave(RejectLeaveRequest $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $employeeName = $leave->employee->full_name ?? 'Unknown';
        
        $this->leaveService->rejectLeave($id, $request->admin_remarks);
        
        // Log activity
        ActivityLog::log(
            'reject',
            'leave',
            "Rejected leave request for {$employeeName} ({$leave->leave_type})",
            $id,
            ['employee' => $employeeName, 'type' => $leave->leave_type, 'remarks' => $request->admin_remarks]
        );
        
        return back()->with('success', 'Leave request rejected.');
    }

    // Reports Management
    public function manageReports(Request $request)
    {
        $reportType = $request->get('report_type');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $exportFormat = $request->get('export');

        $totalEmployees = Employee::count();
        $totalPayrolls = Payroll::count();
        $totalAttendance = Attendance::count();
        $totalLeaves = LeaveApplication::count();

        $reportData = collect();
        
        if ($reportType) {
            $reportData = $this->reportService->generateReport(
                $reportType,
                $dateFrom,
                $dateTo,
                $employeeId
            );

            if ($exportFormat) {
                $summary = $this->reportService->calculateSummary($reportType, $reportData);
                return $this->exportService->exportToCSV(
                    $reportType,
                    $reportData,
                    $summary,
                    $dateFrom,
                    $dateTo
                );
            }
        }

        $employees = Employee::all();
        $users = User::all();

        return view('admin.manage_reports', compact(
            'totalEmployees',
            'totalPayrolls',
            'totalAttendance',
            'totalLeaves',
            'reportData',
            'employees',
            'users',
            'reportType',
            'dateFrom',
            'dateTo',
            'employeeId'
        ));
    }

    // User Account Management
    public function userAccounts()
    {
        $employees = Employee::with('user')->get();
        return view('admin.user_accounts', compact('employees'));
    }

    public function createUserAccount(CreateUserAccountRequest $request, $employeeId)
    {
        try {
            $result = $this->userAccountService->createUserAccount(
                $employeeId,
                $request->validated()
            );

            $employee = Employee::find($employeeId);

            // Log activity
            ActivityLog::log(
                'create',
                'user_account',
                "Created user account for {$employee->full_name} ({$employee->employee_id})",
                $employeeId,
                ['employee' => $employee->full_name, 'email' => $employee->email]
            );

            return back()->with(
                'success',
                "User account created successfully for {$employee->full_name}. Default password is: {$result['default_password']}"
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteUserAccount($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            $employeeName = $employee->full_name;
            $employeeIdNum = $employee->employee_id;
            
            $this->userAccountService->deleteUserAccount($employeeId);
            
            // Log activity
            ActivityLog::log(
                'delete',
                'user_account',
                "Deleted user account for {$employeeName} ({$employeeIdNum})",
                $employeeId,
                ['employee' => $employeeName, 'employee_id' => $employeeIdNum]
            );
            
            return back()->with('success', 'User account deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetUserPassword($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);
            $employeeName = $employee->full_name;
            $employeeIdNum = $employee->employee_id;
            
            $newPassword = $this->userAccountService->resetPassword($employeeId);
            
            // Log activity
            ActivityLog::log(
                'update',
                'user_account',
                "Reset password for {$employeeName} ({$employeeIdNum})",
                $employeeId,
                ['employee' => $employeeName, 'employee_id' => $employeeIdNum]
            );
            
            return back()->with('success', "Password reset successfully! New password is: {$newPassword}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Support Management
    public function manageSupportReports(Request $request)
    {
        $reports = $this->supportService->getFilteredReports(
            $request->status,
            $request->type,
            $request->search
        );

        return view('admin.support_reports', compact('reports'));
    }

    public function replySupportReport(ReplySupportReportRequest $request, $id)
    {
        // Get ticket details before reply
        $report = SupportReport::with('employee')->findOrFail($id);
        
        $this->supportService->replyToReport($id, $request->admin_reply);
        
        // Log the activity
        ActivityLog::log(
            'reply',
            'support',
            "Replied to support ticket #{$id}",
            $id,
            [
                'employee' => $report->employee->first_name . ' ' . $report->employee->last_name,
                'ticket_type' => $report->ticket_type,
                'subject' => $report->subject,
                'status' => 'in-progress'
            ]
        );
        
        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    public function updateSupportStatus(UpdateSupportStatusRequest $request, $id)
    {
        // Get ticket details before update
        $report = SupportReport::with('employee')->findOrFail($id);
        $oldStatus = $report->status;
        $newStatus = $request->status;
        
        $this->supportService->updateStatus($id, $request->status);
        
        // Log the activity
        ActivityLog::log(
            'update',
            'support',
            "Updated support ticket #{$id} status: {$oldStatus} → {$newStatus}",
            $id,
            [
                'employee' => $report->employee->first_name . ' ' . $report->employee->last_name,
                'ticket_type' => $report->ticket_type,
                'subject' => $report->subject,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]
        );
        
        return redirect()->back()->with('success', 'Status updated successfully!');
    }

    // Loan Management
    public function manageLoans(Request $request)
    {
        $loans = $this->loanService->getFilteredLoans(
            $request->status,
            $request->search
        );

        $employees = Employee::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('admin.manage_loans', compact('loans', 'employees'));
    }

    public function approveLoan(ApproveLoanRequest $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $employeeName = $loan->employee->full_name ?? 'Unknown';
        
        $this->loanService->approveLoan($id, $request->start_date);
        
        // Log activity
        ActivityLog::log(
            'approve',
            'loan',
            "Approved loan for {$employeeName} - ₱" . number_format($loan->loan_amount, 2),
            $id,
            ['employee' => $employeeName, 'amount' => $loan->loan_amount, 'start_date' => $request->start_date]
        );
        
        return redirect()->back()->with('success', 'Loan approved successfully!');
    }

    public function rejectLoan(RejectLoanRequest $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $employeeName = $loan->employee->full_name ?? 'Unknown';
        
        $this->loanService->rejectLoan($id, $request->reason);
        
        // Log activity
        ActivityLog::log(
            'reject',
            'loan',
            "Rejected loan for {$employeeName} - ₱" . number_format($loan->loan_amount, 2),
            $id,
            ['employee' => $employeeName, 'amount' => $loan->loan_amount, 'reason' => $request->reason]
        );
        
        return redirect()->back()->with('success', 'Loan rejected.');
    }

    public function updateLoanPayment(UpdateLoanPaymentRequest $request, $id)
    {
        try {
            $this->loanService->recordPayment(
                $id,
                $request->payment_amount,
                $request->notes
            );

            return redirect()->back()->with('success', 'Loan payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['payment_amount' => $e->getMessage()]);
        }
    }

    // Settings
    public function settings()
    {
        return view('admin.settings');
    }

    // Private Helper Methods
    
    /**
     * Get attendance dates for payslip period
     */
    private function getPayslipAttendanceDates(Payslip $payslip): array
    {
        $attendanceRecords = Attendance::where('employee_id', $payslip->employee_id)
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
            
            $isWorkingDay = $payslip->employee->isScheduledToWork($dateStr);
            
            if ($isWorkingDay) {
                if (isset($attendanceByDate[$dateStr])) {
                    $allDates[] = $attendanceByDate[$dateStr];
                } else {
                    $absentRecord = new Attendance();
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
        
        return $allDates;
    }
}
