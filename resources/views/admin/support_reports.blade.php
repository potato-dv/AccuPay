<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
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
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li class="active"><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Support Tickets</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1 id="page-title">Support & Feedback Tickets</h1>
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

        <!-- Filters -->
        <section style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 15px;">Filter Tickets</h3>
            <form method="GET" action="{{ route('admin.support.reports') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject or employee name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Status</label>
                    <select name="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Type</label>
                    <select name="type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All</option>
                        <option value="technical" {{ request('type') == 'technical' ? 'selected' : '' }}>Technical</option>
                        <option value="payroll" {{ request('type') == 'payroll' ? 'selected' : '' }}>Payroll</option>
                        <option value="leave" {{ request('type') == 'leave' ? 'selected' : '' }}>Leave</option>
                        <option value="attendance" {{ request('type') == 'attendance' ? 'selected' : '' }}>Attendance</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Priority</label>
                    <select name="priority" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">All</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Filter</button>
                    <a href="{{ route('admin.support.reports') }}" style="padding: 8px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-block;">Clear</a>
                </div>
            </form>
        </section>

        <!-- Reports Table -->
        <section style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->employee->full_name }}</td>
                                <td>
                                    <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">{{ ucfirst($report->type) }}</span>
                                </td>
                                <td>{{ Str::limit($report->subject, 40) }}</td>
                                <td>
                                    @if($report->priority == 'urgent')
                                        <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">Urgent</span>
                                    @elseif($report->priority == 'high')
                                        <span style="padding: 4px 8px; background: #ffc107; color: #000; border-radius: 4px; font-size: 12px;">High</span>
                                    @elseif($report->priority == 'medium')
                                        <span style="padding: 4px 8px; background: #0dcaf0; color: white; border-radius: 4px; font-size: 12px;">Medium</span>
                                    @else
                                        <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Low</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->status == 'pending')
                                        <span style="padding: 4px 8px; background: #ffc107; color: #000; border-radius: 4px; font-size: 12px;">Pending</span>
                                    @elseif($report->status == 'in-progress')
                                        <span style="padding: 4px 8px; background: #0d6efd; color: white; border-radius: 4px; font-size: 12px;">In Progress</span>
                                    @elseif($report->status == 'resolved')
                                        <span style="padding: 4px 8px; background: #198754; color: white; border-radius: 4px; font-size: 12px;">Resolved</span>
                                    @else
                                        <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Closed</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button onclick="openModal('modal{{ $report->id }}')" style="padding: 6px 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">No support reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->total() > $reports->perPage())
                <div style="margin-top: 20px;">
                    {{ $reports->links() }}
                </div>
            @endif
        </section>

        <!-- Modals for each report -->
        @foreach($reports as $report)
            <div id="modal{{ $report->id }}" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
                <div style="background-color: white; margin: 5% auto; padding: 0; width: 80%; max-width: 800px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0;">Support Report #{{ $report->id }}</h3>
                        <button onclick="closeModal('modal{{ $report->id }}')" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6c757d;">&times;</button>
                    </div>
                    <div style="padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                                <div><strong>Employee:</strong> {{ $report->employee->full_name }}</div>
                                <div><strong>Type:</strong> {{ ucfirst($report->type) }}</div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px;">
                                <div>
                                    <strong>Priority:</strong>
                                    @if($report->priority == 'urgent')
                                        <span style="padding: 4px 8px; background: #dc3545; color: white; border-radius: 4px; font-size: 12px;">Urgent</span>
                                    @elseif($report->priority == 'high')
                                        <span style="padding: 4px 8px; background: #ffc107; color: #000; border-radius: 4px; font-size: 12px;">High</span>
                                    @elseif($report->priority == 'medium')
                                        <span style="padding: 4px 8px; background: #0dcaf0; color: white; border-radius: 4px; font-size: 12px;">Medium</span>
                                    @else
                                        <span style="padding: 4px 8px; background: #6c757d; color: white; border-radius: 4px; font-size: 12px;">Low</span>
                                    @endif
                                </div>
                                <div>
                                    <strong>Status:</strong>
                                    <form method="POST" action="{{ route('admin.support.status', $report->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px;">
                                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in-progress" {{ $report->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $report->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                            <div><strong>Submitted:</strong> {{ $report->created_at->format('F d, Y h:i A') }}</div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Subject:</strong>
                            <p style="margin: 5px 0;">{{ $report->subject }}</p>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <strong>Message:</strong>
                            <p style="white-space: pre-wrap; background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 5px 0;">{{ $report->message }}</p>
                        </div>

                        @if($report->admin_reply)
                            <div style="background: #d1ecf1; padding: 15px; border-left: 4px solid #0dcaf0; border-radius: 4px; margin-bottom: 20px;">
                                <strong>Admin Reply:</strong> <small>(by {{ $report->repliedBy->name ?? 'Admin' }} on {{ $report->replied_at->format('M d, Y h:i A') }})</small>
                                <p style="white-space: pre-wrap; margin: 10px 0 0 0;">{{ $report->admin_reply }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.support.reply', $report->id) }}">
                            @csrf
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">{{ $report->admin_reply ? 'Update Reply' : 'Reply' }}:</label>
                                <textarea name="admin_reply" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;">{{ old('admin_reply', $report->admin_reply) }}</textarea>
                            </div>
                            <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Reply</button>
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
