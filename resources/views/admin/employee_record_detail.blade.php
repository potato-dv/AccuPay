<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Record - {{ $employee->full_name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        .employee-record-container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .record-header {
            background: linear-gradient(135deg, #0057a0 0%, #003d73 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .record-header h2 {
            margin: 0;
            font-size: 28px;
        }
        .record-header .employee-id {
            font-size: 16px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        .summary-card.blue { border-left-color: #2196F3; }
        .summary-card.green { border-left-color: #4CAF50; }
        .summary-card.orange { border-left-color: #FF9800; }
        .summary-card.purple { border-left-color: #9C27B0; }
        .summary-card h4 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            font-weight: 600;
        }
        .summary-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .tabs {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }
        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 15px;
            font-weight: 600;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        .tab.active {
            color: #0057a0;
            border-bottom-color: #0057a0;
        }
        .tab:hover {
            background: #f5f5f5;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .detail-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #0057a0;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .detail-value {
            font-size: 15px;
            color: #333;
        }
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .payroll-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        .payroll-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .payroll-table tr:hover {
            background: #f9f9f9;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .year-group {
            margin-bottom: 30px;
        }
        .year-group h4 {
            color: #0057a0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
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
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li class="active"><a href="{{ route('admin.employee.records') }}"><i class="fa-solid fa-folder-open"></i> <span class="menu-text">Employee Records</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Employee Record</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div style="margin-bottom: 20px;">
            <a href="{{ route('admin.employee.records') }}" class="btn-delete" style="text-decoration: none; display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to Employee Records
            </a>
        </div>

        <!-- EMPLOYEE HEADER -->
        <div class="record-header">
            <div>
                <h2>{{ $employee->full_name }}</h2>
                <div class="employee-id">Employee ID: {{ $employee->employee_id }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge {{ $employee->status }}">{{ ucfirst($employee->status) }}</span>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 14px; opacity: 0.9;">{{ $employee->department }}</div>
                <div style="font-size: 18px; font-weight: 600; margin-top: 5px;">{{ $employee->position }}</div>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="summary-cards">
            <div class="summary-card blue">
                <h4><i class="fa-solid fa-money-bill-wave"></i> Total Earnings</h4>
                <div class="value">₱{{ number_format($totalEarnings, 2) }}</div>
            </div>
            <div class="summary-card green">
                <h4><i class="fa-solid fa-calendar-check"></i> Active Leave Credits</h4>
                <div class="value">{{ $employee->vacation_leave_credits + $employee->sick_leave_credits }}</div>
            </div>
            <div class="summary-card orange">
                <h4><i class="fa-solid fa-hand-holding-dollar"></i> Active Loans</h4>
                <div class="value">₱{{ number_format($remainingLoans, 2) }}</div>
            </div>
            <div class="summary-card purple">
                <h4><i class="fa-solid fa-folder"></i> Record Snapshots</h4>
                <div class="value">{{ $employee->employeeRecords->count() }}</div>
            </div>
        </div>

        <!-- TABS -->
        <div class="employee-record-container">
            <div class="tabs">
                <button class="tab active" onclick="showTab('personal')">
                    <i class="fa-solid fa-user"></i> Personal Information
                </button>
                <button class="tab" onclick="showTab('employment')">
                    <i class="fa-solid fa-briefcase"></i> Employment Details
                </button>
                <button class="tab" onclick="showTab('compensation')">
                    <i class="fa-solid fa-dollar-sign"></i> Compensation
                </button>
                <button class="tab" onclick="showTab('payroll')">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Payroll History
                </button>
                <button class="tab" onclick="showTab('loans')">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Loans
                </button>
                <button class="tab" onclick="showTab('leave')">
                    <i class="fa-solid fa-calendar-check"></i> Leave Applications
                </button>
            </div>

            <!-- PERSONAL INFORMATION TAB -->
            <div id="personal" class="tab-content active">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-user-circle"></i> Personal Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $employee->full_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $employee->email }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value">{{ $employee->phone }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Birthdate</div>
                        <div class="detail-value">{{ $employee->birthdate ? $employee->birthdate->format('F d, Y') : 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Sex</div>
                        <div class="detail-value">{{ $employee->sex ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Civil Status</div>
                        <div class="detail-value">{{ $employee->civil_status ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label">Address</div>
                        <div class="detail-value">{{ $employee->address ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Contact</div>
                        <div class="detail-value">{{ $employee->emergency_contact ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Phone</div>
                        <div class="detail-value">{{ $employee->emergency_phone ?? 'Not set' }}</div>
                    </div>
                </div>

                <h3 style="color: #0057a0; margin: 30px 0 20px;"><i class="fa-solid fa-id-card"></i> Government IDs</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">TIN</div>
                        <div class="detail-value">{{ $employee->tax_id_number ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">SSS Number</div>
                        <div class="detail-value">{{ $employee->sss_number ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">PhilHealth Number</div>
                        <div class="detail-value">{{ $employee->philhealth_number ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Pag-IBIG Number</div>
                        <div class="detail-value">{{ $employee->pagibig_number ?? 'Not set' }}</div>
                    </div>
                </div>

                <h3 style="color: #0057a0; margin: 30px 0 20px;"><i class="fa-solid fa-building-columns"></i> Banking Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Bank Name</div>
                        <div class="detail-value">{{ $employee->bank_name ?? 'Not set' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Account Number</div>
                        <div class="detail-value">{{ $employee->bank_account_number ?? 'Not set' }}</div>
                    </div>
                </div>
            </div>

            <!-- EMPLOYMENT DETAILS TAB -->
            <div id="employment" class="tab-content">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-briefcase"></i> Employment Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Employee ID</div>
                        <div class="detail-value">{{ $employee->employee_id }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $employee->department }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Position</div>
                        <div class="detail-value">{{ $employee->position }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Employment Type</div>
                        <div class="detail-value">{{ ucfirst($employee->employment_type) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Hire Date</div>
                        <div class="detail-value">{{ $employee->hire_date->format('F d, Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <span class="status-badge {{ $employee->status }}">{{ ucfirst($employee->status) }}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Work Schedule</div>
                        <div class="detail-value">
                            @if($employee->workSchedule)
                                {{ $employee->workSchedule->schedule_name }}<br>
                                <small>{{ $employee->workSchedule->formatted_work_hours }}</small>
                            @else
                                Not assigned
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- COMPENSATION TAB -->
            <div id="compensation" class="tab-content">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-money-bill-wave"></i> Compensation Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Basic Salary (Monthly)</div>
                        <div class="detail-value">₱{{ number_format($employee->basic_salary ?? 0, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Hourly Rate</div>
                        <div class="detail-value">₱{{ number_format($employee->hourly_rate ?? 0, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Night Differential Rate</div>
                        <div class="detail-value">₱{{ number_format($employee->night_differential_rate ?? 0, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Holiday Rate Multiplier</div>
                        <div class="detail-value">{{ $employee->holiday_rate_multiplier ?? 0 }}x</div>
                    </div>
                </div>

                <h3 style="color: #0057a0; margin: 30px 0 20px;"><i class="fa-solid fa-umbrella-beach"></i> Leave Credits</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Vacation Leave</div>
                        <div class="detail-value">{{ $employee->vacation_leave_credits }} days</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Sick Leave</div>
                        <div class="detail-value">{{ $employee->sick_leave_credits }} days</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Leave</div>
                        <div class="detail-value">{{ $employee->emergency_leave_credits }} days</div>
                    </div>
                </div>
            </div>

            <!-- PAYROLL HISTORY TAB -->
            <div id="payroll" class="tab-content">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-file-invoice-dollar"></i> Payroll History</h3>
                
                @if($payrollHistory->count() > 0)
                    @foreach($payrollHistory as $year => $payslips)
                        <div class="year-group">
                            <h4><i class="fa-solid fa-calendar"></i> {{ $year }}</h4>
                            <table class="payroll-table">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Basic Salary</th>
                                        <th>Overtime</th>
                                        <th>Gross Pay</th>
                                        <th>Deductions</th>
                                        <th>Net Pay</th>
                                        <th>Hours</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payslips as $payslip)
                                        <tr>
                                            <td>{{ $payslip->payroll->payroll_period }}</td>
                                            <td>₱{{ number_format($payslip->basic_salary, 2) }}</td>
                                            <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                            <td>₱{{ number_format($payslip->gross_pay, 2) }}</td>
                                            <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                                            <td><strong>₱{{ number_format($payslip->net_pay, 2) }}</strong></td>
                                            <td>{{ $payslip->hours_worked }}h</td>
                                            <td>
                                                <a href="{{ route('admin.viewEmployeePayslip', $payslip->id) }}" 
                                                   style="color: #0057a0; text-decoration: none;">
                                                    <i class="fa-solid fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                        No payroll history available
                    </div>
                @endif
            </div>

            <!-- LOANS TAB -->
            <div id="loans" class="tab-content">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-hand-holding-dollar"></i> Loan History</h3>
                
                @if($employee->loans->count() > 0)
                    <table class="payroll-table">
                        <thead>
                            <tr>
                                <th>Date Applied</th>
                                <th>Amount</th>
                                <th>Monthly Deduction</th>
                                <th>Remaining Balance</th>
                                <th>Status</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->loans as $loan)
                                <tr>
                                    <td>{{ $loan->created_at->format('M d, Y') }}</td>
                                    <td>₱{{ number_format($loan->amount, 2) }}</td>
                                    <td>₱{{ number_format($loan->monthly_deduction, 2) }}</td>
                                    <td>₱{{ number_format($loan->remaining_balance, 2) }}</td>
                                    <td>
                                        <span class="status-badge {{ $loan->status }}">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $loan->purpose }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                        No loan records available
                    </div>
                @endif
            </div>

            <!-- LEAVE APPLICATIONS TAB -->
            <div id="leave" class="tab-content">
                <h3 style="color: #0057a0; margin-bottom: 20px;"><i class="fa-solid fa-calendar-check"></i> Leave Applications</h3>
                
                @if($employee->leaveApplications->count() > 0)
                    <table class="payroll-table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee->leaveApplications as $leave)
                                <tr>
                                    <td>{{ ucfirst($leave->leave_type) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                    <td>{{ $leave->reason }}</td>
                                    <td>
                                        <span class="status-badge {{ $leave->status }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                        No leave applications available
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script src="{{ asset('js/admin/script.js') }}"></script>
    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab button
            event.target.closest('.tab').classList.add('active');
        }
    </script>
</body>
</html>
