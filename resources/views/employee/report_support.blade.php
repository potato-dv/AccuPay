<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Support - ACCUPAY INC.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/report_support.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-toggle" id="toggleSidebar">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>

        <ul>
            <li><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('employee.profile') }}"><i class="fa-solid fa-user"></i> <span class="menu-text">Profile</span></a></li>
            <li><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
            <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
            <li><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
            <li class="active"><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Reports & Support</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Leave Summary Section -->
        <section class="reports-section">
            <h2>My Leave Summary ({{ now()->year }})</h2>
            <div class="report-cards">
                <div class="report-card">
                    <i class="fa-solid fa-calendar-days fa-2x"></i>
                    <h3>Total Leaves Taken</h3>
                    <p>{{ $totalLeavesTaken }}</p>
                </div>
                <div class="report-card">
                    <i class="fa-solid fa-suitcase-rolling fa-2x"></i>
                    <h3>Vacation Leave</h3>
                    <p>{{ $vacationLeaves }}</p>
                </div>
                <div class="report-card">
                    <i class="fa-solid fa-bed fa-2x"></i>
                    <h3>Sick Leave</h3>
                    <p>{{ $sickLeaves }}</p>
                </div>
                <div class="report-card">
                    <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                    <h3>Pending Requests</h3>
                    <p>{{ $pendingLeaves }}</p>
                </div>
            </div>
        </section>

        <!-- Payroll Summary Section -->
        <section class="reports-section">
            <h2>Payroll Summary</h2>
            <div class="report-cards">
                <div class="report-card">
                    <i class="fa-solid fa-file-invoice-dollar fa-2x"></i>
                    <h3>Last Month Net Pay</h3>
                    <p>₱{{ $lastPayslip ? number_format($lastPayslip->net_pay, 2) : '0.00' }}</p>
                </div>
                <div class="report-card">
                    <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                    <h3>Average Monthly Pay</h3>
                    <p>₱{{ number_format($avgMonthlyPay ?? 0, 2) }}</p>
                </div>
                <div class="report-card">
                    <i class="fa-solid fa-circle-check fa-2x"></i>
                    <h3>Total Payslips</h3>
                    <p>{{ $totalPayslips }} Received</p>
                </div>
            </div>
        </section>

        <!-- Support Section -->
        <section class="support-section">
            <h2>Support & Feedback</h2>
            <p>If you encounter any issues or need assistance, submit your ticket below:</p>
            <form class="support-form">
                <label for="issue">Issue Title</label>
                <input type="text" id="issue" placeholder="Enter issue title">
                
                <label for="description">Description</label>
                <textarea id="description" placeholder="Describe the issue"></textarea>
                
                <button type="submit"><i class="fa-solid fa-paper-plane"></i> Submit Ticket</button>
            </form>
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
