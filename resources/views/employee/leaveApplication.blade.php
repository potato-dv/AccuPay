<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Leave Application - ACCUPAY INC.</title>
  <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
  <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/employee/leaveApplication.css') }}" />
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
      <li class="active"><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
      <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
      <li><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
      <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
      <li><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
    </ul>
  </div>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="navbar-left">
      <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
      <h1 id="page-title">Leave Application</h1>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="leave-form-container">
      <h2>Leave Application</h2>
      @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
          <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <form class="leave-form" method="POST" action="{{ route('employee.leave.store') }}">
        @csrf
        <label for="leave_type"><i class="fas fa-list"></i> Leave Type</label>
        <select id="leave_type" name="leave_type" required>
          <option value="">Select type</option>
          <option value="Vacation Leave" {{ old('leave_type') == 'Vacation Leave' ? 'selected' : '' }}>Vacation Leave</option>
          <option value="Sick Leave" {{ old('leave_type') == 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
          <option value="Emergency Leave" {{ old('leave_type') == 'Emergency Leave' ? 'selected' : '' }}>Emergency Leave</option>
          <option value="Maternity/Paternity Leave" {{ old('leave_type') == 'Maternity/Paternity Leave' ? 'selected' : '' }}>Maternity/Paternity Leave</option>
        </select>

        <label for="start_date"><i class="fas fa-calendar-day"></i> Start Date</label>
        <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required />

        <label for="end_date"><i class="fas fa-calendar-day"></i> End Date</label>
        <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required />

        <label for="reason"><i class="fas fa-align-left"></i> Reason</label>
        <textarea id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>

        <div class="form-actions">
          <button type="submit"><i class="fas fa-paper-plane"></i> Submit Request</button>
          <a href="{{ route('employee.dashboard') }}" class="cancel-btn"><i class="fas fa-times-circle"></i> Cancel</a>
        </div>
      </form>
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
