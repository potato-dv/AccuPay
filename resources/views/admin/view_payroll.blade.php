<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payroll - {{ $payroll->payroll_period }}</title>
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
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Payroll Details</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <main class="main-content">
        <div style="max-width: 1400px; margin: 0 auto;">
            <div style="margin-bottom: 20px;">
                <a href="{{ route('admin.payroll') }}" class="btn-delete" style="text-decoration: none; display: inline-block;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Payroll
                </a>
                @if($payroll->status == 'pending')
                    <a href="{{ route('admin.payroll.edit', $payroll->id) }}" class="btn-theme" style="text-decoration: none; display: inline-block; margin-left: 10px;">
                        <i class="fa-solid fa-edit"></i> Edit Payroll
                    </a>
                    <form action="{{ route('admin.payroll.approve', $payroll->id) }}" method="POST" style="display: inline; margin-left: 10px;" onsubmit="return confirm('Approve this payroll? Employees will be able to view it.');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-theme" style="background: #28a745;">
                            <i class="fa-solid fa-check"></i> Approve Payroll
                        </button>
                    </form>
                @endif
            </div>

            <!-- Payroll Header -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; overflow: hidden;">
                <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
                    <h2 style="margin: 0 0 15px 0; color: #333; font-size: 20px;">{{ $payroll->payroll_period }}</h2>
                    <div style="display: flex; gap: 30px; flex-wrap: wrap; font-size: 13px; color: #666;">
                        <div><strong>Period:</strong> {{ date('M d', strtotime($payroll->period_start)) }} - {{ date('M d, Y', strtotime($payroll->period_end)) }}</div>
                        <div><strong>Payment Date:</strong> {{ date('M d, Y', strtotime($payroll->payment_date)) }}</div>
                        <div><strong>Total Employees:</strong> {{ $payroll->total_employees }}</div>
                        <div><strong>Status:</strong> 
                            <span style="padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                @if($payroll->status == 'approved') background: #d4edda; color: #155724;
                                @elseif($payroll->status == 'pending') background: #fff3cd; color: #856404;
                                @else background: #e2e3e5; color: #383d41; @endif">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </div>
                    </div>
                    @if($payroll->notes)
                        <div style="margin-top: 15px; padding: 12px; background: white; border-radius: 4px; font-size: 13px; color: #666;">
                            <strong>Notes:</strong> {{ $payroll->notes }}
                        </div>
                    @endif
                </div>

                <!-- Summary Section -->
                @php
                    $totalGrossPay = $payroll->payslips->sum('gross_pay');
                    $totalDeductions = $payroll->payslips->sum('total_deductions');
                    $totalNetPay = $payroll->payslips->sum('net_pay');
                    $totalPresent = $payroll->payslips->sum('days_present');
                    $totalAbsent = $payroll->payslips->sum('days_absent');
                    $totalLate = $payroll->payslips->sum('days_late');
                    $totalHours = $payroll->payslips->sum('hours_worked');
                    $totalOvertimeHours = $payroll->payslips->sum('overtime_hours');
                @endphp

                <div style="padding: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <!-- Financial Summary -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fa-solid fa-calculator"></i> Financial Summary
                            </h4>
                            <table style="width: 100%; font-size: 13px;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Total Gross Pay</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($totalGrossPay, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Total Deductions</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #dc3545;">₱{{ number_format($totalDeductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0 0; color: #333; font-weight: 700; font-size: 15px;">Total Net Pay</td>
                                    <td style="padding: 10px 0 0 0; text-align: right; font-weight: 700; font-size: 15px; color: #28a745;">₱{{ number_format($totalNetPay, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Attendance Summary -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fa-solid fa-calendar-check"></i> Attendance Summary
                            </h4>
                            <table style="width: 100%; font-size: 13px;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Days Present <span style="font-size: 11px; color: #999;">(incl. late)</span></td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $totalPresent }} days</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666; padding-left: 15px;">└ Days Late</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600; color: #856404;">{{ $totalLate }} days</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Days Absent</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ $totalAbsent }} days</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Total Hours Worked</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ number_format($totalHours, 1) }} hrs</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #666;">Overtime Hours</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">{{ number_format($totalOvertimeHours, 1) }} hrs</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Employee Payslips Table -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                        <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                            <i class="fa-solid fa-file-invoice"></i> Employee Payslips ({{ $payroll->total_employees }})
                        </h4>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                        <th style="padding: 10px; text-align: left; font-weight: 600;">Employee ID</th>
                                        <th style="padding: 10px; text-align: left; font-weight: 600;">Name</th>
                                        <th style="padding: 10px; text-align: center; font-weight: 600;">P/A/L</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">Hours</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">OT</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">Basic Pay</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">Gross Pay</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">Deductions</th>
                                        <th style="padding: 10px; text-align: right; font-weight: 600;">Net Pay</th>
                                        <th style="padding: 10px; text-align: center; font-weight: 600;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payroll->payslips as $payslip)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 10px; color: #0057a0; font-weight: 600;">{{ $payslip->employee->employee_id }}</td>
                                        <td style="padding: 10px;">{{ $payslip->employee->full_name }}</td>
                                        <td style="padding: 10px; text-align: center;">
                                            <div style="display: flex; gap: 5px; justify-content: center;">
                                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #d4edda; color: #155724; font-size: 11px; font-weight: 600;">
                                                    {{ $payslip->days_present ?? 0 }}
                                                </span>
                                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #f8d7da; color: #721c24; font-size: 11px; font-weight: 600;">
                                                    {{ $payslip->days_absent ?? 0 }}
                                                </span>
                                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; background: #fff3cd; color: #856404; font-size: 11px; font-weight: 600;">
                                                    {{ $payslip->days_late ?? 0 }}
                                                </span>
                                            </div>
                                        </td>
                                        <td style="padding: 10px; text-align: right;">{{ number_format($payslip->hours_worked, 1) }}</td>
                                        <td style="padding: 10px; text-align: right;">{{ number_format($payslip->overtime_hours, 1) }}</td>
                                        <td style="padding: 10px; text-align: right;">₱{{ number_format($payslip->basic_salary, 2) }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 600;">₱{{ number_format($payslip->gross_pay, 2) }}</td>
                                        <td style="padding: 10px; text-align: right; color: #dc3545;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                        <td style="padding: 10px; text-align: right; font-weight: 700; color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</td>
                                        <td style="padding: 10px; text-align: center;">
                                            <a href="{{ route('admin.viewEmployeePayslip', $payslip->id) }}" style="padding: 5px 12px; background: #0057a0; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; display: inline-block;">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
