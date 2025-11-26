<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="icon" type="image/png" href="{{ asset('images/admin/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
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
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
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
            <h1>Edit Employee</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <section class="add-employee-form">
            <h2>Edit Employee Information</h2>
            @if($errors->any())
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Employee ID (Read-only) -->
                <div class="form-group">
                    <label for="employee_id">Employee ID</label>
                    <input type="text" id="employee_id" value="{{ $employee->employee_id }}" readonly style="background: #f0f0f0;">
                </div>

                <!-- Basic Info -->
                <div class="form-group">
                    <label for="first_name">First Name <span style="color: red;">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" placeholder="John" required>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name<span style="color: red;">*</span></label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}" placeholder="Smith">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span style="color: red;">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" placeholder="Doe" required>
                </div>
                <div class="form-group">
                    <label for="birthdate">Birthdate <span style="color: red;">*</span></label>
                    <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate', $employee->birthdate ? $employee->birthdate->format('Y-m-d') : '') }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
                </div>
                <div class="form-group">
                    <label for="sex">Sex <span style="color: red;">*</span></label>
                    <select id="sex" name="sex" required>
                        <option value="">Select</option>
                        <option value="Male" {{ old('sex', $employee->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex', $employee->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="civil_status">Civil Status <span style="color: red;">*</span></label>
                    <select id="civil_status" name="civil_status" required>
                        <option value="">Select</option>
                        <option value="Single" {{ old('civil_status', $employee->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                        <option value="Married" {{ old('civil_status', $employee->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Widowed" {{ old('civil_status', $employee->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="Separated" {{ old('civil_status', $employee->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department">Department <span style="color: red;">*</span></label>
                    <input type="text" id="department" name="department" value="{{ old('department', $employee->department) }}" placeholder="IT" required>
                </div>
                <div class="form-group">
                    <label for="position">Position <span style="color: red;">*</span></label>
                    <input type="text" id="position" name="position" value="{{ old('position', $employee->position) }}" placeholder="Developer" required>
                </div>
                <div class="form-group">
                    <label for="hire_date">Date Hired <span style="color: red;">*</span></label>
                    <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
                </div>

                <!-- Contact Info -->
                <div class="form-group">
                    <label for="email">Email <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" placeholder="john.doe@example.com" maxlength="255" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number <span style="color: red;">*</span></label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required>
                    <small style="color: #666; font-size: 12px;">10-11 digits only</small>
                </div>

                <!-- Employment Info -->
                <div class="form-group">
                    <label for="employment_type">Employment Type <span style="color: red;">*</span></label>
                    <select id="employment_type" name="employment_type" required>
                        <option value="">Select</option>
                        <option value="full-time" {{ old('employment_type', $employee->employment_type) == 'full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part-time" {{ old('employment_type', $employee->employment_type) == 'part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ old('employment_type', $employee->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status <span style="color: red;">*</span></label>
                    <select id="status" name="status" required>
                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on-leave" {{ old('status', $employee->status) == 'on-leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="basic_salary">Basic Salary</label>
                    <input type="number" step="0.01" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" placeholder="30000" min="0" max="999999999.99">
                </div>
                <div class="form-group">
                    <label for="hourly_rate">Hourly Rate (Optional)</label>
                    <input type="number" step="0.01" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $employee->hourly_rate) }}" placeholder="150" min="0" max="9999.99">
                </div>

                <!-- Work Schedule -->
                <div class="form-group">
                    <label for="work_schedule_id">Work Schedule <span style="color: red;">*</span></label>
                    <select id="work_schedule_id" name="work_schedule_id" required>
                        <option value="">Select Work Schedule</option>
                        @foreach(\App\Models\WorkSchedule::where('is_active', true)->get() as $schedule)
                            <option value="{{ $schedule->id }}" {{ old('work_schedule_id', $employee->work_schedule_id) == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->schedule_name }} - {{ implode(', ', array_map(fn($d) => substr($d, 0, 3), $schedule->working_days)) }} ({{ $schedule->weekly_hours }}hrs/week)
                            </option>
                        @endforeach
                    </select>
                    <small style="color: #666; font-size: 12px;">Current: {{ $employee->workSchedule ? $employee->work_schedule_summary : 'Not assigned' }}</small>
                </div>

                <!-- Government IDs -->
                <div class="form-group">
                    <label for="tax_id_number">TIN (Tax ID Number)</label>
                    <input type="text" id="tax_id_number" name="tax_id_number" value="{{ old('tax_id_number', $employee->tax_id_number) }}" placeholder="123-456-789-000">
                </div>
                <div class="form-group">
                    <label for="sss_number">SSS Number</label>
                    <input type="text" id="sss_number" name="sss_number" value="{{ old('sss_number', $employee->sss_number) }}" placeholder="12-3456789-0">
                </div>
                <div class="form-group">
                    <label for="philhealth_number">PhilHealth Number</label>
                    <input type="text" id="philhealth_number" name="philhealth_number" value="{{ old('philhealth_number', $employee->philhealth_number) }}" placeholder="12-345678901-2">
                </div>
                <div class="form-group">
                    <label for="pagibig_number">Pag-IBIG Number</label>
                    <input type="text" id="pagibig_number" name="pagibig_number" value="{{ old('pagibig_number', $employee->pagibig_number) }}" placeholder="1234-5678-9012">
                </div>

                <!-- Bank Information -->
                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" placeholder="BDO, BPI, Metrobank, etc.">
                </div>
                <div class="form-group">
                    <label for="bank_account_number">Bank Account Number</label>
                    <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $employee->bank_account_number) }}" placeholder="1234567890">
                </div>

                <!-- Leave Credits -->
                <div class="form-group">
                    <label for="vacation_leave_credits">Vacation Leave Credits (Annual)</label>
                    <input type="number" id="vacation_leave_credits" name="vacation_leave_credits" value="{{ old('vacation_leave_credits', $employee->vacation_leave_credits ?? 15) }}" placeholder="15">
                </div>
                <div class="form-group">
                    <label for="sick_leave_credits">Sick Leave Credits (Annual)</label>
                    <input type="number" id="sick_leave_credits" name="sick_leave_credits" value="{{ old('sick_leave_credits', $employee->sick_leave_credits ?? 15) }}" placeholder="15">
                </div>
                <div class="form-group">
                    <label for="emergency_leave_credits">Emergency Leave Credits (Annual)</label>
                    <input type="number" id="emergency_leave_credits" name="emergency_leave_credits" value="{{ old('emergency_leave_credits', $employee->emergency_leave_credits ?? 5) }}" placeholder="5">
                </div>

                <!-- Address / Emergency Contact -->
                <div class="form-group">
                    <label for="address">Address <span style="color: red;">*</span></label>
                    <input type="text" id="address" name="address" value="{{ old('address', $employee->address) }}" placeholder="123 Main St, City" maxlength="500" required>
                </div>
                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact Name <span style="color: red;">*</span></label>
                    <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" placeholder="Jane Doe" maxlength="255" required>
                </div>
                <div class="form-group">
                    <label for="emergency_phone">Emergency Contact Phone <span style="color: red;">*</span></label>
                    <input type="tel" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required>
                    <small style="color: #666; font-size: 12px;">10-11 digits only</small>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn-theme">Save Changes</button>
                    <a href="{{ route('admin.employees') }}" class="btn-delete" style="text-decoration: none; text-align: center;">Cancel</a>
                </div>
            </form>
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
    </script>

</body>
</html>
