<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My QR Code - AccuPay</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
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

        .sidebar ul li {
            margin: 8px 0;
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
            color: #fff;
        }

        .sidebar ul li.active a {
            background: #003f70;
            color: #fff;
        }

        .sidebar ul li a i {
            font-size: 18px;
            width: 24px;
        }

        /* NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            height: 70px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            z-index: 999;
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
            transition: all 0.3s ease;
            min-height: calc(100vh - 70px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-container {
            text-align: center;
            max-width: 450px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .company-logo {
            margin-bottom: 20px;
        }

        .company-logo img {
            width: 60px;
            height: auto;
        }

        .employee-info {
            margin-bottom: 30px;
        }

        .employee-name {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .employee-id {
            font-size: 14px;
            color: #718096;
        }

        .qr-display {
            background: #ffffff;
            border: 3px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .qr-display img {
            width: 250px;
            height: 250px;
            display: block;
            margin: 0 auto;
        }

        .download-btn {
            width: 100%;
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
            background: #0057a0;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .download-btn:hover {
            background: #003f70;
        }

        .download-btn i {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-toggle">
            <i class="fa-solid fa-bars"></i> <span class="logo-text">ACCUPAY INC.</span>
        </div>
        <ul>
            <li>
                <a href="{{ route('employee.dashboard') }}">
                    <i class="fa-solid fa-house"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="{{ route('employee.qr.page') }}">
                    <i class="fa-solid fa-qrcode"></i>
                    <span class="menu-text">QR Code</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.profile') }}">
                    <i class="fa-solid fa-user"></i>
                    <span class="menu-text">Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.leave.application') }}">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span class="menu-text">Leave Application</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.leave.status') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span class="menu-text">Leave Status</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.payslip') }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span class="menu-text">Payslip</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.loans') }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span class="menu-text">Loans</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.report') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="menu-text">Reports</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.settings') }}">
                    <i class="fa-solid fa-gear"></i>
                    <span class="menu-text">Settings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <img src="{{ asset('images/accupay.png') }}" alt="Logo" class="navbar-logo">
            <h1>My QR Code</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="qr-container">
            <div class="company-logo">
                <img src="{{ asset('images/accupay.png') }}" alt="AccuPay">
            </div>
            
            <div class="employee-info">
                <div class="employee-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                <div class="employee-id">Employee ID: {{ $employee->employee_id }}</div>
            </div>

            <div class="qr-display" id="qrCodeContainer">
                <img src="{{ route('employee.qr.generate') }}" alt="QR Code" id="qrCodeImage">
            </div>
            
            <button class="download-btn" onclick="downloadQRCode()">
                <i class="fas fa-file-pdf"></i> DOWNLOAD PDF
            </button>
        </div>
    </main>

    <script>
        function downloadQRCode() {
            const employeeName = '{{ $employee->first_name }} {{ $employee->last_name }}';
            const employeeId = '{{ $employee->employee_id }}';
            const qrImage = document.getElementById('qrCodeImage');
            
            // Fetch the SVG QR code
            fetch(qrImage.src)
                .then(response => response.text())
                .then(svgText => {
                    // Create an image from SVG
                    const img = new Image();
                    const svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                    const url = URL.createObjectURL(svgBlob);
                    
                    img.onload = function() {
                        // Create canvas to convert SVG to image
                        const canvas = document.createElement('canvas');
                        canvas.width = 600;
                        canvas.height = 600;
                        const ctx = canvas.getContext('2d');
                        
                        // Fill white background
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        
                        // Draw QR code
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        
                        // Convert canvas to data URL
                        const qrDataUrl = canvas.toDataURL('image/jpeg', 0.95);
                        
                        // Fetch logo
                        fetch('{{ asset('images/accupay.png') }}')
                            .then(response => response.blob())
                            .then(blob => {
                                const reader = new FileReader();
                                reader.onloadend = function() {
                                    const logoDataUrl = reader.result;
                                    
                                    // Create PDF
                                    const { jsPDF } = window.jspdf;
                                    const pdf = new jsPDF({
                                        orientation: 'portrait',
                                        unit: 'mm',
                                        format: 'a4'
                                    });
                                    
                                    const pageWidth = pdf.internal.pageSize.getWidth();
                                    const pageHeight = pdf.internal.pageSize.getHeight();
                                    
                                    // Add subtle background gradient effect (using rectangles)
                                    pdf.setFillColor(245, 247, 250);
                                    pdf.rect(0, 0, pageWidth, pageHeight, 'F');
                                    
                                    // Add white card background
                                    pdf.setFillColor(255, 255, 255);
                                    pdf.roundedRect(15, 30, pageWidth - 30, pageHeight - 50, 5, 5, 'F');
                                    
                                    // Add subtle shadow/border
                                    pdf.setDrawColor(226, 232, 240);
                                    pdf.setLineWidth(0.5);
                                    pdf.roundedRect(15, 30, pageWidth - 30, pageHeight - 50, 5, 5, 'S');
                                    
                                    // Add logo at top center
                                    const logoSize = 25;
                                    const logoX = (pageWidth - logoSize) / 2;
                                    pdf.addImage(logoDataUrl, 'PNG', logoX, 45, logoSize, logoSize);
                                    
                                    // Add AccuPay title
                                    pdf.setFontSize(22);
                                    pdf.setFont('helvetica', 'bold');
                                    pdf.setTextColor(0, 87, 160);
                                    pdf.text('AccuPay', pageWidth / 2, 80, { align: 'center' });
                                    
                                    pdf.setFontSize(11);
                                    pdf.setFont('helvetica', 'normal');
                                    pdf.setTextColor(113, 128, 150);
                                    pdf.text('Attendance System', pageWidth / 2, 87, { align: 'center' });
                                    
                                    // Add decorative line
                                    pdf.setDrawColor(226, 232, 240);
                                    pdf.setLineWidth(0.3);
                                    pdf.line(40, 95, pageWidth - 40, 95);
                                    
                                    // Add employee info with modern styling
                                    pdf.setFontSize(18);
                                    pdf.setFont('helvetica', 'bold');
                                    pdf.setTextColor(45, 55, 72);
                                    pdf.text(employeeName, pageWidth / 2, 110, { align: 'center' });
                                    
                                    pdf.setFontSize(12);
                                    pdf.setFont('helvetica', 'normal');
                                    pdf.setTextColor(113, 128, 150);
                                    pdf.text('Employee ID: ' + employeeId, pageWidth / 2, 120, { align: 'center' });
                                    
                                    // Add QR code with border
                                    const qrSize = 100;
                                    const qrX = (pageWidth - qrSize) / 2;
                                    const qrY = 135;
                                    
                                    // QR code border/shadow
                                    pdf.setFillColor(248, 250, 252);
                                    pdf.roundedRect(qrX - 5, qrY - 5, qrSize + 10, qrSize + 10, 3, 3, 'F');
                                    
                                    pdf.setDrawColor(226, 232, 240);
                                    pdf.setLineWidth(0.5);
                                    pdf.roundedRect(qrX - 5, qrY - 5, qrSize + 10, qrSize + 10, 3, 3, 'S');
                                    
                                    pdf.addImage(qrDataUrl, 'JPEG', qrX, qrY, qrSize, qrSize);
                                    
                                    // Add scan instruction
                                    pdf.setFontSize(10);
                                    pdf.setFont('helvetica', 'normal');
                                    pdf.setTextColor(113, 128, 150);
                                    pdf.text('Scan this QR code for attendance', pageWidth / 2, qrY + qrSize + 15, { align: 'center' });
                                    
                                    // Add footer
                                    pdf.setFontSize(8);
                                    pdf.setTextColor(160, 174, 192);
                                    const currentDate = new Date().toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    });                                    
                                    // Save PDF
                                    const fileName = `${employeeName.replace(/ /g, '_')}_${employeeId}_QR.pdf`;
                                    pdf.save(fileName);
                                    
                                    // Clean up
                                    URL.revokeObjectURL(url);
                                };
                                reader.readAsDataURL(blob);
                            })
                            .catch(error => {
                                console.error('Error loading logo:', error);
                                alert('Failed to load logo for PDF');
                            });
                    };
                    
                    img.src = url;
                })
                .catch(error => {
                    console.error('Error downloading QR code:', error);
                    alert('Failed to download QR code PDF');
                });
        }
    </script>
</body>
</html>
