<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AccuPay - Login</title>
  <style>
    /* Base layout */
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background-color: #f4f7f9;
      overflow: hidden;
      height: 100%;
      width: 100%;
    }

    /* Flex layout for login + branding */
    .main-layout {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      width: 100vw;
      gap: 40px;
      padding: 20px;
      box-sizing: border-box;
    }

    /* Login container */
    .container-fluid {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .container-fluid h2 {
      color: #0057a0;
      text-align: center;
      margin-bottom: 10px;
    }

    .subtext {
      text-align: center;
      color: #555;
      margin-bottom: 20px;
      font-size: 14px;
    }

    /* Form styling */
    form p {
      margin: 15px 0;
      font-weight: 600;
      color: #0057a0;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      box-sizing: border-box;
    }

    /* Button styling */
    button {
      width: 100%;
      padding: 12px;
      background-color: #00a86b;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 20px;
    }

    button:hover {
      background-color: #008f5a;
    }

    /* Branding section */
    .branding {
      max-width: 400px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px;
    }

    .branding img {
      max-width: 100px; 
      height: auto;
      margin-bottom: 15px;
    }

    .branding h1 {
      font-size: 34px;
      color: #0057a0;
      margin: 10px 0 5px;
      font-weight: 700;
    }

    .branding .slogan {
      font-size: 20px; 
      color: #00a86b;
      font-weight: 600;
      margin-top: 0;
    }

    @media (max-width: 768px) {
      .main-layout {
        flex-direction: column;
        gap: 40px;
      }
    }
  </style>
</head>
<body>
  <div class="main-layout">
    <div class="container-fluid">
      <h2>Welcome to ACCUPAY INC.</h2>
      <p class="subtext">Please log in to access your account</p>
      <form action="{{ route('login') }}" method="POST">
        @csrf
        <p>Email <input type="email" name="email" required /></p>
        <p>Password <input type="password" name="password" required /></p>
        @error('email')
          <p style="color: red; font-size: 12px;">{{ $message }}</p>
        @enderror
        <button type="submit">Login</button>
      </form>
    </div>

    <div class="branding">
      <img src="{{ asset('images/accupay.png') }}" alt="ACCUPAY INC. Logo" onerror="this.style.display='none'" />
      <h1>ACCUPAY INC.</h1>
      <p class="slogan">Smart Payroll. Seamless Service.</p>
    </div>
  </div>
</body>
</html>
