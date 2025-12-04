<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <title>Employee Records</title>
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
            <h1>Employee Records</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- SEARCH -->
        <section class="quick-actions">
            <div class="table-header">
                <div class="left-controls">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Search employee records...">
                    </div>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="color: #666; font-size: 14px;">Total Employees: <strong>{{ $employees->count() }}</strong></span>
                </div>
            </div>
        </section>

        <!-- EMPLOYEE RECORDS LIST -->
        <section class="employee-list" style="overflow-x: auto;">
            <table id="employeeTable" style="min-width: 1000px;">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Total Records</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            <span class="status-badge {{ $employee->status }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-weight: 600;">
                                {{ $employee->employee_records_count }}
                            </span>
                        </td>
                        <td>
                            @if($employee->employeeRecords->first())
                                {{ $employee->employeeRecords->first()->record_date->format('M d, Y') }}
                            @else
                                <span style="color: #999;">No records</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.employee.record.view', $employee->id) }}" 
                               class="btn-sm" 
                               style="background: #17a2b8; color: white;"
                               title="View Employee Record">
                                <i class="fa-solid fa-folder-open"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                            No employee records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <script src="{{ asset('js/admin/script.js') }}"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employeeTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
