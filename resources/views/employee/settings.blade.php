<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings - ACCUPAY INC.</title>
  <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
  <link rel="stylesheet" href="{{ asset('css/employee/dashboard.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/employee/settings.css') }}" />
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
          <li><a href="{{ route('employee.leave.status') }}"><i class="fa-solid fa-calendar-check"></i> <span class="menu-text">Leave Status</span></a></li>
          <li><a href="{{ route('employee.payslip') }}"><i class="fa-solid fa-file-invoice-dollar"></i> <span class="menu-text">Payslip</span></a></li>
          <li><a href="{{ route('employee.report') }}"><i class="fa-solid fa-chart-line"></i> <span class="menu-text">Reports</span></a></li>
          <li class="active"><a href="{{ route('employee.settings') }}"><i class="fa-solid fa-gear"></i> <span class="menu-text">Settings</span></a></li>
      </ul>
  </div>

  <!-- NAVBAR -->
  <header class="navbar">
      <div class="navbar-left">
          <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
          <h1 id="page-title">Settings</h1>
      </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="main-content">
      <section class="settings-section">
          <h2>Account Settings</h2>
          
          @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          
          @if($errors->any())
              <div class="alert alert-error">
                  <ul>
                      @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif

          <form class="settings-form" action="{{ route('employee.password.update') }}" method="POST">
              @csrf
              @method('PUT')
              
              <label for="current_password"><i class="fas fa-lock"></i> Current Password <span style="color: red;">*</span></label>
              <input type="password" id="current_password" name="current_password" placeholder="Enter current password" required />

              <label for="password"><i class="fas fa-lock"></i> New Password <span style="color: red;">*</span></label>
              <input type="password" id="password" name="password" placeholder="Enter new password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$" />
              <small style="color: #666; display: block; margin-top: 5px; margin-bottom: 20px; font-size: 13px;">Minimum 8 characters with uppercase, lowercase, and number</small>

              <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm New Password <span style="color: red;">*</span></label>
              <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required minlength="8" />

              <button type="submit"><i class="fas fa-save"></i> Update Password</button>
          </form>

          <div class="logout-section">
              <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                  @csrf
                  <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</button>
              </form>
          </div>
      </section>
  </main>

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
