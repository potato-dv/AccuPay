<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employee - {{ $employee->full_name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        .employee-details {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 1200px;
            margin: 0 auto;
        }
        .detail-section {
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .detail-section:last-child {
            border-bottom: none;
        }
        .detail-section h3 {
            color: #0057a0;
            margin-bottom: 20px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .detail-section h3 i {
            font-size: 20px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-value {
            font-size: 15px;
            color: #333;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid #0057a0;
        }
        .detail-value.empty {
            color: #999;
            font-style: italic;
        }
        .employee-header {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #0057a0 0%, #003d73 100%);
            color: white;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .employee-header h2 {
            margin: 10px 0;
            font-size: 28px;
        }
        .employee-header .employee-id {
            font-size: 16px;
            opacity: 0.9;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        .badge.active {
            background: #28a745;
            color: white;
        }
        .badge.on-leave {
            background: #ffc107;
            color: #000;
        }
        .badge.terminated {
            background: #dc3545;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
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
            <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
            <li><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li class="active"><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Employee Details</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="employee-details">
            <!-- Employee Header -->
            <div class="employee-header">
                <div class="employee-id">{{ $employee->employee_id }}</div>
                <h2>{{ $employee->full_name }}</h2>
                <div>{{ $employee->position }} - {{ $employee->department }}</div>
                <span class="badge {{ $employee->status }}">{{ ucfirst($employee->status) }}</span>
            </div>

            <!-- Personal Information -->
            <div class="detail-section">
                <h3><i class="fas fa-user"></i> Personal Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">First Name</div>
                        <div class="detail-value">{{ $employee->first_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Middle Name</div>
                        <div class="detail-value {{ !$employee->middle_name ? 'empty' : '' }}">
                            {{ $employee->middle_name ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Last Name</div>
                        <div class="detail-value">{{ $employee->last_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Birthdate</div>
                        <div class="detail-value {{ !$employee->birthdate ? 'empty' : '' }}">
                            {{ $employee->birthdate ? $employee->birthdate->format('F d, Y') : 'Not provided' }}
                            @if($employee->birthdate)
                                <small style="color: #666;"> ({{ $employee->age }} years old)</small>
                            @endif
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Sex</div>
                        <div class="detail-value {{ !$employee->sex ? 'empty' : '' }}">
                            {{ $employee->sex ?? 'Not specified' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Civil Status</div>
                        <div class="detail-value">{{ $employee->civil_status }}</div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="detail-section">
                <h3><i class="fas fa-address-book"></i> Contact Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Email Address</div>
                        <div class="detail-value">{{ $employee->email }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value">{{ $employee->phone }}</div>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label">Address</div>
                        <div class="detail-value {{ !$employee->address ? 'empty' : '' }}">
                            {{ $employee->address ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Contact Name</div>
                        <div class="detail-value {{ !$employee->emergency_contact ? 'empty' : '' }}">
                            {{ $employee->emergency_contact ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Contact Phone</div>
                        <div class="detail-value {{ !$employee->emergency_phone ? 'empty' : '' }}">
                            {{ $employee->emergency_phone ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment Details -->
            <div class="detail-section">
                <h3><i class="fas fa-briefcase"></i> Employment Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Department</div>
                        <div class="detail-value">{{ $employee->department }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Position</div>
                        <div class="detail-value">{{ $employee->position }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Employment Type</div>
                        <div class="detail-value">{{ ucfirst($employee->employment_type) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Hire Date</div>
                        <div class="detail-value">{{ $employee->hire_date->format('F d, Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">{{ ucfirst($employee->status) }}</div>
                    </div>
                </div>
            </div>

            <!-- Work Schedule -->
            <div class="detail-section">
                <h3><i class="fas fa-calendar-alt"></i> Work Schedule</h3>
                <div class="detail-grid">
                    @if($employee->workSchedule)
                    <div class="detail-item">
                        <div class="detail-label">Schedule Name</div>
                        <div class="detail-value">{{ $employee->workSchedule->schedule_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Shift Hours</div>
                        <div class="detail-value">{{ $employee->workSchedule->formatted_work_hours }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Daily Hours</div>
                        <div class="detail-value">{{ $employee->workSchedule->daily_hours }} hours</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Weekly Hours</div>
                        <div class="detail-value">{{ $employee->workSchedule->weekly_hours }} hours</div>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label">Working Days</div>
                        <div class="detail-value">{{ implode(', ', $employee->workSchedule->working_days) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Overtime Allowed</div>
                        <div class="detail-value">{{ $employee->workSchedule->overtime_allowed ? 'Yes' : 'No' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Overtime Rate</div>
                        <div class="detail-value">{{ $employee->workSchedule->overtime_rate_multiplier * 100 }}%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Grace Period</div>
                        <div class="detail-value">{{ $employee->workSchedule->grace_period_minutes }} minutes</div>
                    </div>
                    @else
                    <div class="detail-item">
                        <div class="detail-value empty">No work schedule assigned</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Compensation -->
            <div class="detail-section">
                <h3><i class="fas fa-money-bill-wave"></i> Compensation</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Basic Salary</div>
                        <div class="detail-value">₱{{ number_format($employee->basic_salary, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Hourly Rate</div>
                        <div class="detail-value {{ !$employee->hourly_rate ? 'empty' : '' }}">
                            {{ $employee->hourly_rate ? '₱' . number_format($employee->hourly_rate, 2) : 'Not set' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Night Differential Rate</div>
                        <div class="detail-value {{ !$employee->night_differential_rate ? 'empty' : '' }}">
                            {{ $employee->night_differential_rate ? '₱' . number_format($employee->night_differential_rate, 2) : 'Not set' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Holiday Rate Multiplier</div>
                        <div class="detail-value">{{ $employee->holiday_rate_multiplier * 100 }}%</div>
                    </div>
                </div>
            </div>

            <!-- Government IDs -->
            <div class="detail-section">
                <h3><i class="fas fa-id-card"></i> Government IDs & Contributions</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">TIN (Tax ID Number)</div>
                        <div class="detail-value {{ !$employee->tax_id_number ? 'empty' : '' }}">
                            {{ $employee->tax_id_number ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">SSS Number</div>
                        <div class="detail-value {{ !$employee->sss_number ? 'empty' : '' }}">
                            {{ $employee->sss_number ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">PhilHealth Number</div>
                        <div class="detail-value {{ !$employee->philhealth_number ? 'empty' : '' }}">
                            {{ $employee->philhealth_number ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Pag-IBIG Number</div>
                        <div class="detail-value {{ !$employee->pagibig_number ? 'empty' : '' }}">
                            {{ $employee->pagibig_number ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Information -->
            <div class="detail-section">
                <h3><i class="fas fa-university"></i> Bank Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Bank Name</div>
                        <div class="detail-value {{ !$employee->bank_name ? 'empty' : '' }}">
                            {{ $employee->bank_name ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Account Number</div>
                        <div class="detail-value {{ !$employee->bank_account_number ? 'empty' : '' }}">
                            {{ $employee->bank_account_number ?? 'Not provided' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Credits -->
            <div class="detail-section">
                <h3><i class="fas fa-calendar-check"></i> Leave Credits (Annual)</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Vacation Leave</div>
                        <div class="detail-value">{{ $employee->vacation_leave_credits ?? 0 }} days</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Sick Leave</div>
                        <div class="detail-value">{{ $employee->sick_leave_credits ?? 0 }} days</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Emergency Leave</div>
                        <div class="detail-value">{{ $employee->emergency_leave_credits ?? 0 }} days</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="action-btn btn-theme">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Employee
                </a>
                <a href="{{ route('admin.employees') }}" class="action-btn" style="background: #6c757d;">
                    <i class="fa-solid fa-arrow-left"></i> Back to List
                </a>
            </div>
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
    </script>

</body>
</html>
