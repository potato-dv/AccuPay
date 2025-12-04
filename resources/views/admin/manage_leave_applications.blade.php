<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Applications</title>
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
            <li><a href="{{ route('admin.employee.records') }}"><i class="fa-solid fa-folder-open"></i> <span class="menu-text">Employee Records</span></a></li>
            <li><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li class="active"><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Leave Requests Management</h1>
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

        <!-- Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Total Requests</h3>
                <p style="margin: 0; font-size: 32px; font-weight: bold; color: #0057a0;">{{ $leaves->count() }}</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Pending</h3>
                <p style="margin: 0; font-size: 32px; font-weight: bold; color: #f39c12;">{{ $leaves->where('status', 'pending')->count() }}</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Approved</h3>
                <p style="margin: 0; font-size: 32px; font-weight: bold; color: #28a745;">{{ $leaves->where('status', 'approved')->count() }}</p>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Rejected</h3>
                <p style="margin: 0; font-size: 32px; font-weight: bold; color: #dc3545;">{{ $leaves->where('status', 'rejected')->count() }}</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.leave') }}" method="GET" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 180px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Status</label>
                    <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 180px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Leave Type</label>
                    <select name="leave_type" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                        <option value="">All Types</option>
                        <option value="sick" {{ request('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                        <option value="vacation" {{ request('leave_type') == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                        <option value="emergency" {{ request('leave_type') == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                        <option value="maternity" {{ request('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                        <option value="paternity" {{ request('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                    </select>
                </div>
                <button type="submit" class="btn-theme"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="{{ route('admin.leave') }}" class="btn-delete"><i class="fa-solid fa-times"></i> Clear</a>
            </form>
        </div>

        <!-- Leave Requests Table -->
        <section class="employee-list">
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td><strong>{{ $leave->employee->employee_id }}</strong></td>
                        <td>{{ $leave->employee->full_name }}</td>
                        <td>{{ $leave->employee->department }}</td>
                        <td>{{ ucfirst($leave->leave_type) }}</td>
                        <td>{{ date('M d, Y', strtotime($leave->start_date)) }}</td>
                        <td>{{ date('M d, Y', strtotime($leave->end_date)) }}</td>
                        <td>{{ $leave->days_count }} days</td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 500;
                                @if($leave->status == 'approved') background: #d4edda; color: #155724;
                                @elseif($leave->status == 'rejected') background: #f8d7da; color: #721c24;
                                @else background: #fff3cd; color: #856404; @endif">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                <button type="button" class="btn-sm" style="background: #17a2b8; color: white; border: none; cursor: pointer;" onclick="viewLeave({{ $leave->id }}, '{{ $leave->employee->employee_id }}', '{{ $leave->employee->full_name }}', '{{ ucfirst($leave->leave_type) }}', '{{ date('M d, Y', strtotime($leave->start_date)) }}', '{{ date('M d, Y', strtotime($leave->end_date)) }}', '{{ $leave->days_count }}', '{{ $leave->reason }}', '{{ $leave->status }}', '{{ $leave->admin_remarks }}')">
                                    <i class="fa-solid fa-eye"></i> View
                                </button>
                                @if($leave->status == 'pending')
                                    <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-sm" style="background: #28a745; color: white; border: none; cursor: pointer;" onclick="return confirm('Approve this leave request?')">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn-sm" style="background: #e74c3c; color: white; border: none; cursor: pointer;" onclick="showRejectModal({{ $leave->id }})">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                            No leave requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <!-- View Leave Modal -->
    <div id="viewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%;">
            <h3 style="margin-top: 0; color: #0057a0;">Leave Request Details</h3>
            
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="margin-bottom: 15px;">
                    <strong>Employee ID:</strong> <span id="viewEmpId"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Employee Name:</strong> <span id="viewEmpName"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Leave Type:</strong> <span id="viewLeaveType"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Period:</strong> <span id="viewStartDate"></span> to <span id="viewEndDate"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Total Days:</strong> <span id="viewDays"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Status:</strong> <span id="viewStatus"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Reason:</strong><br>
                    <p id="viewReason" style="margin: 5px 0; padding: 10px; background: white; border-radius: 6px;"></p>
                </div>
                <div id="viewRemarksDiv" style="margin-bottom: 15px; display: none;">
                    <strong>Admin Remarks:</strong><br>
                    <p id="viewRemarks" style="margin: 5px 0; padding: 10px; background: white; border-radius: 6px;"></p>
                </div>
            </div>

            <button type="button" class="btn-theme" style="width: 100%;" onclick="closeViewModal()">Close</button>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
            <h3 style="margin-top: 0; color: #0057a0;">Reject Leave Request</h3>
            
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Reason for Rejection *</label>
                    <textarea name="admin_remarks" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;" placeholder="Enter reason for rejecting this leave request..."></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-delete" style="flex: 1;">
                        <i class="fa-solid fa-xmark"></i> Reject Request
                    </button>
                    <button type="button" class="btn-theme" style="flex: 1;" onclick="closeRejectModal()">Cancel</button>
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

        function viewLeave(id, empId, empName, leaveType, startDate, endDate, days, reason, status, remarks) {
            document.getElementById('viewEmpId').textContent = empId;
            document.getElementById('viewEmpName').textContent = empName;
            document.getElementById('viewLeaveType').textContent = leaveType;
            document.getElementById('viewStartDate').textContent = startDate;
            document.getElementById('viewEndDate').textContent = endDate;
            document.getElementById('viewDays').textContent = days + ' days';
            document.getElementById('viewStatus').textContent = status.charAt(0).toUpperCase() + status.slice(1);
            document.getElementById('viewReason').textContent = reason;
            
            if (remarks) {
                document.getElementById('viewRemarks').textContent = remarks;
                document.getElementById('viewRemarksDiv').style.display = 'block';
            } else {
                document.getElementById('viewRemarksDiv').style.display = 'none';
            }
            
            document.getElementById('viewModal').style.display = 'flex';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function showRejectModal(id) {
            document.getElementById('rejectForm').action = '/admin/leave-applications/reject/' + id;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
    </script>

</body>
</html>
