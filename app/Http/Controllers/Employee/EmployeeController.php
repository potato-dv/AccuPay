<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\Payslip;
use App\Models\SupportReport;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee) {
            return redirect('/')->with('error', 'No employee profile found. Please contact HR.');
        }
        
        // Auto-link employee to user if not linked
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get attendance statistics
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'desc')
            ->get();
        
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();
        $totalOvertimeHours = $attendanceRecords->sum('overtime_hours');
        
        $data = [
            'employee' => $employee,
            'remaining_leave' => 12 - LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->whereYear('start_date', $currentYear)
                ->sum('days_count'),
            'attendance_count' => Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->count(),
            'total_work_days' => now()->day,
            'last_payslip' => Payslip::where('employee_id', $employee->id)->latest()->first(),
            'recent_attendance' => Attendance::where('employee_id', $employee->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get(),
            'recent_activities' => $this->getRecentActivities($employee->id),
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'total_overtime_hours' => $totalOvertimeHours,
            'attendance_records' => $attendanceRecords,
        ];

        return view('employee.empDashboard', $data);
    }

    public function profile()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee) {
            return redirect('/')->with('error', 'No employee profile found.');
        }
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        return view('employee.profile', compact('employee'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        $validated = $request->validate([
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|regex:/^[0-9]{10,11}$/',
        ], [
            'phone.regex' => 'Phone number must be 10 or 11 digits.',
            'emergency_phone.regex' => 'Emergency phone must be 10 or 11 digits.',
        ]);

        $employee->update($validated);

        return redirect()->route('employee.profile')->with('success', 'Profile updated successfully!');
    }

    public function leaveApplication()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
        
        return view('employee.leaveApplication', compact('employee'));
    }

    public function storeLeaveApplication(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }

        $validated = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $startDate = new \DateTime($validated['start_date']);
        $endDate = new \DateTime($validated['end_date']);
        $daysCount = $startDate->diff($endDate)->days + 1;

        LeaveApplication::create([
            'employee_id' => $employee->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => $daysCount,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('employee.leave.status')->with('success', 'Leave application submitted successfully!');
    }

    public function leaveStatus()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
        
        $leaveApplications = LeaveApplication::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.leaveStatus', compact('employee', 'leaveApplications'));
    }

    public function payslip()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
        
        // Only show payslips from approved payrolls
        $payslips = Payslip::with(['payroll'])
            ->where('employee_id', $employee->id)
            ->whereHas('payroll', function($query) {
                $query->where('status', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get attendance records for each payslip including absent days
        $attendanceByPayslip = [];
        foreach ($payslips as $payslip) {
            $attendanceRecords = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$payslip->payroll->period_start, $payslip->payroll->period_end])
                ->orderBy('date', 'asc')
                ->get();
            
            // Index by date for easy lookup
            $attendanceByDate = [];
            foreach ($attendanceRecords as $record) {
                $attendanceByDate[$record->date->format('Y-m-d')] = $record;
            }
            
            // Generate all working dates in the payroll period
            $periodStart = \Carbon\Carbon::parse($payslip->payroll->period_start);
            $periodEnd = \Carbon\Carbon::parse($payslip->payroll->period_end);
            $allDates = [];
            $currentDate = $periodStart->copy();
            
            while ($currentDate->lte($periodEnd)) {
                $dateStr = $currentDate->format('Y-m-d');
                
                // Check if employee should work on this date
                $isWorkingDay = $employee->isScheduledToWork($dateStr);
                
                if ($isWorkingDay) {
                    // Check if attendance record exists
                    if (isset($attendanceByDate[$dateStr])) {
                        $allDates[] = $attendanceByDate[$dateStr];
                    } else {
                        // Create absent record for display
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

        return view('employee.payslip', compact('employee', 'payslips', 'attendanceByPayslip'));
    }

    public function reportSupport()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
        
        $currentYear = now()->year;
        
        // Leave statistics
        $totalLeavesTaken = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->sum('days_count');
            
        $vacationLeaves = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('leave_type', 'Vacation Leave')
            ->whereYear('start_date', $currentYear)
            ->sum('days_count');
            
        $sickLeaves = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('leave_type', 'Sick Leave')
            ->whereYear('start_date', $currentYear)
            ->sum('days_count');
            
        $pendingLeaves = LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();
        
        // Payroll statistics
        $lastPayslip = Payslip::where('employee_id', $employee->id)
            ->latest()
            ->first();
            
        $avgMonthlyPay = Payslip::where('employee_id', $employee->id)
            ->avg('net_pay');
            
        $totalPayslips = Payslip::where('employee_id', $employee->id)->count();
        
        // Support reports
        $supportReports = SupportReport::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('employee.report_support', compact(
            'employee', 
            'totalLeavesTaken', 
            'vacationLeaves', 
            'sickLeaves', 
            'pendingLeaves',
            'lastPayslip',
            'avgMonthlyPay',
            'totalPayslips',
            'supportReports'
        ));
    }

    public function submitSupportReport(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:technical,payroll,leave,attendance,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();

        SupportReport::create([
            'employee_id' => $employee->id,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return redirect()->route('employee.report')->with('success', 'Help desk ticket submitted successfully!');
    }

    public function settings()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();
        
        if (!$employee->user_id) {
            $employee->update(['user_id' => $user->id]);
        }
        
        return view('employee.settings', compact('employee', 'user'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employee.settings')->with('success', 'Password updated successfully!');
    }

    private function getRecentActivities($employeeId)
    {
        $activities = [];

        $recentLeave = LeaveApplication::where('employee_id', $employeeId)
            ->latest()
            ->first();
        if ($recentLeave) {
            $activities[] = "Leave request " . $recentLeave->status . " on " . $recentLeave->created_at->format('M d');
        }

        $recentPayslip = Payslip::where('employee_id', $employeeId)
            ->latest()
            ->first();
        if ($recentPayslip) {
            $activities[] = "Payslip for " . $recentPayslip->period . " available";
        }

        $recentAttendance = Attendance::where('employee_id', $employeeId)
            ->latest()
            ->first();
        if ($recentAttendance) {
            $activities[] = "Attendance updated for " . $recentAttendance->date->format('M d');
        }

        return $activities;
    }

    public function loans()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();

        $loans = Loan::where('employee_id', $employee->id)
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeLoans = Loan::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('remaining_balance', '>', 0)
            ->with('payments')
            ->get();

        // Calculate totals using paid_amount field
        $totalBorrowed = $loans->whereIn('status', ['approved', 'completed'])->sum('amount');
        $totalPaid = $loans->whereIn('status', ['approved', 'completed'])->sum('paid_amount');
        $totalRemaining = $loans->where('status', 'approved')->sum('remaining_balance');

        return view('employee.loans', compact('employee', 'loans', 'activeLoans', 'totalBorrowed', 'totalPaid', 'totalRemaining'));
    }

    public function storeLoan(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100|max:100000',
            'purpose' => 'required|string|max:255',
            'terms' => 'required|integer|min:1|max:24',
        ]);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->orWhere('email', $user->email)->first();

        $monthlyDeduction = round($validated['amount'] / $validated['terms'], 2);

        Loan::create([
            'employee_id' => $employee->id,
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'terms' => $validated['terms'],
            'monthly_deduction' => $monthlyDeduction,
            'paid_amount' => 0,
            'remaining_balance' => $validated['amount'],
            'status' => 'pending',
        ]);

        return redirect()->route('employee.loans')->with('success', 'Loan request submitted successfully! Wait for admin approval.');
    }
}
