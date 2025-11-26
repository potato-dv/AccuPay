<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
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
            <li class="active"><a href="{{ route('admin.attendance') }}"><i class="fa-solid fa-calendar-days"></i> <span class="menu-text">Manage Attendance</span></a></li>
            <li><a href="{{ route('admin.employees') }}"><i class="fa-solid fa-users"></i> <span class="menu-text">Employee List</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Manage Attendance</h1>
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
        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- QUICK ACTIONS -->
        <section class="quick-actions">
            <div class="table-header">
                <div class="employee-count">Total Records: {{ $attendances->total() }}</div>
                <button type="button" class="action-btn" onclick="showAddModal()">
                    <i class="fa-solid fa-plus"></i> Add Attendance Record
                </button>
            </div>
        </section>

        <!-- FILTER SECTION -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.attendance') }}" method="GET" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Employee</label>
                    <select name="employee_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->employee_id }} - {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-theme"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="{{ route('admin.attendance') }}" class="btn-delete"><i class="fa-solid fa-times"></i> Clear</a>
            </form>
        </div>

        <!-- ATTENDANCE TABLE -->
        <section class="attendance-list">
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours Worked</th>
                        <th>Overtime</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        <td><strong>{{ $attendance->employee->employee_id }}</strong></td>
                        <td>{{ $attendance->employee->full_name }}</td>
                        <td>{{ date('M d, Y', strtotime($attendance->date)) }}</td>
                        <td>{{ $attendance->time_in ? date('h:i A', strtotime($attendance->time_in)) : '-' }}</td>
                        <td>{{ $attendance->time_out ? date('h:i A', strtotime($attendance->time_out)) : '-' }}</td>
                        <td>{{ $attendance->hours_worked ?? '-' }} hrs</td>
                        <td>{{ $attendance->overtime_hours ?? '0' }} hrs</td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 500;
                                @if($attendance->status == 'present') background: #d4edda; color: #155724;
                                @elseif($attendance->status == 'absent') background: #f8d7da; color: #721c24;
                                @elseif($attendance->status == 'on-leave') background: #fff3cd; color: #856404;
                                @else background: #e2e3e5; color: #383d41; @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="action-btn btn-theme btn-sm" onclick="editAttendance({{ $attendance->id }}, '{{ $attendance->employee_id }}', '{{ $attendance->date }}', '{{ $attendance->time_in ? date('H:i', strtotime($attendance->time_in)) : '' }}', '{{ $attendance->time_out ? date('H:i', strtotime($attendance->time_out)) : '' }}', '{{ $attendance->status }}', '{{ addslashes($attendance->remarks ?? '') }}')">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('admin.attendance.delete', $attendance->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this attendance record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete btn-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                            No attendance records found. <button type="button" onclick="showAddModal()" style="color: #0057a0; background: none; border: none; text-decoration: underline; cursor: pointer;">Add first record</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($attendances->hasPages())
                <div style="margin-top: 20px;">
                    {{ $attendances->links() }}
                </div>
            @endif
        </section>
    </main>

    <!-- ADD ATTENDANCE MODAL -->
    <div id="addModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-top: 0; color: #0057a0;">Add Attendance Record</h3>
            
            <form action="{{ route('admin.attendance.store') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Employee *</label>
                    <select name="employee_id" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="">-- Select Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->employee_id }} - {{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Time In</label>
                        <input type="time" name="time_in" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Time Out</label>
                        <input type="time" name="time_out" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Status *</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="on-leave">On Leave</option>
                        <option value="late">Late</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Remarks</label>
                    <textarea name="remarks" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-theme" style="flex: 1;">
                        <i class="fa-solid fa-save"></i> Save Record
                    </button>
                    <button type="button" class="btn-delete" style="flex: 1;" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT ATTENDANCE MODAL -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-top: 0; color: #0057a0;">Edit Attendance Record</h3>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Employee</label>
                    <input type="text" id="editEmployee" readonly style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #e9ecef;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Date *</label>
                    <input type="date" id="editDate" name="date" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Time In</label>
                        <input type="time" id="editTimeIn" name="time_in" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Time Out</label>
                        <input type="time" id="editTimeOut" name="time_out" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="background: #e7f3ff; border-left: 4px solid #0057a0; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
                    <small style="color: #0057a0;">
                        <i class="fa-solid fa-info-circle"></i> <strong>Note:</strong> Hours worked and overtime will be automatically calculated based on the employee's work schedule when you save.
                    </small>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Status *</label>
                    <select id="editStatus" name="status" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="on-leave">On Leave</option>
                        <option value="late">Late</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Remarks</label>
                    <textarea id="editRemarks" name="remarks" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-theme" style="flex: 1;">
                        <i class="fa-solid fa-save"></i> Update Record
                    </button>
                    <button type="button" class="btn-delete" style="flex: 1;" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
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

        function showAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function editAttendance(id, employeeId, date, timeIn, timeOut, status, remarks) {
            document.getElementById('editEmployee').value = employeeId;
            document.getElementById('editDate').value = date;
            document.getElementById('editTimeIn').value = timeIn;
            document.getElementById('editTimeOut').value = timeOut;
            document.getElementById('editStatus').value = status;
            document.getElementById('editRemarks').value = remarks || '';
            document.getElementById('editForm').action = '/admin/attendance/update/' + id;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

</body>
</html>
