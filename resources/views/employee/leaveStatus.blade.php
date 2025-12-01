<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Leave Status - ACCUPAY INC.</title>
  <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    body {
      display: flex;
      background: #f5f7fa;
      color: #2d3748;
    }

    /* SIDEBAR */
    .sidebar {
      width: 250px;
      height: 100vh;
      background: #0057a0;
      color: white;
      padding: 20px 0;
      position: fixed;
      left: 0;
      transition: width 0.3s;
      overflow: hidden;
    }

    .sidebar-toggle {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 18px;
      font-weight: 600;
      margin: 0 20px 30px;
      cursor: pointer;
      color: #fff;
      padding: 10px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      transition: all 0.2s;
    }

    .sidebar ul li a:hover {
      background: #008f5a;
    }

    .sidebar ul li.active a {
      background: #003f70;
    }

    .sidebar.collapsed {
      width: 70px;
    }

    .sidebar.collapsed .menu-text,
    .sidebar.collapsed .logo-text {
      display: none;
    }

    /* NAVBAR */
    .navbar {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 70px;
      padding: 0 30px;
      background: #fff;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 100;
      transition: left 0.3s;
    }

    .navbar-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .navbar-logo {
      height: 40px;
    }

    .navbar h1 {
      font-size: 20px;
      font-weight: 600;
      color: #2d3748;
    }

    .logout-btn {
      background: #e53e3e;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: background 0.2s;
    }

    .logout-btn:hover {
      background: #c53030;
    }

    /* MAIN CONTENT */
    .main-content {
      margin-left: 250px;
      margin-top: 70px;
      padding: 30px;
      width: calc(100% - 250px);
      transition: margin-left 0.3s, width 0.3s;
    }

    .leave-status-container {
      max-width: 1200px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .leave-status-container h2 {
      margin-bottom: 30px;
      color: #0057a0;
      font-size: 24px;
      border-bottom: 3px solid #0057a0;
      padding-bottom: 12px;
    }

    .leave-table {
      width: 100%;
      border-collapse: collapse;
    }

    .leave-table thead {
      background: #f7fafc;
    }

    .leave-table th {
      padding: 15px;
      text-align: left;
      font-weight: 600;
      color: #4a5568;
      border-bottom: 2px solid #e2e8f0;
      font-size: 14px;
    }

    .leave-table td {
      padding: 15px;
      border-bottom: 1px solid #e2e8f0;
      color: #2d3748;
      font-size: 14px;
    }

    .leave-table tbody tr:hover {
      background: #f7fafc;
    }

    .badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .badge.pending {
      background: #fef5e7;
      color: #d68910;
    }

    .badge.approved {
      background: #d4edda;
      color: #155724;
    }

    .badge.rejected {
      background: #f8d7da;
      color: #721c24;
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
      <li><a href="{{ route('employee.dashboard') }}"><i class="fa-solid fa-house"></i> <span class="menu-text">Dashboard</span></a></li>
      <li><a href="{{ route('employee.qr.page') }}"><i class="fa-solid fa-qrcode"></i> <span class="menu-text">QR Code</span></a></li>
      <li><a href="{{ route('employee.profile') }}"><i class="fa-solid fa-user"></i> <span class="menu-text">Profile</span></a></li>
      <li><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
      <li class="active"><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
      <li><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
      <li><a href="{{ route('employee.loans') }}"><i class="fa-solid fa-hand-holding-dollar"></i> <span class="menu-text">Loans</span></a></li>
      <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
      <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
    </ul>
  </div>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="navbar-left">
      <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
      <h1 id="page-title">Leave Status</h1>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
      @csrf
      <button type="submit" class="logout-btn">Log Out</button>
    </form>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="leave-status-container">
      <h2>Leave Status</h2>
      <table class="leave-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse($leaveApplications as $leave)
          <tr>
            <td>{{ $leave->leave_type }}</td>
            <td>{{ $leave->start_date->format('M d, Y') }}</td>
            <td>{{ $leave->end_date->format('M d, Y') }}</td>
            <td>
              <span class="badge {{ strtolower($leave->status) }}">
                {{ ucfirst($leave->status) }}
              </span>
            </td>
            <td>{{ $leave->admin_remarks ?? 'Pending review' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="text-align: center;">No leave applications found.</td>
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
  </script>
</body>
</html>
