<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'functions.php';

if (!isset($_SESSION['login'])) {
    echo "<script>document.location.href = '../index.php';</script>";
    exit;
}

 $id_siswa = $_SESSION['id_siswa'];

// Ambil jurusan siswa
 $jurusan_siswa = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT konsentrasi FROM siswa WHERE id_siswa='$id_siswa'")
)['konsentrasi'];

// =====================
// LOGIKA UPLOAD / EDIT
// =====================
if (isset($_POST['upload'])) {
    $id = $_POST['id_tugas'];

    // 🔐 Cek deadline sebelum proses upload
    $cekDeadline = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tanggal_deadline FROM tugas WHERE id_tugas='$id'"));
    $now = date('Y-m-d H:i:s');
    if ($now > $cekDeadline['tanggal_deadline']) {
        $_SESSION['error'] = 'Deadline sudah lewat! Tidak bisa upload.';
        header('Location: dashboard_siswa.php');
        exit;
    }

    $targetDir = "uploads/tugas/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["file_tugas"]["name"]);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    if (in_array($fileType, $allowed)) {
        if (move_uploaded_file($_FILES["file_tugas"]["tmp_name"], $targetFile)) {
            // cek apakah sudah ada histori sebelumnya
            $cek = mysqli_query($conn, "SELECT * FROM histori_tugas WHERE id_tugas='$id' AND id_siswa='$id_siswa'");
            if (mysqli_num_rows($cek) > 0) {
                // update
                $row = mysqli_fetch_assoc($cek);
                $oldFile = "uploads/tugas/" . $row['jawaban'];
                if (file_exists($oldFile)) unlink($oldFile);

                mysqli_query($conn, "UPDATE histori_tugas 
                                      SET jawaban='$fileName', status='Selesai', tanggal_upload=NOW() 
                                      WHERE id_tugas='$id' AND id_siswa='$id_siswa'");
            } else {
                // insert baru
                mysqli_query($conn, "INSERT INTO histori_tugas (id_tugas, id_siswa, jawaban, status, tanggal_upload) 
                                      VALUES ('$id','$id_siswa','$fileName','Selesai',NOW())");
            }
            $_SESSION['success'] = 'Tugas berhasil diupload!';
            header('Location: dashboard_siswa.php');
            exit;
        } else {
            $_SESSION['error'] = 'Gagal upload file!';
            header('Location: dashboard_siswa.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Format file tidak diizinkan!';
        header('Location: dashboard_siswa.php');
        exit;
    }
}

// =====================
// LOGIKA HAPUS
// =====================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // 🔐 Cek deadline sebelum hapus
    $cekDeadline = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tanggal_deadline FROM tugas WHERE id_tugas='$id'"));
    $now = date('Y-m-d H:i:s');
    if ($now > $cekDeadline['tanggal_deadline']) {
        $_SESSION['error'] = 'Deadline sudah lewat! Tidak bisa hapus jawaban.';
        header('Location: dashboard_siswa.php');
        exit;
    }

    $cek = mysqli_query($conn, "SELECT * FROM histori_tugas WHERE id_tugas='$id' AND id_siswa='$id_siswa'");
    if ($row = mysqli_fetch_assoc($cek)) {
        $oldFile = "uploads/tugas/" . $row['jawaban'];
        if (file_exists($oldFile)) unlink($oldFile);
        mysqli_query($conn, "DELETE FROM histori_tugas WHERE id_tugas='$id' AND id_siswa='$id_siswa'");
    }
    $_SESSION['success'] = 'Jawaban berhasil dihapus!';
    header('Location: dashboard_siswa.php');
    exit;
}

// =====================
// Ambil semua tugas sesuai jurusan siswa
// =====================
 $tugas = mysqli_query($conn, "SELECT * FROM tugas WHERE jurusan='$jurusan_siswa' ORDER BY tanggal_deadline ASC");

// Ambil histori tugas siswa
 $histori = [];
 $res = mysqli_query($conn, "SELECT * FROM histori_tugas WHERE id_siswa='$id_siswa'");
while ($row = mysqli_fetch_assoc($res)) {
    $histori[$row['id_tugas']] = $row;
}

// Hitung progres
 $total_tugas = mysqli_num_rows($tugas);
 $tugas_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM histori_tugas WHERE id_siswa='$id_siswa' AND status='Selesai'"));
 $progres = ($total_tugas > 0) ? ($tugas_selesai / $total_tugas) * 100 : 0;

// Ambil data absensi hari ini
 $today = date('Y-m-d');
 $absensi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM absensi WHERE id_siswa='$id_siswa' AND tanggal_absensi='$today'"));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

    <title>Dashboard Siswa - Sistem PKL</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            /* Warna biru gelap yang sama dengan halaman siswa */
            --primary: #1a237e;
            --secondary: #3949ab;
            --info: #03a9f4;
            --success: #1cc88a;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --sidebar-bg: linear-gradient(180deg, #1a237e 0%, #0d47a1 100%);

            /* Warna aksen biru */
            --accent-light: #90caf9;
            --accent-medium: #42a5f5;
            --accent-dark: #1976d2;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 700;
            font-size: 1.2rem;
            border-radius: 10px 10px 0 0 !important;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .table {
            margin-bottom: 0;
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

        .table-hover tbody tr:hover {
            background-color: rgba(26, 35, 126, 0.05);
        }

        .btn {
            border-radius: 0.35rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #0d47a1;
            border-color: #0d47a1;
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .btn-warning {
            background-color: var(--warning);
            border-color: var(--warning);
        }

        .btn-info {
            background-color: var(--info);
            border-color: var(--info);
        }

        .form-control,
        .form-select {
            border-radius: 0.35rem;
            border: 1px solid #d1d3e2;
            padding: 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
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

        /* Custom progress bar */
        .progress {
            height: 25px;
            border-radius: 10px;
            background-color: #e9ecef;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        /* Custom badge styling */
        .badge-jurusan {
            font-size: 0.75rem;
            padding: 0.35rem 0.5rem;
            border-radius: 0.35rem;
            font-weight: 600;
        }

        .badge-rpl {
            background-color: #4e73df;
            color: white;
        }

        .badge-kuliner {
            background-color: #1cc88a;
            color: white;
        }

        .badge-busana {
            background-color: #36b9cc;
            color: white;
        }

        .badge-atph {
            background-color: #f6c23e;
            color: #333;
        }

        /* Custom alert styling */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        /* Custom file upload styling */
        .upload-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .upload-container .file-input {
            flex: 1;
        }

        .upload-container .btn-upload {
            white-space: nowrap;
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

        /* Status badge styling */
        .status-selesai {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
            font-weight: 500;
        }

        .status-belum {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger);
            font-weight: 500;
        }

        .status-proses {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background-color: rgba(34, 31, 45, 0.16);
            color: var(--dark);
            font-weight: 500;
        }

        /* Absensi card styling */
        .absensi-status {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .absensi-status.belum {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger);
        }

        .absensi-status.masuk {
            background-color: rgba(3, 169, 244, 0.1);
            color: var(--info);
        }

        .absensi-status.keluar {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .upload-container {
                flex-direction: column;
                align-items: stretch;
            }

            .upload-container .btn-upload {
                margin-top: 10px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-group .btn {
                border-radius: 0.35rem !important;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard_siswa.php">
            <i class="fas fa-user-graduate me-2"></i>Dashboard Siswa
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
                    <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include 'menu.php'; ?>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-graduate me-2"></i>Dashboard Siswa
                    </h1>
                    <div>
                        <span class="badge badge-jurusan 
                            <?php
                            $badgeClass = '';
                            switch (strtolower($jurusan_siswa)) {
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
                            echo $badgeClass;
                            ?>
                        "><?= $jurusan_siswa ?></span>
                    </div>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Dashboard Siswa</li>
                </ol>

                <!-- Tampilkan pesan sukses atau error -->
                <?php if (isset($_SESSION['success'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: '<?= $_SESSION['success'] ?>',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        });
                    </script>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: '<?= $_SESSION['error'] ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Absensi Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Absensi</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status Absensi Hari Ini -->
                        <?php if ($absensi_hari_ini): ?>
                            <?php if ($absensi_hari_ini['jam_masuk'] && $absensi_hari_ini['jam_keluar']): ?>
                                <div class="absensi-status keluar">
                                    <i class="fas fa-check-circle fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Anda sudah absen hari ini</h6>
                                        <p class="mb-0">Jam Masuk: <?= date('H:i', strtotime($absensi_hari_ini['jam_masuk'])) ?> | Jam Keluar: <?= date('H:i', strtotime($absensi_hari_ini['jam_keluar'])) ?></p>
                                    </div>
                                </div>
                            <?php elseif ($absensi_hari_ini['jam_masuk']): ?>
                                <div class="absensi-status masuk">
                                    <i class="fas fa-clock fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Anda sudah absen masuk</h6>
                                        <p class="mb-0">Jam Masuk: <?= date('H:i', strtotime($absensi_hari_ini['jam_masuk'])) ?> | Silakan absen keluar</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="absensi-status belum">
                                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Anda belum absen hari ini</h6>
                                    <p class="mb-0">Silakan generate QR Code untuk melakukan absensi</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <div class="btn-group" role="group">
                                <a href="generate_qr.php" class="btn btn-primary">
                                    <i class="fas fa-qrcode me-1"></i> Generate QR Code
                                </a>
                                <a href="recap_harian.php" class="btn btn-info">
                                    <i class="fas fa-calendar-alt me-1"></i> Recap Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center py-3">
                        <div class="card-header-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span>Progres Tugas</span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-2">Progres Tugas: <span class="text-primary"><?= round($progres, 2) ?>%</span></h5>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?= $progres ?>%;" aria-valuenow="<?= $progres ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= round($progres, 2) ?>%
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="d-flex justify-content-center">
                                    <div class="text-center mx-3">
                                        <h3 class="text-primary"><?= $tugas_selesai ?></h3>
                                        <p class="mb-0">Tugas Selesai</p>
                                    </div>
                                    <div class="text-center mx-3">
                                        <h3 class="text-secondary"><?= $total_tugas - $tugas_selesai ?></h3>
                                        <p class="mb-0">Tugas Tersisa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tugas Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center py-3">
                        <div class="card-header-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>Daftar Tugas</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Tugas</th>
                                        <th>Deskripsi</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Upload/Edit</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($tugas as $t): ?>
                                        <?php
                                        $now = date('Y-m-d H:i:s');
                                        $is_deadline = ($now > $t['tanggal_deadline']);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($t['judul']) ?></td>
                                            <td><?= nl2br(htmlspecialchars(substr($t['deskripsi'], 0, 100) . (strlen($t['deskripsi']) > 100 ? '...' : ''))) ?></td>
                                            <td>
                                                <?= date('d M Y', strtotime($t['tanggal_deadline'])) ?>
                                                <?php if ($is_deadline): ?>
                                                    <span class="badge bg-danger ms-1">Lewat Deadline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (isset($histori[$t['id_tugas']]) && $histori[$t['id_tugas']]['status'] == 'Selesai') {
                                                    echo "<span class='status-selesai'><i class='fas fa-check-circle me-1'></i>Selesai</span>";
                                                } else if (isset($histori[$t['id_tugas']]) && $histori[$t['id_tugas']]['status'] == 'Proses') {
                                                    echo "<span class='status-proses'><i class='fas fa-spinner me-1'></i>Proses</span>";
                                                } else {
                                                    echo "<span class='status-belum'><i class='fas fa-times-circle me-1'></i>Belum</span>";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!$is_deadline): ?>
                                                    <form method="POST" enctype="multipart/form-data" id="formUpload<?= $t['id_tugas'] ?>">
                                                        <div class="upload-container">
                                                            <input type="file" name="file_tugas" class="form-control form-control-sm file-input" required>
                                                            <input type="hidden" name="id_tugas" value="<?= $t['id_tugas'] ?>">
                                                            <button type="submit" name="upload" class="btn btn-sm btn-primary btn-upload">
                                                                <i class="fas fa-upload me-1"></i>Upload
                                                            </button>
                                                        </div>
                                                    </form>
                                                    <?php if (isset($histori[$t['id_tugas']])): ?>
                                                        <small class="text-muted d-block mt-1">File: <?= $histori[$t['id_tugas']]['jawaban'] ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted"><i class="fas fa-ban me-1"></i> Tidak bisa upload</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($histori[$t['id_tugas']])): ?>
                                                    <?php if (!$is_deadline): ?>
                                                        <a href="?hapus=<?= $t['id_tugas'] ?>" onclick="return confirm('Yakin ingin hapus jawaban ini?')" class="btn btn-sm btn-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted"><i class="fas fa-ban"></i></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sertifikat -->
                <?php
                $reward = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM reward WHERE id_siswa='$id_siswa'"));
                if ($reward && $reward['sertifikat_terbit']):
                ?>
                    <div class="alert alert-success">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-certificate fa-3x text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="alert-heading">Selamat!</h5>
                                <p>Kamu sudah menyelesaikan semua tugas. <a href="cetak_sertifikat.php" class="alert-link">Download Sertifikat</a></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>

            <?php include './assets/template/footer.php'; ?>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Inisialisasi tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Validasi file upload sebelum submit
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[method="POST"]');

            forms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    const fileInput = form.querySelector('input[type="file"]');
                    const file = fileInput.files[0];

                    if (!file) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih file terlebih dahulu!',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Validasi ukuran file (maksimal 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Ukuran file terlalu besar! Maksimal 5MB.',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Validasi tipe file
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tipe file tidak diizinkan! Hanya PDF, DOC, DOCX, JPG, JPEG, PNG.',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    return true;
                });
            });
        });
    </script>
</body>

</html>