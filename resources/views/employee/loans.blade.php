<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loans - ACCUPAY INC.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/loans.css') }}">
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
