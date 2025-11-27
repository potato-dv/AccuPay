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
            <li><a href="{{ route('employee.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
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

            @if(session('success'))
                <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #155724;">
                    {{ session('success') }}
                </div>
            @endif

            <p>If you encounter any issues or need assistance, submit your ticket below:</p>
            <form action="{{ route('employee.support.submit') }}" method="POST" class="support-form">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label for="type">Issue Type</label>
                    <select id="type" name="type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Select Type</option>
                        <option value="technical">Technical Issue</option>
                        <option value="payroll">Payroll Issue</option>
                        <option value="leave">Leave Issue</option>
                        <option value="attendance">Attendance Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" placeholder="Enter issue subject" required>
                
                <label for="message">Description</label>
                <textarea id="message" name="message" placeholder="Describe the issue in detail" rows="5" required></textarea>
                
                <button type="submit"><i class="fa-solid fa-paper-plane"></i> Submit Ticket</button>
            </form>
        </section>

        <!-- Help Desk History -->
        <section class="reports-section">
            <h2>My Help Desk Tickets</h2>
            @if($supportReports->count() > 0)
                <div class="table-responsive" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left;">#</th>
                                <th style="padding: 12px; text-align: left;">Type</th>
                                <th style="padding: 12px; text-align: left;">Subject</th>
                                <th style="padding: 12px; text-align: left;">Status</th>
                                <th style="padding: 12px; text-align: left;">Submitted</th>
                                <th style="padding: 12px; text-align: left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supportReports as $report)
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 12px;">{{ $report->id }}</td>
                                    <td style="padding: 12px;">
                                        <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">
                                            {{ ucfirst($report->type) }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">{{ Str::limit($report->subject, 40) }}</td>
                                    <td style="padding: 12px;">
                                        @if($report->status == 'pending')
                                            <span style="padding: 4px 8px; background: #ffc107; color: #000; border-radius: 4px; font-size: 12px;">Pending</span>
                                        @elseif($report->status == 'in-progress')
                                            <span style="padding: 4px 8px; background: #0d6efd; color: white; border-radius: 4px; font-size: 12px;">In Progress</span>
                                        @elseif($report->status == 'resolved')
                                            <span style="padding: 4px 8px; background: #198754; color: white; border-radius: 4px; font-size: 12px;">Resolved</span>
                                        @else
                                            <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Closed</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px;">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td style="padding: 12px;">
                                        <button onclick="toggleTicket('ticket-{{ $report->id }}')" style="padding: 6px 12px; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr id="ticket-{{ $report->id }}" style="display: none;">
                                    <td colspan="7" style="padding: 20px; background: #f8f9fa;">
                                        <div style="background: white; padding: 20px; border-radius: 8px;">
                                            <h4 style="margin-top: 0;">{{ $report->subject }}</h4>
                                            <p style="margin: 10px 0;"><strong>Message:</strong></p>
                                            <p style="white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 4px;">{{ $report->message }}</p>
                                            
                                            @if($report->admin_reply)
                                                <div style="margin-top: 20px; padding: 15px; background: #d1ecf1; border-left: 4px solid #0dcaf0; border-radius: 4px;">
                                                    <p style="margin: 0 0 5px 0;"><strong>Admin Reply:</strong> <small>({{ $report->replied_at->format('M d, Y h:i A') }})</small></p>
                                                    <p style="white-space: pre-wrap; margin: 0;">{{ $report->admin_reply }}</p>
                                                </div>
                                            @else
                                                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px;">
                                                    <p style="margin: 0;">Waiting for admin response...</p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <p style="color: #6c757d; margin: 0;">No help desk tickets submitted yet.</p>
                </div>
            @endif
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

        function toggleTicket(id) {
            const element = document.getElementById(id);
            if (element.style.display === 'none') {
                element.style.display = 'table-row';
            } else {
                element.style.display = 'none';
            }
        }
    </script>
</body>
</html>
