<?php
session_start();
require_once 'config_absensi.php';

// Cek login - untuk admin dan guru
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['id_gurupem']) && !isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}

// Ambil parameter filter
 $tanggal = isset($_GET['tanggal']) ? clean_input($_GET['tanggal']) : date('Y-m-d');
 $jurusan = isset($_GET['jurusan']) ? clean_input($_GET['jurusan']) : '';
 $type = isset($_GET['type']) ? clean_input($_GET['type']) : 'harian'; // harian atau bulanan

// Dapatkan data rekap
if ($type === 'harian') {
    $data = get_recap_harian($tanggal, $jurusan);
    $title = 'Rekap Absensi Harian';
} else {
    $bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
    $tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
    $data = get_recap_bulanan($bulan, $tahun, $jurusan);
    $title = 'Rekap Absensi Bulanan';
}

// Dapatkan daftar jurusan untuk filter
 $jurusan_query = "SELECT DISTINCT konsentrasi FROM siswa ORDER BY konsentrasi";
 $jurusan_result = $conn->query($jurusan_query);
 $jurusan_list = array();

if ($jurusan_result->num_rows > 0) {
    while ($row = $jurusan_result->fetch_assoc()) {
        $jurusan_list[] = $row['konsentrasi'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem PKL - Rekap Absensi" />
    <meta name="author" content="" />
    <title>Rekap Absensi - Sistem PKL</title>

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

        /* Progress bar styling */
        .progress {
            height: 20px;
            border-radius: 10px;
        }

        .progress-bar {
            font-weight: 600;
            font-size: 0.75rem;
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

            .card-header .form-control, 
            .card-header .form-select {
                margin-bottom: 5px;
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

        /* Custom filter styling */
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .filter-group .form-control,
        .filter-group .form-select {
            min-width: 120px;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-chart-bar me-2"></i>Rekap Absensi
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
                        <i class="fas fa-chart-bar me-2"></i><?php echo $title; ?>
                    </h1>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?php echo $title; ?></li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-table me-1"></i> Data Rekap Absensi
                        </div>
                        <div class="filter-group">
                            <?php if ($type === 'harian'): ?>
                                <input type="date" id="tanggal-filter" class="form-control form-control-sm" value="<?php echo $tanggal; ?>">
                            <?php else: ?>
                                <select id="bulan-filter" class="form-select form-select-sm">
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($bulan) && $bulan == $i) ? 'selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <select id="tahun-filter" class="form-select form-select-sm">
                                    <?php for ($i = date('Y') - 2; $i <= date('Y') + 2; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($tahun) && $tahun == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            <?php endif; ?>
                            <select id="jurusan-filter" class="form-select form-select-sm">
                                <option value="">Semua Jurusan</option>
                                <?php foreach ($jurusan_list as $j): ?>
                                    <option value="<?php echo $j; ?>" <?php echo ($jurusan == $j) ? 'selected' : ''; ?>><?php echo $j; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="type-filter" class="form-select form-select-sm">
                                <option value="harian" <?php echo ($type == 'harian') ? 'selected' : ''; ?>>Harian</option>
                                <option value="bulanan" <?php echo ($type == 'bulanan') ? 'selected' : ''; ?>>Bulanan</option>
                            </select>
                            <button id="export-btn" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Export Excel
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="recap-table" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Jurusan</th>
                                        <th>Total Siswa</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Alpha</th>
                                        <th>Persentase Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($data as $row): ?>
                                        <?php
                                        $total = $row['total_siswa'];
                                        $hadir = $row['total_hadir'];
                                        $izin = $row['total_izin'];
                                        $sakit = $row['total_sakit'];
                                        $alpha = $row['total_alpha'];
                                        
                                        $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;
                                        
                                        // Tentukan warna berdasarkan persentase
                                        $persentaseClass = 'success';
                                        if ($persentase < 70) {
                                            $persentaseClass = 'danger';
                                        } else if ($persentase < 85) {
                                            $persentaseClass = 'warning';
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $row['jurusan']; ?></td>
                                            <td><?php echo $total; ?></td>
                                            <td><?php echo $hadir; ?></td>
                                            <td><?php echo $izin; ?></td>
                                            <td><?php echo $sakit; ?></td>
                                            <td><?php echo $alpha; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-<?php echo $persentaseClass; ?>" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $persentase; ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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

    <script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#recap-table').DataTable({
            responsive: true,
            language: {
                processing: "Memproses...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: 1
                },
                {
                    orderable: false,
                    targets: [7]
                }
            ],
            order: [
                [0, 'asc']
            ],
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ]
        });
        
        // Event listener untuk filter tanggal
        const tanggalFilter = document.getElementById('tanggal-filter');
        if (tanggalFilter) {
            tanggalFilter.addEventListener('change', function() {
                applyFilters();
            });
        }
        
        // Event listener untuk filter bulan
        const bulanFilter = document.getElementById('bulan-filter');
        if (bulanFilter) {
            bulanFilter.addEventListener('change', function() {
                applyFilters();
            });
        }
        
        // Event listener untuk filter tahun
        const tahunFilter = document.getElementById('tahun-filter');
        if (tahunFilter) {
            tahunFilter.addEventListener('change', function() {
                applyFilters();
            });
        }
        
        // Event listener untuk filter jurusan
        document.getElementById('jurusan-filter').addEventListener('change', function() {
            applyFilters();
        });
        
        // Event listener untuk filter type
        document.getElementById('type-filter').addEventListener('change', function() {
            applyFilters();
        });
        
        // Event listener untuk tombol export
        document.getElementById('export-btn').addEventListener('click', function() {
            exportToExcel();
        });
        
        // Fungsi untuk menerapkan filter
        function applyFilters() {
            const type = document.getElementById('type-filter').value;
            let url = 'recap_harian.php?type=' + encodeURIComponent(type);
            
            if (type === 'harian') {
                const tanggal = document.getElementById('tanggal-filter').value;
                url += '&tanggal=' + encodeURIComponent(tanggal);
            } else {
                const bulan = document.getElementById('bulan-filter').value;
                const tahun = document.getElementById('tahun-filter').value;
                url += '&bulan=' + encodeURIComponent(bulan) + '&tahun=' + encodeURIComponent(tahun);
            }
            
            const jurusan = document.getElementById('jurusan-filter').value;
            if (jurusan) {
                url += '&jurusan=' + encodeURIComponent(jurusan);
            }
            
            window.location.href = url;
        }
        
        // Fungsi untuk export ke Excel
        function exportToExcel() {
            const type = document.getElementById('type-filter').value;
            let url = 'export_recap.php?type=' + encodeURIComponent(type);
            
            if (type === 'harian') {
                const tanggal = document.getElementById('tanggal-filter').value;
                url += '&tanggal=' + encodeURIComponent(tanggal);
            } else {
                const bulan = document.getElementById('bulan-filter').value;
                const tahun = document.getElementById('tahun-filter').value;
                url += '&bulan=' + encodeURIComponent(bulan) + '&tahun=' + encodeURIComponent(tahun);
            }
            
            const jurusan = document.getElementById('jurusan-filter').value;
            if (jurusan) {
                url += '&jurusan=' + encodeURIComponent(jurusan);
            }
            
            window.location.href = url;
        }
    });
    </script>
    <script src="assets/js/absensi.js"></script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>