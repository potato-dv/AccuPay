<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            <li class="active"><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>

    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Admin Dashboard</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- DASHBOARD BOXES -->
        <section class="stats">
            <div class="box">
                <h3>Total Employees</h3>
                <p>{{ $total_employees }}</p>
                <small style="color: #666; display: block; margin-top: 5px;">{{ $active_employees }} Active</small>
            </div>
            <div class="box">
                <h3>Pending Leave Requests</h3>
                <p>{{ $pending_leaves }}</p>
                <small style="color: #666; display: block; margin-top: 5px;">Awaiting Approval</small>
            </div>
            <div class="box">
                <h3>Payroll Generated</h3>
                <p>{{ $total_payrolls }}</p>
                <small style="color: #666; display: block; margin-top: 5px;">{{ ucfirst($payroll_status) }}</small>
            </div>
            <div class="box">
                <h3>Attendance Today</h3>
                <p>{{ $total_attendance_today }}</p>
                <small style="color: #666; display: block; margin-top: 5px;">{{ date('M d, Y') }}</small>
            </div>
        </section>

        <!-- QUICK ACTIONS -->
        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="{{ route('admin.attendance') }}" class="action-btn">Manage Attendance</a>
                <a href="{{ route('admin.employees') }}" class="action-btn">Manage Employees</a>
                <a href="{{ route('admin.payroll') }}" class="action-btn">Manage Payroll</a>
                <a href="{{ route('admin.payslip') }}" class="action-btn">Manage Payslip</a>
                <a href="{{ route('admin.leave') }}" class="action-btn">Manage Leave Applications</a>
                <a href="{{ route('admin.reports') }}" class="action-btn">Reports</a>        
            </div>

        </section>

        <!-- RECENT EMPLOYEES -->
        <section class="employee-list">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2>Recent Employees</h2>
                <a href="{{ route('admin.employees') }}" class="btn-theme" style="padding: 8px 16px; text-decoration: none; display: inline-block;">
                    View All Employees <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_employees as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 500;
                                @if($employee->status == 'active') background: #d4edda; color: #155724;
                                @elseif($employee->status == 'on-leave') background: #fff3cd; color: #856404;
                                @else background: #f8d7da; color: #721c24; @endif">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td>{{ date('M d, Y', strtotime($employee->hire_date)) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            <i class="fa-solid fa-users" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                            No employees yet. <a href="{{ route('admin.employees.add') }}" style="color: #0057a0;">Add your first employee</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
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
