<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Employee</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
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
            <li><a href="{{ route('admin.employee.records') }}"><i class="fa-solid fa-folder-open"></i> <span class="menu-text">Employee Records</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
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
            <h1>Delete Employee</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <section class="add-employee-form">
            <div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 6px; margin-bottom: 30px; border: 1px solid #f5c6cb;">
                <h2 style="margin-top: 0;"><i class="fa-solid fa-triangle-exclamation"></i> Confirm Deletion</h2>
                <p style="font-size: 16px; margin-bottom: 10px;">
                    Are you sure you want to delete this employee? This action cannot be undone.
                </p>
            </div>

            <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <h3 style="color: #0057a0; margin-top: 0;">Employee Details</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600; width: 200px;">Employee ID:</td>
                        <td style="padding: 12px 0;">{{ $employee->employee_id }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600;">Full Name:</td>
                        <td style="padding: 12px 0;">{{ $employee->full_name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600;">Email:</td>
                        <td style="padding: 12px 0;">{{ $employee->email }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600;">Department:</td>
                        <td style="padding: 12px 0;">{{ $employee->department }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600;">Position:</td>
                        <td style="padding: 12px 0;">{{ $employee->position }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; font-weight: 600;">Status:</td>
                        <td style="padding: 12px 0;">{{ ucfirst($employee->status) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 0; font-weight: 600;">Hire Date:</td>
                        <td style="padding: 12px 0;">{{ $employee->hire_date->format('F d, Y') }}</td>
                    </tr>
                </table>
            </div>

            <div class="form-actions">
                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete" onclick="return confirm('Are you absolutely sure? This action cannot be undone!')">
                        <i class="fa-solid fa-trash"></i> Delete Employee
                    </button>
                </form>
                <a href="{{ route('admin.employees') }}" class="btn-theme" style="text-decoration: none; text-align: center; display: inline-block;">
                    <i class="fa-solid fa-arrow-left"></i> Cancel
                </a>
            </div>
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