<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - ACCUPAY INC.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            display: flex;
            background: #f5f7fa;
            color: #2d3748;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #0057a0;
            color: white;
            padding: 20px 0;
            position: fixed;
            left: 0;
            transition: width 0.3s;
            overflow: hidden;
        }

        .sidebar-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 18px;
            font-weight: 600;
            margin: 0 20px 30px;
            cursor: pointer;
            color: #fff;
            padding: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .sidebar ul li a:hover {
            background: #008f5a;
        }

        .sidebar ul li.active a {
            background: #003f70;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .logo-text {
            display: none;
        }

        /* NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 70px;
            padding: 0 30px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            transition: left 0.3s;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-logo {
            height: 40px;
        }

        .navbar h1 {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
        }

        .logout-btn {
            background: #e53e3e;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #c53030;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 30px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s, width 0.3s;
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 30px;
            color: #2d3748;
        }

        .loan-summary-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            padding: 25px;
            border-left: 4px solid #e53e3e;
        }

        .loan-summary-title {
            margin: 0 0 20px 0;
            color: #2d3748;
            font-size: 18px;
        }

        .loan-summary-title i {
            color: #e53e3e;
            margin-right: 8px;
        }

        .loan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .loan-item {
            background: #f7fafc;
            padding: 18px;
            border-radius: 8px;
            border-left: 3px solid #e53e3e;
        }

        .loan-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-left: none;
        }

        .payslip-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .payslip-header {
            padding: 20px 25px;
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: background 0.2s;
        }

        .payslip-header:hover {
            background: #edf2f7;
        }

        .payslip-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .download-btn {
            padding: 8px 16px;
            background: #0057a0;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .download-btn:hover {
            background: #003f70;
        }

        .download-btn i {
            margin-right: 6px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payslip-details {
            padding: 25px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .detail-section h4 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 16px;
            border-bottom: 2px solid #0057a0;
            padding-bottom: 10px;
        }

        .detail-section h4 i {
            color: #0057a0;
            margin-right: 8px;
        }

        .detail-table {
            width: 100%;
            font-size: 14px;
        }

        .detail-table tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .detail-table td {
            padding: 10px 0;
        }

        .detail-table td:first-child {
            color: #718096;
        }

        .detail-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
        }

        .attendance-table thead {
            background: #f7fafc;
        }

        .attendance-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
        }

        .attendance-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .attendance-table tbody tr:hover {
            background: #f7fafc;
        }

        .attendance-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .attendance-badge.present {
            background: #d4edda;
            color: #155724;
        }

        .attendance-badge.absent {
            background: #f8d7da;
            color: #721c24;
        }

        .attendance-badge.late {
            background: #fff3cd;
            color: #856404;
        }

        .attendance-badge.on-leave {
            background: #e2e3e5;
            color: #383d41;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 48px;
            opacity: 0.3;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>

        <ul>
            <li><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('employee.qr.page') }}"><i class="fa-solid fa-qrcode"></i> <span class="menu-text">QR Code</span></a></li>
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
        <div style="max-width: 1400px; margin: 0 auto;">
            <!-- Loan Summary Card -->
            @if($activeLoans->count() > 0)
            <div class="loan-summary-card">
                <h3 class="loan-summary-title">
                    <i class="fas fa-hand-holding-dollar"></i> Active Loan Summary
                </h3>
                <div class="loan-grid">
                    @foreach($activeLoans as $loan)
                    <div class="loan-item">
                        <div style="font-size: 12px; color: #718096; margin-bottom: 5px;">{{ $loan->purpose }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 13px; color: #718096;">Balance:</span>
                            <span style="font-weight: 700; font-size: 15px; color: #e53e3e;">₱{{ number_format($loan->remaining_balance, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 12px; color: #718096;">Monthly Deduction:</span>
                            <span style="font-weight: 600; font-size: 13px;">₱{{ number_format($loan->monthly_deduction, 2) }}</span>
                        </div>
                        <div style="background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden;">
                            <div style="background: #00a86b; height: 100%; width: {{ $loan->progress_percentage }}%;"></div>
                        </div>
                        <div style="font-size: 11px; color: #718096; margin-top: 5px; text-align: right;">{{ number_format($loan->progress_percentage, 1) }}% paid</div>
                    </div>
                    @endforeach
                    <div class="loan-item loan-total">
                        <div style="font-size: 12px; margin-bottom: 8px; opacity: 0.9;">Total Outstanding Balance</div>
                        <div style="font-weight: 700; font-size: 20px; margin-bottom: 10px;">₱{{ number_format($totalLoanBalance, 2) }}</div>
                        <div style="font-size: 12px; opacity: 0.9;">Monthly Deduction: <strong>₱{{ number_format($totalMonthlyDeduction, 2) }}</strong></div>
                    </div>
                </div>
            </div>
            @endif

            <h2 class="page-title">Payslip Summary</h2>
            
            @forelse($payslips as $payslip)
            <div class="payslip-card">
                <!-- Payslip Header -->
                <div class="payslip-header" onclick="toggleDetails('payslip-{{ $payslip->id }}')">
                    <div class="payslip-summary">
                        <div style="flex: 1; min-width: 200px;">
                            <div style="font-weight: 600; font-size: 15px; color: #2d3748; margin-bottom: 5px;">
                                {{ $payslip->payroll->payroll_period ?? 'N/A' }}
                            </div>
                            <div style="font-size: 12px; color: #718096;">
                                Days Worked: {{ number_format($payslip->hours_worked / 8, 1) }} | 
                                Overtime: {{ number_format($payslip->overtime_hours, 1) }} hrs
                            </div>
                        </div>
                        <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #718096; margin-bottom: 3px;">Gross Pay</div>
                                <div style="font-weight: 600; font-size: 14px; color: #2d3748;">₱{{ number_format($payslip->gross_pay, 2) }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #718096; margin-bottom: 3px;">Deductions</div>
                                <div style="font-weight: 600; font-size: 14px; color: #e53e3e;">₱{{ number_format($payslip->total_deductions, 2) }}</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; color: #718096; margin-bottom: 3px;">Net Pay</div>
                                <div style="font-weight: 700; font-size: 16px; color: #00a86b;">₱{{ number_format($payslip->net_pay, 2) }}</div>
                            </div>
                            <div>
                                <span class="status-badge @if(($payslip->payroll->status ?? 'pending') == 'approved') approved @else pending @endif">
                                    {{ ucfirst($payslip->payroll->status ?? 'pending') }}
                                </span>
                            </div>
                            <button onclick="event.stopPropagation();" class="download-btn">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payslip Details -->
                <div id="payslip-{{ $payslip->id }}" class="payslip-details" style="display: none; border-top: 1px solid #e2e8f0;">
                    <div class="details-grid">
                        <!-- Attendance Summary -->
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-calendar-check"></i> Attendance Summary
                            </h4>
                            <table class="detail-table">
                                <tr>
                                    <td>Days Present (incl. late)</td>
                                    <td>{{ $payslip->days_present ?? 0 }} days</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;">└ Days Late</td>
                                    <td style="color: #d68910;">{{ $payslip->days_late ?? 0 }} days</td>
                                </tr>
                                <tr>
                                    <td>Days Absent</td>
                                    <td>{{ $payslip->days_absent ?? 0 }} days</td>
                                </tr>
                                <tr>
                                    <td>Total Hours Worked</td>
                                    <td>{{ number_format($payslip->hours_worked, 2) }} hrs</td>
                                </tr>
                                @if($payslip->undertime_hours > 0)
                                <tr>
                                    <td style="padding-left: 15px;">└ Undertime Hours</td>
                                    <td style="color: #e53e3e;">{{ number_format($payslip->undertime_hours, 2) }} hrs</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <!-- Loan Information -->
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-hand-holding-dollar"></i> Loan Deductions
                            </h4>
                            @if($payslip->loan_deductions > 0)
                                <table class="detail-table">
                                    <tr>
                                        <td>This Period</td>
                                        <td style="color: #e53e3e;">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
                                    </tr>
                                    @if($activeLoans->count() > 0)
                                    <tr>
                                        <td>Remaining Balance</td>
                                        <td>₱{{ number_format($totalLoanBalance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Active Loans</td>
                                        <td>{{ $activeLoans->count() }}</td>
                                    </tr>
                                    @endif
                                </table>
                            @else
                                <p style="color: #a0aec0; font-size: 13px; padding: 10px 0;">No loan deductions this period</p>
                            @endif
                        </div>

                        <!-- Earnings & Deductions -->
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-money-bill-wave"></i> Earnings & Deductions
                            </h4>
                            <table class="detail-table">
                                <tr>
                                    <td>Basic Pay</td>
                                    <td>₱{{ number_format($payslip->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Overtime ({{ number_format($payslip->overtime_hours, 1) }} hrs)</td>
                                    <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowances</td>
                                    <td>₱{{ number_format($payslip->allowances, 2) }}</td>
                                </tr>
                                </tr>
                                <tr>
                                    <td>Bonuses</td>
                                    <td>₱{{ number_format($payslip->bonuses, 2) }}</td>
                                </tr>
                                <tr style="border-top: 2px solid #e2e8f0;">
                                    <td style="color: #2d3748; font-weight: 700;">Gross Pay</td>
                                    <td style="font-weight: 700;">₱{{ number_format($payslip->gross_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>SSS ({{ number_format($payslip->sss_rate ?? 4.5, 1) }}%)</td>
                                    <td>₱{{ number_format($payslip->sss, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>PhilHealth ({{ number_format($payslip->philhealth_rate ?? 2.0, 1) }}%)</td>
                                    <td>₱{{ number_format($payslip->philhealth, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Pag-IBIG ({{ number_format($payslip->pagibig_rate ?? 2.0, 1) }}%)</td>
                                    <td>₱{{ number_format($payslip->pagibig, 2) }}</td>
                                </tr>
                                @if(isset($payslip->tax) && $payslip->tax > 0)
                                <tr>
                                    <td>Tax ({{ number_format($payslip->tax_rate ?? 0, 1) }}%)</td>
                                    <td>₱{{ number_format($payslip->tax, 2) }}</td>
                                </tr>
                                @endif
                                @if(isset($payslip->absence_deduction) && $payslip->absence_deduction > 0)
                                <tr>
                                    <td>Absence Deduction ({{ $payslip->days_absent ?? 0 }} days)</td>
                                    <td style="color: #e53e3e;">₱{{ number_format($payslip->absence_deduction, 2) }}</td>
                                </tr>
                                @endif
                                @if(isset($payslip->late_deduction) && $payslip->late_deduction > 0)
                                <tr>
                                    <td>Late Deduction</td>
                                    <td style="color: #e53e3e;">₱{{ number_format($payslip->late_deduction, 2) }}</td>
                                </tr>
                                @endif
                                @if(isset($payslip->undertime_deduction) && $payslip->undertime_deduction > 0)
                                <tr>
                                    <td>Undertime Deduction ({{ number_format($payslip->undertime_hours, 2) }} hrs)</td>
                                    <td style="color: #e53e3e;">₱{{ number_format($payslip->undertime_deduction, 2) }}</td>
                                </tr>
                                @endif
                                @if(isset($payslip->loan_deductions) && $payslip->loan_deductions > 0)
                                <tr>
                                    <td>Loan Deduction</td>
                                    <td style="color: #e53e3e;">₱{{ number_format($payslip->loan_deductions, 2) }}</td>
                                </tr>
                                @endif
                                <tr style="border-top: 2px solid #e2e8f0;">
                                    <td style="color: #2d3748; font-weight: 700;">Total Deductions</td>
                                    <td style="font-weight: 700; color: #e53e3e;">₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0 0 0; color: #2d3748; font-weight: 700; font-size: 15px;">Net Pay</td>
                                    <td style="padding: 10px 0 0 0; font-weight: 700; font-size: 15px; color: #00a86b;">₱{{ number_format($payslip->net_pay, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Daily Attendance Details -->
                    <div style="margin-top: 25px; padding-top: 25px; border-top: 2px solid #e2e8f0;">
                        <h4 style="margin: 0 0 15px 0; color: #2d3748; font-size: 16px; border-bottom: 2px solid #0057a0; padding-bottom: 10px;">
                            <i class="fas fa-calendar-alt"></i> Daily Attendance Details
                        </h4>
                        @if(isset($attendanceByPayslip[$payslip->id]) && count($attendanceByPayslip[$payslip->id]) > 0)
                            <div style="overflow-x: auto;">
                                <table class="attendance-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th style="text-align: center;">Status</th>
                                            <th style="text-align: center;">Time In</th>
                                            <th style="text-align: center;">Time Out</th>
                                            <th style="text-align: right;">Hours</th>
                                            <th style="text-align: right;">Overtime</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendanceByPayslip[$payslip->id] as $record)
                                            <tr>
                                                <td>{{ date('M d, Y', strtotime($record->date)) }}</td>
                                                <td style="color: #718096;">{{ date('l', strtotime($record->date)) }}</td>
                                                <td style="text-align: center;">
                                                    <span class="attendance-badge {{ $record->status }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $record->time_in ? date('h:i A', strtotime($record->time_in)) : '--' }}
                                                </td>
                                                <td style="text-align: center;">
                                                    {{ $record->time_out ? date('h:i A', strtotime($record->time_out)) : '--' }}
                                                </td>
                                                <td style="text-align: right; font-weight: 600;">
                                                    {{ $record->hours_worked > 0 ? number_format($record->hours_worked, 1) : '0.0' }}
                                                </td>
                                                <td style="text-align: right; font-weight: 600;">
                                                    {{ isset($record->overtime_hours) && $record->overtime_hours > 0 ? number_format($record->overtime_hours, 1) : '0.0' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p style="color: #a0aec0; text-align: center; padding: 20px;">No working days in this period</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar"></i>
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
