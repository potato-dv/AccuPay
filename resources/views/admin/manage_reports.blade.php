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
            <li class="active"><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
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

        <!-- SUMMARY CARDS -->
        <section class="stats">
            <div class="box">
                <h3>Total Employees</h3>
                <p>{{ $totalEmployees }}</p>
            </div>
            <div class="box">
                <h3>Total Payrolls</h3>
                <p>{{ $totalPayrolls }}</p>
            </div>
            <div class="box">
                <h3>Total Attendance Records</h3>
                <p>{{ $totalAttendance }}</p>
            </div>
            <div class="box">
                <h3>Total Leave Applications</h3>
                <p>{{ $totalLeaves }}</p>
            </div>
        </section>

        <!-- FILTER SECTION -->
        <section class="filter-section" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px;">Generate Report</h3>
            <form method="GET" action="{{ route('admin.reports') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Report Type</label>
                    <select name="report_type" class="form-input" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Select Type</option>
                        <option value="payroll" {{ $reportType == 'payroll' ? 'selected' : '' }}>Payroll Report</option>
                        <option value="attendance" {{ $reportType == 'attendance' ? 'selected' : '' }}>Attendance Report</option>
                        <option value="leave" {{ $reportType == 'leave' ? 'selected' : '' }}>Leave Report</option>
                        <option value="employee" {{ $reportType == 'employee' ? 'selected' : '' }}>Employee Report</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Employee (Optional)</label>
                    <select name="employee_id" class="form-input" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->employee_id }} - {{ $emp->first_name }} {{ $emp->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-theme" style="width: 100%; padding: 8px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <i class="fa-solid fa-filter"></i> Generate Report
                    </button>
                </div>
            </form>
        </section>

        @if($reportType)
        <!-- REPORT RESULTS -->
        <div class="table-header">
            <h2>
                @if($reportType == 'payroll') Payroll Report
                @elseif($reportType == 'attendance') Attendance Report
                @elseif($reportType == 'leave') Leave Report
                @elseif($reportType == 'employee') Employee Report
                @endif
            </h2>
        </div>

        <section class="employee-list">
            @if($reportType == 'payroll')
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Payroll Period</th>
                        <th>Period Start</th>
                        <th>Period End</th>
                        <th>Basic Salary</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $payslip)
                    <tr>
                        <td>{{ $payslip->employee->employee_id }}</td>
                        <td>{{ $payslip->employee->full_name }}</td>
                        <td>{{ $payslip->payroll->payroll_period }}</td>
                        <td>{{ $payslip->payroll->period_start->format('M d, Y') }}</td>
                        <td>{{ $payslip->payroll->period_end->format('M d, Y') }}</td>
                        <td>₱{{ number_format($payslip->basic_salary, 2) }}</td>
                        <td>₱{{ number_format($payslip->gross_pay, 2) }}</td>
                        <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        <td><strong>₱{{ number_format($payslip->net_pay, 2) }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px;">No payroll data found for selected filters</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'attendance')
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours Worked</th>
                        <th>Overtime Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $attendance)
                    <tr>
                        <td>{{ $attendance->employee->employee_id }}</td>
                        <td>{{ $attendance->employee->full_name }}</td>
                        <td>{{ $attendance->date->format('M d, Y') }}</td>
                        <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '-' }}</td>
                        <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('h:i A') : '-' }}</td>
                        <td>{{ number_format($attendance->hours_worked, 2) }}</td>
                        <td>{{ number_format($attendance->overtime_hours, 2) }}</td>
                        <td>{{ ucfirst($attendance->status) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">No attendance data found for selected filters</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'leave')
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $leave)
                    <tr>
                        <td>{{ $leave->employee->employee_id }}</td>
                        <td>{{ $leave->employee->full_name }}</td>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>{{ $leave->start_date->format('M d, Y') }}</td>
                        <td>{{ $leave->end_date->format('M d, Y') }}</td>
                        <td>{{ $leave->days_count }}</td>
                        <td>{{ $leave->reason }}</td>
                        <td>
                            @if($leave->status == 'approved')
                                <span style="color: green; font-weight: bold;">Approved</span>
                            @elseif($leave->status == 'rejected')
                                <span style="color: red; font-weight: bold;">Rejected</span>
                            @else
                                <span style="color: orange; font-weight: bold;">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">No leave data found for selected filters</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @elseif($reportType == 'employee')
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Employment Type</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $employee)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ ucfirst($employee->employment_type) }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>
                            @if($employee->status == 'active')
                                <span style="color: green; font-weight: bold;">Active</span>
                            @else
                                <span style="color: red; font-weight: bold;">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $employee->hire_date->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px;">No employee data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            <!-- PAGINATION -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $reportData->links() }}
            </div>
        </section>
        @else
        <!-- NO REPORT SELECTED MESSAGE -->
        <section style="background: white; padding: 40px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center;">
            <i class="fa-solid fa-chart-line" style="font-size: 48px; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666;">Select a report type and click "Generate Report" to view data</h3>
        </section>
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
