<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts Management</title>
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
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li class="active"><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>User Accounts Management</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
    </header>

    <!-- MAIN CONTENT -->
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

        <!-- QUICK ACTIONS -->
        <section class="quick-actions" style="width: 100%;">
            <div class="table-header">
                <h2 style="margin: 0; color: #0057a0;">
                    <i class="fa-solid fa-check-circle" style="color: #28a745;"></i> Employees with User Accounts
                </h2>
                <button type="button" class="action-btn" onclick="showCreateAccountModal()">
                    <i class="fa-solid fa-user-plus"></i> Create Account for Employee
                </button>
            </div>
        </section>

        <!-- Employees WITH Accounts -->
        <section class="employee-list" style="width: 100%;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $employeesWithAccounts = $employees->filter(fn($e) => $e->user_id);
                    @endphp
                    @forelse($employeesWithAccounts as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <form action="{{ route('admin.users.resetPassword', $employee->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Reset password to Employee ID ({{ $employee->employee_id }})?');">
                                    @csrf
                                    <button type="submit" class="btn-sm" style="background: #ffa500; color: white; border: none; cursor: pointer;">
                                        <i class="fa-solid fa-key"></i> Reset Password
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.delete', $employee->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user account? The employee record will remain.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm" style="background: #e74c3c; color: white; border: none; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            No employees have user accounts yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- QUICK ACTIONS -->
        <section class="quick-actions" style="margin-top: 40px; width: 100%;">
            <div class="table-header">
                <h2 style="margin: 0; color: #0057a0;">
                    <i class="fa-solid fa-exclamation-circle" style="color: #dc3545;"></i> Employees without User Accounts
                </h2>
            </div>
        </section>

        <!-- Employees WITHOUT Accounts -->
        <section class="employee-list" style="width: 100%;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $employeesWithoutAccounts = $employees->filter(fn($e) => !$e->user_id);
                    @endphp
                    @forelse($employeesWithoutAccounts as $employee)
                    <tr>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                        <td>{{ $employee->full_name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <button type="button" class="btn-sm" style="background: #00a86b; color: white; border: none; cursor: pointer;" onclick="showPasswordModal({{ $employee->id }}, '{{ $employee->employee_id }}', '{{ $employee->full_name }}', '{{ $employee->email }}')">
                                    <i class="fa-solid fa-user-plus"></i> Create Account
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                            All employees have user accounts!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <!-- Create Account Selection Modal -->
    <div id="createAccountModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
            <h3 style="margin-top: 0; color: #0057a0;">Create Account for Employee</h3>
            <p style="margin-bottom: 20px; color: #666;">Select an employee to create a user account</p>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Select Employee</label>
                <select id="employeeSelect" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 15px;" onchange="selectEmployee()">
                    <option value="">-- Select Employee --</option>
                    @foreach($employeesWithoutAccounts as $employee)
                        <option value="{{ $employee->id }}" 
                                data-empid="{{ $employee->employee_id }}"
                                data-name="{{ $employee->full_name }}"
                                data-email="{{ $employee->email }}">
                            {{ $employee->employee_id }} - {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="employeeDetails" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <p style="margin: 5px 0;"><strong>Employee ID:</strong> <span id="detailEmpId"></span></p>
                <p style="margin: 5px 0;"><strong>Name:</strong> <span id="detailName"></span></p>
                <p style="margin: 5px 0;"><strong>Email:</strong> <span id="detailEmail"></span></p>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-theme" style="flex: 1;" onclick="proceedToPassword()" id="proceedBtn" disabled>Continue</button>
                <button type="button" class="btn-delete" style="flex: 1;" onclick="closeCreateAccountModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
            <h3 style="margin-top: 0; color: #0057a0;">Create User Account</h3>
            
            <!-- Employee Information (Editable) -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h4 style="margin-top: 0; color: #0057a0; font-size: 16px;">Account Information</h4>
                
                <form id="createAccountForm" method="POST">
                    @csrf
                    
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Employee ID</label>
                        <input type="text" id="formEmpId" readonly style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #e9ecef;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Full Name</label>
                        <input type="text" id="formName" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <small style="color: #666;">You can edit this if needed</small>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Email</label>
                        <input type="email" id="formEmail" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <small style="color: #666;">You can edit this if needed</small>
                    </div>

                    <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                        <i class="fa-solid fa-info-circle" style="color: #856404;"></i>
                        <small style="color: #856404;">
                            <strong>Note:</strong> The default password will be set to the employee's ID. The employee can change it after logging in.
                        </small>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn-theme" style="flex: 1;">
                            <i class="fa-solid fa-user-check"></i> Create Account
                        </button>
                        <button type="button" class="btn-delete" style="flex: 1;" onclick="closePasswordModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
php artisan migrate:fresh --seedphp artisan migrate:fresh --seedphp artisan migrate:fresh --seed
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

        let selectedEmployeeId = null;

        function showCreateAccountModal() {
            document.getElementById('createAccountModal').style.display = 'flex';
            document.getElementById('employeeSelect').value = '';
            document.getElementById('employeeDetails').style.display = 'none';
            document.getElementById('proceedBtn').disabled = true;
        }

        function closeCreateAccountModal() {
            document.getElementById('createAccountModal').style.display = 'none';
            document.getElementById('employeeSelect').value = '';
            selectedEmployeeId = null;
        }

        function selectEmployee() {
            const select = document.getElementById('employeeSelect');
            const option = select.options[select.selectedIndex];
            
            if (select.value) {
                selectedEmployeeId = select.value;
                const empId = option.getAttribute('data-empid');
                const name = option.getAttribute('data-name');
                const email = option.getAttribute('data-email');
                
                document.getElementById('detailEmpId').textContent = empId;
                document.getElementById('detailName').textContent = name;
                document.getElementById('detailEmail').textContent = email;
                document.getElementById('employeeDetails').style.display = 'block';
                document.getElementById('proceedBtn').disabled = false;
            } else {
                document.getElementById('employeeDetails').style.display = 'none';
                document.getElementById('proceedBtn').disabled = true;
                selectedEmployeeId = null;
            }
        }

        function proceedToPassword() {
            if (!selectedEmployeeId) return;
            
            const select = document.getElementById('employeeSelect');
            const option = select.options[select.selectedIndex];
            const empId = option.getAttribute('data-empid');
            const name = option.getAttribute('data-name');
            const email = option.getAttribute('data-email');
            
            showPasswordModal(selectedEmployeeId, empId, name, email);
            closeCreateAccountModal();
        }

        function showPasswordModal(employeeId, empId, employeeName, employeeEmail) {
            document.getElementById('formEmpId').value = empId;
            document.getElementById('formName').value = employeeName;
            document.getElementById('formEmail').value = employeeEmail;
            document.getElementById('createAccountForm').action = '/admin/user-accounts/create/' + employeeId;
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('createAccountForm').reset();
        }
    </script>

</body>
</html>
