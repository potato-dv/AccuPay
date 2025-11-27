<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - ACCUPAY INC.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/payslip.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>

        <ul>
            <li><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('employee.profile') }}"><i class="fa-solid fa-user"></i> <span class="menu-text">Profile</span></a></li>
            <li><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
            <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
            <li class="active"><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
            <li><a href="{{ route('employee.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Payslip</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
            <h2 style="margin-bottom: 20px; color: #333;">Payslip Summary</h2>
            
            @forelse($payslips as $payslip)
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; overflow: hidden;">
                <!-- Payslip Header -->
                <div style="padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #ddd; cursor: pointer;" onclick="toggleDetails('payslip-{{ $payslip->id }}')">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                        <div style="flex: 1; min-width: 200px;">
                            <div style="font-weight: 600; font-size: 15px; color: #333; margin-bottom: 5px;">
                                {{ $payslip->payroll->payroll_period ?? 'N/A' }}
                            </div>
                            <div style="font-size: 12px; color: #666;">
                                Days Worked: {{ number_format($payslip->hours_worked / 8, 1) }} | 
                                Overtime: {{ number_format($payslip->overtime_hours, 1) }} hrs
                            </div>
                        </div>
                        <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #666; margin-bottom: 3px;">Gross Pay</div>
                                <div style="font-weight: 600; font-size: 14px; color: #333;">₱{{ number_format($payslip->gross_pay, 2) }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #666; margin-bottom: 3px;">Deductions</div>
                                <div style="font-weight: 600; font-size: 14px; color: #dc3545;">₱{{ number_format($payslip->total_deductions, 2) }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #666; margin-bottom: 3px;">Net Pay</div>
                                <div style="font-weight: 700; font-size: 16px; color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</div>
                            </div>
                            <div>
                                <span style="display: inline-block; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                    @if(($payslip->payroll->status ?? 'pending') == 'approved') background: #d4edda; color: #155724;
                                    @else background: #fff3cd; color: #856404; @endif">
                                    {{ ucfirst($payslip->payroll->status ?? 'pending') }}
                                </span>
                            </div>
                            <button onclick="event.stopPropagation();" style="padding: 6px 15px; background: #0057a0; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payslip Details -->
                <div id="payslip-{{ $payslip->id }}" style="display: none; padding: 20px; border-top: 1px solid #ddd;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <!-- Attendance Summary -->
                        <div>
                            <h4 style="margin: 0 0 15px 0; color: #333; font-size: 14px; border-bottom: 2px solid #0057a0; padding-bottom: 8px;">
                                <i class="fas fa-calendar-check"></i> Attendance Summary
                            </h4>
                            <table style="width: 100%; font-size: 13px;">
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">Days Present (incl. late)</td>
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
                                    <td style="padding: 8px 0; color: #666;">SSS</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->sss, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 8px 0; color: #666;">PhilHealth</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->philhealth, 2) }}</td>
                                </tr>
                                <tr style="border-bottom: 2px solid #ddd;">
                                    <td style="padding: 8px 0; color: #666;">Pag-IBIG</td>
                                    <td style="padding: 8px 0; text-align: right; font-weight: 600;">₱{{ number_format($payslip->pagibig, 2) }}</td>
                                </tr>
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
                        @if(isset($attendanceByPayslip[$payslip->id]) && count($attendanceByPayslip[$payslip->id]) > 0)
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
                                        @foreach($attendanceByPayslip[$payslip->id] as $record)
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
            @empty
            <div style="text-align: center; padding: 60px 20px; color: #999;">
                <i class="fas fa-file-invoice-dollar" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px;"></i>
                <p style="font-size: 16px;">No payslips found.</p>
            </div>
            @endforelse
        </div>
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

        function toggleDetails(id) {
            const element = document.getElementById(id);
            if (element.style.display === 'none' || element.style.display === '') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }
    </script>
</body>
</html>
