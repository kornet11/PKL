<?php
// Syarat menggunakan session
session_start();

// Cek jika tidak ada session login
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
// Penghubung antar file di PHP
require 'functions.php';



// Hitung jumlah peminjaman
$dataP = mysqli_query($conn, "SELECT COUNT(*) total FROM peminjaman");
$dp = mysqli_fetch_assoc($dataP);

// Hitung jumlah guru pembimbing
$dataG = mysqli_query($conn, "SELECT COUNT(*) total FROM gurupembimbing");
$dg = mysqli_fetch_assoc($dataG);

// Hitung jumlah anggota
$dataS = mysqli_query($conn, "SELECT COUNT(*) total FROM siswa");
$ds = mysqli_fetch_assoc($dataS);

// Hitung jumlah user
$dataU = mysqli_query($conn, "SELECT COUNT(*) total FROM admin");
$du = mysqli_fetch_assoc($dataU);

// Hitung jumlah kakomli
$dataK = mysqli_query($conn, "SELECT COUNT(*) total FROM gurukaprok");
$dk = mysqli_fetch_assoc($dataK);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Dashboard PKL" />
    <meta name="author" content="Admin PKL" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PKL</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />


    <title>Dashboard PKL</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

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

        .sidebar {
            background: linear-gradient(180deg, var(--primary) 0%, #224abe 100%);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .card.border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .card.border-left-secondary {
            border-left: 0.25rem solid #858796 !important;
        }

        .card.border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .card.border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .card.border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
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

        .breadcrumb-item+.breadcrumb-item::before {
            color: #858796;
        }

        .search-form {
            position: relative;
        }

        .search-form .form-control {
            border-radius: 2rem;
            padding-right: 2.5rem;
        }

        .search-form .btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-logout {
            color: var(--danger);
            font-weight: 500;
        }

        .btn-logout:hover {
            background-color: var(--light);
            color: var(--danger);
        }

        #layoutSidenav_nav .nav-link {
            padding: 1rem;
            font-weight: 500;
        }

        #layoutSidenav_nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        #layoutSidenav_nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 700;
        }

        #sidebarToggle {
            color: rgba(255, 255, 255, 0.8);
        }

        #sidebarToggle:hover {
            color: #fff;
        }

        footer {
            background-color: #f8f9fc !important;
            border-top: 1px solid #e3e6f0;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 2rem;
        }

        /* Custom card header icon */
        .card-header-icon {
            background-color: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .empty-data {
            text-align: center;
            padding: 2rem;
            color: #858796;
        }

        .empty-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dddfeb;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-graduation-cap me-2"></i>Dashboard PKL
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Search Form -->
        <!-- <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0 search-form">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Cari data..." aria-label="Cari data" aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form> -->

        <!-- User Menu -->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                    <li><hr class="dropdown-divider" /></li> -->
                    <li><a class="dropdown-item btn-logout" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <div id="layoutSidenav_nav"> <!-- ✅ wrapper sidebar -->
            <?php include 'menu.php'; ?>
        </div>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>

                <!-- Cards Row -->
                <!-- Tambahkan setelah row cards -->
                <div class="row">
                    <!-- Tambahkan setelah row cards -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="card-header-icon">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <h6 class="m-0 font-weight-bold text-primary">Sistem Absensi QR Code</h6>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5>Kelola sistem absensi siswa dengan QR Code</h5>
                                            <p class="text-muted">Scan atau Upload QR Code untuk absensi siswa, lacak lokasi siswa, dan lihat rekap absensi harian.</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <div class="btn-group-vertical" role="group">
                                                <a href="scan_qr.php" class="btn btn-primary mb-2">
                                                    <i class="fas fa-qrcode me-2"></i>Scan QR Code
                                                </a>
                                                <!-- <a href="upload_qr.php" class="btn btn-primary mb-2">
                                                    <i class="fas fa-upload me-2"></i>Upload QR Code
                                                </a> -->
                                                <a href="tracking_gps.php" class="btn btn-info mb-2">
                                                    <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS
                                                </a>
                                                <a href="recap_harian.php" class="btn btn-info">
                                                    <i class="fas fa-chart-bar me-2"></i>Recap Harian
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- DUDI Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">DUDI</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dp['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-building-user fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GURU PEMBIMBING Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-secondary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">GURU PEMBIMBING</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dg['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-user fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SISWA Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">SISWA</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $ds['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- USER Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">USER</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $du['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Row for KAKOMLI -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">KAKOMLI</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $dk['total']; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Area -->
                    <div class="col-xl-9 col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="card-header-icon">
                                        <i class="fas fa-chart-area"></i>
                                    </div>
                                    <h6 class="m-0 font-weight-bold text-primary">Grafik Data PKL</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="myAreaChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Content Row -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="d-flex align-items-center">
                                    <div class="card-header-icon">
                                        <i class="fas fa-table"></i>
                                    </div>
                                    <h6 class="m-0 font-weight-bold text-primary">Data Terbaru</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="empty-data">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada data terbaru</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="d-flex align-items-center">
                                    <div class="card-header-icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Data</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4">
                                    <canvas id="myPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php include './assets/template/footer.php'; ?>
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

    <script>
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.font.family = 'Poppins', 'sans-serif';
        Chart.defaults.color = '#858796';

        // Area Chart Example
        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Siswa PKL",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [0, 10, 20, 15, 25, 30, 35, 40, 30, 45, 50, 65],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return value + ' orang';
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
            }
        });

        // Pie Chart Example
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Siswa", "Guru Pembimbing", "DUDI", "User", "Kakomli"],
                datasets: [{
                    data: [<?= $ds['total'] ?>, <?= $dg['total'] ?>, <?= $dp['total'] ?>, <?= $du['total'] ?>, <?= $dk['total'] ?>],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#858796', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#6c757d', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                },
                cutout: '80%',
            },
        });
    </script>
</body>

</html>