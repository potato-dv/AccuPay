<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payslip</title>
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
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li class="active"><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Payslip Management</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <main class="main-content">
        <!-- Filter -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.payslip') }}" method="GET" style="display: flex; gap: 15px; align-items: end;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Filter by Payroll</label>
                    <select name="payroll_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="">All Payrolls</option>
                        @foreach($payrolls as $pr)
                            <option value="{{ $pr->id }}" {{ request('payroll_id') == $pr->id ? 'selected' : '' }}>
                                {{ $pr->payroll_period }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-theme"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="{{ route('admin.payslip') }}" class="btn-delete"><i class="fa-solid fa-times"></i> Clear</a>
            </form>
        </div>

        <section class="employee-list">
            <div class="table-header" style="margin-bottom: 15px;">
                <div class="employee-count">Total Payslips: {{ $payslips->total() }}</div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Payroll Period</th>
                        <th>Hours</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $payslip)
                    <tr>
                        <td><strong>{{ $payslip->employee->employee_id }}</strong></td>
                        <td>{{ $payslip->employee->full_name }}</td>
                        <td>{{ $payslip->employee->department }}</td>
                        <td>{{ $payslip->payroll->payroll_period }}</td>
                        <td>{{ $payslip->hours_worked }} hrs</td>
                        <td>₱{{ number_format($payslip->gross_pay, 2) }}</td>
                        <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        <td><strong>₱{{ number_format($payslip->net_pay, 2) }}</strong></td>
                        <td>
                            <button type="button" class="action-btn btn-theme btn-sm" onclick="viewPayslip(
                                '{{ $payslip->employee->employee_id }}',
                                '{{ $payslip->employee->full_name }}',
                                '{{ $payslip->employee->department }}',
                                '{{ $payslip->employee->position }}',
                                '{{ $payslip->payroll->payroll_period }}',
                                '{{ $payslip->hours_worked }}',
                                '{{ $payslip->overtime_hours }}',
                                '{{ number_format($payslip->basic_salary, 2) }}',
                                '{{ number_format($payslip->overtime_pay, 2) }}',
                                '{{ number_format($payslip->gross_pay, 2) }}',
                                '{{ number_format($payslip->sss, 2) }}',
                                '{{ number_format($payslip->philhealth, 2) }}',
                                '{{ number_format($payslip->pagibig, 2) }}',
                                '{{ number_format($payslip->tax, 2) }}',
                                '{{ number_format($payslip->total_deductions, 2) }}',
                                '{{ number_format($payslip->net_pay, 2) }}'
                            )">
                                <i class="fa-solid fa-eye"></i> View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fa-solid fa-file-lines" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                            No payslips generated yet. Go to <a href="{{ route('admin.payroll') }}" style="color: #0057a0;">Payroll</a> to generate.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($payslips->hasPages())
                <div style="margin-top: 20px;">
                    {{ $payslips->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- View Payslip Modal -->
    <div id="viewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; overflow-y: auto;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 700px; width: 90%; margin: 20px auto;">
            <h3 style="margin-top: 0; color: #0057a0; text-align: center;">PAYSLIP</h3>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <strong>Employee ID:</strong> <span id="viewEmpId"></span>
                    </div>
                    <div>
                        <strong>Payroll Period:</strong> <span id="viewPeriod"></span>
                    </div>
                    <div>
                        <strong>Name:</strong> <span id="viewName"></span>
                    </div>
                    <div>
                        <strong>Department:</strong> <span id="viewDept"></span>
                    </div>
                    <div style="grid-column: span 2;">
                        <strong>Position:</strong> <span id="viewPos"></span>
                    </div>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background: #0057a0; color: white;">
                    <td colspan="2" style="padding: 10px; font-weight: bold;">EARNINGS</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Hours Worked</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;"><span id="viewHours"></span> hrs</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Overtime Hours</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;"><span id="viewOT"></span> hrs</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Basic Salary</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewBasic"></span></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Overtime Pay</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewOTPay"></span></td>
                </tr>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td style="padding: 10px;">GROSS PAY</td>
                    <td style="padding: 10px; text-align: right;">₱<span id="viewGross"></span></td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background: #dc3545; color: white;">
                    <td colspan="2" style="padding: 10px; font-weight: bold;">DEDUCTIONS</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">SSS</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewSSS"></span></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">PhilHealth</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewPhil"></span></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Pag-IBIG</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewPagibig"></span></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">Withholding Tax</td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">₱<span id="viewTax"></span></td>
                </tr>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td style="padding: 10px;">TOTAL DEDUCTIONS</td>
                    <td style="padding: 10px; text-align: right;">₱<span id="viewTotalDed"></span></td>
                </tr>
            </table>

            <div style="background: #28a745; color: white; padding: 15px; border-radius: 6px; text-align: center; margin-bottom: 20px;">
                <div style="font-size: 14px; margin-bottom: 5px;">NET PAY</div>
                <div style="font-size: 32px; font-weight: bold;">₱<span id="viewNet"></span></div>
            </div>

            <button type="button" class="btn-theme" style="width: 100%;" onclick="closeViewModal()">Close</button>
        </div>
    </div>

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

        function viewPayslip(empId, name, dept, pos, period, hours, ot, basic, otPay, gross, sss, phil, pagibig, tax, totalDed, net) {
            document.getElementById('viewEmpId').textContent = empId;
            document.getElementById('viewName').textContent = name;
            document.getElementById('viewDept').textContent = dept;
            document.getElementById('viewPos').textContent = pos;
            document.getElementById('viewPeriod').textContent = period;
            document.getElementById('viewHours').textContent = hours;
            document.getElementById('viewOT').textContent = ot;
            document.getElementById('viewBasic').textContent = basic;
            document.getElementById('viewOTPay').textContent = otPay;
            document.getElementById('viewGross').textContent = gross;
            document.getElementById('viewSSS').textContent = sss;
            document.getElementById('viewPhil').textContent = phil;
            document.getElementById('viewPagibig').textContent = pagibig;
            document.getElementById('viewTax').textContent = tax;
            document.getElementById('viewTotalDed').textContent = totalDed;
            document.getElementById('viewNet').textContent = net;
            document.getElementById('viewModal').style.display = 'flex';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
    </script>

</body>
</html>
