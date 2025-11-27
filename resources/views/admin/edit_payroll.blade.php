<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payroll - {{ $payroll->payroll_period }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
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
            <li class="active"><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Edit Payroll</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <main class="main-content">
        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div style="margin-bottom: 20px;">
            <a href="{{ route('admin.payroll.view', $payroll->id) }}" class="btn-delete" style="text-decoration: none; display: inline-block;">
                <i class="fa-solid fa-arrow-left"></i> Back to View
            </a>
        </div>

        <form action="{{ route('admin.payroll.update', $payroll->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Payroll Details -->
            <div style="background: white; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 15px 0; color: #333; font-size: 16px; border-bottom: 2px solid #0057a0; padding-bottom: 10px;">Payroll Information</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 13px;">Payroll Period *</label>
                        <input type="text" name="payroll_period" value="{{ $payroll->payroll_period }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 13px;">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ $payroll->payment_date->format('Y-m-d') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 13px;">Period Start *</label>
                        <input type="date" name="period_start" value="{{ $payroll->period_start->format('Y-m-d') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 13px;">Period End *</label>
                        <input type="date" name="period_end" value="{{ $payroll->period_end->format('Y-m-d') }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333; font-size: 13px;">Notes</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 13px;">{{ $payroll->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Payroll Summary Totals -->
            @php
                $totalGrossPay = $payroll->payslips->sum('gross_pay');
                $totalDeductions = $payroll->payslips->sum('total_deductions');
                $totalNetPay = $payroll->payslips->sum('net_pay');
                $totalLoanDeductions = $payroll->payslips->sum('loan_deductions');
            @endphp
            <div style="background: white; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; color: #333; font-size: 16px; border-bottom: 2px solid #0057a0; padding-bottom: 10px;">
                    <i class="fa-solid fa-calculator"></i> Payroll Summary
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div style="padding: 15px; background: #f8f9fa; border-left: 4px solid #0057a0; border-radius: 4px;">
                        <div style="color: #666; font-size: 12px; margin-bottom: 5px;">Total Gross Pay</div>
                        <div style="color: #333; font-size: 20px; font-weight: 600;">₱{{ number_format($totalGrossPay, 2) }}</div>
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-left: 4px solid #dc3545; border-radius: 4px;">
                        <div style="color: #666; font-size: 12px; margin-bottom: 5px;">Total Deductions</div>
                        <div style="color: #333; font-size: 20px; font-weight: 600;">₱{{ number_format($totalDeductions, 2) }}</div>
                        @if($totalLoanDeductions > 0)
                        <div style="color: #999; font-size: 11px; margin-top: 3px;">incl. ₱{{ number_format($totalLoanDeductions, 2) }} loans</div>
                        @endif
                    </div>
                    <div style="padding: 15px; background: #f8f9fa; border-left: 4px solid #28a745; border-radius: 4px;">
                        <div style="color: #666; font-size: 12px; margin-bottom: 5px;">Total Net Pay</div>
                        <div style="color: #28a745; font-size: 20px; font-weight: 600;">₱{{ number_format($totalNetPay, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Employee Payslips -->
            <h3 style="margin-bottom: 15px; color: #333;">Employee Payslips</h3>
            <div style="background: white; border: 1px solid #ddd; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                                <th style="padding: 10px; text-align: left; font-weight: 600; color: #333;">Employee</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Hours</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">OT Hrs</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Basic Salary</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">OT Pay</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Allowances</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Bonuses</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333; background: #e3f2fd;">Gross Pay</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">SSS</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">PhilHealth</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Pag-IBIG</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Tax</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Late Ded.</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Loan Ded.</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333;">Other Ded.</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; color: #333; background: #e8f5e9;">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payroll->payslips as $payslip)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">
                                    <div style="font-weight: 600; color: #333;">{{ $payslip->employee->employee_id }}</div>
                                    <div style="font-size: 12px; color: #666;">{{ $payslip->employee->full_name }}</div>
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][hours_worked]" 
                                           value="{{ $payslip->hours_worked }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 70px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][overtime_hours]" 
                                           value="{{ $payslip->overtime_hours }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 70px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][basic_salary]" 
                                           value="{{ $payslip->basic_salary }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][overtime_pay]" 
                                           value="{{ $payslip->overtime_pay }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][allowances]" 
                                           value="{{ $payslip->allowances }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][bonuses]" 
                                           value="{{ $payslip->bonuses }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px; background: #e3f2fd;">
                                    <strong style="color: #0057a0;">₱{{ number_format($payslip->gross_pay, 2) }}</strong>
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][sss]" 
                                           value="{{ $payslip->sss }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][philhealth]" 
                                           value="{{ $payslip->philhealth }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][pagibig]" 
                                           value="{{ $payslip->pagibig }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][tax]" 
                                           value="{{ $payslip->tax }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][late_deduction]" 
                                           value="{{ $payslip->late_deduction ?? 0 }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][loan_deductions]" 
                                           value="{{ $payslip->loan_deductions ?? 0 }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;"
                                           title="Employee active loans: {{ $payslip->employee->activeLoans()->count() }}">
                                </td>
                                <td style="padding: 10px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][other_deductions]" 
                                           value="{{ $payslip->other_deductions }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 90px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                                </td>
                                <td style="padding: 10px; background: #e8f5e9;">
                                    <strong style="color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 15px; padding: 12px; background: #f8f9fa; border-left: 4px solid #0057a0; border-radius: 4px;">
                    <p style="margin: 0; color: #666; font-size: 13px;">
                        <i class="fa-solid fa-info-circle"></i> <strong>Note:</strong> Gross Pay and Net Pay will be recalculated when you save. Edit earnings or deductions and click Save Changes.
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ route('admin.payroll.view', $payroll->id) }}" class="btn-delete" style="text-decoration: none; display: inline-block;">
                    <i class="fa-solid fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-theme">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </div>
        </form>
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
    </script>
</body>
</html>
