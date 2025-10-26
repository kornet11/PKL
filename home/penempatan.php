<?php
session_start();
require 'functions.php';

// Cek login
if (!isset($_SESSION['login'])) {
    echo "<script>document.location.href = '../index.php';</script>";
    exit;
}

// ============= FILTER & SEARCH =============
 $where = "1=1";
 $selectedJurusan = $_GET['jurusan'] ?? "";
 $keyword = $_GET['keyword'] ?? "";

if (!empty($selectedJurusan)) {
    $where .= " AND siswa.konsentrasi='$selectedJurusan'";
}
if (!empty($keyword)) {
    $where .= " AND (siswa.nama LIKE '%$keyword%' OR dudi.nama LIKE '%$keyword%')";
}

// ============= QUERY PENEMPATAN =============
if ($_SESSION['hak_akses'] == 'siswa') {
    $id_siswa = $_SESSION['id_siswa'];
    $query = "
        SELECT p.*, siswa.nama AS nama_siswa, siswa.konsentrasi AS jurusan_siswa,
               dudi.nama AS nama_dudi, gurupembimbing.nama AS nama_guru
        FROM penempatan p
        JOIN siswa ON p.siswa_id = siswa.id_siswa
        JOIN dudi ON p.dudi_id = dudi.id_dudi
        JOIN gurupembimbing ON p.gurupem_id = gurupembimbing.id_gurupem
        WHERE p.siswa_id = '$id_siswa'
        ORDER BY p.id_penempatan DESC
    ";
} else {
    $query = "
        SELECT p.*, siswa.nama AS nama_siswa, siswa.konsentrasi AS jurusan_siswa,
               dudi.nama AS nama_dudi, gurupembimbing.nama AS nama_guru
        FROM penempatan p
        JOIN siswa ON p.siswa_id = siswa.id_siswa
        JOIN dudi ON p.dudi_id = dudi.id_dudi
        JOIN gurupembimbing ON p.gurupem_id = gurupembimbing.id_gurupem
        WHERE $where
        ORDER BY p.id_penempatan DESC
    ";
}
 $penempatans = mysqli_query($conn, $query);

// Dropdown data
 $siswa = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
 $dudi = mysqli_query($conn, "SELECT * FROM dudi ORDER BY nama ASC");
 $guru = mysqli_query($conn, "SELECT * FROM gurupembimbing ORDER BY nama ASC");

// ============= TAMBAH DATA =============
if (isset($_POST['tambah'])) {
    $siswa_id = $_POST['siswa_id'];
    $dudi_id = $_POST['dudi_id'];
    $gurupem_id = $_POST['gurupem_id'];
    $tgl_berangkat = $_POST['tanggal_berangkat'];
    $tgl_pulang = $_POST['tanggal_pulang'];

    $result = mysqli_query($conn, "INSERT INTO penempatan 
        (siswa_id, dudi_id, gurupem_id, tanggal_berangkat, tanggal_pulang) 
        VALUES ('$siswa_id','$dudi_id','$gurupem_id','$tgl_berangkat','$tgl_pulang')");
    
    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Berhasil Menambah Data Penempatan.'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal',
            'message' => 'Gagal Menambah Data Penempatan!'
        ];
    }
    header("Location: penempatan.php");
    exit;
}

// ============= EDIT DATA =============
if (isset($_POST['edit'])) {
    $id_penempatan = $_POST['id_penempatan'];
    $siswa_id = $_POST['siswa_id'];
    $dudi_id = $_POST['dudi_id'];
    $gurupem_id = $_POST['gurupem_id'];
    $tgl_berangkat = $_POST['tanggal_berangkat'];
    $tgl_pulang = $_POST['tanggal_pulang'];

    $result = mysqli_query($conn, "UPDATE penempatan 
        SET siswa_id='$siswa_id', dudi_id='$dudi_id', gurupem_id='$gurupem_id',
            tanggal_berangkat='$tgl_berangkat', tanggal_pulang='$tgl_pulang'
        WHERE id_penempatan='$id_penempatan'");
    
    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Data Penempatan berhasil diubah!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal',
            'message' => 'Data Penempatan gagal diubah!'
        ];
    }
    header("Location: penempatan.php");
    exit;
}

// ============= HAPUS DATA =============
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $result = mysqli_query($conn, "DELETE FROM penempatan WHERE id_penempatan='$id'");
    
    if ($result) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Data Penempatan berhasil dihapus!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal',
            'message' => 'Data Penempatan gagal dihapus!'
        ];
    }
    header("Location: penempatan.php");
    exit;
}

// Tentukan jumlah kolom berdasarkan hak akses
 $columnCount = $_SESSION['hak_akses'] != 'siswa' ? 8 : 7;
 $targetColumn = $_SESSION['hak_akses'] != 'siswa' ? 7 : 6;
?>
<!DOCTYPE html>
<html lang="id">
<head>
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
    <title>Penempatan PKL - Sistem PKL</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
        
        .filter-section {
            background-color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .modal-header {
            background: linear-gradient(90deg, var(--primary) 0%, #224abe 100%);
            color: white;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        /* Custom checkbox */
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .dataTables_length {
            margin-top: 1rem;
        }
        
        .dataTables_filter {
            margin: 1rem;
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
        
        /* Mobile collapse untuk tabel */
        @media (max-width: 576px) {
            table thead {
                display: none;
            }
            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 10px;
                background-color: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 8px;
                border: none;
                border-bottom: 1px solid #eee;
            }
            table tbody td:last-child {
                border-bottom: none;
            }
            table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--primary);
            }
        }
        
        /* Custom styling untuk mobile detail view */
        .mobile-detail {
            background-color: #f8f9fc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            border: 1px solid #e3e6f0;
        }
        
        .mobile-detail p {
            margin-bottom: 8px;
        }
        
        .mobile-detail strong {
            color: var(--primary);
        }
        
        /* Custom accordion styling */
        .accordion-button:not(.collapsed) {
            background-color: var(--primary);
            color: white;
        }
        
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .accordion-item {
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 10px !important;
            margin-bottom: 10px;
        }
        
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-building-user me-2"></i>Penempatan PKL
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Search Form -->
        <form action="" method="GET" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
            <div class="input-group">
                <input type="text" class="form-control" name="keyword" placeholder="Cari siswa atau DUDI..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
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
        <div id="layoutSidenav_nav">   <!-- ✅ wrapper sidebar -->
            <?php include 'menu.php'; ?>
        </div>
        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building-user me-2"></i>Penempatan PKL</h1>
                    <a href="cetak_excel_penempatan.php" class="btn btn-success">
                        <i class="fa-solid fa-file-excel me-1"></i>Export Excel
                    </a>
                </div>
                
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Penempatan PKL</li>
                </ol>
                
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-table me-1"></i>Penempatan PKL
                        </div>
                        <div>
                            <?php if ($_SESSION['hak_akses'] != 'siswa') { ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-1"></i>Tambah Data
                            </button>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <?php if ($_SESSION['hak_akses'] != 'siswa') { ?>
                        <div class="filter-section mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Filter Berdasarkan Jurusan</strong></label>
                                    <select class="form-select" onchange="window.location.href='penempatan.php?jurusan=' + this.value + '&keyword=<?= isset($_GET['keyword']) ? urlencode($_GET['keyword']) : '' ?>'">
                                        <option value="">Semua Jurusan</option>
                                        <?php
                                        $jurusans = mysqli_query($conn, "SELECT DISTINCT konsentrasi FROM siswa ORDER BY konsentrasi ASC");
                                        while ($j = mysqli_fetch_assoc($jurusans)) :
                                            $selected = (isset($_GET['jurusan']) && $_GET['jurusan'] == $j["konsentrasi"]) ? "selected" : "";
                                        ?>
                                            <option value="<?= $j["konsentrasi"]; ?>" <?= $selected; ?>>
                                                <?= $j["konsentrasi"]; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Pencarian</strong></label>
                                    <form action="" method="GET">
                                        <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="keyword" placeholder="Cari siswa atau DUDI..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                            <a href="penempatan.php" class="btn btn-secondary"><i class="fas fa-sync"></i></a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Versi Desktop -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Siswa</th>
                                        <th>Jurusan</th>
                                        <th>DUDI</th>
                                        <th>Guru Pembimbing</th>
                                        <th>Tgl Berangkat</th>
                                        <th>Tgl Pulang</th>
                                        <?php if ($_SESSION['hak_akses'] != 'siswa') { ?><th width="100">Aksi</th><?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1; 
                                    if (mysqli_num_rows($penempatans) > 0) {
                                        while ($row = mysqli_fetch_assoc($penempatans)) { ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= $row['nama_siswa']; ?></td>
                                            <td><?= $row['jurusan_siswa']; ?></td>
                                            <td><?= $row['nama_dudi']; ?></td>
                                            <td><?= $row['nama_guru']; ?></td>
                                            <td><?= $row['tanggal_berangkat']; ?></td>
                                            <td><?= $row['tanggal_pulang']; ?></td>
                                            <?php if ($_SESSION['hak_akses'] != 'siswa') { ?>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-sm btn-warning btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_penempatan']; ?>" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger btn-action delete-btn" data-id="<?= $row['id_penempatan']; ?>" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <?php } ?>
                                        </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="<?= $columnCount ?>" class="text-center py-4">
                                                <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data penempatan
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Versi Mobile -->
                        <div class="d-md-none">
                            <div class="accordion" id="penempatanAccordion">
                                <?php 
                                $no = 1; 
                                if (mysqli_num_rows($penempatans) > 0) {
                                    // Reset pointer result set
                                    mysqli_data_seek($penempatans, 0);
                                    while ($row = mysqli_fetch_assoc($penempatans)) { ?>
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#detail<?= $row['id_penempatan']; ?>">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <span><?= $no++; ?>. <?= $row['nama_siswa']; ?></span>
                                                    <span class="badge bg-primary"><?= $row['jurusan_siswa']; ?></span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="detail<?= $row['id_penempatan']; ?>" class="accordion-collapse collapse" data-bs-parent="#penempatanAccordion">
                                            <div class="accordion-body mobile-detail">
                                                <p><strong>Jurusan:</strong> <?= $row['jurusan_siswa']; ?></p>
                                                <p><strong>DUDI:</strong> <?= $row['nama_dudi']; ?></p>
                                                <p><strong>Guru Pembimbing:</strong> <?= $row['nama_guru']; ?></p>
                                                <p><strong>Tanggal Berangkat:</strong> <?= $row['tanggal_berangkat']; ?></p>
                                                <p><strong>Tanggal Pulang:</strong> <?= $row['tanggal_pulang']; ?></p>
                                                
                                                <?php if ($_SESSION['hak_akses'] != 'siswa'): ?>
                                                <div class="d-flex gap-2 mt-3">
                                                    <button class="btn btn-warning btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_penempatan']; ?>">
                                                        <i class="fas fa-pen me-1"></i> Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm flex-fill delete-btn" data-id="<?= $row['id_penempatan']; ?>">
                                                        <i class="fas fa-trash me-1"></i> Hapus
                                                    </button>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }
                                } else { ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data penempatan
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include './assets/template/footer.php'; ?>
        </div>
    </div>
    
    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Data Penempatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Siswa <span class="text-danger">*</span></label>
                                <select name="siswa_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Siswa --</option>
                                    <?php 
                                    $siswa2 = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
                                    while ($s = mysqli_fetch_assoc($siswa2)) { ?>
                                        <option value="<?= $s['id_siswa']; ?>">
                                            <?= $s['nama']; ?> (<?= $s['konsentrasi']; ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DUDI <span class="text-danger">*</span></label>
                                <select name="dudi_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih DUDI --</option>
                                    <?php 
                                    $dudi2 = mysqli_query($conn, "SELECT * FROM dudi ORDER BY nama ASC");
                                    while ($d = mysqli_fetch_assoc($dudi2)) { ?>
                                        <option value="<?= $d['id_dudi']; ?>">
                                            <?= $d['nama']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pembimbing <span class="text-danger">*</span></label>
                                <select name="gurupem_id" class="form-select" required>
                                    <option value="" selected disabled>-- Pilih Guru --</option>
                                    <?php 
                                    $guru2 = mysqli_query($conn, "SELECT * FROM gurupembimbing ORDER BY nama ASC");
                                    while ($g = mysqli_fetch_assoc($guru2)) { ?>
                                        <option value="<?= $g['id_gurupem']; ?>">
                                            <?= $g['nama']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_berangkat" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pulang <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pulang" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit -->
    <?php 
    mysqli_data_seek($penempatans, 0); // Reset pointer result set
    while ($row = mysqli_fetch_assoc($penempatans)) : ?>
    <div class="modal fade" id="modalEdit<?= $row['id_penempatan']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['id_penempatan']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST">
                    <input type="hidden" name="id_penempatan" value="<?= $row['id_penempatan']; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel<?= $row['id_penempatan']; ?>">Edit Data Penempatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Siswa <span class="text-danger">*</span></label>
                                <select name="siswa_id" class="form-select" required>
                                    <?php 
                                    $siswa2 = mysqli_query($conn, "SELECT * FROM siswa ORDER BY nama ASC");
                                    while ($s = mysqli_fetch_assoc($siswa2)) { ?>
                                        <option value="<?= $s['id_siswa']; ?>" <?= $s['id_siswa']==$row['siswa_id']?'selected':''; ?>>
                                            <?= $s['nama']; ?> (<?= $s['konsentrasi']; ?>)
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DUDI <span class="text-danger">*</span></label>
                                <select name="dudi_id" class="form-select" required>
                                    <?php 
                                    $dudi2 = mysqli_query($conn, "SELECT * FROM dudi ORDER BY nama ASC");
                                    while ($d = mysqli_fetch_assoc($dudi2)) { ?>
                                        <option value="<?= $d['id_dudi']; ?>" <?= $d['id_dudi']==$row['dudi_id']?'selected':''; ?>>
                                            <?= $d['nama']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pembimbing <span class="text-danger">*</span></label>
                                <select name="gurupem_id" class="form-select" required>
                                    <?php 
                                    $guru2 = mysqli_query($conn, "SELECT * FROM gurupembimbing ORDER BY nama ASC");
                                    while ($g = mysqli_fetch_assoc($guru2)) { ?>
                                        <option value="<?= $g['id_gurupem']; ?>" <?= $g['id_gurupem']==$row['gurupem_id']?'selected':''; ?>>
                                            <?= $g['nama']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_berangkat" class="form-control" value="<?= $row['tanggal_berangkat']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pulang <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pulang" class="form-control" value="<?= $row['tanggal_pulang']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    
    <!-- JavaScript Libraries -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js" integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/datatables-simple-demo.js"></script>
  <script src="./assets/template/footer.php"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: <?= $targetColumn ?> }
                ]
            });
            
            // Tampilkan alert dari session jika ada
            <?php if (isset($_SESSION['alert'])): ?>
                Swal.fire({
                    icon: '<?= $_SESSION['alert']['type'] ?>',
                    title: '<?= $_SESSION['alert']['title'] ?>',
                    text: '<?= $_SESSION['alert']['message'] ?>',
                    showConfirmButton: false,
                    timer: 1500
                });
                <?php unset($_SESSION['alert']); ?>
            <?php endif; ?>
            
            // Event listener untuk tombol hapus
            $('.delete-btn').on('click', function(e) {
                e.preventDefault(); // Mencegah aksi default dari button
                
                var id = $(this).data('id');
                
                // Periksa apakah Swal tersedia
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika pengguna mengklik "Ya, Hapus!", arahkan ke URL hapus
                            window.location.href = 'penempatan.php?hapus=' + id;
                        }
                    });
                } else {
                    // Fallback jika Swal tidak tersedia
                    if (confirm('Apakah Anda Yakin? Data yang dihapus tidak dapat dikembalikan!')) {
                        window.location.href = 'penempatan.php?hapus=' + id;
                    }
                }
            });
        });
        
        // Inisialisasi tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>