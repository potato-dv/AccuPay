<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Desk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
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
            <li class="active"><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Help Desk</h1>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
        </form>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        @if(session('success'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #155724;">
                {{ session('success') }}
            </div>
        @endif

        <!-- FILTER SECTION -->
        <section style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px; font-size: 18px;">Filter Tickets</h3>
            <form method="GET" action="{{ route('admin.support.reports') }}">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject or employee name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Status</label>
                        <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-size: 14px;">Type</label>
                        <select name="type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                            <option value="">All</option>
                            <option value="technical" {{ request('type') == 'technical' ? 'selected' : '' }}>Technical</option>
                            <option value="payroll" {{ request('type') == 'payroll' ? 'selected' : '' }}>Payroll</option>
                            <option value="leave" {{ request('type') == 'leave' ? 'selected' : '' }}>Leave</option>
                            <option value="attendance" {{ request('type') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end; gap: 10px;">
                        <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; white-space: nowrap;">Filter</button>
                        <a href="{{ route('admin.support.reports') }}" style="padding: 8px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 14px;">Clear</a>
                    </div>
                </div>
            </form>
        </section>

        <!-- TICKETS TABLE -->
        <section style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; font-size: 13px; width: 60px;">#</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Employee</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px; width: 120px;">Type</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px;">Subject</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px; width: 120px;">Status</th>
                            <th style="padding: 12px; text-align: left; font-size: 13px; width: 120px;">Submitted</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-size: 13px;">{{ $report->id }}</td>
                                <td style="padding: 12px; font-size: 13px;">{{ $report->employee->full_name }}</td>
                                <td style="padding: 12px; font-size: 13px;">
                                    <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">
                                        {{ ucfirst($report->type) }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 13px;">{{ Str::limit($report->subject, 50) }}</td>
                                <td style="padding: 12px; text-align: center; font-size: 13px;">
                                    @if($report->status == 'pending')
                                        <span style="padding: 4px 8px; background: #fff3cd; color: #856404; border-radius: 4px; font-size: 12px; font-weight: 500;">Pending</span>
                                    @elseif($report->status == 'in-progress')
                                        <span style="padding: 4px 8px; background: #cfe2ff; color: #084298; border-radius: 4px; font-size: 12px; font-weight: 500;">In Progress</span>
                                    @elseif($report->status == 'resolved')
                                        <span style="padding: 4px 8px; background: #d1e7dd; color: #0a3622; border-radius: 4px; font-size: 12px; font-weight: 500;">Resolved</span>
                                    @else
                                        <span style="padding: 4px 8px; background: #e2e3e5; color: #41464b; border-radius: 4px; font-size: 12px; font-weight: 500;">Closed</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; font-size: 13px;">{{ $report->created_at->format('M d, Y') }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    <button onclick="openModal('modal{{ $report->id }}')" style="padding: 6px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
                                        <i class="fa-solid fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d; font-size: 14px;">No help desk tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Modals for each report -->
        @foreach($reports as $report)
            <div id="modal{{ $report->id }}" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); overflow-y: auto;">
                <div style="background-color: white; margin: 3% auto; padding: 0; width: 90%; max-width: 700px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Modal Header -->
                    <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 8px 8px 0 0;">
                        <h3 style="margin: 0; font-size: 18px; color: #333;">
                            <i class="fa-solid fa-ticket"></i> Ticket #{{ $report->id }}
                        </h3>
                        <button onclick="closeModal('modal{{ $report->id }}')" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #6c757d; line-height: 1; padding: 0; width: 30px; height: 30px;">&times;</button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div style="padding: 25px;">
                        <!-- Ticket Info -->
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
                                <div>
                                    <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 4px;">EMPLOYEE</label>
                                    <div style="font-size: 14px; font-weight: 500;">{{ $report->employee->full_name }}</div>
                                </div>
                                <div>
                                    <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 4px;">TYPE</label>
                                    <div>
                                        <span style="padding: 4px 10px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                            {{ ucfirst($report->type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 4px;">STATUS</label>
                                    <form method="POST" action="{{ route('admin.support.status', $report->id) }}" style="margin-top: 4px;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" style="padding: 6px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 100%;">
                                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in-progress" {{ $report->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $report->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </form>
                                </div>
                                <div>
                                    <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 4px;">SUBMITTED</label>
                                    <div style="font-size: 14px; font-weight: 500;">{{ $report->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 6px; font-weight: 600;">SUBJECT</label>
                            <div style="font-size: 15px; font-weight: 500; color: #333;">{{ $report->subject }}</div>
                        </div>

                        <!-- Message -->
                        <div style="margin-bottom: 20px;">
                            <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 6px; font-weight: 600;">MESSAGE</label>
                            <div style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 6px; font-size: 14px; line-height: 1.6; color: #333; border-left: 3px solid #007bff;">{{ $report->message }}</div>
                        </div>

                        <!-- Admin Reply Display -->
                        @if($report->admin_reply)
                            <div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #007bff; border-radius: 6px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <label style="font-size: 12px; color: #0056b3; font-weight: 600;">ADMIN REPLY</label>
                                    <small style="font-size: 11px; color: #6c757d;">
                                        by {{ $report->repliedBy->name ?? 'Admin' }} • {{ $report->replied_at->format('M d, Y h:i A') }}
                                    </small>
                                </div>
                                <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.6; color: #333;">{{ $report->admin_reply }}</div>
                            </div>
                        @endif

                        <!-- Reply Form -->
                        <form method="POST" action="{{ route('admin.support.reply', $report->id) }}">
                            @csrf
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 8px; font-weight: 600;">
                                    {{ $report->admin_reply ? 'UPDATE REPLY' : 'SEND REPLY' }}
                                </label>
                                <textarea name="admin_reply" rows="4" required placeholder="Type your reply here..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; font-size: 14px; resize: vertical;">{{ old('admin_reply', $report->admin_reply) }}</textarea>
                            </div>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" onclick="closeModal('modal{{ $report->id }}')" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                                    Cancel
                                </button>
                                <button type="submit" style="padding: 10px 24px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                                    <i class="fa-solid fa-paper-plane"></i> Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

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

        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
