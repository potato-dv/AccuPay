<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reports</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>

        <ul>
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li class="active"><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Manage Reports</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- FILTER SECTION -->
        <section style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px; font-size: 18px;">Generate Report</h3>
            <form method="GET" action="{{ route('admin.reports') }}">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Report Type</label>
                        <select name="report_type" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">Select Type</option>
                            <optgroup label="Payroll">
                                <option value="payroll" {{ $reportType == 'payroll' ? 'selected' : '' }}>Payroll Summary</option>
                                <option value="payroll_detailed" {{ $reportType == 'payroll_detailed' ? 'selected' : '' }}>Detailed Payroll</option>
                                <option value="deductions" {{ $reportType == 'deductions' ? 'selected' : '' }}>Deductions</option>
                            </optgroup>
                            <optgroup label="Attendance">
                                <option value="attendance" {{ $reportType == 'attendance' ? 'selected' : '' }}>Attendance</option>
                                <option value="overtime" {{ $reportType == 'overtime' ? 'selected' : '' }}>Overtime</option>
                            </optgroup>
                            <optgroup label="Leave">
                                <option value="leave" {{ $reportType == 'leave' ? 'selected' : '' }}>Leave Applications</option>
                                <option value="leave_balance" {{ $reportType == 'leave_balance' ? 'selected' : '' }}>Leave Balance</option>
                            </optgroup>
                            <optgroup label="Employee">
                                <option value="employee" {{ $reportType == 'employee' ? 'selected' : '' }}>Employee List</option>
                                <option value="employee_compensation" {{ $reportType == 'employee_compensation' ? 'selected' : '' }}>Compensation</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">From</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">To</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Employee (Optional)</label>
                        <select name="employee_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->employee_id }} - {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; white-space: nowrap;">
                            Generate
                        </button>
                    </div>
                </div>
            </form>
        </section>

        @if($reportType)
        <!-- REPORT HEADER -->
        <div style="background: white; padding: 15px 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 20px;">
                @if($reportType == 'payroll') Payroll Summary
                @elseif($reportType == 'payroll_detailed') Detailed Payroll Report
                @elseif($reportType == 'deductions') Deductions Report
                @elseif($reportType == 'attendance') Attendance Report
                @elseif($reportType == 'overtime') Overtime Report
                @elseif($reportType == 'leave') Leave Applications
                @elseif($reportType == 'leave_balance') Leave Balance Report
                @elseif($reportType == 'employee') Employee Master List
                @elseif($reportType == 'employee_compensation') Compensation Report
                @endif
            </h2>
            <a href="{{ route('admin.reports', array_merge(request()->all(), ['export' => 'csv'])) }}" 
               style="padding: 8px 16px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">
                <i class="fa-solid fa-download"></i> Export CSV
            </a>
        </div>

        <!-- SUMMARY STATISTICS -->
        @if(($reportType == 'payroll' || $reportType == 'payroll_detailed') && $reportData->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Records</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->total() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Gross Pay</div>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">₱{{ number_format($reportData->sum('gross_pay'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Deductions</div>
                <div style="font-size: 24px; font-weight: bold; color: #dc3545;">₱{{ number_format($reportData->sum('total_deductions'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Net Pay</div>
                <div style="font-size: 24px; font-weight: bold; color: #007bff;">₱{{ number_format($reportData->sum('net_pay'), 2) }}</div>
            </div>
        </div>
        @elseif($reportType == 'deductions' && $reportData->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">SSS</div>
                <div style="font-size: 24px; font-weight: bold;">₱{{ number_format($reportData->sum('sss'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">PhilHealth</div>
                <div style="font-size: 24px; font-weight: bold;">₱{{ number_format($reportData->sum('philhealth'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Pag-IBIG</div>
                <div style="font-size: 24px; font-weight: bold;">₱{{ number_format($reportData->sum('pagibig'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Tax</div>
                <div style="font-size: 24px; font-weight: bold;">₱{{ number_format($reportData->sum('tax'), 2) }}</div>
            </div>
        </div>
        @elseif(($reportType == 'attendance' || $reportType == 'overtime') && $reportData->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Records</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->total() }}</div>
            </div>
            @if($reportType == 'attendance')
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Present</div>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $reportData->where('status', 'present')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Late</div>
                <div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $reportData->where('status', 'late')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Absent</div>
                <div style="font-size: 24px; font-weight: bold; color: #dc3545;">{{ $reportData->where('status', 'absent')->count() }}</div>
            </div>
            @else
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Total OT Hours</div>
                <div style="font-size: 24px; font-weight: bold; color: #007bff;">{{ number_format($reportData->sum('overtime_hours'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Avg OT/Day</div>
                <div style="font-size: 24px; font-weight: bold;">{{ number_format($reportData->avg('overtime_hours'), 2) }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Total Hours</div>
                <div style="font-size: 24px; font-weight: bold;">{{ number_format($reportData->sum('hours_worked'), 2) }}</div>
            </div>
            @endif
        </div>
        @elseif($reportType == 'leave' && $reportData->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Total</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->total() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Approved</div>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $reportData->where('status', 'approved')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Pending</div>
                <div style="font-size: 24px; font-weight: bold; color: #ffc107;">{{ $reportData->where('status', 'pending')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Rejected</div>
                <div style="font-size: 24px; font-weight: bold; color: #dc3545;">{{ $reportData->where('status', 'rejected')->count() }}</div>
            </div>
        </div>
        @elseif(($reportType == 'employee' || $reportType == 'employee_compensation' || $reportType == 'leave_balance') && $reportData->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Total</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->total() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Active</div>
                <div style="font-size: 24px; font-weight: bold; color: #28a745;">{{ $reportData->where('status', 'active')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Full-time</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->where('employment_type', 'full-time')->count() }}</div>
            </div>
            <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Part-time</div>
                <div style="font-size: 24px; font-weight: bold;">{{ $reportData->where('employment_type', 'part-time')->count() }}</div>
            </div>
        </div>
        @endif

        <!-- DATA TABLE -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="overflow-x: auto;">
            @if($reportType == 'payroll' || $reportType == 'payroll_detailed')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee ID</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Name</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Period</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Basic</th>
                        @if($reportType == 'payroll_detailed')
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Allowances</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">OT</th>
                        @endif
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Gross</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Deductions</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px; font-weight: 600;">Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $payslip)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $payslip->employee->employee_id }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $payslip->employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $payslip->payroll->payroll_period }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->basic_salary, 2) }}</td>
                        @if($reportType == 'payroll_detailed')
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->allowances, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                        @endif
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->gross_pay, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px; color: #dc3545;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px; font-weight: 600;">₱{{ number_format($payslip->net_pay, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $reportType == 'payroll_detailed' ? '9' : '7' }}" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'deductions')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Period</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">SSS</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">PhilHealth</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Pag-IBIG</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Tax</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Other</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px; font-weight: 600;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $payslip)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $payslip->employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $payslip->payroll->payroll_period }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->sss, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->philhealth, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->pagibig, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->tax, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($payslip->other_deductions, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px; font-weight: 600;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'attendance')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Date</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Time In</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Time Out</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Hours</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">OT</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $attendance)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $attendance->employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $attendance->date->format('M d, Y') }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ number_format($attendance->hours_worked, 2) }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ number_format($attendance->overtime_hours, 2) }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">
                            <span style="padding: 4px 8px; background: {{ $attendance->status == 'present' ? '#d4edda' : ($attendance->status == 'late' ? '#fff3cd' : '#f8d7da') }}; color: {{ $attendance->status == 'present' ? '#155724' : ($attendance->status == 'late' ? '#856404' : '#721c24') }}; border-radius: 4px; font-size: 12px;">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'overtime')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Date</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Time In</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Time Out</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Regular Hours</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px; font-weight: 600;">OT Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $attendance)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $attendance->employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $attendance->date->format('M d, Y') }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ number_format($attendance->hours_worked, 2) }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px; font-weight: 600; color: #007bff;">{{ number_format($attendance->overtime_hours, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'leave')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Type</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Start</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">End</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Days</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Reason</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $leave)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $leave->employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ ucfirst($leave->leave_type) }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $leave->start_date->format('M d, Y') }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $leave->end_date->format('M d, Y') }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $leave->days_count }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ Str::limit($leave->reason, 40) }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">
                            <span style="padding: 4px 8px; background: {{ $leave->status == 'approved' ? '#d4edda' : ($leave->status == 'rejected' ? '#f8d7da' : '#fff3cd') }}; color: {{ $leave->status == 'approved' ? '#155724' : ($leave->status == 'rejected' ? '#721c24' : '#856404') }}; border-radius: 4px; font-size: 12px;">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'leave_balance')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Employee ID</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Name</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Department</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Total Credits</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Used</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px; font-weight: 600;">Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $employee)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->employee_id }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->department }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $employee->total_leave ?? 12 }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px; color: #dc3545;">{{ $employee->used_leave ?? 0 }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px; font-weight: 600; color: #28a745;">{{ $employee->remaining_leave ?? 12 }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'employee')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">ID</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Name</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Position</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Department</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Type</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Contact</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $employee)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->employee_id }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->position }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->department }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ ucfirst($employee->employment_type) }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->phone }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">
                            <span style="padding: 4px 8px; background: {{ $employee->status == 'active' ? '#d4edda' : '#f8d7da' }}; color: {{ $employee->status == 'active' ? '#155724' : '#721c24' }}; border-radius: 4px; font-size: 12px;">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'employee_compensation')
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">ID</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Name</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Position</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Type</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Basic Salary</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Hourly Rate</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $employee)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->employee_id }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->full_name }}</td>
                        <td style="padding: 12px; font-size: 13px;">{{ $employee->position }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">{{ ucfirst($employee->employment_type) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($employee->basic_salary ?? 0, 2) }}</td>
                        <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($employee->hourly_rate ?? 0, 2) }}</td>
                        <td style="padding: 12px; text-align: center; font-size: 13px;">
                            <span style="padding: 4px 8px; background: {{ $employee->status == 'active' ? '#d4edda' : '#f8d7da' }}; color: {{ $employee->status == 'active' ? '#155724' : '#721c24' }}; border-radius: 4px; font-size: 12px;">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d;">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
            </div>

            <!-- PAGINATION -->
        </div>
        @else
        <!-- NO REPORT SELECTED MESSAGE -->
        <div style="background: white; padding: 60px; margin-top: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <i class="fa-solid fa-chart-line" style="font-size: 64px; color: #dee2e6; margin-bottom: 20px;"></i>
            <h3 style="color: #6c757d; font-size: 18px; margin: 0;">Select a report type and click "Generate" to view data</h3>
        </div>
        @endif

    </main>

    <!-- SIDEBAR TOGGLE SCRIPT -->
    <script>
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const navbar = document.querySelector('.navbar');
        const mainContent = document.querySelector('.main-content');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if(sidebar.classList.contains('collapsed')){
                navbar.style.left = '90px';
                mainContent.style.marginLeft = '90px';
            } else {
                navbar.style.left = '230px';
                mainContent.style.marginLeft = '230px';
            }
        });
    </script>

</body>
</html>
