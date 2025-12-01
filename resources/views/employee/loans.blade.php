<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loans - ACCUPAY INC.</title>
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

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #fff5f5;
            border: 1px solid #fc8181;
            color: #742a2a;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-danger ul {
            margin: 0;
            padding-left: 20px;
        }

        .loans-summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .loan-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 4px solid #0057a0;
        }

        .loan-card.paid {
            border-left-color: #00a86b;
        }

        .loan-card.balance {
            border-left-color: #e53e3e;
        }

        .loan-card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .loan-card-icon {
            font-size: 32px;
            opacity: 0.2;
        }

        .card-label {
            font-size: 13px;
            color: #718096;
            margin-bottom: 8px;
        }

        .card-value {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .section-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 25px;
            color: #2d3748;
            border-bottom: 3px solid #0057a0;
            padding-bottom: 12px;
        }

        .section-title i {
            color: #0057a0;
            margin-right: 10px;
        }

        .active-loan {
            background: #f7fafc;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #0057a0;
        }

        .loan-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .loan-detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 11px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .detail-value {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .detail-value.primary {
            color: #0057a0;
        }

        .detail-value.success {
            color: #00a86b;
        }

        .detail-value.danger {
            color: #e53e3e;
        }

        .detail-meta {
            font-size: 12px;
            color: #718096;
            margin-top: 4px;
        }

        .progress-bar {
            background: #e2e8f0;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, #0057a0 0%, #00a86b 100%);
            height: 100%;
            transition: width 0.3s;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #718096;
        }

        .loan-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0057a0;
            box-shadow: 0 0 0 3px rgba(0, 87, 160, 0.1);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #718096;
        }

        .info-box {
            background: #ebf8ff;
            border-left: 4px solid #4299e1;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info-box-text {
            font-size: 13px;
            color: #2c5282;
        }

        .info-box-text i {
            margin-right: 8px;
        }

        .submit-btn {
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

        .submit-btn:hover {
            background: #003f70;
        }

        .submit-btn i {
            margin-right: 8px;
        }

        .loans-table {
            width: 100%;
            border-collapse: collapse;
        }

        .loans-table thead {
            background: #f7fafc;
        }

        .loans-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
        }

        .loans-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .loans-table tbody tr:hover {
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

        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.completed {
            background: #e2e8f0;
            color: #4a5568;
        }

        .view-btn {
            padding: 6px 14px;
            background: #0057a0;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .view-btn:hover {
            background: #003f70;
        }

        .loan-details-row {
            display: none;
        }

        .loan-details-content {
            background: #f7fafc;
            padding: 25px;
            border-radius: 8px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .payment-history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .payment-history-table th {
            padding: 8px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }

        .payment-history-table td {
            padding: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
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
            <li class="active"><a href="{{ route('employee.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">My Loans</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-danger">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="loans-summary-cards">
            <div class="loan-card">
                <div class="loan-card-content">
                    <div class="loan-card-info">
                        <div class="card-label">Total Borrowed</div>
                        <div class="card-value">₱{{ number_format($totalBorrowed, 2) }}</div>
                    </div>
                    <i class="fa-solid fa-hand-holding-dollar loan-card-icon"></i>
                </div>
            </div>
            <div class="loan-card paid">
                <div class="loan-card-content">
                    <div class="loan-card-info">
                        <div class="card-label">Total Paid</div>
                        <div class="card-value">₱{{ number_format($totalPaid, 2) }}</div>
                    </div>
                    <i class="fa-solid fa-circle-check loan-card-icon"></i>
                </div>
            </div>
            <div class="loan-card balance">
                <div class="loan-card-content">
                    <div class="loan-card-info">
                        <div class="card-label">Remaining Balance</div>
                        <div class="card-value">₱{{ number_format($totalRemaining, 2) }}</div>
                    </div>
                    <i class="fa-solid fa-coins loan-card-icon"></i>
                </div>
            </div>
        </div>

        <!-- Active Loans Section -->
        @if($activeLoans->count() > 0)
        <section class="section-card">
            <h3 class="section-title"><i class="fa-solid fa-hourglass-half"></i> Active Loans ({{ $activeLoans->count() }})</h3>
            <div>
                @foreach($activeLoans as $loan)
                <div class="active-loan">
                    <div class="loan-details-grid">
                        <div class="loan-detail-item">
                            <div class="detail-label">LOAN AMOUNT</div>
                            <div class="detail-value primary">₱{{ number_format($loan->amount, 2) }}</div>
                            <div class="detail-meta">{{ $loan->purpose }}</div>
                        </div>
                        <div class="loan-detail-item">
                            <div class="detail-label">PER CUTOFF</div>
                            <div class="detail-value">₱{{ number_format($loan->monthly_deduction, 2) }}</div>
                            <div class="detail-meta">{{ $loan->terms }} cutoffs total</div>
                        </div>
                        <div class="loan-detail-item">
                            <div class="detail-label">PAID</div>
                            <div class="detail-value success">₱{{ number_format($loan->paid_amount, 2) }}</div>
                            <div class="detail-meta">{{ $loan->payments_made }}/{{ $loan->terms }} payments</div>
                        </div>
                        <div class="loan-detail-item">
                            <div class="detail-label">BALANCE</div>
                            <div class="detail-value danger">₱{{ number_format($loan->remaining_balance, 2) }}</div>
                            <div class="detail-meta">{{ $loan->remaining_payments }} left</div>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $loan->progress_percentage }}%;"></div>
                    </div>
                    <div class="progress-info">
                        <span>{{ $loan->progress_percentage }}% complete</span>
                        <span>Deductions start: {{ $loan->start_date ? $loan->start_date->format('M d, Y') : 'Pending approval' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- New Loan Request Section -->
        <section class="section-card">
            <h3 class="section-title"><i class="fa-solid fa-plus-circle"></i> Request New Loan</h3>
            <form method="POST" action="{{ route('employee.loans.store') }}">
                @csrf
                <div class="loan-form-grid">
                    <div class="form-group">
                        <label>Loan Amount (₱)</label>
                        <input type="number" name="amount" required min="100" max="100000" step="0.01" value="{{ old('amount') }}">
                        <small>Min: ₱100 | Max: ₱100,000</small>
                    </div>
                    <div class="form-group">
                        <label>Payment Terms (Cutoffs)</label>
                        <input type="number" name="terms" required min="1" max="24" value="{{ old('terms') }}">
                        <small>1-24 cutoffs (max 1 year)</small>
                    </div>
                    <div class="form-group">
                        <label>Purpose</label>
                        <input type="text" name="purpose" required maxlength="255" value="{{ old('purpose') }}" placeholder="e.g., Emergency, Medical">
                        <small>Brief description</small>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-box-text">
                        <i class="fa-solid fa-info-circle"></i> <strong>How it works:</strong> Once approved, deductions will be automatically taken from your payroll each cutoff until fully paid. Admin will set the start date.
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </section>

        <!-- All Loans Table Section -->
        <section class="section-card">
            <h3 class="section-title"><i class="fa-solid fa-history"></i> All Loans</h3>
            <div style="overflow-x: auto;">
                <table class="loans-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th style="text-align: right;">Amount</th>
                            <th>Purpose</th>
                            <th style="text-align: center;">Terms</th>
                            <th style="text-align: right;">Paid</th>
                            <th style="text-align: right;">Balance</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->created_at->format('M d, Y') }}</td>
                            <td style="text-align: right; font-weight: 600;">₱{{ number_format($loan->amount, 2) }}</td>
                            <td>{{ Str::limit($loan->purpose, 30) }}</td>
                            <td style="text-align: center;">{{ $loan->payments_made }}/{{ $loan->terms }}</td>
                            <td style="text-align: right; font-weight: 600; color: #28a745;">₱{{ number_format($loan->paid_amount, 2) }}</td>
                            <td style="text-align: right; font-weight: 600; color: {{ $loan->remaining_balance > 0 ? '#dc3545' : '#28a745' }};">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                            <td style="text-align: center;">
                                @if($loan->status == 'pending')
                                    <span class="status-badge pending">Pending</span>
                                @elseif($loan->status == 'approved')
                                    <span class="status-badge approved">Active</span>
                                @elseif($loan->status == 'rejected')
                                    <span class="status-badge rejected">Rejected</span>
                                @else
                                    <span class="status-badge completed">Completed</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button onclick="toggleDetails('loan{{ $loan->id }}')" class="view-btn">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="loan{{ $loan->id }}" class="loan-details-row">
                            <td colspan="8">
                                <div class="loan-details-content">
                                    <div class="details-grid">
                                        <div>
                                            <h4 style="margin: 0 0 15px 0; font-size: 15px;">Loan Details</h4>
                                            <div style="margin-bottom: 10px;">
                                                <strong style="font-size: 13px; color: #666;">Full Purpose:</strong><br>
                                                <span style="font-size: 14px;">{{ $loan->purpose }}</span>
                                            </div>
                                            <div style="margin-bottom: 10px;">
                                                <strong style="font-size: 13px; color: #666;">Deduction per Cutoff:</strong><br>
                                                <span style="font-size: 14px;">₱{{ number_format($loan->monthly_deduction, 2) }}</span>
                                            </div>
                                            @if($loan->start_date)
                                            <div style="margin-bottom: 10px;">
                                                <strong style="font-size: 13px; color: #666;">Deduction Start Date:</strong><br>
                                                <span style="font-size: 14px;">{{ $loan->start_date->format('F d, Y') }}</span>
                                            </div>
                                            @endif
                                            @if($loan->status == 'rejected' && $loan->reason)
                                            <div style="background: #f8d7da; padding: 12px; border-radius: 4px; border-left: 3px solid #dc3545;">
                                                <strong style="font-size: 13px; color: #721c24;">Rejection Reason:</strong><br>
                                                <span style="font-size: 13px; color: #721c24;">{{ $loan->reason }}</span>
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 style="margin: 0 0 15px 0; font-size: 15px;">Payment History ({{ $loan->payments->count() }})</h4>
                                            @if($loan->payments->count() > 0)
                                            <div style="max-height: 200px; overflow-y: auto;">
                                                <table class="payment-history-table">
                                                    <thead>
                                                        <tr>
                                                            <th style="text-align: left;">Date</th>
                                                            <th style="text-align: right;">Amount</th>
                                                            <th style="text-align: left;">Source</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($loan->payments->sortByDesc('payment_date') as $payment)
                                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                                            <td style="text-align: right; font-weight: 600;">₱{{ number_format($payment->amount, 2) }}</td>
                                                            <td>
                                                                @if($payment->payment_type == 'automatic')
                                                                    <span style="color: #28a745;"><i class="fa-solid fa-robot"></i> Payroll Deduction</span>
                                                                @else
                                                                    <span style="color: #667eea;"><i class="fa-solid fa-hand-holding-dollar"></i> Manual Payment</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @else
                                            <div style="text-align: center; padding: 20px; color: #999; font-size: 13px;">
                                                <i class="fa-solid fa-inbox"></i><br>No payments yet
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                No loans yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
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
        function toggleDetails(id) {
            const row = document.getElementById(id);
            row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
        }
    </script>
</body>
</html>
