<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <title>Manage Employees</title>
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
            <li class="active"><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Manage Employees</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
        
        <!-- QUICK ACTIONS -->
        <section class="quick-actions">
            <div class="table-header">
                <div class="left-controls">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" class="search-input" placeholder="Search employees...">
                    </div>
                </div>
                <a href="{{ route('admin.employees.add') }}" class="action-btn">
                    <i class="fa-solid fa-plus"></i> Add Employee
                </a>
            </div>
        </section>

        <!-- EMPLOYEE LIST -->
        <section class="employee-list">
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Work Schedule</th>
                        <th>Status</th>
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
                            @if($employee->workSchedule)
                                <span title="{{ $employee->workSchedule->formatted_work_hours }}">
                                    {{ $employee->workSchedule->schedule_name }}
                                </span>
                            @else
                                <span style="color: #999;">Not assigned</span>
                            @endif
                        </td>
                        <td>{{ ucfirst($employee->status) }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('admin.employees.view', $employee->id) }}" class="btn-sm" style="background: #17a2b8; color: white;" title="View Details">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn-sm" style="background: #00a86b; color: white;" title="Edit Employee">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="{{ route('admin.employees.delete', $employee->id) }}" class="btn-sm" style="background: #e74c3c; color: white;" title="Delete Employee" onclick="return confirm('Are you sure you want to delete this employee?')">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No employees found. <a href="{{ route('admin.employees.add') }}" style="color: #0057a0;">Add your first employee</a>
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

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        const tableRows = document.querySelectorAll('.employee-list tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            tableRows.forEach(row => {
                // Skip the "no employees" row
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                const employeeId = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const department = row.cells[2].textContent.toLowerCase();
                const position = row.cells[3].textContent.toLowerCase();
                const status = row.cells[4].textContent.toLowerCase();

                const matches = employeeId.includes(searchTerm) ||
                              name.includes(searchTerm) ||
                              department.includes(searchTerm) ||
                              position.includes(searchTerm) ||
                              status.includes(searchTerm);

                row.style.display = matches ? '' : 'none';
            });
        });
    </script>

</body>
</html>
