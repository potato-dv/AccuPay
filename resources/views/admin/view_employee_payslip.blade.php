<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payslip Details - AccuPay</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>
        <ul>
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li class="active"><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-envelope-open-text"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-file-contract"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-user-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Employee Payslip Details</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <main class="main-content">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div style="margin-bottom: 20px;">
                <a href="{{ route('admin.payroll.view', $payslip->payroll_id) }}" class="btn-delete" style="text-decoration: none; display: inline-block;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Payroll
                </a>
                @if($payslip->payroll->status == 'pending')
                    <a href="{{ route('admin.payroll.edit', $payslip->payroll_id) }}" class="btn-theme" style="text-decoration: none; display: inline-block; margin-left: 10px;">
                        <i class="fa-solid fa-edit"></i> Edit Payroll
                    </a>
                @endif
            </div>

            <!-- Employee Header -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; overflow: hidden;">
                <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
                    <h3 style="margin: 0 0 15px 0; color: #333; font-size: 18px;">
                        <i class="fa-solid fa-user"></i> {{ $payslip->employee->full_name }}
                    </h3>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap; font-size: 13px; color: #666;">
                        <div><strong>Employee ID:</strong> {{ $payslip->employee->employee_id }}</div>
                        <div><strong>Position:</strong> {{ $payslip->employee->position ?? 'N/A' }}</div>
                        <div><strong>Department:</strong> {{ $payslip->employee->department ?? 'N/A' }}</div>
                        <div><strong>Period:</strong> {{ $payslip->payroll->payroll_period }}</div>
                        <div><strong>Dates:</strong> {{ date('M d', strtotime($payslip->payroll->period_start)) }} - {{ date('M d, Y', strtotime($payslip->payroll->period_end)) }}</div>
                    </div>
                </div>

                <!-- Work Schedule Information -->
                @if($payslip->employee->workSchedule)
                <div style="padding: 15px 20px; background: #fff8e1; border-bottom: 1px solid #ddd;">
                    <h4 style="margin: 0 0 12px 0; color: #333; font-size: 14px;">
                        <i class="fas fa-clock"></i> Work Schedule: {{ $payslip->employee->workSchedule->schedule_name }}
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 13px;">
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Working Days</div>
                            <div style="font-weight: 600; color: #333;">
                                {{ implode(', ', array_map(fn($d) => substr($d, 0, 3), $payslip->employee->workSchedule->working_days)) }}
                            </div>
                        </div>
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Shift Hours</div>
                            <div style="font-weight: 600; color: #333;">
                                {{ date('h:i A', strtotime($payslip->employee->workSchedule->shift_start)) }} - 
                                {{ date('h:i A', strtotime($payslip->employee->workSchedule->shift_end)) }}
                            </div>
                        </div>
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Daily Hours</div>
                            <div style="font-weight: 600; color: #333;">{{ number_format($payslip->employee->workSchedule->daily_hours, 1) }} hours/day</div>
                        </div>
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Weekly Hours</div>
                            <div style="font-weight: 600; color: #333;">{{ number_format($payslip->employee->workSchedule->weekly_hours, 1) }} hours/week</div>
                        </div>
                        @if($payslip->employee->workSchedule->break_start && $payslip->employee->workSchedule->break_end)
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Break Time</div>
                            <div style="font-weight: 600; color: #333;">
                                {{ date('h:i A', strtotime($payslip->employee->workSchedule->break_start)) }} - 
                                {{ date('h:i A', strtotime($payslip->employee->workSchedule->break_end)) }}
                                <span style="font-size: 11px; color: #666;">({{ $payslip->employee->workSchedule->break_paid ? 'Paid' : 'Unpaid' }})</span>
                            </div>
                        </div>
                        @endif
                        @if($payslip->employee->workSchedule->overtime_allowed)
                        <div>
                            <div style="color: #666; margin-bottom: 5px;">Overtime Rate</div>
                            <div style="font-weight: 600; color: #333;">{{ number_format($payslip->employee->workSchedule->overtime_rate_multiplier, 2) }}x</div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div style="padding: 15px 20px; background: #fff3cd; border-bottom: 1px solid #ddd;">
                    <div style="color: #856404; font-size: 13px;">
                        <i class="fas fa-exclamation-triangle"></i> No work schedule assigned to this employee
                    </div>
                </div>
                @endif

                <!-- Payslip Details -->
                <div style="padding: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <!-- Attendance Summary -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fas fa-calendar-check"></i> Attendance Summary
                            </h4>
                            <table style="width: 100%; font-size: 13px;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Days Present <span style="font-size: 11px; color: #999;">(incl. late)</span></td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $payslip->days_present ?? 0 }} days</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666; padding-left: 15px;">└ Days Late</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #856404;">{{ $payslip->days_late ?? 0 }} days</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Days Absent</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $payslip->days_absent ?? 0 }} days</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666;">Total Hours Worked</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ number_format($payslip->hours_worked, 2) }} hrs</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Loan Information -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fas fa-hand-holding-dollar"></i> Active Loans
                            </h4>
                            @if($activeLoans->count() > 0)
                                <table style="width: 100%; font-size: 13px;">
                                    @foreach($activeLoans as $loan)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 8px 0; color: #666;">{{ $loop->iteration }}. {{ Str::limit($loan->purpose, 20) }}</td>
                                        <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    <tr style="border-top: 2px solid #ddd;">
                                        <td style="padding: 8px 0; color: #333; font-weight: 700;">Total Balance</td>
                                        <td style="padding: 8px 0; text-align: right; font-weight: 700; color: #dc3545;">₱{{ number_format($totalLoanBalance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #666;">Monthly Deduction</td>
                                        <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #dc3545;">₱{{ number_format($totalMonthlyDeduction, 2) }}</td>
                                    </tr>
                                </table>
                            @else
                                <p style="color: #999; font-size: 13px; padding: 10px 0;">No active loans</p>
                            @endif
                        </div>

                        <!-- Earnings & Deductions -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fas fa-money-bill-wave"></i> Earnings & Deductions
                            </h4>
                            <table style="width: 100%; font-size: 13px;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Basic Pay</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->basic_salary, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Overtime ({{ number_format($payslip->overtime_hours, 1) }} hrs)</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Allowances</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->allowances, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Bonuses</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->bonuses, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 2px solid #ddd;">
                                    <td style="padding: 8px 0; color: #333; font-weight: 700;">Gross Pay</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 700;">₱{{ number_format($payslip->gross_pay, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">SSS ({{ number_format($payslip->sss_rate ?? 4.5, 1) }}%)</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->sss, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">PhilHealth ({{ number_format($payslip->philhealth_rate ?? 2.0, 1) }}%)</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->philhealth, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Pag-IBIG ({{ number_format($payslip->pagibig_rate ?? 2.0, 1) }}%)</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->pagibig, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Tax ({{ number_format($payslip->tax_rate ?? 0, 1) }}%)</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->tax, 2) }}</td>
                                </tr>
                                @if($payslip->late_deduction > 0)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Late Deduction</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #dc3545;">₱{{ number_format($payslip->late_deduction, 2) }}</td>
                                </tr>
                                @endif
                                @if($payslip->loan_deductions > 0)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Loan Deduction</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
                                </tr>
                                @endif
                                <tr style="border-bottom: 2px solid #ddd;">
                                    <td style="padding: 8px 0; color: #333; font-weight: 700;">Total Deductions</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 700; color: #dc3545;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0 0; color: #333; font-weight: 700; font-size: 15px;">Net Pay</td>
                                    <td style="padding: 10px 0 0 0; text-align: right; font-weight: 700; font-size: 15px; color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Daily Attendance Details -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                        <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                            <i class="fas fa-calendar-alt"></i> Daily Attendance Details
                        </h4>
                        @if(count($allDates) > 0)
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <thead>
                                        <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                            <th style="padding: 10px; text-align: left; font-weight: 600;">Date</th>
                                            <th style="padding: 10px; text-align: left; font-weight: 600;">Day</th>
                                            <th style="padding: 10px; text-align: center; font-weight: 600;">Status</th>
                                            <th style="padding: 10px; text-align: center; font-weight: 600;">Time In</th>
                                            <th style="padding: 10px; text-align: center; font-weight: 600;">Time Out</th>
                                            <th style="padding: 10px; text-align: right; font-weight: 600;">Hours</th>
                                            <th style="padding: 10px; text-align: right; font-weight: 600;">Overtime</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allDates as $record)
                                            <tr style="border-bottom: 1px solid #eee;">
                                                <td style="padding: 10px;">{{ date('M d, Y', strtotime($record->date)) }}</td>
                                                <td style="padding: 10px; color: #666;">{{ date('l', strtotime($record->date)) }}</td>
                                                <td style="padding: 10px; text-align: center;">
                                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 600;
                                                        @if($record->status == 'present') background: #d4edda; color: #155724;
                                                        @elseif($record->status == 'absent') background: #f8d7da; color: #721c24;
                                                        @elseif($record->status == 'late') background: #fff3cd; color: #856404;
                                                        @elseif($record->status == 'on-leave') background: #e2e3e5; color: #383d41;
                                                        @else background: #e2e3e5; color: #383d41; @endif">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                                <td style="padding: 10px; text-align: center;">
                                                    {{ $record->time_in ? date('h:i A', strtotime($record->time_in)) : '--' }}
                                                </td>
                                                <td style="padding: 10px; text-align: center;">
                                                    {{ $record->time_out ? date('h:i A', strtotime($record->time_out)) : '--' }}
                                                </td>
                                                <td style="padding: 10px; text-align: right; font-weight: 600;">
                                                    {{ $record->hours_worked > 0 ? number_format($record->hours_worked, 1) : '0.0' }}
                                                </td>
                                                <td style="padding: 10px; text-align: right; font-weight: 600;">
                                                    {{ isset($record->overtime_hours) && $record->overtime_hours > 0 ? number_format($record->overtime_hours, 1) : '0.0' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p style="color: #999; text-align: center; padding: 20px;">No working days in this period</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
