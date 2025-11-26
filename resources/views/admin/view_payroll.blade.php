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
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
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

        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2 style="margin-top: 0; color: #0057a0;">{{ $payroll->payroll_period }}</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <strong>Period:</strong><br>
                    {{ date('M d, Y', strtotime($payroll->period_start)) }} - {{ date('M d, Y', strtotime($payroll->period_end)) }}
                </div>
                <div>
                    <strong>Payment Date:</strong><br>
                    {{ date('M d, Y', strtotime($payroll->payment_date)) }}
                </div>
                <div>
                    <strong>Total Employees:</strong><br>
                    {{ $payroll->total_employees }}
                </div>
                <div>
                    <strong>Total Net Pay:</strong><br>
                    ₱{{ number_format($payroll->total_amount, 2) }}
                </div>
                <div>
                    <strong>Status:</strong><br>
                    <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 500;
                        @if($payroll->status == 'approved') background: #d4edda; color: #155724;
                        @elseif($payroll->status == 'pending') background: #fff3cd; color: #856404;
                        @else background: #e2e3e5; color: #383d41; @endif">
                        {{ ucfirst($payroll->status) }}
                    </span>
                </div>
            </div>
            @if($payroll->notes)
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                    <strong>Notes:</strong><br>
                    {{ $payroll->notes }}
                </div>
            @endif
        </div>

        <!-- Payroll Summary Totals -->
        @php
            $totalGrossPay = $payroll->payslips->sum('gross_pay');
            $totalDeductions = $payroll->payslips->sum('total_deductions');
            $totalNetPay = $payroll->payslips->sum('net_pay');
        @endphp
        <div style="background: linear-gradient(135deg, #0057a0 0%, #0080d0 100%); padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h3 style="margin: 0 0 20px 0; color: white; font-size: 20px;">
                <i class="fa-solid fa-calculator"></i> Payroll Summary
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                    <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Gross Pay</div>
                    <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalGrossPay, 2) }}</div>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                    <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Deductions</div>
                    <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalDeductions, 2) }}</div>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                    <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Net Pay</div>
                    <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalNetPay, 2) }}</div>
                </div>
            </div>
        </div>

        <h3>Employee Payslips</h3>
        <section class="employee-list">
            <table>
                <thead>
                <thead>
                    <tr style="background: #0057a0; color: white;">
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Hours</th>
                        <th>Overtime</th>
                        <th>Basic Salary</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payroll->payslips as $payslip)
                    <tr>
                        <td><strong>{{ $payslip->employee->employee_id }}</strong></td>
                        <td>{{ $payslip->employee->full_name }}</td>
                        <td>{{ $payslip->hours_worked }} hrs</td>
                        <td>{{ $payslip->overtime_hours }} hrs</td>
                        <td>₱{{ number_format($payslip->basic_salary, 2) }}</td>
                        <td><strong>₱{{ number_format($payslip->gross_pay, 2) }}</strong></td>
                        <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        <td><strong style="color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</strong></td>
                        <td style="text-align: center;">
                            <a href="{{ route('admin.viewEmployeePayslip', $payslip->id) }}" class="btn-sm" style="background: #17a2b8; color: white; border: none; cursor: pointer; text-decoration: none;">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
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
