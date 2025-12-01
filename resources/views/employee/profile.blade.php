<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile - ACCUPAY INC.</title>
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

    .profile-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .profile-header {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      display: flex;
      gap: 30px;
      align-items: flex-start;
    }

    #profile-pic {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #0057a0;
    }

    .profile-info {
      flex: 1;
    }

    .profile-info h2 {
      font-size: 28px;
      color: #2d3748;
      margin-bottom: 15px;
    }

    .profile-info p {
      margin: 8px 0;
      color: #4a5568;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .profile-info p i {
      color: #0057a0;
      width: 20px;
    }

    .profile-form {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .profile-form h3 {
      font-size: 20px;
      color: #2d3748;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid #e2e8f0;
    }

    .alert-success {
      background: #c6f6d5;
      color: #22543d;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #00a86b;
    }

    .alert-error {
      background: #fed7d7;
      color: #742a2a;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #e53e3e;
    }

    .profile-form label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #2d3748;
      font-size: 14px;
    }

    .profile-form label i {
      color: #0057a0;
      margin-right: 6px;
      width: 16px;
    }

    .profile-form input,
    .profile-form textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #cbd5e0;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 14px;
      transition: border 0.2s;
    }

    .profile-form input:focus,
    .profile-form textarea:focus {
      outline: none;
      border-color: #0057a0;
      box-shadow: 0 0 0 3px rgba(0, 87, 160, 0.1);
    }

    .profile-form input[readonly] {
      background: #f7fafc;
      color: #718096;
      cursor: not-allowed;
    }

    .profile-form small {
      color: #718096;
      font-size: 12px;
      display: block;
      margin-top: -15px;
      margin-bottom: 15px;
    }

    .form-actions {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 2px solid #e2e8f0;
    }

    .form-actions button {
      padding: 12px 30px;
      background: #0057a0;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
    }

    .form-actions button:hover {
      background: #003f70;
    }

    .cancel-btn {
      padding: 12px 30px;
      background: #e2e8f0;
      color: #4a5568;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: background 0.2s;
    }

    .cancel-btn:hover {
      background: #cbd5e0;
    }

    @media (max-width: 768px) {
      .profile-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
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
      <li class="active"><a href="{{ route('employee.profile') }}"><i class="fa-solid fa-user"></i> <span class="menu-text">Profile</span></a></li>
      <li><a href="{{ route('employee.leave.application') }}"><i class="fa-solid fa-calendar-plus"></i> <span class="menu-text">Leave Application</span></a></li>
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
      <h1 id="page-title">Profile</h1>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button></form>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <div class="profile-container">
      <div class="profile-header">
        <img src="{{ asset('images/default.jpg') }}" alt="Employee Photo" id="profile-pic" onerror="this.src='{{ asset('images/accupay.png') }}'"/>
        <div class="profile-info">
          <h2>{{ $employee->full_name }}</h2>
          <p><i class="fas fa-id-badge"></i> Employee ID: {{ $employee->employee_id }}</p>
          <p><i class="fas fa-briefcase"></i> Role: {{ $employee->position }}</p>
          @if($employee->birthdate)
          <p><i class="fas fa-birthday-cake"></i> Age: {{ $employee->age }} years old</p>
          @endif
          @if($employee->sex)
          <p><i class="fas fa-venus-mars"></i> Sex: {{ $employee->sex }}</p>
          @endif
          <p><i class="fas fa-heart"></i> Civil Status: {{ $employee->civil_status }}</p>
          <p><i class="fas fa-calendar-alt"></i> Work Schedule: {{ $employee->workSchedule ? $employee->workSchedule->schedule_name : 'Not assigned' }}</p>
          @if($employee->workSchedule)
          <p><i class="fas fa-clock"></i> Shift: {{ $employee->workSchedule->formatted_work_hours }} ({{ $employee->workSchedule->weekly_hours }} hrs/week)</p>
          <p><i class="fas fa-calendar-days"></i> Working Days: {{ implode(', ', $employee->workSchedule->working_days) }}</p>
          @endif
        </div>
      </div>

      <form class="profile-form" method="POST" action="{{ route('employee.profile.update') }}">
        @csrf
        @method('PUT')
        <h3>Edit Personal Information</h3>
        @if(session('success'))
          <div class="alert-success">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
          </div>
        @endif
        @if($errors->any())
          <div class="alert-error">
            <ul style="margin: 0; padding-left: 20px;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <label for="email"><i class="fas fa-envelope"></i> Email</label>
        <input type="email" id="email" name="email" value="{{ $employee->email }}" readonly />

        <label for="birthdate"><i class="fas fa-birthday-cake"></i> Birthdate</label>
        <input type="date" id="birthdate" value="{{ $employee->birthdate ? $employee->birthdate->format('Y-m-d') : '' }}" readonly />

        <label for="sex"><i class="fas fa-venus-mars"></i> Sex</label>
        <input type="text" id="sex" value="{{ $employee->sex ?? 'Not specified' }}" readonly />

        <label for="civil_status"><i class="fas fa-heart"></i> Civil Status</label>
        <input type="text" id="civil_status" value="{{ $employee->civil_status }}" readonly />

        @if($employee->bank_account_number)
        <label for="bank_info"><i class="fas fa-university"></i> Bank Information</label>
        <input type="text" id="bank_info" value="{{ $employee->bank_name ?? 'Bank' }} - {{ $employee->bank_account_number }}" readonly />
        @endif

        <label for="phone"><i class="fas fa-phone"></i> Contact Number <span style="color: #e53e3e;">*</span></label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required />
        <small>10-11 digits only</small>

        <label for="address"><i class="fas fa-map-marker-alt"></i> Address <span style="color: #e53e3e;">*</span></label>
        <textarea id="address" name="address" rows="3" maxlength="500" required>{{ old('address', $employee->address) }}</textarea>

        <label for="emergency_contact"><i class="fas fa-user"></i> Emergency Contact Name <span style="color: #e53e3e;">*</span></label>
        <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" maxlength="255" required />

        <label for="emergency_phone"><i class="fas fa-phone"></i> Emergency Contact Phone <span style="color: #e53e3e;">*</span></label>
        <input type="tel" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required />
        <small>10-11 digits only</small>

        <div class="form-actions">
          <button type="submit"><i class="fas fa-save"></i> Update Profile</button>
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
        navbar.style.left = '70px';
        mainContent.style.marginLeft = '70px';
      } else {
        navbar.style.left = '250px';
        mainContent.style.marginLeft = '250px';
      }
    });
  </script>
</body>
</html>
