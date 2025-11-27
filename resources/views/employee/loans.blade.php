<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loans</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
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
            <li><a href="{{ route('employee.support') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>
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
    <main class="main-content">
        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #155724;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #721c24;">
                <ul style="margin: 0; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div><div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Borrowed</div>
                        <div style="font-size: 28px; font-weight: 700;">₱{{ number_format($totalBorrowed, 2) }}</div></div>
                    <i class="fa-solid fa-hand-holding-dollar" style="font-size: 48px; opacity: 0.3;"></i>
                </div>
            </div>
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div><div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total Paid</div>
                        <div style="font-size: 28px; font-weight: 700;">₱{{ number_format($totalPaid, 2) }}</div></div>
                    <i class="fa-solid fa-circle-check" style="font-size: 48px; opacity: 0.3;"></i>
                </div>
            </div>
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 10px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div><div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Remaining Balance</div>
                        <div style="font-size: 28px; font-weight: 700;">₱{{ number_format($totalRemaining, 2) }}</div></div>
                    <i class="fa-solid fa-coins" style="font-size: 48px; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        @if($activeLoans->count() > 0)
        <section style="background: white; padding: 25px; margin-bottom: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; font-size: 20px; color: #333;"><i class="fa-solid fa-hourglass-half"></i> Active Loans ({{ $activeLoans->count() }})</h3>
            <div style="display: grid; gap: 20px;">
                @foreach($activeLoans as $loan)
                <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background: #fafafa;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                        <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">LOAN AMOUNT</div>
                            <div style="font-size: 22px; font-weight: 700; color: #667eea;">₱{{ number_format($loan->amount, 2) }}</div>
                            <div style="font-size: 13px; color: #666; margin-top: 4px;">{{ $loan->purpose }}</div></div>
                        <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">PER CUTOFF</div>
                            <div style="font-size: 18px; font-weight: 600; color: #333;">₱{{ number_format($loan->monthly_deduction, 2) }}</div>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">{{ $loan->terms }} cutoffs total</div></div>
                        <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">PAID</div>
                            <div style="font-size: 18px; font-weight: 600; color: #28a745;">₱{{ number_format($loan->paid_amount, 2) }}</div>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">{{ $loan->payments_made }}/{{ $loan->terms }} payments</div></div>
                        <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">BALANCE</div>
                            <div style="font-size: 18px; font-weight: 600; color: #dc3545;">₱{{ number_format($loan->remaining_balance, 2) }}</div>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">{{ $loan->remaining_payments }} left</div></div>
                    </div>
                    <div style="background: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 10px;">
                        <div style="background: linear-gradient(90deg, #28a745, #20c997); height: 100%; width: {{ $loan->progress_percentage }}%;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: #666;">
                        <span>{{ $loan->progress_percentage }}% complete</span>
                        <span>Deductions start: {{ $loan->start_date ? $loan->start_date->format('M d, Y') : 'Pending approval' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif
        <section style="background: white; padding: 25px; margin-bottom: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; font-size: 20px; color: #333;"><i class="fa-solid fa-plus-circle"></i> Request New Loan</h3>
            <form method="POST" action="{{ route('employee.loans.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                    <div><label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px;">Loan Amount (₱)</label>
                        <input type="number" name="amount" required min="100" max="100000" step="0.01" value="{{ old('amount') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <small style="color: #666; font-size: 12px;">Min: ₱100 | Max: ₱100,000</small></div>
                    <div><label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px;">Payment Terms (Cutoffs)</label>
                        <input type="number" name="terms" required min="1" max="24" value="{{ old('terms') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <small style="color: #666; font-size: 12px;">1-24 cutoffs (max 1 year)</small></div>
                    <div><label style="display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px;">Purpose</label>
                        <input type="text" name="purpose" required maxlength="255" value="{{ old('purpose') }}" placeholder="e.g., Emergency, Medical"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <small style="color: #666; font-size: 12px;">Brief description</small></div>
                </div>
                <div style="background: #e7f3ff; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #2196f3;">
                    <div style="font-size: 13px; color: #0d47a1; line-height: 1.6;">
                        <i class="fa-solid fa-info-circle"></i> <strong>How it works:</strong> Once approved, deductions will be automatically taken from your payroll each cutoff until fully paid. Admin will set the start date.
                    </div>
                </div>
                <button type="submit" style="padding: 12px 32px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; font-weight: 500;">
                    <i class="fa-solid fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </section>
        <section style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 20px 0; font-size: 20px; color: #333;"><i class="fa-solid fa-history"></i> All Loans</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead><tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Date</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Amount</th>
                        <th style="padding: 12px; text-align: left; font-size: 13px;">Purpose</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Terms</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Paid</th>
                        <th style="padding: 12px; text-align: right; font-size: 13px;">Balance</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                        <th style="padding: 12px; text-align: center; font-size: 13px;">Details</th>
                    </tr></thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; font-size: 13px;">{{ $loan->created_at->format('M d, Y') }}</td>
                            <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px;">₱{{ number_format($loan->amount, 2) }}</td>
                            <td style="padding: 12px; font-size: 13px;">{{ Str::limit($loan->purpose, 30) }}</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $loan->payments_made }}/{{ $loan->terms }}</td>
                            <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px; color: #28a745;">₱{{ number_format($loan->paid_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px; color: {{ $loan->remaining_balance > 0 ? '#dc3545' : '#28a745' }};">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                            <td style="padding: 12px; text-align: center; font-size: 13px;">
                                @if($loan->status == 'pending')<span style="padding: 4px 10px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px; font-weight: 500;">Pending</span>
                                @elseif($loan->status == 'approved')<span style="padding: 4px 10px; background: #d1e7dd; color: #0a3622; border-radius: 4px; font-size: 12px; font-weight: 500;">Active</span>
                                @elseif($loan->status == 'rejected')<span style="padding: 4px 10px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 12px; font-weight: 500;">Rejected</span>
                                @else<span style="padding: 4px 10px; background: #d1e7dd; color: #0a3622; border-radius: 4px; font-size: 12px; font-weight: 500;">Completed</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <button onclick="toggleDetails('loan{{ $loan->id }}')" style="padding: 6px 14px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="loan{{ $loan->id }}" style="display: none;">
                            <td colspan="8" style="padding: 20px; background: #f8f9fa;">
                                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
                                    <div><h4 style="margin: 0 0 15px 0; font-size: 15px;">Loan Details</h4>
                                        <div style="margin-bottom: 10px;"><strong style="font-size: 13px; color: #666;">Full Purpose:</strong><br><span style="font-size: 14px;">{{ $loan->purpose }}</span></div>
                                        <div style="margin-bottom: 10px;"><strong style="font-size: 13px; color: #666;">Deduction per Cutoff:</strong><br><span style="font-size: 14px;">₱{{ number_format($loan->monthly_deduction, 2) }}</span></div>
                                        @if($loan->start_date)<div style="margin-bottom: 10px;"><strong style="font-size: 13px; color: #666;">Deduction Start Date:</strong><br><span style="font-size: 14px;">{{ $loan->start_date->format('F d, Y') }}</span></div>@endif
                                        @if($loan->status == 'rejected' && $loan->reason)
                                        <div style="background: #f8d7da; padding: 12px; border-radius: 4px; border-left: 3px solid #dc3545;">
                                            <strong style="font-size: 13px; color: #721c24;">Rejection Reason:</strong><br><span style="font-size: 13px; color: #721c24;">{{ $loan->reason }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <div><h4 style="margin: 0 0 15px 0; font-size: 15px;">Payment History ({{ $loan->payments->count() }})</h4>
                                        @if($loan->payments->count() > 0)
                                        <div style="max-height: 200px; overflow-y: auto;">
                                            <table style="width: 100%; font-size: 12px;">
                                                <thead><tr style="background: #e9ecef;">
                                                    <th style="padding: 8px; text-align: left;">Date</th>
                                                    <th style="padding: 8px; text-align: right;">Amount</th>
                                                    <th style="padding: 8px; text-align: left;">Source</th>
                                                </tr></thead>
                                                <tbody>
                                                    @foreach($loan->payments->sortByDesc('payment_date') as $payment)
                                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                                        <td style="padding: 8px;">{{ $payment->payment_date->format('M d, Y') }}</td>
                                                        <td style="padding: 8px; text-align: right; font-weight: 600;">₱{{ number_format($payment->amount, 2) }}</td>
                                                        <td style="padding: 8px;">
                                                            @if($payment->payment_type == 'automatic')<span style="color: #28a745;"><i class="fa-solid fa-robot"></i> Payroll Deduction</span>
                                                            @else<span style="color: #667eea;"><i class="fa-solid fa-hand-holding-dollar"></i> Manual Payment</span>@endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <div style="text-align: center; padding: 20px; color: #999; font-size: 13px;"><i class="fa-solid fa-inbox"></i><br>No payments yet</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" style="text-align: center; padding: 40px; color: #6c757d; font-size: 14px;">
                            <i class="fa-solid fa-inbox" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px;"></i><br>No loans yet
                        </td></tr>
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
