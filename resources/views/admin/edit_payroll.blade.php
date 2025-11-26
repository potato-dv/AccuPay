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
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
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
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
                <h2 style="margin-top: 0; color: #0057a0;">Payroll Information</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Payroll Period *</label>
                        <input type="text" name="payroll_period" value="{{ $payroll->payroll_period }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ $payroll->payment_date->format('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Period Start *</label>
                        <input type="date" name="period_start" value="{{ $payroll->period_start->format('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Period End *</label>
                        <input type="date" name="period_end" value="{{ $payroll->period_end->format('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Notes</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;">{{ $payroll->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Payroll Summary Totals -->
            @php
                $totalGrossPay = $payroll->payslips->sum('gross_pay');
                $totalDeductions = $payroll->payslips->sum('total_deductions');
                $totalNetPay = $payroll->payslips->sum('net_pay');
            @endphp
            <div style="background: linear-gradient(135deg, #0057a0 0%, #0080d0 100%); padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: white; font-size: 20px;">
                    <i class="fa-solid fa-calculator"></i> Payroll Summary
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                        <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Gross Pay</div>
                        <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalGrossPay, 2) }}</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                        <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Deductions</div>
                        <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalDeductions, 2) }}</div>
                    </div>
                    <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px; backdrop-filter: blur(10px);">
                        <div style="color: rgba(255,255,255,0.9); font-size: 13px; margin-bottom: 8px;">Total Net Pay</div>
                        <div style="color: white; font-size: 28px; font-weight: bold;">₱{{ number_format($totalNetPay, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Employee Payslips -->
            <h3 style="margin-bottom: 15px;">Employee Payslips</h3>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #0057a0; color: white;">
                                <th style="padding: 12px; text-align: left;">Employee</th>
                                <th style="padding: 12px; text-align: right;">Hours</th>
                                <th style="padding: 12px; text-align: right;">OT Hours</th>
                                <th style="padding: 12px; text-align: right;">Basic Salary</th>
                                <th style="padding: 12px; text-align: right;">OT Pay</th>
                                <th style="padding: 12px; text-align: right;">Allowances</th>
                                <th style="padding: 12px; text-align: right;">Bonuses</th>
                                <th style="padding: 12px; text-align: right;">Gross Pay</th>
                                <th style="padding: 12px; text-align: right;">SSS</th>
                                <th style="padding: 12px; text-align: right;">PhilHealth</th>
                                <th style="padding: 12px; text-align: right;">Pag-IBIG</th>
                                <th style="padding: 12px; text-align: right;">Tax</th>
                                <th style="padding: 12px; text-align: right;">Other Ded.</th>
                                <th style="padding: 12px; text-align: right;">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payroll->payslips as $payslip)
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td style="padding: 12px;">
                                    <strong>{{ $payslip->employee->employee_id }}</strong><br>
                                    <small>{{ $payslip->employee->full_name }}</small>
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][hours_worked]" 
                                           value="{{ $payslip->hours_worked }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 80px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][overtime_hours]" 
                                           value="{{ $payslip->overtime_hours }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 80px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][basic_salary]" 
                                           value="{{ $payslip->basic_salary }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][overtime_pay]" 
                                           value="{{ $payslip->overtime_pay }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][allowances]" 
                                           value="{{ $payslip->allowances }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           name="payslips[{{ $payslip->id }}][bonuses]" 
                                           value="{{ $payslip->bonuses }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px; background: #f0f8ff;">
                                    <strong style="color: #0057a0;">₱{{ number_format($payslip->gross_pay, 2) }}</strong>
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           class="payslip-input" 
                                           name="payslips[{{ $payslip->id }}][sss]" 
                                           value="{{ $payslip->sss }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           class="payslip-input" 
                                           name="payslips[{{ $payslip->id }}][philhealth]" 
                                           value="{{ $payslip->philhealth }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           class="payslip-input" 
                                           name="payslips[{{ $payslip->id }}][pagibig]" 
                                           value="{{ $payslip->pagibig }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           class="payslip-input" 
                                           name="payslips[{{ $payslip->id }}][tax]" 
                                           value="{{ $payslip->tax }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px;">
                                    <input type="number" 
                                           class="payslip-input" 
                                           name="payslips[{{ $payslip->id }}][other_deductions]" 
                                           value="{{ $payslip->other_deductions }}" 
                                           step="0.01" 
                                           min="0" 
                                           style="width: 100px; padding: 6px; border: 1px solid #ccc; border-radius: 4px; text-align: right;">
                                </td>
                                <td style="padding: 12px; background: #e8f5e9;">
                                    <strong style="color: #28a745;">₱{{ number_format($payslip->net_pay, 2) }}</strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        <i class="fa-solid fa-info-circle"></i> <strong>Note:</strong> Gross Pay and Net Pay will be calculated when you save. Change any earnings or deductions and click Save Changes.
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
