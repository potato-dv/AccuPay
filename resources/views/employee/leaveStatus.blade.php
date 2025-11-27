<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Leave Status - ACCUPAY INC.</title>
  <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
  <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/employee/leaveStatus.css') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
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
