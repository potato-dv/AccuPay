<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Support - ACCUPAY INC.</title>
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
            transition: width 0.3s;
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
        }

        .sidebar ul li.active a {
            background: #003f70;
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
            transition: left 0.3s;
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
            transition: margin-left 0.3s, width 0.3s;
        }

        .reports-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .reports-section h2 {
            font-size: 22px;
            margin-bottom: 25px;
            color: #0057a0;
            border-bottom: 3px solid #0057a0;
            padding-bottom: 12px;
        }

        .report-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .report-card {
            background: #f7fafc;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #0057a0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .report-card i {
            color: #0057a0;
            margin-bottom: 15px;
        }

        .report-card h3 {
            font-size: 14px;
            color: #718096;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .report-card p {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .support-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .support-section h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #0057a0;
            border-bottom: 3px solid #0057a0;
            padding-bottom: 12px;
        }

        .support-section > p {
            margin-bottom: 25px;
            color: #718096;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .support-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
            font-size: 14px;
        }

        .support-form input,
        .support-form select,
        .support-form textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .support-form input:focus,
        .support-form select:focus,
        .support-form textarea:focus {
            outline: none;
            border-color: #0057a0;
            box-shadow: 0 0 0 3px rgba(0, 87, 160, 0.1);
        }

        .support-form textarea {
            resize: vertical;
            font-family: inherit;
        }

        .support-form button {
            width: 100%;
            padding: 12px 24px;
            background: #0057a0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .support-form button:hover {
            background: #003f70;
        }

        .support-form button i {
            margin-right: 8px;
        }

        .table-responsive {
            background: white;
            padding: 25px;
            border-radius: 8px;
            overflow-x: auto;
        }

        .table-responsive table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-responsive thead {
            background: #f7fafc;
        }

        .table-responsive th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
        }

        .table-responsive td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .table-responsive tbody tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.pending {
            background: #fef5e7;
            color: #d68910;
        }

        .status-badge.in-progress {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-badge.resolved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.closed {
            background: #e2e8f0;
            color: #4a5568;
        }

        .view-ticket-btn {
            padding: 6px 14px;
            background: #0057a0;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .view-ticket-btn:hover {
            background: #003f70;
        }

        .ticket-details {
            background: #f7fafc;
            padding: 25px;
        }

        .ticket-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        .ticket-content h4 {
            margin-top: 0;
            color: #2d3748;
        }

        .admin-reply {
            margin-top: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            border-radius: 6px;
        }

        .waiting-reply {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 6px;
        }

        .empty-state {
            background: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 8px;
            color: #a0aec0;
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
            <li><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
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
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <p>If you encounter any issues or need assistance, submit your ticket below:</p>
            <form action="{{ route('employee.support.submit') }}" method="POST" class="support-form">
                @csrf
                <div>
                    <label for="type">Issue Type</label>
                    <select id="type" name="type" required>
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
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supportReports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        <span class="status-badge" style="background: #718096; color: white;">
                                            {{ ucfirst($report->type) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($report->subject, 40) }}</td>
                                    <td>
                                        @if($report->status == 'pending')
                                            <span class="status-badge pending">Pending</span>
                                        @elseif($report->status == 'in-progress')
                                            <span class="status-badge in-progress">In Progress</span>
                                        @elseif($report->status == 'resolved')
                                            <span class="status-badge resolved">Resolved</span>
                                        @else
                                            <span class="status-badge closed">Closed</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button onclick="toggleTicket('ticket-{{ $report->id }}')" class="view-ticket-btn">
                                            View
                                        </button>
                                    </td>
                                </tr>
                                <tr id="ticket-{{ $report->id }}" style="display: none;">
                                    <td colspan="7" class="ticket-details">
                                        <div class="ticket-content">
                                            <h4>{{ $report->subject }}</h4>
                                            <p style="margin: 10px 0;"><strong>Message:</strong></p>
                                            <p style="white-space: pre-wrap; background: #f7fafc; padding: 15px; border-radius: 6px;">{{ $report->message }}</p>
                                            
                                            @if($report->admin_reply)
                                                <div class="admin-reply">
                                                    <p style="margin: 0 0 5px 0;"><strong>Admin Reply:</strong> <small>({{ $report->replied_at->format('M d, Y h:i A') }})</small></p>
                                                    <p style="white-space: pre-wrap; margin: 0;">{{ $report->admin_reply }}</p>
                                                </div>
                                            @else
                                                <div class="waiting-reply">
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
                <div class="empty-state">
                    <p>No help desk tickets submitted yet.</p>
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
