<?php
session_start();
require_once 'config_absensi.php';

// Cek login - untuk siswa
if (!isset($_SESSION['id_siswa']) && !isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}

// Jika siswa yang login
if (isset($_SESSION['id_siswa'])) {
    $id_siswa = $_SESSION['id_siswa'];
    
    // Generate QR Code
    $qr_code = generate_qr_code($id_siswa);
    
    if ($qr_code) {
        // Tampilkan QR Code
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
            <meta name="description" content="Sistem PKL - QR Code Absensi" />
            <meta name="author" content="" />
            <title>QR Code Absensi - Sistem PKL</title>

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

                .table-hover tbody tr:hover {
                    background-color: rgba(78, 115, 223, 0.05);
                }

                /* Custom QR Code styling */
                .qr-container {
                    background-color: white;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
                    margin-bottom: 20px;
                    display: inline-block;
                }

                .qr-code {
                    border: 2px solid #e3e6f0;
                    padding: 10px;
                    border-radius: 5px;
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

                /* DataTables custom styling */
                .dataTables_wrapper .dataTables_paginate .paginate_button {
                    border-radius: 5px !important;
                    margin: 0 3px;
                    padding: 5px 10px;
                }

                .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                    background: var(--primary) !important;
                    color: white !important;
                    border: none !important;
                }

                .dataTables_wrapper .dataTables_filter input {
                    border-radius: 5px;
                    padding: 5px 10px;
                    border: 1px solid #ced4da;
                }
            </style>
        </head>

        <body class="sb-nav-fixed">
            <!-- Navbar -->
            <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
                <a class="navbar-brand ps-3" href="index.php">
                    <i class="fas fa-qrcode me-2"></i>QR Code Absensi
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
                                <i class="fas fa-qrcode me-2"></i>QR Code Absensi
                            </h1>
                        </div>

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">QR Code Absensi</li>
                        </ol>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-qrcode me-1"></i> QR Code untuk Absensi
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-6 text-center">
                                        <p class="mb-3">Tanggal: <?php echo date('d-m-Y'); ?></p>
                                        <div class="qr-container mb-3">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $qr_code; ?>" alt="QR Code" class="qr-code">
                                        </div>
                                        <div class="alert alert-info">
                                            <p class="mb-0"><strong>Kode:</strong> <?php echo $qr_code; ?></p>
                                        </div>
                                        <p class="text-muted mb-4">Tunjukkan QR Code ini untuk melakukan absensi</p>
                                        <a href="../dashboard_siswa.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-info-circle me-1"></i> Informasi Penting
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Cara Menggunakan QR Code:</h5>
                                        <ol>
                                            <li>Tunjukkan QR Code ini kepada guru atau petugas absensi</li>
                                            <li>Pastikan QR Code terlihat jelas dan tidak terpotong</li>
                                            <li>QR Code ini berlaku hanya untuk hari ini</li>
                                            <li>Jika QR Code tidak dapat discan, silakan refresh halaman ini</li>
                                        </ol>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Informasi Tambahan:</h5>
                                        <ul>
                                            <li>QR Code akan otomatis diperbarui setiap hari</li>
                                            <li>Jangan berbagikan QR Code Anda kepada orang lain</li>
                                            <li>QR Code mengandung informasi identitas Anda</li>
                                            <li>Hubungi administrator jika mengalami masalah</li>
                                        </ul>
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

            <!-- Custom JS for absensi -->
            <script src="assets/js/absensi.js"></script>
            <script src="./assets/template/logout-alert.php"></script>
        </body>

        </html>
        <?php
    } else {
        echo "Gagal generate QR Code";
    }
} else {
    // Jika bukan siswa, redirect ke dashboard
    if (isset($_SESSION['id_admin'])) {
        header("Location: ../index.php");
    } else if (isset($_SESSION['id_gurupem'])) {
        header("Location: ../dashboard_guru.php");
    }
    exit();
}
?>