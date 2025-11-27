<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>

        <ul>
            <li class="active"><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
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

        <!-- Welcome Panel -->
        <section class="welcome-panel">
            <h2>Employee Dashboard</h2>
            <p>Today is {{ now()->format('l, F d, Y') }}</p>
            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-top: 15px;">
                    {{ session('success') }}
                </div>
            @endif
        </section>

        <!-- Summary Cards -->
        <section class="summary-cards">
            <div class="card"><h3>Remaining Leave</h3><p>{{ $remaining_leave }} days</p></div>
            <div class="card"><h3>Last Payslip</h3><p>{{ $last_payslip ? $last_payslip->period : 'No payslip yet' }}</p></div>
            <div class="card"><h3>Next Payroll</h3><p>{{ now()->endOfMonth()->format('M d, Y') }}</p></div>
            <div class="card"><h3>Attendance</h3><p>{{ $total_work_days > 0 ? round(($attendance_count / $total_work_days) * 100) : 0 }}% this month</p></div>
        </section>

        <!-- Attendance Summary Table -->
        <section class="attendance-summary">
            <h2>Attendance Summary (This Month)</h2>
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Days Present</td>
                        <td>{{ $present_days }}</td>
                        <td>
                            @php
                                $presentRecords = $attendance_records->where('status', 'present')->take(5);
                                $allPresent = $attendance_records->where('status', 'present');
                            @endphp
                            @if($presentRecords->count() > 0)
                                <span id="recentPresent" class="clickable">
                                    {{ $presentRecords->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') }}
                                </span>
                                @if($allPresent->count() > 5)
                                    <div id="fullPresent" class="hidden">
                                        {{ $allPresent->skip(5)->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') }}
                                    </div>
                                    <span id="togglePresent" class="toggle-btn clickable">Show more</span>
                                @endif
                            @else
                                No present days this month
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Days Absent</td>
                        <td>{{ $absent_days }}</td>
                        <td>
                            @php
                                $absentRecords = $attendance_records->where('status', 'absent');
                            @endphp
                            {{ $absentRecords->count() > 0 ? $absentRecords->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') : 'None' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Late Arrivals</td>
                        <td>{{ $late_days }}</td>
                        <td>
                            @php
                                $lateRecords = $attendance_records->where('status', 'late');
                            @endphp
                            {{ $lateRecords->count() > 0 ? $lateRecords->pluck('date')->map(fn($d) => $d->format('M d'))->implode(', ') : 'None' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Overtime Hours</td>
                        <td>{{ number_format($total_overtime_hours, 1) }} hrs</td>
                        <td>
                            @php
                                $overtimeRecords = $attendance_records->where('overtime_hours', '>', 0);
                            @endphp
                            @if($overtimeRecords->count() > 0)
                                {{ $overtimeRecords->map(fn($r) => $r->date->format('M d') . ' – ' . number_format($r->overtime_hours, 1) . ' hrs')->implode(', ') }}
                            @else
                                No overtime this month
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Quick Actions -->
        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="{{ route('employee.leave.application') }}" class="action-btn">Apply for Leave</a>
                <a href="{{ route('employee.payslip') }}" class="action-btn">View Payslip</a>
                <a href="{{ route('employee.profile') }}" class="action-btn">Update Profile</a>
                <a href="{{ route('employee.leave.status') }}" class="action-btn">Check Leave Status</a>
            </div>
        </section>

        <!-- Activity Feed -->
        <section class="activity-feed">
            <h2>Recent Activity</h2>
            <ul>
                @forelse($recent_activities as $activity)
                    <li>{{ $activity }}</li>
                @empty
                    <li>No recent activity</li>
                @endforelse
            </ul>
        </section>

        <!-- Announcements -->
        <section class="announcements">
            <h2>Announcements</h2>
            <p>🎄 Company holiday on Dec 25 – Merry Christmas!</p>
            <p>📅 Submit leave requests before Nov 28 for year-end processing.</p>
        </section>

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
                navbar.style.left = '90px';
                mainContent.style.marginLeft = '90px';
            } else {
                navbar.style.left = '230px';
                mainContent.style.marginLeft = '230px';
            }
        });

        // Attendance "Days Present" toggle
        const togglePresentBtn = document.getElementById('togglePresent');
        const fullPresent = document.getElementById('fullPresent');
        togglePresentBtn.addEventListener('click', () => {
            if(fullPresent.classList.contains('hidden')){
                fullPresent.classList.remove('hidden');
                togglePresentBtn.textContent = 'Show less';
            } else {
                fullPresent.classList.add('hidden');
                togglePresentBtn.textContent = 'Show more';
            }
        });
    </script>

</body>
</html>
