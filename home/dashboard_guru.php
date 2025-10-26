<?php
session_start();
require 'functions.php';

// cek login
if (!isset($_SESSION['login']) || !isset($_SESSION['id_gurupem'])) {
    header('Location: ../index.php');
    exit;
}

 $id_gurupem = $_SESSION['id_gurupem'];

// ======================
// LOGIKA TAMBAH TUGAS (PRG)
// ======================
if (isset($_POST['tambah_tugas'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $deadline = mysqli_real_escape_string($conn, $_POST['tanggal_deadline']);

    mysqli_query($conn, "INSERT INTO tugas (judul, deskripsi, jurusan, tanggal_deadline, id_gurupem) 
                         VALUES ('$judul','$deskripsi','$jurusan','$deadline','$id_gurupem')");
    $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Tugas berhasil ditambahkan.'];
    header('Location: dashboard_guru.php');
    exit;
}

// ======================
// LOGIKA EDIT TUGAS (PRG)
// ======================
if (isset($_POST['edit_tugas'])) {
    $id = intval($_POST['id_tugas']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $deadline = mysqli_real_escape_string($conn, $_POST['tanggal_deadline']);

    mysqli_query($conn, "UPDATE tugas SET judul='$judul', deskripsi='$deskripsi', jurusan='$jurusan', tanggal_deadline='$deadline' WHERE id_tugas='$id'");
    $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Tugas berhasil diubah.'];
    header('Location: dashboard_guru.php');
    exit;
}

// ======================
// LOGIKA HAPUS TUGAS (PRG)
// ======================
if (isset($_POST['hapus_tugas'])) {
    $id = intval($_POST['id_tugas']);
    mysqli_query($conn, "DELETE FROM histori_tugas WHERE id_tugas='$id'");
    mysqli_query($conn, "DELETE FROM tugas WHERE id_tugas='$id'");
    $_SESSION['flash'] = ['icon'=>'success','title'=>'Berhasil','text'=>'Tugas berhasil dihapus.'];
    header('Location: dashboard_guru.php');
    exit;
}

// ======================
// AMBIL DATA TUGAS
// ======================
 $tugas = [];
 $tugas_query = mysqli_query($conn, "
    SELECT t.*, 
    (SELECT COUNT(*) FROM histori_tugas h WHERE h.id_tugas=t.id_tugas AND h.status='Selesai') AS siswa_selesai
    FROM tugas t 
    WHERE t.id_gurupem='$id_gurupem' 
    ORDER BY t.tanggal_deadline ASC
");
while ($row = mysqli_fetch_assoc($tugas_query)) {
    // ambil siswa yang sudah mengumpulkan tugas
    $sub_q = mysqli_query($conn, "
        SELECT h.*, s.nama 
        FROM histori_tugas h
        JOIN siswa s ON h.id_siswa = s.id_siswa
        WHERE h.id_tugas = " . $row['id_tugas'] . "
        ORDER BY h.tanggal_upload DESC
    ");
    $subs = [];
    while ($s = mysqli_fetch_assoc($sub_q)) $subs[] = $s;
    $row['subs'] = $subs;

    $tugas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem PKL - Dashboard Guru" />
    <meta name="author" content="" />
    <title>Dashboard Guru - Sistem PKL</title>

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        /* Custom badge styling */
        .badge-jurusan {
            font-size: 0.75rem;
            padding: 0.35rem 0.5rem;
            border-radius: 0.35rem;
            font-weight: 600;
        }

        .badge-rpl {
            background-color: rgba(78, 115, 223, 0.2);
            color: #4e73df;
        }

        .badge-kuliner {
            background-color: rgba(28, 200, 138, 0.2);
            color: #1cc88a;
        }

        .badge-busana {
            background-color: rgba(54, 185, 204, 0.2);
            color: #36b9cc;
        }

        .badge-atph {
            background-color: rgba(246, 194, 62, 0.2);
            color: #f6c23e;
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
            <i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Guru
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
                        <i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Guru
                    </h1>
                    <!-- <a href="cetak_excel_tugas.php" class="btn btn-success">
                        <i class="fa-solid fa-file-excel me-1"></i>Export Excel
                    </a> -->
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Dashboard Guru</li>
                </ol>

                <!-- Card untuk Absensi Siswa -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center py-3">
                        <div class="card-header-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <span>Absensi Siswa</span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5>Scan atau Upload QR Code untuk absensi siswa</h5>
                                <p class="text-muted">Lacak lokasi siswa dan lihat rekap absensi harian.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="btn-group-vertical" role="group">
                                    <a href="absensi/scan_qr.php" class="btn btn-primary mb-2">
                                        <i class="fas fa-qrcode me-2"></i>Scan QR Code
                                    </a>
                                    <a href="absensi/upload_qr.php" class="btn btn-primary mb-2">
                                        <i class="fas fa-upload me-2"></i>Upload QR Code
                                    </a>
                                    <a href="absensi/tracking_gps.php" class="btn btn-info mb-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS
                                    </a>
                                    <a href="absensi/recap_harian.php" class="btn btn-info">
                                        <i class="fas fa-chart-bar me-2"></i>Recap Harian
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card untuk Daftar Tugas -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center">
                            <div class="card-header-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <span>Daftar Tugas</span>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="fas fa-plus me-1"></i>Tambah Tugas
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tugasTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Tugas</th>
                                        <th>Jurusan</th>
                                        <th>Deadline</th>
                                        <th>Siswa Selesai</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($tugas as $t): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($t['judul']) ?></td>
                                            <td>
                                                <?php
                                                $badgeClass = '';
                                                switch (strtolower($t['jurusan'])) {
                                                    case 'rpl':
                                                        $badgeClass = 'badge-rpl';
                                                        break;
                                                    case 'kuliner':
                                                        $badgeClass = 'badge-kuliner';
                                                        break;
                                                    case 'busana':
                                                        $badgeClass = 'badge-busana';
                                                        break;
                                                    case 'atph':
                                                        $badgeClass = 'badge-atph';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge badge-jurusan <?= $badgeClass ?>"><?= htmlspecialchars($t['jurusan']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($t['tanggal_deadline']) ?></td>
                                            <td>
                                                <a href="lihat_tugas.php?id_tugas=<?= $t['id_tugas'] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-check-circle me-1"></i><?= $t['siswa_selesai'] ?> siswa selesai
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-warning btn-action" onclick="openEditModal(<?= $t['id_tugas'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-action" onclick="openHapusModal(<?= $t['id_tugas'] ?>)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

    <!-- Modal Tambah Tugas -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Tugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select name="jurusan" class="form-select" required>
                                    <option value="" selected disabled>Pilih Jurusan</option>
                                    <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                                    <option value="KULINER">Tata Boga (KULINER)</option>
                                    <option value="BUSANA">Tata Busana (BUSANA)</option>
                                    <option value="ATPH">Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deadline <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_deadline" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah_tugas" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tugas -->
    <?php foreach ($tugas as $t): ?>
        <div class="modal fade" id="modalEdit<?= $t['id_tugas'] ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $t['id_tugas'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditLabel<?= $t['id_tugas'] ?>">Edit Tugas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_tugas" value="<?= $t['id_tugas'] ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($t['judul']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                    <select name="jurusan" class="form-select" required>
                                        <option value="RPL" <?= $t['jurusan'] == 'RPL' ? 'selected' : '' ?>>Rekayasa Perangkat Lunak (RPL)</option>
                                        <option value="KULINER" <?= $t['jurusan'] == 'KULINER' ? 'selected' : '' ?>>Tata Boga (KULINER)</option>
                                        <option value="BUSANA" <?= $t['jurusan'] == 'BUSANA' ? 'selected' : '' ?>>Tata Busana (BUSANA)</option>
                                        <option value="ATPH" <?= $t['jurusan'] == 'ATPH' ? 'selected' : '' ?>>Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($t['deskripsi']) ?></textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deadline <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_deadline" class="form-control" value="<?= $t['tanggal_deadline'] ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="edit_tugas" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Modal Hapus Tugas -->
    <?php foreach ($tugas as $t): ?>
        <div class="modal fade" id="modalHapus<?= $t['id_tugas'] ?>" tabindex="-1" aria-labelledby="modalHapusLabel<?= $t['id_tugas'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalHapusLabel<?= $t['id_tugas'] ?>">Hapus Tugas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_tugas" value="<?= $t['id_tugas'] ?>">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-warning fa-3x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p>Yakin ingin menghapus tugas <strong>"<?= htmlspecialchars($t['judul']) ?>"</strong>?</p>
                                    <p class="text-muted small">Semua data terkait tugas ini akan ikut terhapus.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="hapus_tugas" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

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
            $('#tugasTable').DataTable({
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
                        targets: [5]
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

            // Inisialisasi tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Menangani modal dengan benar
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Hapus backdrop yang mungkin tersisa
                    document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                        backdrop.remove();
                    });
                    // Pastikan class modal-open dihapus dari body
                    document.body.classList.remove('modal-open');
                    document.body.style.paddingRight = '';
                });
            });
            
            // Mengatasi konflik dengan SweetAlert
            var originalShow = bootstrap.Modal.prototype.show;
            bootstrap.Modal.prototype.show = function() {
                if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                    Swal.close();
                }
                originalShow.apply(this, arguments);
            };
        });

        // Fungsi untuk membuka modal edit
        function openEditModal(id) {
            // Tutup semua modal yang terbuka
            document.querySelectorAll('.modal.show').forEach(function(openModal) {
                var modal = bootstrap.Modal.getInstance(openModal);
                if (modal) modal.hide();
            });
            
            // Buka modal yang dipilih
            var modalElement = document.getElementById('modalEdit' + id);
            if (modalElement) {
                var modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
        
        // Fungsi untuk membuka modal hapus
        function openHapusModal(id) {
            // Tutup semua modal yang terbuka
            document.querySelectorAll('.modal.show').forEach(function(openModal) {
                var modal = bootstrap.Modal.getInstance(openModal);
                if (modal) modal.hide();
            });
            
            // Buka modal yang dipilih
            var modalElement = document.getElementById('modalHapus' + id);
            if (modalElement) {
                var modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }
    </script>

    <!-- Flash message (tampilkan sekali jika ada) -->
    <?php if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: <?= json_encode($flash['icon']) ?>,
                title: <?= json_encode($flash['title']) ?>,
                text: <?= json_encode($flash['text']) ?>,
                showConfirmButton: false,
                timer: 1500
            });
        });
    </script>
    <?php endif; ?>

    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>