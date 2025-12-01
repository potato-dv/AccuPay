<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Scanner</title>
    <link rel="icon" type="image/png" href="{{ asset('images/accupay.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .scanner-card {
            background: white;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            filter: grayscale(100%);
        }

        h1 {
            color: #000000;
            font-size: 24px;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 700;
        }

        .subtitle {
            color: #666666;
            margin-bottom: 25px;
            font-size: 13px;
            text-align: center;
        }

        .time-display {
            font-size: 36px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 5px;
            text-align: center;
            font-family: 'Courier New', monospace;
        }

        .date-display {
            font-size: 14px;
            color: #666666;
            margin-bottom: 25px;
            text-align: center;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 25px;
        }

        .action-btn {
            padding: 15px;
            border: 2px solid #000000;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #000000;
        }

        .action-btn i {
            font-size: 24px;
        }

        .action-btn.active {
            background: #000000;
            color: #ffffff;
        }

        .action-btn:hover:not(.active) {
            background: #f5f5f5;
        }

        .scanner-area {
            background: #f9f9f9;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        #qr-reader {
            width: 100%;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 12px;
            border: 2px solid #000000;
        }

        #qr-reader video {
            width: 100% !important;
            border-radius: 4px;
        }

        .scanner-controls {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 12px;
        }

        .control-btn {
            padding: 10px 18px;
            border: 2px solid #000000;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 13px;
            background: #ffffff;
            color: #000000;
        }

        .control-btn:hover {
            background: #000000;
            color: #ffffff;
        }

        .control-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f5f5f5;
        }

        .control-btn:disabled:hover {
            background: #f5f5f5;
            color: #000000;
        }

        #qr-input {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border: 2px solid #000000;
            border-radius: 4px;
            outline: none;
            transition: all 0.2s ease;
            text-align: center;
            font-family: 'Courier New', monospace;
            background: #ffffff;
        }

        #qr-input:focus {
            border-color: #000000;
            background: #f9f9f9;
        }

        .instruction {
            color: #666666;
            font-size: 12px;
            margin-top: 12px;
            text-align: center;
        }

        .camera-status {
            background: #f5f5f5;
            border: 1px solid #cccccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 12px;
            text-align: center;
            font-weight: 600;
            color: #000000;
            font-size: 13px;
        }

        .camera-status.active {
            background: #000000;
            color: #ffffff;
            border-color: #000000;
        }

        .camera-status.error {
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
        }

        .result-message {
            margin-top: 15px;
            padding: 15px;
            border-radius: 4px;
            font-weight: 600;
            display: none;
            animation: slideIn 0.3s ease;
            text-align: center;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-message.success {
            background: #ffffff;
            color: #000000;
            border: 2px solid #000000;
        }

        .result-message.error {
            background: #f5f5f5;
            color: #000000;
            border: 2px dashed #000000;
        }

        .employee-info {
            margin-top: 10px;
            font-size: 13px;
            color: #333333;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            color: #000000;
            padding: 10px 20px;
            border: 2px solid #000000;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-top: 15px;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #000000;
            color: #ffffff;
        }

        .divider {
            margin: 15px 0;
            text-align: center;
            color: #999999;
            font-weight: 600;
            font-size: 12px;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #cccccc;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        @media (max-width: 640px) {
            .scanner-card {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            .time-display {
                font-size: 28px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="scanner-card">
            <img src="{{ asset('images/accupay.png') }}" alt="AccuPay Logo" class="logo">
            <h1>QR Attendance</h1>
            <p class="subtitle">Scan QR code for check-in/out</p>

            <div class="time-display" id="currentTime">--:--:--</div>
            <div class="date-display" id="currentDate">---</div>

            <div class="action-buttons">
                <button class="action-btn active" id="checkInBtn" onclick="selectAction('check-in')">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Check In</span>
                </button>
                <button class="action-btn" id="checkOutBtn" onclick="selectAction('check-out')">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Check Out</span>
                </button>
            </div>

            <div class="scanner-area">
                <div class="camera-status" id="cameraStatus">
                    <i class="fas fa-camera"></i> Camera Ready
                </div>

                <div id="qr-reader"></div>

                <div class="scanner-controls">
                    <button class="control-btn" id="startBtn" onclick="startScanner()">
                        <i class="fas fa-play"></i> Start
                    </button>
                    <button class="control-btn" id="stopBtn" onclick="stopScanner()" disabled>
                        <i class="fas fa-stop"></i> Stop
                    </button>
                </div>

                <div class="divider">OR</div>

                <input 
                    type="text" 
                    id="qr-input" 
                    placeholder="Enter QR Token Manually" 
                    autocomplete="off"
                >
                <p class="instruction">
                    <i class="fas fa-keyboard"></i> Manual entry available
                </p>
            </div>

            <div class="result-message" id="resultMessage"></div>

            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        let selectedAction = 'check-in';
        let html5QrCode = null;
        let isScanning = false;

        // Update time and date
        function updateDateTime() {
            const now = new Date();
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            
            document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
            document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Select action
        function selectAction(action) {
            selectedAction = action;
            document.getElementById('checkInBtn').classList.toggle('active', action === 'check-in');
            document.getElementById('checkOutBtn').classList.toggle('active', action === 'check-out');
        }

        // Initialize QR Code Scanner
        function startScanner() {
            const cameraStatus = document.getElementById('cameraStatus');
            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("qr-reader");
            }

            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };

            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    // QR code successfully scanned
                    if (!isScanning) {
                        isScanning = true;
                        processScan(decodedText);
                        
                        // Prevent rapid successive scans
                        setTimeout(() => {
                            isScanning = false;
                        }, 3000);
                    }
                },
                (errorMessage) => {
                    // Scanning errors can be ignored (happens continuously)
                }
            ).then(() => {
                cameraStatus.className = 'camera-status active';
                cameraStatus.innerHTML = '<i class="fas fa-video"></i> Camera Active - Scanning...';
                startBtn.disabled = true;
                stopBtn.disabled = false;
            }).catch((err) => {
                cameraStatus.className = 'camera-status error';
                cameraStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Camera Error: ' + err;
                console.error("Camera start error:", err);
            });
        }

        // Stop scanner
        function stopScanner() {
            const cameraStatus = document.getElementById('cameraStatus');
            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');

            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    cameraStatus.className = 'camera-status';
                    cameraStatus.innerHTML = '<i class="fas fa-camera"></i> Camera Stopped';
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                }).catch((err) => {
                    console.error("Camera stop error:", err);
                });
            }
        }

        // Manual input handler
        document.getElementById('qr-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const qrToken = this.value.trim();
                if (qrToken) {
                    processScan(qrToken);
                    this.value = '';
                }
            }
        });

        // Process QR scan
        async function processScan(qrToken) {
            const resultDiv = document.getElementById('resultMessage');

            try {
                const response = await fetch('{{ route("qr.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        qr_token: qrToken,
                        action: selectedAction
                    })
                });

                const data = await response.json();

                if (data.success) {
                    resultDiv.className = 'result-message success';
                    resultDiv.innerHTML = `
                        <div style="font-size: 20px; margin-bottom: 10px;">
                            <i class="fas fa-check-circle"></i> ${data.message}
                        </div>
                        <div class="employee-info">
                            <strong>${data.employee_name}</strong><br>
                            ${data.action === 'check-in' ? 'Checked in' : 'Checked out'} at ${data.time}
                            ${data.hours_worked ? '<br>Hours worked: ' + data.hours_worked : ''}
                        </div>
                    `;

                    // Play success sound (optional)
                    playSound('success');
                } else {
                    resultDiv.className = 'result-message error';
                    resultDiv.innerHTML = `
                        <div style="font-size: 18px;">
                            <i class="fas fa-exclamation-circle"></i> ${data.message}
                        </div>
                    `;

                    // Play error sound (optional)
                    playSound('error');
                }

                resultDiv.style.display = 'block';

                // Clear result after 4 seconds
                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 4000);

            } catch (error) {
                resultDiv.className = 'result-message error';
                resultDiv.innerHTML = `
                    <div style="font-size: 18px;">
                        <i class="fas fa-exclamation-triangle"></i> Error processing scan. Please try again.
                    </div>
                `;
                resultDiv.style.display = 'block';

                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 4000);
            }
        }

        // Simple beep sound for feedback
        function playSound(type) {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            if (type === 'success') {
                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.3;
                oscillator.start();
                setTimeout(() => oscillator.stop(), 100);
            } else {
                oscillator.frequency.value = 400;
                gainNode.gain.value = 0.3;
                oscillator.start();
                setTimeout(() => oscillator.stop(), 200);
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.error(err));
            }
        });
    </script>
</body>
</html>
