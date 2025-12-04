<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Loans</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<body>
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
            <li class="active"><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Manage Loans</h1>
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
        <section style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px; font-size: 18px;">Filter Loans</h3>
            <form method="GET" action="{{ route('admin.loans') }}">
                <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Search Employee</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Employee ID" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Status</label>
                        <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Active</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Filter</button>
                        <a href="{{ route('admin.loans') }}" style="padding: 8px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 14px;">Clear</a>
                    </div>
                </div>
            </form>
        </section>
        <section style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 20px; font-size: 20px;">Loan Requests</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                            <th style="padding: 12px; text-align: right; font-size: 13px;">Amount</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Purpose</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px;">Terms</th>
                            <th style="padding: 12px; text-align: right; font-size: 13px;">Per Cutoff</th>
                            <th style="padding: 12px; text-align: right; font-size: 13px;">Paid</th>
                            <th style="padding: 12px; text-align: right; font-size: 13px;">Balance</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px;">Status</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-size: 13px;">
                                    <div style="font-weight: 600;">{{ $loan->employee->first_name }} {{ $loan->employee->last_name }}</div>
                                    <div style="font-size: 11px; color: #666;">{{ $loan->employee->employee_id }}</div>
                                </td>
                                <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px;">₱{{ number_format($loan->amount, 2) }}</td>
                                <td style="padding: 12px; font-size: 13px;">{{ Str::limit($loan->purpose, 30) }}</td>
                                <td style="padding: 12px; text-align: center; font-size: 13px;">{{ $loan->payments_made }}/{{ $loan->terms }}</td>
                                <td style="padding: 12px; text-align: right; font-size: 13px;">₱{{ number_format($loan->monthly_deduction, 2) }}</td>
                                <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px; color: #28a745;">₱{{ number_format($loan->paid_amount, 2) }}</td>
                                <td style="padding: 12px; text-align: right; font-weight: 600; font-size: 13px; color: {{ $loan->remaining_balance > 0 ? '#dc3545' : '#28a745' }};">₱{{ number_format($loan->remaining_balance, 2) }}</td>
                                <td style="padding: 12px; text-align: center; font-size: 13px;">
                                    @if($loan->status == 'pending')
                                        <span style="padding: 4px 10px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px; font-weight: 500;">Pending</span>
                                    @elseif($loan->status == 'approved')
                                        <span style="padding: 4px 10px; background: #d1e7dd; color: #0a3622; border-radius: 4px; font-size: 12px; font-weight: 500;">Active</span>
                                    @elseif($loan->status == 'rejected')
                                        <span style="padding: 4px 10px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 12px; font-weight: 500;">Rejected</span>
                                    @else
                                        <span style="padding: 4px 10px; background: #e2e3e5; color: #41464b; border-radius: 4px; font-size: 12px; font-weight: 500;">Completed</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <button onclick="openModal('modal{{ $loan->id }}')" style="padding: 6px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                        <i class="fa-solid fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 40px; color: #6c757d; font-size: 14px;">No loan requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        
        @foreach($loans as $loan)
            <div id="modal{{ $loan->id }}" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
                <div style="background-color: white; margin: 3% auto; padding: 0; width: 90%; max-width: 800px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 8px 8px 0 0;">
                        <h3 style="margin: 0; font-size: 18px; color: #333;"><i class="fa-solid fa-hand-holding-dollar"></i> Loan #{{ $loan->id }} - {{ $loan->employee->first_name }} {{ $loan->employee->last_name }}</h3>
                        <button onclick="closeModal('modal{{ $loan->id }}')" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #6c757d; line-height: 1;">&times;</button>
                    </div>
                    <div style="padding: 25px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 4px;">LOAN AMOUNT</div>
                                <div style="font-size: 24px; font-weight: 700; color: #667eea;">₱{{ number_format($loan->amount, 2) }}</div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                                <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">TERMS</div>
                                    <div style="font-size: 14px; font-weight: 600;">{{ $loan->terms }} cutoffs</div></div>
                                <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">PER CUTOFF</div>
                                    <div style="font-size: 14px; font-weight: 600;">₱{{ number_format($loan->monthly_deduction, 2) }}</div></div>
                                <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">BALANCE</div>
                                    <div style="font-size: 14px; font-weight: 600; color: {{ $loan->remaining_balance > 0 ? '#dc3545' : '#28a745' }};">₱{{ number_format($loan->remaining_balance, 2) }}</div></div>
                            </div>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">PURPOSE</label>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 3px solid #007bff;">{{ $loan->purpose }}</div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">STATUS</div>
                                @if($loan->status == 'pending')<span style="padding: 4px 10px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px;">Pending</span>
                                @elseif($loan->status == 'approved')<span style="padding: 4px 10px; background: #d1e7dd; color: #0a3622; border-radius: 4px; font-size: 12px;">Active</span>
                                @elseif($loan->status == 'rejected')<span style="padding: 4px 10px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 12px;">Rejected</span>
                                @else<span style="padding: 4px 10px; background: #e2e3e5; color: #41464b; border-radius: 4px; font-size: 12px;">Completed</span>@endif
                            </div>
                            <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">REQUESTED</div>
                                <div style="font-size: 14px;">{{ $loan->created_at->format('M d, Y') }}</div></div>
                            <div><div style="font-size: 12px; color: #666; margin-bottom: 4px;">START DATE</div>
                                <div style="font-size: 14px;">{{ $loan->start_date ? $loan->start_date->format('M d, Y') : 'Not set' }}</div></div>
                        </div>
                        @if($loan->status == 'rejected' && $loan->reason)
                            <div style="background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; border-radius: 6px; margin-bottom: 20px;">
                                <label style="font-size: 12px; color: #721c24; font-weight: 600; display: block; margin-bottom: 6px;">REJECTION REASON</label>
                                <div style="font-size: 14px; color: #721c24;">{{ $loan->reason }}</div>
                            </div>
                        @endif
                        @if($loan->payments->count() > 0)
                        <div style="margin-bottom: 20px;">
                            <h4 style="margin: 0 0 15px 0; font-size: 15px;">Payment History ({{ $loan->payments->count() }} payments)</h4>
                            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                                <table style="width: 100%; font-size: 12px;">
                                    <thead><tr style="background: #f8f9fa; position: sticky; top: 0;">
                                        <th style="padding: 8px; text-align: left;">Date</th>
                                        <th style="padding: 8px; text-align: right;">Amount</th>
                                        <th style="padding: 8px; text-align: left;">Type</th>
                                        <th style="padding: 8px; text-align: left;">Notes</th>
                                    </tr></thead>
                                    <tbody>
                                        @foreach($loan->payments->sortByDesc('payment_date') as $payment)
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td style="padding: 8px;">{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td style="padding: 8px; text-align: right; font-weight: 600;">₱{{ number_format($payment->amount, 2) }}</td>
                                            <td style="padding: 8px;">
                                                @if($payment->payment_type == 'automatic')<span style="color: #28a745;"><i class="fa-solid fa-robot"></i> Payroll</span>
                                                @else<span style="color: #667eea;"><i class="fa-solid fa-hand-holding-dollar"></i> Manual</span>@endif
                                            </td>
                                            <td style="padding: 8px; font-size: 11px; color: #666;">{{ Str::limit($payment->notes, 40) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        
                        @if(in_array($loan->status, ['approved', 'completed']))
                        <div style="margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h4 style="margin: 0; font-size: 15px; color: #333;">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Loan Details
                                </h4>
                                <button type="button" onclick="toggleEditForm('editForm{{ $loan->id }}')" style="padding: 6px 14px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </button>
                            </div>
                            <div id="editForm{{ $loan->id }}" style="display: none;">
                                <form method="POST" action="{{ route('admin.loans.update', $loan->id) }}" onsubmit="return confirm('Update loan details? This will recalculate the per-cutoff deduction.');">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                        <div>
                                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">LOAN AMOUNT (₱)</label>
                                            <input type="number" name="amount" value="{{ $loan->amount }}" required min="1" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">TERMS (Cutoffs)</label>
                                            <input type="number" name="terms" value="{{ $loan->terms }}" required min="1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                        <div>
                                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">PAID AMOUNT (₱)</label>
                                            <input type="number" name="paid_amount" value="{{ $loan->paid_amount }}" required min="0" step="0.01" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                            <small style="color: #666; font-size: 11px; display: block; margin-top: 3px;">Adjust if needed to correct payment records</small>
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">START DATE</label>
                                            <input type="date" name="start_date" value="{{ $loan->start_date ? $loan->start_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 4px; margin-bottom: 15px;">
                                        <small style="color: #856404; font-size: 12px;">
                                            <i class="fa-solid fa-warning"></i> <strong>Note:</strong> Updating loan details will automatically recalculate the per-cutoff deduction. The remaining balance will be updated based on the new amount and paid amount.
                                        </small>
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" style="flex: 1; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                            <i class="fa-solid fa-save"></i> Save Changes
                                        </button>
                                        <button type="button" onclick="toggleEditForm('editForm{{ $loan->id }}')" style="flex: 1; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                            <i class="fa-solid fa-times"></i> Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                        
                        @if($loan->status == 'pending')
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
                            <form method="POST" action="{{ route('admin.loans.approve', $loan->id) }}" onsubmit="return confirm('Approve this loan?');">
                                @csrf
                                <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">START DATE</label>
                                <input type="date" name="start_date" required min="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; margin-bottom: 10px;">
                                <button type="submit" style="width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                    <i class="fa-solid fa-check"></i> Approve Loan
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.loans.reject', $loan->id) }}" onsubmit="return confirm('Reject this loan?');">
                                @csrf
                                <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">REASON</label>
                                <textarea name="reason" required rows="2" placeholder="Reason for rejection" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; margin-bottom: 10px; resize: vertical;"></textarea>
                                <button type="submit" style="width: 100%; padding: 10px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                    <i class="fa-solid fa-times"></i> Reject Loan
                                </button>
                            </form>
                        </div>
                        @endif
                        @if($loan->status == 'approved' && $loan->remaining_balance > 0)
                        <form method="POST" action="{{ route('admin.loans.payment', $loan->id) }}" style="margin-top: 20px;">
                            @csrf
                            <label style="font-size: 12px; color: #666; font-weight: 600; display: block; margin-bottom: 6px;">RECORD MANUAL PAYMENT</label>
                            <div style="display: grid; grid-template-columns: 2fr 3fr auto; gap: 10px;">
                                <input type="number" name="payment_amount" required min="0.01" step="0.01" max="{{ $loan->remaining_balance }}" placeholder="Amount" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                <input type="text" name="notes" placeholder="Payment notes (optional)" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                                <button type="submit" style="padding: 10px 24px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500; white-space: nowrap;">
                                    <i class="fa-solid fa-money-bill"></i> Record
                                </button>
                            </div>
                            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Max: ₱{{ number_format($loan->remaining_balance, 2) }}</small>
                        </form>
                        @endif
                        @if($loan->approved_at)
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                            <small style="color: #6c757d; font-size: 12px;">Processed by {{ $loan->approvedBy->name ?? 'Admin' }} on {{ $loan->approved_at->format('M d, Y h:i A') }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
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
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        function toggleEditForm(id) {
            const form = document.getElementById(id);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
        window.onclick = function(event) {
            if (event.target.style && event.target.style.position === 'fixed') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
