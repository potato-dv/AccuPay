<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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
            transition: all 0.3s ease;
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
            color: #fff;
        }

        .sidebar ul li.active a {
            background: #003f70;
            color: #fff;
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
        }

        /* WELCOME SECTION */
        .welcome-section {
            background: linear-gradient(135deg, #0057a0 0%, #00a86b 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .welcome-section p {
            font-size: 16px;
            opacity: 0.9;
        }

        .alert-success {
            background: #00a86b;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin-top: 15px;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border-left: 4px solid #0057a0;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            border: 1px solid #d1d5db;
        }

        .stat-card.green { border-left-color: #00a86b; }
        .stat-card.purple { border-left-color: #0057a0; }
        .stat-card.orange { border-left-color: #00a86b; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 14px;
            color: #718096;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-icon.blue { background: #e6f2ff; color: #0057a0; }
        .stat-icon.green { background: #e6f7f0; color: #00a86b; }
        .stat-icon.purple { background: #e6f2ff; color: #0057a0; }
        .stat-icon.orange { background: #e6f7f0; color: #00a86b; }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
        }

        /* QR CODE SECTION */
        .qr-code-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            text-align: center;
            margin-bottom: 24px;
        }

        .qr-code-container {
            background: #f7fafc;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            display: inline-block;
        }

        .qr-code-container img {
            max-width: 250px;
            height: auto;
            display: block;
        }

        .qr-info {
            margin-top: 15px;
            padding: 15px;
            background: #e6f2ff;
            border-radius: 8px;
            font-size: 14px;
            color: #2d3748;
        }

        .qr-status {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
            gap: 10px;
        }

        .qr-status-item {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
        }

        .qr-status-item.checked-in {
            background: #c6f6d5;
            color: #22543d;
        }

        .qr-status-item.checked-out {
            background: #fed7d7;
            color: #742a2a;
        }

        .qr-status-item.pending {
            background: #fef5e7;
            color: #744210;
        }

        /* CONTENT SECTIONS */
        .content-section {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid transparent;
            margin-bottom: 24px;
            transition: all 0.2s ease;
        }

        .content-section:hover {
            border: 1px solid #d1d5db;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        /* TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #f7fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table th {
            padding: 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #2d3748;
        }

        .data-table tr:hover {
            background: #f7fafc;
        }

        /* QUICK ACTIONS */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            text-decoration: none;
            color: #2d3748;
            text-align: center;
        }

        .action-card:hover {
            border: 2px solid #d1d5db;
        }

        .action-card i {
            font-size: 32px;
            color: #0057a0;
            margin-bottom: 10px;
        }

        .action-card span {
            display: block;
            font-size: 14px;
            font-weight: 500;
        }

        /* ACTIVITY FEED */
        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e6f2ff;
            color: #0057a0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .activity-text {
            flex: 1;
            font-size: 14px;
            color: #4a5568;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success { background: #c6f6d5; color: #22543d; }
        .badge-warning { background: #feebc8; color: #744210; }
        .badge-info { background: #bee3f8; color: #2c5282; }
        .badge-danger { background: #fed7d7; color: #742a2a; }

        .two-column-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .two-column-grid {
                grid-template-columns: 1fr;
            }
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
            <li class="active"><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('employee.qr.page') }}"><i class="fa-solid fa-qrcode"></i> <span class="menu-text">QR Code</span></a></li>
            <li><a href="{{ route('employee.profile') }}"><i class="fa-solid fa-user"></i> <span class="menu-text">Profile</span></a></li>
            <li><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
            <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
            <li><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
            <li><a href="{{ route('employee.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Welcome back, {{ $employee->first_name }}!</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- Welcome Section -->
        <section class="welcome-section">
            <h2>Welcome back, {{ $employee->first_name }}!</h2>
            <p>{{ now()->format('l, F d, Y') }}</p>
            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
        </section>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-header">
                    <span class="stat-title">Remaining Leave</span>
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $remaining_leave }} days</div>
            </div>

            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-title">Attendance Rate</span>
                    <div class="stat-icon green">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $attendance_rate }}%</div>
            </div>

            <div class="stat-card purple">
                <div class="stat-header">
                    <span class="stat-title">Overtime Hours</span>
                    <div class="stat-icon purple">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($total_overtime_hours, 1) }} hrs</div>
            </div>

            <div class="stat-card orange">
                <div class="stat-header">
                    <span class="stat-title">Active Loans</span>
                    <div class="stat-icon orange">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="stat-value">{{ $active_loans_count }}</div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column-grid">
            <!-- Left Column -->
            <div>
                <!-- Attendance Summary -->
                <section class="content-section">
                    <div class="section-header">
                        <h3 class="section-title">Attendance Summary</h3>
                        <form method="GET" action="{{ route('employee.dashboard') }}" style="display: flex; gap: 10px; align-items: center;">
                            <select name="month" class="form-control" style="width: 120px; padding: 6px 10px; border: 1px solid #cbd5e0; border-radius: 6px;">
                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}" {{ $selected_month == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="year" class="form-control" style="width: 100px; padding: 6px 10px; border: 1px solid #cbd5e0; border-radius: 6px;">
                                @foreach(range(date('Y') - 2, date('Y')) as $year)
                                    <option value="{{ $year }}" {{ $selected_year == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" style="padding: 6px 15px; background: #0057a0; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                    <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #0057a0;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
                            <div>
                                <div style="font-size: 32px; font-weight: 700; color: #00a86b;">{{ $present_days }}</div>
                                <div style="font-size: 14px; color: #718096; margin-top: 6px; font-weight: 500;">Present Days</div>
                            </div>
                            <div>
                                <div style="font-size: 32px; font-weight: 700; color: #ed8936;">{{ $late_days }}</div>
                                <div style="font-size: 14px; color: #718096; margin-top: 6px; font-weight: 500;">Late Days</div>
                            </div>
                            <div>
                                <div style="font-size: 32px; font-weight: 700; color: #e53e3e;">{{ $absent_days }}</div>
                                <div style="font-size: 14px; color: #718096; margin-top: 6px; font-weight: 500;">Absent Days</div>
                            </div>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Dates</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge badge-success">Present ({{ $present_days }})</span></td>
                                <td>
                                    @php
                                        $presentDates = $attendance_records->where('status', 'present')->take(3);
                                    @endphp
                                    @if($presentDates->count() > 0)
                                        {{ $presentDates->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') }}
                                        @if($attendance_records->where('status', 'present')->count() > 3)
                                            <span style="color: #718096;">+{{ $attendance_records->where('status', 'present')->count() - 3 }} more</span>
                                        @endif
                                    @else
                                        <span style="color: #a0aec0;">No records yet</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-warning">Late ({{ $late_days }})</span></td>
                                <td>
                                    @php
                                        $lateDates = $attendance_records->where('status', 'late');
                                    @endphp
                                    @if($lateDates->count() > 0)
                                        {{ $lateDates->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') }}
                                    @else
                                        <span style="color: #a0aec0;">Perfect! No late arrivals</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge badge-danger">Absent ({{ $absent_days }})</span></td>
                                <td>
                                    @if($absent_days > 0)
                                        @php
                                            // Calculate absent dates based on work schedule
                                            $workSchedule = $employee->workSchedule;
                                            $absentDatesList = [];
                                            
                                            if ($workSchedule) {
                                                $workingDays = $workSchedule->working_days ?? [];
                                                $startDate = now()->startOfMonth();
                                                $endDate = now()->day < now()->daysInMonth ? now() : now()->endOfMonth();
                                                $currentDate = $startDate->copy();
                                                $expectedDates = [];
                                                
                                                while ($currentDate <= $endDate) {
                                                    $dayName = $currentDate->format('l');
                                                    if (in_array($dayName, $workingDays)) {
                                                        $expectedDates[] = $currentDate->format('Y-m-d');
                                                    }
                                                    $currentDate->addDay();
                                                }
                                                
                                                $recordedDates = $attendance_records->pluck('date')->map(fn($d) => $d->format('Y-m-d'))->toArray();
                                                $absentDates = array_diff($expectedDates, $recordedDates);
                                                
                                                foreach ($absentDates as $date) {
                                                    $absentDatesList[] = \Carbon\Carbon::parse($date)->format('M d');
                                                }
                                            }
                                        @endphp
                                        @if(count($absentDatesList) > 0)
                                            {{ implode(', ', array_slice($absentDatesList, 0, 5)) }}
                                            @if(count($absentDatesList) > 5)
                                                <span style="color: #718096;">+{{ count($absentDatesList) - 5 }} more</span>
                                            @endif
                                        @else
                                            <span style="color: #a0aec0;">No attendance records</span>
                                        @endif
                                    @else
                                        <span style="color: #00a86b;">Perfect attendance! 🎉</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- Quick Actions -->
                <section class="content-section">
                    <h3 class="section-title">Quick Actions</h3>
                    <div class="action-grid">
                        <a href="{{ route('employee.leave.application') }}" class="action-card">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Apply Leave</span>
                        </a>
                        <a href="{{ route('employee.payslip') }}" class="action-card">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>View Payslip</span>
                        </a>
                        <a href="{{ route('employee.profile') }}" class="action-card">
                            <i class="fa-solid fa-user-edit"></i>
                            <span>Update Profile</span>
                        </a>
                        <a href="{{ route('employee.loans') }}" class="action-card">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <span>Request Loan</span>
                        </a>
                    </div>
                </section>
            </div>

            <!-- Right Column -->
            <div>
                <!-- Recent Activity -->
                <section class="content-section">
                    <h3 class="section-title">Recent Activity</h3>
                    <ul class="activity-list">
                        @forelse($recent_activities as $activity)
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fa-solid fa-{{ $activity['icon'] }}"></i>
                                </div>
                                <span class="activity-text">{{ $activity['text'] }}</span>
                            </li>
                        @empty
                            <li class="activity-item">
                                <span class="activity-text" style="color: #a0aec0;">No recent activity</span>
                            </li>
                        @endforelse
                    </ul>
                </section>

                <!-- Payslip Info -->
                @if($last_payslip)
                    <section class="content-section">
                        <h3 class="section-title">Latest Payslip</h3>
                        <div style="padding: 15px; background: #f7fafc; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #718096; font-size: 13px;">Period</span>
                                <span style="font-weight: 600;">{{ $last_payslip->period }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="color: #718096; font-size: 13px;">Gross Pay</span>
                                <span style="font-weight: 600;">₱{{ number_format($last_payslip->gross_pay, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 2px solid #e2e8f0;">
                                <span style="color: #718096; font-size: 13px;">Net Pay</span>
                                <span style="font-weight: 700; color: #00a86b; font-size: 18px;">₱{{ number_format($last_payslip->net_pay, 2) }}</span>
                            </div>
                        </div>
                        <a href="{{ route('employee.payslip') }}" style="display: block; text-align: center; padding: 10px; background: #0057a0; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;">
                            View All Payslips
                        </a>
                    </section>
                @endif

                <!-- Leave Balance -->
                <section class="content-section">
                    <h3 class="section-title">Leave Balance</h3>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 48px; font-weight: 700; color: #0057a0;">{{ $remaining_leave }}</div>
                        <div style="color: #718096; margin-top: 8px;">days remaining</div>
                        <a href="{{ route('employee.leave.application') }}" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #00a86b; color: white; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;">
                            Apply for Leave
                        </a>
                    </div>
                </section>
            </div>
        </div>

    </main>

    <!-- SIDEBAR TOGGLE & Attendance Script -->
    <script>
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const navbar = document.querySelector('.navbar');
        const mainContent = document.querySelector('.main-content');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if(sidebar.classList.contains('collapsed')){
                navbar.style.left = '70px';
                mainContent.style.marginLeft = '70px';
                mainContent.style.width = 'calc(100% - 70px)';
            } else {
                navbar.style.left = '250px';
                mainContent.style.marginLeft = '250px';
                mainContent.style.width = 'calc(100% - 250px)';
            }
        });

    </script>

</body>
</html>
