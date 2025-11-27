<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile - ACCUPAY INC.</title>
  <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
  <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/employee/profile.css') }}" />
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
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">@csrf<button type="submit" class="logout-btn">Log Out</button></form>
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
          <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
          </div>
        @endif
        @if($errors->any())
          <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <label for="email"><i class="fas fa-envelope"></i> Email</label>
        <input type="email" id="email" name="email" value="{{ $employee->email }}" readonly style="background: #f0f0f0;" />

        <label for="birthdate"><i class="fas fa-birthday-cake"></i> Birthdate</label>
        <input type="date" id="birthdate" value="{{ $employee->birthdate ? $employee->birthdate->format('Y-m-d') : '' }}" readonly style="background: #f0f0f0;" />

        <label for="sex"><i class="fas fa-venus-mars"></i> Sex</label>
        <input type="text" id="sex" value="{{ $employee->sex ?? 'Not specified' }}" readonly style="background: #f0f0f0;" />

        <label for="civil_status"><i class="fas fa-heart"></i> Civil Status</label>
        <input type="text" id="civil_status" value="{{ $employee->civil_status }}" readonly style="background: #f0f0f0;" />

        @if($employee->bank_account_number)
        <label for="bank_info"><i class="fas fa-university"></i> Bank Information</label>
        <input type="text" id="bank_info" value="{{ $employee->bank_name ?? 'Bank' }} - {{ $employee->bank_account_number }}" readonly style="background: #f0f0f0;" />
        @endif

        <label for="phone"><i class="fas fa-phone"></i> Contact Number <span style="color: red;">*</span></label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required />
        <small style="color: #666; font-size: 12px; display: block; margin-top: -10px; margin-bottom: 15px;">10-11 digits only</small>

        <label for="address"><i class="fas fa-map-marker-alt"></i> Address <span style="color: red;">*</span></label>
        <textarea id="address" name="address" rows="3" maxlength="500" required>{{ old('address', $employee->address) }}</textarea>

        <label for="emergency_contact"><i class="fas fa-user"></i> Emergency Contact Name <span style="color: red;">*</span></label>
        <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" maxlength="255" required />

        <label for="emergency_phone"><i class="fas fa-phone"></i> Emergency Contact Phone <span style="color: red;">*</span></label>
        <input type="tel" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $employee->emergency_phone) }}" placeholder="09123456789" pattern="[0-9]{10,11}" maxlength="11" required />
        <small style="color: #666; font-size: 12px; display: block; margin-top: -10px; margin-bottom: 15px;">10-11 digits only</small>

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
