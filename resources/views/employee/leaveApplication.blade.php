<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Leave Application - ACCUPAY INC.</title>
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

    .leave-form-container {
      max-width: 700px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .leave-form-container h2 {
      margin-bottom: 30px;
      color: #0057a0;
      font-size: 24px;
      border-bottom: 3px solid #0057a0;
      padding-bottom: 12px;
    }

    .leave-form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #4a5568;
      font-size: 14px;
    }

    .leave-form label i {
      color: #0057a0;
      margin-right: 8px;
    }

    .leave-form input,
    .leave-form select,
    .leave-form textarea {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      font-size: 14px;
      transition: all 0.2s;
    }

    .leave-form input:focus,
    .leave-form select:focus,
    .leave-form textarea:focus {
      outline: none;
      border-color: #0057a0;
      box-shadow: 0 0 0 3px rgba(0, 87, 160, 0.1);
    }

    .leave-form textarea {
      resize: vertical;
      font-family: inherit;
    }

    .form-actions {
      display: flex;
      gap: 15px;
      margin-top: 30px;
    }

    .form-actions button {
      flex: 1;
      padding: 12px 24px;
      background: #0057a0;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }

    .form-actions button:hover {
      background: #003f70;
    }

    .form-actions button i {
      margin-right: 8px;
    }

    .cancel-btn {
      flex: 1;
      padding: 12px 24px;
      background: #718096;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
      text-decoration: none;
      text-align: center;
      display: inline-block;
    }

    .cancel-btn:hover {
      background: #4a5568;
    }

    .cancel-btn i {
      margin-right: 8px;
    }

    .alert-error {
      background: #fff5f5;
      border: 1px solid #fc8181;
      color: #742a2a;
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .alert-error ul {
      margin: 0;
      padding-left: 20px;
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
      <li class="active"><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
      <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
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
      <h1 id="page-title">Leave Application</h1>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="leave-form-container">
      <h2>Leave Application</h2>
      @if($errors->any())
        <div class="alert-error">
          <ul>
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
