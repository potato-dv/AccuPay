<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payroll</title>
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
            <li><a href="{{ route('admin.employee.records') }}"><i class="fa-solid fa-folder-open"></i> <span class="menu-text">Employee Records</span></a></li>
            <li class="active"><a href="{{ route('admin.payroll') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Manage Payroll</span></a></li>
            <li><a href="{{ route('admin.payslip') }}"><i class="fa-solid fa-file-lines"></i> <span class="menu-text">Manage Payslip</span></a></li>
            <li><a href="{{ route('admin.leave') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Requests</span></a></li>
            <li><a href="{{ route('admin.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
            <li><a href="{{ route('admin.reports') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
            <li><a href="{{ route('admin.support.reports') }}"><i class="fa-solid fa-headset"></i> <span class="menu-text">Help Desk</span></a></li>
            <li><a href="{{ route('admin.users') }}"><i class="fa-solid fa-users-gear"></i> <span class="menu-text">User Accounts</span></a></li>
            <li><a href="{{ route('admin.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
        </ul>
    </div>

    <header class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>Payroll Management</h1>
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
        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Payroll Records</h2>
            <button type="button" class="btn-theme" onclick="showGenerateModal()">
                <i class="fa-solid fa-plus"></i> Generate New Payroll
            </button>
        </div>

        <div style="background: #e7f3ff; border-left: 4px solid #0057a0; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h4 style="margin: 0 0 8px 0; color: #0057a0;">
                <i class="fa-solid fa-info-circle"></i> Payroll Workflow
            </h4>
            <p style="margin: 0; color: #555; font-size: 14px;">
                <strong>Pending:</strong> Review and edit calculations → <strong>Approve:</strong> Lock payroll and make visible to employees
            </p>
        </div>

        <section class="employee-list">
            <table>
                <thead>
                    <tr>
                        <th>Payroll Period</th>
                        <th>Period Start</th>
                        <th>Period End</th>
                        <th>Payment Date</th>
                        <th>Total Employees</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr>
                        <td><strong>{{ $payroll->payroll_period }}</strong></td>
                        <td>{{ date('M d, Y', strtotime($payroll->period_start)) }}</td>
                        <td>{{ date('M d, Y', strtotime($payroll->period_end)) }}</td>
                        <td>{{ date('M d, Y', strtotime($payroll->payment_date)) }}</td>
                        <td>{{ $payroll->total_employees }}</td>
                        <td>₱{{ number_format($payroll->total_amount, 2) }}</td>
                        <td>
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 500;
                                @if($payroll->status == 'approved') background: #d4edda; color: #155724;
                                @elseif($payroll->status == 'pending') background: #fff3cd; color: #856404;
                                @else background: #e2e3e5; color: #383d41; @endif">
                                {{ ucfirst($payroll->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px; justify-content: flex-start; flex-wrap: wrap;">
                                <a href="{{ route('admin.payroll.view', $payroll->id) }}" class="btn-sm" style="background: #17a2b8; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; white-space: nowrap; min-width: 90px; display: inline-block; text-align: center;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                @if($payroll->status == 'pending')
                                    <a href="{{ route('admin.payroll.edit', $payroll->id) }}" class="btn-sm" style="background: #00a86b; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; white-space: nowrap; min-width: 90px; display: inline-block; text-align: center;">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.payroll.approve', $payroll->id) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Approve this payroll? Employees will be able to view it.');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-sm" style="background: #28a745; color: white; border: none; cursor: pointer; padding: 6px 12px; border-radius: 4px; font-size: 13px; white-space: nowrap; min-width: 90px; text-align: center;">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.payroll.delete', $payroll->id) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Delete this pending payroll?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm" style="background: #e74c3c; color: white; border: none; cursor: pointer; padding: 6px 12px; border-radius: 4px; font-size: 13px; white-space: nowrap; min-width: 90px; text-align: center;">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fa-solid fa-file-invoice-dollar" style="font-size: 48px; color: #ddd; display: block; margin-bottom: 10px;"></i>
                            No payroll records yet. <button type="button" onclick="showGenerateModal()" style="color: #0057a0; background: none; border: none; text-decoration: underline; cursor: pointer;">Generate first payroll</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <!-- Generate Payroll Modal -->
    <div id="generateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%;">
            <h3 style="margin-top: 0; color: #0057a0;">Generate Payroll</h3>
            
            <form action="{{ route('admin.payroll.generate') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Payroll Period *</label>
                    <input type="text" name="payroll_period" required placeholder="e.g., November 1-15, 2025" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Period Start *</label>
                        <input type="date" name="period_start" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Period End *</label>
                        <input type="date" name="period_end" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Payment Date *</label>
                    <input type="date" name="payment_date" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #555;">Notes</label>
                    <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit;"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-theme" style="flex: 1;">
                        <i class="fa-solid fa-calculator"></i> Generate Payroll
                    </button>
                    <button type="button" class="btn-delete" style="flex: 1;" onclick="closeGenerateModal()">Cancel</button>
                </div>
            </form>
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

        function showGenerateModal() {
            document.getElementById('generateModal').style.display = 'flex';
        }

        function closeGenerateModal() {
            document.getElementById('generateModal').style.display = 'none';
        }
    </script>

</body>
</html>
