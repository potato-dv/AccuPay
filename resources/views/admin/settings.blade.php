<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Settings</title>
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
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.employee.records') }}"><i class="fa-solid fa-folder-open"></i> <span class="menu-text">Employee Records</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li class="active"><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Admin Settings</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <section class="add-employee-form">

            <!-- Time & Attendance -->
            <section class="settings-section">
                <h2>Time & Attendance</h2>
                <form id="attendanceForm">
                    <div class="settings-row">
                        <div class="settings-input small">
                            <label for="timeIn">Standard Time In</label>
                            <input id="timeIn" name="timeIn" type="time" value="08:00">
                        </div>

                        <div class="settings-input small">
                            <label for="timeOut">Standard Time Out</label>
                            <input id="timeOut" name="timeOut" type="time" value="17:00">
                        </div>

                        <div class="settings-note">
                            <small>These values are used as system defaults for attendance calculations.</small>
                        </div>
                    </div>

                    <div class="settings-save">
                        <button type="button" class="btn-theme settings-small-btn" id="saveAttendance">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                    </div>
                </form>
            </section>

            <hr>
            <br>

            <!-- Payroll Defaults -->
            <section class="settings-section">
                <h2>Payroll Defaults</h2>
                <form id="payrollForm">
                    <div class="settings-row">
                        <div class="settings-input small">
                            <label for="salaryType">Salary Type</label>
                            <select id="salaryType">
                                <option value="monthly">Monthly</option>
                                <option value="daily">Daily</option>
                                <option value="hourly">Hourly</option>
                            </select>
                        </div>

                        <div class="settings-input small">
                            <label for="defaultSalary">Default Basic Salary</label>
                            <input id="defaultSalary" type="number" min="0" step="0.01" placeholder="0.00">
                        </div>

                        <div class="settings-note">
                            <small>Applied automatically when adding new employees.</small>
                        </div>
                    </div>

                    <div class="settings-save">
                        <button type="button" class="btn-theme settings-small-btn" id="savePayroll">
                            <i class="fa-solid fa-floppy-disk"></i> Save
                        </button>
                    </div>
                </form>
            </section>

            <hr>
            <br>

            <!-- Admin Account -->
            <section class="settings-section">
                <h2>Admin Account</h2>
                <form id="passwordForm">
                    <div class="settings-column">
                        <label>Current Password</label>
                        <input id="currentPassword" type="password">

                        <label>New Password</label>
                        <input id="newPassword" type="password">

                        <label>Confirm Password</label>
                        <input id="confirmPassword" type="password">
                    </div>

                    <div class="settings-save">
                        <button type="reset" class="btn-delete settings-small-btn">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                        <button type="button" class="btn-theme settings-small-btn" id="changePassword">
                            <i class="fa-solid fa-key"></i> Change Password
                        </button>
                    </div>
                </form>
            </section>

            <hr>
            <br>

            <!-- Appearance / Theme -->
            <section class="settings-section">
                <h2>Appearance</h2>
                <form id="themeForm">
                    <div class="settings-row">
                        <div class="settings-input small">
                            <label for="themeSelect">Theme</label>
                            <select id="themeSelect">
                                <option value="light">Light</option>
                                <option value="dark">Dark</option>
                                <option value="system">Match System</option>
                            </select>
                        </div>

                        <div class="settings-note">
                            <small>Choose how the interface should appear. (UI only)</small>
                        </div>
                    </div>

                    <div class="settings-save">
                        <button type="button" class="btn-theme settings-small-btn">
                            <i class="fa-solid fa-paint-roller"></i> Save
                        </button>
                    </div>
                </form>
            </section>

        </section>
    </main>

    <script>
        const toggleButton = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const navbar = document.querySelector('.navbar');
        const mainContent = document.querySelector('.main-content');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                navbar.style.left = '90px';
                mainContent.style.marginLeft = '90px';
            } else {
                navbar.style.left = '230px';
                mainContent.style.marginLeft = '230px';
            }
        });

        document.getElementById('saveAttendance').addEventListener('click', () => alert('Attendance defaults saved (UI only).'));
        document.getElementById('savePayroll').addEventListener('click', () => alert('Payroll defaults saved (UI only).'));
        document.getElementById('changePassword').addEventListener('click', () => {
            const newP = document.getElementById('newPassword').value;
            const confP = document.getElementById('confirmPassword').value;
            if (!newP) return alert('Enter a new password.');
            if (newP !== confP) return alert('Passwords do not match.');
            alert('Password changed (UI only).');
            document.getElementById('passwordForm').reset();
        });
    </script>

</body>
</html>
