<?php
session_start();
require_once 'config_absensi.php';

// Cek login - untuk admin dan guru
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['id_gurupem']) && !isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem PKL - Scan QR Code Absensi" />
    <meta name="author" content="" />
    <title>Scan QR Code Absensi - Sistem PKL</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS for absensi -->
    <link rel="stylesheet" href="assets/css/absensi.css">

    <style>
        :root {
            --primary: #4e73df;
            --secondary: #6c757d;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark);
            padding: 1rem 0.75rem;
            background-color: #f8f9fc;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        /* Custom styling for QR Scanner */
        #qr-reader {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        #qr-video {
            width: 100%;
            height: auto;
            display: block;
        }

        .nav-tabs .nav-link {
            color: var(--dark);
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header>div {
                margin-top: 10px;
                width: 100%;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin: 2px;
            }
        }

        /* Custom map container */
        #map {
            height: 500px;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-qrcode me-2"></i>Scan QR Code Absensi
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- User Menu -->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item btn-logout" href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <?php include 'menu.php'; ?>
        </div>

        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-qrcode me-2"></i>Scan QR Code Absensi
                    </h1>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Scan QR Code Absensi</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-qrcode me-1"></i> Scanner QR Code
                    </div>
                    <div class="card-body">
                        <p>Pilih salah satu metode untuk scan QR Code:</p>
                        
                        <!-- Tab untuk memilih metode -->
                        <ul class="nav nav-tabs mb-4" id="qrMethodTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="camera-tab" data-bs-toggle="tab" data-bs-target="#camera" type="button" role="tab">
                                    <i class="fas fa-camera me-2"></i>Kamera
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                                    <i class="fas fa-keyboard me-2"></i>Input Manual
                                </button>
                            </li>
                        </ul>
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="qrMethodTabsContent">
                            <!-- Tab Kamera -->
                            <div class="tab-pane fade show active" id="camera" role="tabpanel">
                                <div id="qr-reader" class="mb-4">
                                    <video id="qr-video" class="w-100" autoplay playsinline></video>
                                </div>
                                
                                <!-- Camera Selection -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="camera-select" class="form-label">Pilih Kamera:</label>
                                        <select class="form-select" id="camera-select">
                                            <option value="">Memuat daftar kamera...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Aksi:</label>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary" id="start-camera">
                                                <i class="fas fa-play me-2"></i>Mulai
                                            </button>
                                            <button class="btn btn-danger" id="stop-camera" disabled>
                                                <i class="fas fa-stop me-2"></i>Stop
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Debug Info -->
                                <div class="alert alert-secondary" id="debug-info" style="display: none;">
                                    <h6><i class="fas fa-bug me-2"></i>Debug Information:</h6>
                                    <pre id="debug-content"></pre>
                                </div>
                            </div>
                            
                            <!-- Tab Manual -->
                            <div class="tab-pane fade" id="manual" role="tabpanel">
                                <div class="text-center">
                                    <div class="mb-4">
                                        <label for="qr-code-input" class="form-label">Masukkan Kode QR Manual:</label>
                                        <input type="text" class="form-control" id="qr-code-input" placeholder="Masukkan kode QR Code">
                                        <div class="form-text">Masukkan kode QR Code yang tertera di kartu siswa</div>
                                    </div>
                                    <button class="btn btn-primary" id="submit-qr-manual">
                                        <i class="fas fa-sign-in-alt me-2"></i>Proses
                                    </button>
                                    <div id="manual-result"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hasil Scan -->
                        <div id="result-container" class="mt-3" style="display: none;">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    Data Siswa
                                </div>
                                <div class="card-body" id="student-data">
                                    <!-- Data siswa akan ditampilkan di sini -->
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-success" id="confirm-absence">Konfirmasi Absensi</button>
                                    <button class="btn btn-secondary" id="reset-scan">Scan Ulang</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Help Section -->
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle me-2"></i>Mengalami Masalah?</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Jika kamera tidak berfungsi:</strong></p>
                                        <ol>
                                            <li>Pastikan browser memiliki izin kamera</li>
                                            <li>Coba pilih kamera lain dari dropdown</li>
                                            <li>Gunakan opsi "Input Manual"</li>
                                            <li>Coba browser lain (Chrome, Firefox, Edge)</li>
                                        </ol>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Cara mengizinkan kamera:</strong></p>
                                        <ol>
                                            <li>Klik ikon <i class="fas fa-lock"></i> di address bar browser</li>
                                            <li>Cari "Kamera" atau "Camera"</li>
                                            <li>Pilih "Izinkan" atau "Allow"</li>
                                            <li>Refresh halaman ini</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button class="btn btn-outline-info" id="toggle-debug">
                                    <i class="fas fa-bug me-2"></i>Tampilkan Debug Info
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-center small">
                        <?php $date = date('Y'); ?>
                        <div class="text-muted">Copyright &copy; Web PKL By Kornet <?= $date ?>.</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="./assets/js/scripts.js"></script>
    
    <!-- Library untuk scan QR code -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let html5QrCode = null;
        let currentCameraId = null;
        let isScanning = false;
        let currentStudentData = null;
        
        const qrReader = document.getElementById("qr-reader");
        const qrVideo = document.getElementById("qr-video");
        const cameraSelect = document.getElementById("camera-select");
        const startBtn = document.getElementById("start-camera");
        const stopBtn = document.getElementById("stop-camera");
        const debugInfo = document.getElementById("debug-info");
        const debugContent = document.getElementById("debug-content");
        const toggleDebugBtn = document.getElementById("toggle-debug");
        const qrCodeInput = document.getElementById("qr-code-input");
        const submitManualBtn = document.getElementById("submit-qr-manual");
        const manualResult = document.getElementById("manual-result");
        const resultContainer = document.getElementById("result-container");
        const studentData = document.getElementById("student-data");
        const confirmAbsence = document.getElementById("confirm-absence");
        const resetScan = document.getElementById("reset-scan");
        
        // Debug function
        function logDebug(message) {
            const timestamp = new Date().toLocaleTimeString();
            debugContent.textContent += `[${timestamp}] ${message}\n`;
            console.log(message);
        }
        
        // Toggle debug info
        toggleDebugBtn.addEventListener('click', function() {
            if (debugInfo.style.display === 'none') {
                debugInfo.style.display = 'block';
                toggleDebugBtn.innerHTML = '<i class="fas fa-eye-slash me-2"></i>Sembunyikan Debug Info';
            } else {
                debugInfo.style.display = 'none';
                toggleDebugBtn.innerHTML = '<i class="fas fa-bug me-2"></i>Tampilkan Debug Info';
            }
        });
        
        // Initialize
        logDebug("Initializing QR Scanner...");
        logDebug("User Agent: " + navigator.userAgent);
        logDebug("HTTPS: " + (location.protocol === 'https:' ? 'Yes' : 'No'));
        
        // Check if browser supports camera
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            logDebug("Browser does not support camera access");
            qrReader.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Browser Anda tidak mendukung akses kamera. Silakan gunakan opsi lain.
                </div>
            `;
            return;
        }
        
        // Get available cameras
        Html5Qrcode.getCameras().then(devices => {
            logDebug(`Found ${devices.length} cameras`);
            
            if (devices && devices.length > 0) {
                cameraSelect.innerHTML = '';
                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    option.text = device.label || `Camera ${index + 1}`;
                    cameraSelect.appendChild(option);
                    logDebug(`Camera ${index + 1}: ${device.label || 'Unknown'} (ID: ${device.id})`);
                });
                
                // Auto-select back camera if available
                const backCamera = devices.find(device => 
                    device.label.toLowerCase().includes('back') || 
                    device.label.toLowerCase().includes('environment')
                );
                if (backCamera) {
                    cameraSelect.value = backCamera.id;
                    logDebug(`Auto-selected back camera: ${backCamera.label}`);
                }
            } else {
                cameraSelect.innerHTML = '<option value="">Tidak ada kamera ditemukan</option>';
                logDebug("No cameras found");
            }
        }).catch(err => {
            logDebug(`Error getting cameras: ${err}`);
            cameraSelect.innerHTML = '<option value="">Error loading cameras</option>';
        });
        
        // Start camera
        startBtn.addEventListener('click', function() {
            if (isScanning) return;
            
            const cameraId = cameraSelect.value;
            if (!cameraId) {
                alert('Silakan pilih kamera terlebih dahulu');
                return;
            }
            
            logDebug(`Starting camera with ID: ${cameraId}`);
            
            // Clear previous scanner
            if (html5QrCode) {
                html5QrCode.clear();
            }
            
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0
            };
            
            html5QrCode.start(
                cameraId,
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                isScanning = true;
                currentCameraId = cameraId;
                startBtn.disabled = true;
                stopBtn.disabled = false;
                cameraSelect.disabled = true;
                logDebug("Camera started successfully");
            }).catch(err => {
                logDebug(`Error starting camera: ${err}`);
                handleCameraError(err);
            });
        });
        
        // Stop camera
        stopBtn.addEventListener('click', function() {
            if (!isScanning || !html5QrCode) return;
            
            logDebug("Stopping camera...");
            
            html5QrCode.stop().then(() => {
                isScanning = false;
                currentCameraId = null;
                startBtn.disabled = false;
                stopBtn.disabled = true;
                cameraSelect.disabled = false;
                logDebug("Camera stopped successfully");
            }).catch(err => {
                logDebug(`Error stopping camera: ${err}`);
            });
        });
        
        // Scan success
        function onScanSuccess(decodedText, decodedResult) {
            logDebug(`QR Code detected: ${decodedText}`);
            logDebug(`Scan result: ${JSON.stringify(decodedResult)}`);
            
            // Stop scanning
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    cameraSelect.disabled = false;
                }).catch(err => {
                    logDebug(`Error stopping camera after scan: ${err}`);
                });
            }
            
            // Process QR Code
            processQRCode(decodedText);
        }
        
        // Scan error
        function onScanError(errorMessage) {
            // This is called frequently, so we don't log every error
            // console.log(`QR scan error: ${errorMessage}`);
        }
        
        // Handle camera error
        function handleCameraError(error) {
            console.error('Camera error:', error);
            
            let errorMessage = 'Tidak dapat mengakses kamera. ';
            
            if (error.name === 'NotAllowedError') {
                errorMessage += 'Izin kamera ditolak. Silakan izinkan akses kamera di browser Anda.';
            } else if (error.name === 'NotFoundError') {
                errorMessage += 'Kamera tidak ditemukan. Pastikan kamera terhubung dengan baik.';
            } else if (error.name === 'NotSupportedError') {
                errorMessage += 'Browser tidak mendukung fitur kamera.';
            } else if (error.name === 'NotReadableError') {
                errorMessage += 'Kamera sedang digunakan oleh aplikasi lain.';
            } else {
                errorMessage += `Error: ${error.message || error}`;
            }
            
            qrReader.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${errorMessage}
                </div>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-lightbulb me-2"></i>Solusi:</h6>
                    <ol>
                        <li>Klik ikon <i class="fas fa-lock"></i> di address bar browser</li>
                        <li>Cari opsi "Kamera" atau "Camera"</li>
                        <li>Pilih "Izinkan" atau "Allow"</li>
                        <li>Refresh halaman ini</li>
                        <li>Atau gunakan opsi "Input Manual"</li>
                    </ol>
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh Halaman
                    </button>
                </div>
            `;
        }
        
        // Process QR Code
        function processQRCode(qrCode) {
            logDebug(`Processing QR Code: ${qrCode}`);
            
            // Show loading
            studentData.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>';
            resultContainer.style.display = 'block';
            
            fetch('process_absensi.php?action=validate&qr_code=' + encodeURIComponent(qrCode))
                .then(response => {
                    logDebug(`Response status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    logDebug(`Response data: ${JSON.stringify(data)}`);
                    
                    if (data.status === 'success') {
                        currentStudentData = data.data;
                        displayStudentData(data.data);
                    } else {
                        logDebug(`QR Code validation failed: ${data.message}`);
                        
                        // Show error message
                        studentData.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                QR Code tidak valid: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    logDebug(`Network error: ${error}`);
                    
                    // Show error message
                    studentData.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memvalidasi QR Code. Silakan coba lagi.
                        </div>
                    `;
                });
        }
        
        // Display student data
        function displayStudentData(student) {
            let statusText = 'Belum Absen';
            let statusClass = 'warning';
            
            if (student.jam_masuk && !student.jam_keluar) {
                statusText = 'Sudah Absen Masuk';
                statusClass = 'info';
            } else if (student.jam_masuk && student.jam_keluar) {
                statusText = 'Sudah Absen Pulang';
                statusClass = 'success';
            }
            
            studentData.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>NISN:</strong> ${student.nisn}</p>
                        <p><strong>Nama:</strong> ${student.nama}</p>
                        <p><strong>Kelas:</strong> ${student.kelas}</p>
                        <p><strong>Jurusan:</strong> ${student.konsentrasi}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-${statusClass}">${statusText}</span></p>
                        ${student.jam_masuk ? `<p><strong>Jam Masuk:</strong> ${student.jam_masuk}</p>` : ''}
                        ${student.jam_keluar ? `<p><strong>Jam Keluar:</strong> ${student.jam_keluar}</p>` : ''}
                        ${student.lokasi_masuk ? `<p><strong>Lokasi Masuk:</strong> ${student.lokasi_masuk}</p>` : ''}
                    </div>
                </div>
            `;
            
            resultContainer.style.display = 'block';
            
            // Disable confirm button if already checked out
            if (student.jam_masuk && student.jam_keluar) {
                confirmAbsence.disabled = true;
                confirmAbsence.textContent = 'Sudah Absen Hari Ini';
            } else {
                confirmAbsence.disabled = false;
                confirmAbsence.textContent = student.jam_masuk ? 'Absen Pulang' : 'Absen Masuk';
            }
        }
        
        // Event listener for reset button
        resetScan.addEventListener('click', function() {
            resultContainer.style.display = 'none';
            qrCodeInput.value = '';
            currentStudentData = null;
        });
        
        // Event listener for manual input
        submitManualBtn.addEventListener('click', function() {
            const qrCode = qrCodeInput.value.trim();
            if (!qrCode) {
                alert('Silakan masukkan kode QR Code');
                return;
            }
            
            logDebug(`Manual QR Code input: ${qrCode}`);
            processQRCode(qrCode);
        });
        
        // Event listener for confirm absence
        confirmAbsence.addEventListener('click', function() {
            if (currentStudentData) {
                // Get location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            // Send data to server
                            processAttendance(currentStudentData, latitude, longitude);
                        },
                        function(error) {
                            alert('Error getting location: ' + error.message);
                        }
                    );
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            }
        });
        
        // Process attendance
        function processAttendance(student, latitude, longitude) {
            const actionType = student.jam_masuk ? 'keluar' : 'masuk';
            
            // Show loading
            confirmAbsence.disabled = true;
            confirmAbsence.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            
            // Send attendance data to server
            const formData = new FormData();
            formData.append('action', 'process');
            formData.append('id_siswa', student.id_siswa);
            formData.append('id_qr', student.id_qr);
            formData.append('latitude', latitude);
            formData.append('longitude', longitude);
            formData.append('lokasi', 'Lokasi tidak diketahui');
            formData.append('type', actionType);
            
            fetch('process_absensi.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    // Refresh student data
                    processQRCode(qrCodeInput.value);
                } else {
                    alert('Gagal menyimpan absensi: ' + data.message);
                }
                
                // Reset button
                confirmAbsence.disabled = false;
                confirmAbsence.textContent = actionType === 'masuk' ? 'Absen Masuk' : 'Absen Pulang';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan absensi');
                
                // Reset button
                confirmAbsence.disabled = false;
                confirmAbsence.textContent = actionType === 'masuk' ? 'Absen Masuk' : 'Absen Pulang';
            });
        }
        
        // Auto-start camera if permission already granted
        setTimeout(() => {
            if (cameraSelect.value && !isScanning) {
                logDebug("Attempting to auto-start camera...");
                startBtn.click();
            }
        }, 1000);
    });
    </script>
    <script src="js/absensi.js"></script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>