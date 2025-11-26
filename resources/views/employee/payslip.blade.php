<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - ACCUPAY INC.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/employee/payslip.css') }}">
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
            <li class="active"><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
            <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Payslip</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="payslip-container">
            <h2>Payslip Summary</h2>
            <table class="payslip-table">
                <thead>
                    <tr>
                        <th>Pay Period</th>
                        <th>Days Worked</th>
                        <th>Overtime (hrs)</th>
                        <th>Gross Pay</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $payslip)
                    <tr class="payslip-row" onclick="toggleDetails(this)">
                        <td>{{ $payslip->payroll->payroll_period ?? 'N/A' }}</td>
                        <td>{{ number_format($payslip->hours_worked / 8, 1) }}</td>
                        <td>{{ number_format($payslip->overtime_hours, 1) }}</td>
                        <td>₱{{ number_format($payslip->gross_pay, 2) }}</td>
                        <td>₱{{ number_format($payslip->total_deductions, 2) }}</td>
                        <td>₱{{ number_format($payslip->net_pay, 2) }}</td>
                        <td><span class="badge {{ strtolower($payslip->payroll->status ?? 'pending') }}">{{ ucfirst($payslip->payroll->status ?? 'pending') }}</span></td>
                        <td><a href="#" class="download-btn"><i class="fas fa-download"></i> PDF</a></td>
                    </tr>
                    <tr class="payslip-details">
                        <td colspan="8">
                            <div class="details-box">
                                <table class="computation-table">
                                    <tr>
                                        <th>Computation</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr>
                                        <td>Basic Pay</td>
                                        <td>₱{{ number_format($payslip->basic_salary, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Overtime ({{ number_format($payslip->overtime_hours, 1) }} hrs)</td>
                                        <td>₱{{ number_format($payslip->overtime_pay, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Allowances</td>
                                        <td>₱{{ number_format($payslip->allowances, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Bonuses</td>
                                        <td>₱{{ number_format($payslip->bonuses, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gross Pay</strong></td>
                                        <td><strong>₱{{ number_format($payslip->gross_pay, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>SSS</td>
                                        <td>₱{{ number_format($payslip->sss, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>PhilHealth</td>
                                        <td>₱{{ number_format($payslip->philhealth, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pag-IBIG</td>
                                        <td>₱{{ number_format($payslip->pagibig, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Deductions</strong></td>
                                        <td><strong>₱{{ number_format($payslip->total_deductions, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Net Pay</strong></td>
                                        <td><strong>₱{{ number_format($payslip->net_pay, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">No payslips found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

        function toggleDetails(row) {
            const next = row.nextElementSibling;
            if (next && next.classList.contains('payslip-details')) {
                next.style.display = next.style.display === 'table-row' ? 'none' : 'table-row';
            }
        }
    </script>
</body>
</html>
