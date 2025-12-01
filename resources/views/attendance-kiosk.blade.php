<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AccuPay Attendance Kiosk</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #ffffff;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .kiosk-container {
            text-align: center;
            max-width: 400px;
            padding: 30px;
        }

        .company-logo img {
            width: 80px;
            height: auto;
            margin-bottom: 20px;
        }

        .time-display {
            margin-bottom: 30px;
        }

        .current-time {
            font-size: 48px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #000000;
            margin-bottom: 5px;
        }

        .current-date {
            font-size: 16px;
            color: #666666;
        }

        .action-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            justify-content: center;
        }

        .action-btn {
            flex: 1;
            background: #ffffff;
            border: 2px solid #000000;
            color: #000000;
            padding: 15px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: bold;
        }

        .action-btn:hover {
            background: #f0f0f0;
        }

        .action-btn.active {
            background: #000000;
            color: #ffffff;
        }

        #qr-reader {
            width: 100%;
            height: 300px;
            border: 2px solid #000000;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
            background: #000000;
            display: none;
        }

        #qr-reader video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .control-btn {
            width: 100%;
            padding: 18px;
            font-size: 16px;
            font-weight: bold;
            background: #000000;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .control-btn:hover:not(:disabled) {
            background: #333333;
        }

        .control-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .control-btn i {
            margin-right: 8px;
        }

        .validation-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            border: 3px solid;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            z-index: 1000;
            min-width: 350px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .validation-message.show {
            display: block;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        .validation-message.success {
            border-color: #2e7d32;
        }

        .validation-message.warning {
            border-color: #ed6c02;
        }

        .validation-message.error {
            border-color: #c62828;
        }

        .validation-message i {
            font-size: 56px;
            margin-bottom: 15px;
        }

        .validation-message.success i {
            color: #2e7d32;
        }

        .validation-message.warning i {
            color: #ed6c02;
        }

        .validation-message.error i {
            color: #c62828;
        }

        .validation-message .name {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 10px;
        }

        .validation-message .time {
            font-size: 18px;
            color: #666666;
            margin-bottom: 5px;
        }

        .validation-message .status {
            font-size: 16px;
            color: #666666;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    
    <div class="kiosk-container">
        <div class="company-logo">
            <img src="{{ asset('images/accupay.png') }}" alt="AccuPay">
        </div>
        
        <div class="time-display">
            <div class="current-time" id="currentTime">--:--:--</div>
            <div class="current-date" id="currentDate">---</div>
        </div>

        <div class="action-selector">
            <div class="action-btn active" id="checkInBtn" onclick="selectAction('check-in')">
                CHECK IN
            </div>
            <div class="action-btn" id="checkOutBtn" onclick="selectAction('check-out')">
                CHECK OUT
            </div>
        </div>

        <div id="qr-reader"></div>
        
        <button class="control-btn" id="scanBtn" onclick="toggleScanner()">
            <i class="fas fa-camera"></i> SCAN QR CODE
        </button>
    </div>

    <div class="validation-message" id="validationMessage"></div>

    <script>
        let html5QrCode;
        let selectedAction = 'check-in';
        let isScanning = false;

        function selectAction(action) {
            selectedAction = action;
            
            document.getElementById('checkInBtn').classList.remove('active');
            document.getElementById('checkOutBtn').classList.remove('active');
            
            if (action === 'check-in') {
                document.getElementById('checkInBtn').classList.add('active');
            } else {
                document.getElementById('checkOutBtn').classList.add('active');
            }
        }

        function updateDateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
            const dateString = now.toLocaleDateString('en-US', { 
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            document.getElementById('currentTime').textContent = timeString;
            document.getElementById('currentDate').textContent = dateString;
        }

        function toggleScanner() {
            if (isScanning) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        function startScanner() {
            const qrReader = document.getElementById('qr-reader');
            const scanBtn = document.getElementById('scanBtn');

            qrReader.style.display = 'block';
            html5QrCode = new Html5Qrcode("qr-reader");
            
            const config = { 
                fps: 10, 
                qrbox: { width: 200, height: 200 }
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                isScanning = true;
                scanBtn.innerHTML = '<i class="fas fa-stop"></i> STOP SCANNING';
            }).catch(err => {
                qrReader.style.display = 'none';
                showValidation(false, 'Camera Error', 'Unable to access camera');
            });
        }

        function stopScanner() {
            const qrReader = document.getElementById('qr-reader');
            const scanBtn = document.getElementById('scanBtn');

            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    qrReader.style.display = 'none';
                    isScanning = false;
                    scanBtn.innerHTML = '<i class="fas fa-camera"></i> SCAN QR CODE';
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (html5QrCode) {
                html5QrCode.stop();
            }
            document.getElementById('qr-reader').style.display = 'none';
            isScanning = false;
            document.getElementById('scanBtn').innerHTML = '<i class="fas fa-camera"></i> SCAN QR CODE';
            
            processAttendance(decodedText);
        }

        function onScanError(errorMessage) {
            // Ignore continuous scan errors
        }

        function processAttendance(qrToken) {
            fetch('{{ route("qr.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_token: qrToken,
                    action: selectedAction
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.employee) {
                    const employeeName = data.employee.first_name + ' ' + data.employee.last_name;
                    const time = data.time || new Date().toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        hour12: true 
                    });
                    
                    let statusText = selectedAction === 'check-in' ? 'Checked In' : 'Checked Out';
                    
                    // Check if late
                    if (data.status === 'late') {
                        statusText = 'Checked In (Late)';
                    }
                    
                    showValidation(true, employeeName, time, statusText, data.status);
                } else {
                    showValidation(false, 'Error', data.message || 'Invalid QR Code');
                }
            })
            .catch(error => {
                showValidation(false, 'System Error', 'Please try again');
            });
        }

        function showValidation(success, name, time, status, attendanceStatus) {
            const overlay = document.getElementById('overlay');
            const validationEl = document.getElementById('validationMessage');
            
            let validationClass = 'validation-message show ';
            if (success) {
                validationClass += attendanceStatus === 'late' ? 'warning' : 'success';
            } else {
                validationClass += 'error';
            }
            
            validationEl.className = validationClass;
            
            if (success) {
                const iconClass = attendanceStatus === 'late' ? 'fa-exclamation-circle' : 'fa-check-circle';
                validationEl.innerHTML = `
                    <i class="fas ${iconClass}"></i>
                    <div class="name">${name}</div>
                    <div class="time">${time}</div>
                    <div class="status">${status}</div>
                `;
            } else {
                validationEl.innerHTML = `
                    <i class="fas fa-times-circle"></i>
                    <div class="name">${name}</div>
                    <div class="status">${time}</div>
                `;
            }
            
            overlay.classList.add('active');
            
            setTimeout(() => {
                validationEl.classList.remove('show');
                overlay.classList.remove('active');
            }, 3000);
        }

        // Update time every second
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>
