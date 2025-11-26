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
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-file-contract"></i> <span class="menu-text">Reports</span></a></li>
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

        <section class="content-card">

            <!-- Employee Information -->
            <div style="background: linear-gradient(135deg, #0057a0 0%, #0077cc 100%); padding: 25px; border-radius: 12px; margin-bottom: 30px; color: white;">
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px;">
                    <i class="fa-solid fa-user"></i> Employee Information
                </h3>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    <div>
                        <div style="opacity: 0.85; font-size: 13px; margin-bottom: 5px;">Employee ID</div>
                        <div style="font-size: 16px; font-weight: bold;">{{ $payslip->employee->employee_id }}</div>
                    </div>
                    <div>
                        <div style="opacity: 0.85; font-size: 13px; margin-bottom: 5px;">Full Name</div>
                        <div style="font-size: 16px; font-weight: bold;">{{ $payslip->employee->full_name }}</div>
                    </div>
                    <div>
                        <div style="opacity: 0.85; font-size: 13px; margin-bottom: 5px;">Position</div>
                        <div style="font-size: 16px; font-weight: bold;">{{ $payslip->employee->position ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="opacity: 0.85; font-size: 13px; margin-bottom: 5px;">Department</div>
                        <div style="font-size: 16px; font-weight: bold;">{{ $payslip->employee->department ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Payroll Period -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #0057a0;">
                <h4 style="margin-top: 0; margin-bottom: 15px; color: #0057a0;">
                    <i class="fa-solid fa-calendar-alt"></i> Payroll Period
                </h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div>
                        <strong>Period:</strong> {{ $payslip->payroll->payroll_period }}
                    </div>
                    <div>
                        <strong>Start Date:</strong> {{ date('F d, Y', strtotime($payslip->payroll->period_start)) }}
                    </div>
                    <div>
                        <strong>End Date:</strong> {{ date('F d, Y', strtotime($payslip->payroll->period_end)) }}
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-bottom: 30px;">
                <!-- Earnings Section -->
                <div>
                    <h4 style="color: #0057a0; margin-top: 0; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #0057a0;">
                        <i class="fa-solid fa-money-bill-wave"></i> Earnings
                    </h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Hours Worked:</td>
                            <td style="padding: 12px 0; text-align: right;">{{ number_format($payslip->hours_worked, 2) }} hrs</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Overtime Hours:</td>
                            <td style="padding: 12px 0; text-align: right;">{{ number_format($payslip->overtime_hours, 2) }} hrs</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Basic Salary:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->basic_salary, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Overtime Pay:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Allowances:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->allowances, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Bonuses:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->bonuses, 2) }}</td>
                        </tr>
                        <tr style="background: #e8f5e9;">
                            <td style="padding: 15px; font-weight: bold; color: #28a745; font-size: 16px;">GROSS PAY:</td>
                            <td style="padding: 15px; text-align: right; font-weight: bold; color: #28a745; font-size: 20px;">₱{{ number_format($payslip->gross_pay, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Deductions Section -->
                <div>
                    <h4 style="color: #0057a0; margin-top: 0; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #0057a0;">
                        <i class="fa-solid fa-minus-circle"></i> Deductions
                    </h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">SSS:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->sss, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">PhilHealth:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->philhealth, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Pag-IBIG:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->pagibig, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Withholding Tax:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->tax, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e0e0e0;">
                            <td style="padding: 12px 0; font-weight: 600;">Other Deductions:</td>
                            <td style="padding: 12px 0; text-align: right;">₱{{ number_format($payslip->other_deductions, 2) }}</td>
                        </tr>
                        <tr style="background: #ffebee;">
                            <td style="padding: 15px; font-weight: bold; color: #e74c3c; font-size: 16px;">TOTAL DEDUCTIONS:</td>
                            <td style="padding: 15px; text-align: right; font-weight: bold; color: #e74c3c; font-size: 20px;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Net Pay Summary -->
            <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);">
                <div style="color: white; font-size: 16px; margin-bottom: 8px; opacity: 0.95;">
                    <i class="fa-solid fa-hand-holding-dollar"></i> NET PAY
                </div>
                <div style="color: white; font-size: 42px; font-weight: bold; letter-spacing: 1px;">
                    ₱{{ number_format($payslip->net_pay, 2) }}
                </div>
                <div style="color: white; font-size: 13px; margin-top: 8px; opacity: 0.9;">
                    Gross Pay (₱{{ number_format($payslip->gross_pay, 2) }}) - Total Deductions (₱{{ number_format($payslip->total_deductions, 2) }})
                </div>
            </div>
        </section>
    </main>
</body>
</html>
