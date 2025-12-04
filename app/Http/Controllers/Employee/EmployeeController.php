<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateProfileRequest;
use App\Http\Requests\Employee\StoreLeaveApplicationRequest;
use App\Http\Requests\Employee\StoreLoanRequest;
use App\Http\Requests\Employee\SubmitSupportReportRequest;
use App\Http\Requests\Employee\UpdatePasswordRequest;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\ActivityLog;
use App\Services\Employee\EmployeeDashboardService;
use App\Services\Employee\EmployeeProfileService;
use App\Services\Employee\EmployeeLoanService;
use App\Services\Employee\EmployeeLeaveService;
use App\Services\Employee\EmployeePayslipService;
use App\Services\Employee\EmployeeSupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct(
        private EmployeeDashboardService $dashboardService,
        private EmployeeProfileService $profileService,
        private EmployeeLoanService $loanService,
        private EmployeeLeaveService $leaveService,
        private EmployeePayslipService $payslipService,
        private EmployeeSupportService $supportService
    ) {
    }

    /**
     * Get authenticated employee
     */
    protected function getAuthEmployee(): Employee
    {
        $user = Auth::user();
        $employee = $this->profileService->getEmployeeByUser($user);
        
        if (!$employee) {
            abort(redirect('/')->with('error', 'No employee profile found. Please contact HR.'));
        }
        
        $this->profileService->linkEmployeeToUser($employee, $user);
        
        return $employee;
    }

    /**
     * Show employee dashboard
     */
    public function dashboard(Request $request)
    {
        $employee = $this->getAuthEmployee();
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $data = $this->dashboardService->getDashboardData($employee, $month, $year);
        
        return view('employee.empDashboard', $data);
    }

    /**
     * Show employee profile
     */
    public function profile()
    {
        $employee = $this->getAuthEmployee();
        return view('employee.profile', compact('employee'));
    }

    /**
     * Update employee profile
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $employee = $this->getAuthEmployee();
        
        try {
            $this->profileService->updateProfile($employee, $request->validated());
            
            ActivityLog::log(
                'update',
                'employee',
                "Employee {$employee->full_name} updated their profile information",
                $employee->id,
                ['employee' => $employee->full_name]
            );
            
            return redirect()->route('employee.profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile.')->withInput();
        }
    }

    /**
     * Show leave application form
     */
    public function leaveApplication()
    {
        $employee = $this->getAuthEmployee();
        return view('employee.leaveApplication', compact('employee'));
    }

    /**
     * Store leave application
     */
    public function storeLeaveApplication(StoreLeaveApplicationRequest $request)
    {
        $employee = $this->getAuthEmployee();

        try {
            $this->leaveService->createLeaveApplication($employee, $request->validated());
            return redirect()->route('employee.leave.status')->with('success', 'Leave application submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit leave application.')->withInput();
        }
    }

    /**
     * Show leave status
     */
    public function leaveStatus()
    {
        $employee = $this->getAuthEmployee();
        $leaveApplications = $this->leaveService->getLeaveApplications($employee);

        return view('employee.leaveStatus', compact('employee', 'leaveApplications'));
    }

    /**
     * Show payslips
     */
    public function payslip()
    {
        $employee = $this->getAuthEmployee();
        $data = $this->payslipService->getPayslipsData($employee);

        return view('employee.payslip', [
            'employee' => $employee,
            'payslips' => $data['payslips'],
            'attendanceByPayslip' => $data['attendance_by_payslip'],
            'activeLoans' => $data['active_loans'],
            'totalLoanBalance' => $data['total_loan_balance'],
            'totalMonthlyDeduction' => $data['total_monthly_deduction'],
        ]);
    }

    /**
     * Show reports and support
     */
    public function reportSupport()
    {
        $employee = $this->getAuthEmployee();
        
        $leaveStats = $this->leaveService->getLeaveStatistics($employee);
        $payslipStats = $this->payslipService->getPayslipStatistics($employee);
        $supportReports = $this->supportService->getSupportReports($employee);
        
        return view('employee.report_support', [
            'employee' => $employee,
            'totalLeavesTaken' => $leaveStats['total_taken'],
            'vacationLeaves' => $leaveStats['vacation_leaves'],
            'sickLeaves' => $leaveStats['sick_leaves'],
            'pendingLeaves' => $leaveStats['pending_count'],
            'lastPayslip' => $payslipStats['last_payslip'],
            'avgMonthlyPay' => $payslipStats['avg_monthly_pay'],
            'totalPayslips' => $payslipStats['total_payslips'],
            'supportReports' => $supportReports,
        ]);
    }

    /**
     * Submit support report
     */
    public function submitSupportReport(SubmitSupportReportRequest $request)
    {
        $employee = $this->getAuthEmployee();

        try {
            $this->supportService->createSupportReport($employee, $request->validated());
            return redirect()->route('employee.report')->with('success', 'Help desk ticket submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit support ticket.')->withInput();
        }
    }

    /**
     * Show settings
     */
    public function settings()
    {
        $employee = $this->getAuthEmployee();
        $user = Auth::user();
        
        return view('employee.settings', compact('employee', 'user'));
    }

    /**
     * Update password
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $employee = $this->getAuthEmployee();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        try {
            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            ActivityLog::log(
                'update',
                'user_account',
                "Employee {$employee->full_name} changed their password",
                $employee->id,
                ['employee' => $employee->full_name]
            );

            return redirect()->route('employee.settings')->with('success', 'Password updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password.');
        }
    }

    /**
     * Show loans
     */
    public function loans()
    {
        $employee = $this->getAuthEmployee();

        $loans = Loan::where('employee_id', $employee->id)
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->with('payments')
            ->get();

        $totalBorrowed = $loans->whereIn('status', ['approved', 'completed'])->sum('amount');
        $totalPaid = $loans->whereIn('status', ['approved', 'completed'])->sum('paid_amount');
        $totalRemaining = $loans->where('status', 'approved')->sum('remaining_balance');

        return view('employee.loans', compact('employee', 'loans', 'activeLoans', 'totalBorrowed', 'totalPaid', 'totalRemaining'));
    }

    /**
     * Store loan request
     */
    public function storeLoan(StoreLoanRequest $request)
    {
        $employee = $this->getAuthEmployee();

        try {
            $loan = $this->loanService->createLoanRequest($employee, [
                'amount' => $request->input('amount'),
                'purpose' => $request->input('purpose'),
                'terms' => $request->input('terms'),
            ]);

            ActivityLog::log(
                'create',
                'loan',
                "Employee {$employee->full_name} submitted a loan request - ₱" . number_format($loan->amount, 2),
                $loan->id,
                ['employee' => $employee->full_name, 'amount' => $loan->amount, 'terms' => $loan->terms]
            );

            return redirect()->route('employee.loans')->with('success', 'Loan request submitted successfully! Wait for admin approval!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
