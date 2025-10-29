<?php
session_start();
// Redirect jika belum login
if (!isset($_SESSION['login'])) {
  echo "<script>document.location.href = '../index.php';</script>";
  exit;
}
// Koneksi ke database
require 'functions.php';
// hanya admin, gurupem, gurukaprok yang boleh akses
cekAkses(['admin', 'gurupem', 'gurukaprok']);

// Set session jurusan jika belum ada dan user sudah login sebagai gurukaprok/gurupem
if (
  (isset($_SESSION['hak_akses']) &&
   in_array($_SESSION['hak_akses'], ['gurukaprok', 'gurupem'])) &&
  empty($_SESSION['jurusan'])
) {
  $id_user = $_SESSION['id_user'] ?? null;
  if ($id_user) {
    $result = mysqli_query($conn, "SELECT jurusan FROM users WHERE id_user='$id_user' LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
      $_SESSION['jurusan'] = $row['jurusan'];
    }
  }
}

// ======== Gabungan Filter Jurusan + Keyword + Hak Akses ========
$where = [];

// Jika role guru (gurukaprok/gurupem), filter jurusan otomatis dari session
if (
  (isset($_SESSION['hak_akses']) &&
   in_array($_SESSION['hak_akses'], ['gurukaprok', 'gurupem'])) &&
  !empty($_SESSION['jurusan'])
) {
  $jurusan_user = mysqli_real_escape_string($conn, $_SESSION['jurusan']);
  $where[] = "konsentrasi = '$jurusan_user'";
} else if (isset($_SESSION['hak_akses']) && $_SESSION['hak_akses'] == 'admin') {
  // Admin bisa filter manual dari dropdown
  if (!empty($_GET['jurusan'])) {
    $jurusan = mysqli_real_escape_string($conn, $_GET['jurusan']);
    $where[] = "konsentrasi = '$jurusan'";
  }
}

// Filter keyword (berlaku untuk semua)
if (!empty($_GET['keyword'])) {
  $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
  $where[] = "(nama LIKE '%$keyword%' OR kelas LIKE '%$keyword%')";
}

// Susun query
$sql = "SELECT * FROM siswa";
if (!empty($where)) {
  $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id_siswa DESC";
$siswa1 = tampilData($sql);
// Tambah siswa
if (isset($_POST["tambah"])) {
  if (tambahSiswa($_POST)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Menambah Siswa.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'siswa.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Menambah Siswa!',
                confirmButtonText: 'OK'
              });
            });
          </script>";
  }
}
// Edit siswa
if (isset($_POST['edit'])) {
  if (editSiswa($_POST) !== false) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Siswa berhasil diubah!',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'siswa.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Siswa gagal diubah!',
                confirmButtonText: 'OK'
              });
            });
          </script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

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
  <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Data Siswa - Sistem PKL</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

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

    .profile-img {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #e3e6f0;
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

    .badge-status {
      padding: 0.35rem 0.5rem;
      border-radius: 0.35rem;
      font-size: 0.75rem;
      font-weight: 600;
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

    /* responsive tweaks for siswa table */
    table.dataTable td,
    table.dataTable th {
      white-space: normal !important;
      word-break: break-word;
    }

    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 576px) {
      .profile-img {
        width: 36px;
        height: 36px;
      }

      .btn-action {
        width: 28px;
        height: 28px;
      }

      .table th,
      .table td {
        padding: 0.45rem 0.5rem;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">
      <i class="fas fa-graduation-cap me-2"></i>Data Siswa
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <!-- Search Form -->
    <form action="" method="GET" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
      <div class="input-group">
        <input type="text" class="form-control" name="keyword" placeholder="Cari nama siswa..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
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
          <li><a class="dropdown-item btn-logout" href="../logout.php">Logout</a></li>
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
          <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-graduation-cap me-2"></i>Data Siswa</h1>
          <a href="cetak_excel_siswa.php" class="btn btn-success">
            <i class="fa-solid fa-file-excel me-1"></i>Export Excel
          </a>
          <a href="import_excel_siswa.php" class="btn btn-success">
            <i class="fa-solid fa-file-excel me-1"></i>Import Excel
          </a>
        </div>

        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Data Siswa</li>
        </ol>

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fas fa-table me-1"></i> Data Siswa
            </div>
            <div>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-1"></i>Tambah Data
              </button>
            </div>
          </div>

          <div class="card-body">
            <div class="filter-section mb-4">
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label"><strong>Filter Berdasarkan Jurusan</strong></label>
                  <select class="form-select" onchange="window.location.href='siswa.php?jurusan=' + this.value + '&keyword=<?= isset($_GET['keyword']) ? urlencode($_GET['keyword']) : '' ?>'">
                    <option value="">Semua Jurusan</option>
                    <?php
                    $sql_jurusan = mysqli_query($conn, "SELECT DISTINCT konsentrasi FROM siswa ORDER BY konsentrasi ASC");
                    while ($r_jurusan = mysqli_fetch_assoc($sql_jurusan)) :
                      $selected = (isset($_GET['jurusan']) && $_GET['jurusan'] == $r_jurusan["konsentrasi"]) ? "selected" : "";
                    ?>
                      <option value="<?= $r_jurusan["konsentrasi"]; ?>" <?= $selected; ?>>
                        <?= $r_jurusan["konsentrasi"]; ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label"><strong>Pencarian</strong></label>
                  <form action="" method="GET">
                    <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
                    <div class="input-group">
                      <input type="text" class="form-control" name="keyword" placeholder="Cari nama siswa atau kelas..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                      <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                      <a href="siswa.php" class="btn btn-secondary"><i class="fas fa-sync"></i></a>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table id="dataTable" class="table table-bordered table-hover" width="100%" cellspacing="0">
                 <thead class="thead-dark">
                   <tr>
                     <th width="50">No</th>
                     <th width="80">Foto</th>
                     <th>NISN</th>
                     <th>Nama Siswa</th>
                     <th>Kelas</th>
                     <th>Konsentrasi</th>
                     <th>No Telepon</th>
                     <th width="100">Aksi</th>
                   </tr>
                 </thead>
                 <tbody>
                   <?php if (empty($siswa1)): ?>
                     <tr>
                       <td colspan="8" class="text-center py-4">
                         <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data siswa
                       </td>
                     </tr>
                   <?php else: ?>
                     <?php $no = 1;
                     foreach ($siswa1 as $siswa) : ?>
                       <tr>
                         <td class="text-center"><?= $no++; ?></td>
                         <td class="text-center">
                           <img src="assets/img/siswa/<?= $siswa['foto']; ?>" alt="Foto Siswa" class="profile-img">
                         </td>
                         <td><?= $siswa['nisn']; ?></td>
                         <td><?= $siswa['nama']; ?></td>
                         <td><?= $siswa['kelas']; ?></td>
                         <td>
                           <span class="badge bg-primary"><?= $siswa['konsentrasi']; ?></span>
                         </td>
                         <td><?= $siswa['no_telepon']; ?></td>
                         <td class="text-center">
                           <div class="d-flex justify-content-center gap-1">
                             <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $siswa['id_siswa']; ?>" title="Edit">
                               <i class="fas fa-edit"></i>
                             </a>
                             <a href="hapus_siswa.php?id=<?= $siswa['id_siswa']; ?>" class="btn btn-logout btn-sm btn-danger btn-action" title="Hapus">
                               <i class="fas fa-trash"></i>
                             </a>
                             <a href="cetakKartuSiswa.php?id=<?= $siswa['id_siswa']; ?>" class="btn btn-sm btn-warning btn-action" title="Kartu Siswa">
                               <i class="fas fa-address-card"></i>
                             </a>
                             <a href="biodata.php?id=<?= $siswa['id_siswa']; ?>" class="btn btn-sm btn-info btn-action text-white" title="Biodata">
                               <i class="fas fa-address-book"></i>
                             </a>
                           </div>
                         </td>
                       </tr>
                     <?php endforeach; ?>
                   <?php endif; ?>
                 </tbody>
               </table>
             </div>
           </div>
         </div>
       </main>

      <?php include 'assets/template/footer.php'; ?>
     </div>
   </div>

   <!-- Modal Tambah Siswa -->
   <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Data Siswa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">NISN <span class="text-danger">*</span></label>
                <input type="text" name="nisn" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                <input type="text" name="kelas" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Konsentrasi <span class="text-danger">*</span></label>
                <select class="form-select" name="konsentrasi" required>
                  <option value="" selected disabled>Pilih Konsentrasi</option>
                  <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                  <option value="KULINER">Tata Boga (KULINER)</option>
                  <option value="BUSANA">Tata Busana (BUSANA)</option>
                  <option value="ATPH">Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                <input type="text" name="no_telepon" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Foto <span class="text-danger">*</span></label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB.</div>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password" required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="form-text">Minimal 6 karakter</div>
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

  <!-- Modal Edit Siswa -->
  <?php foreach ($siswa1 as $siswa) : ?>
    <div class="modal fade" id="modalEdit<?= $siswa['id_siswa']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $siswa['id_siswa']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_siswa" value="<?= $siswa['id_siswa']; ?>">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel<?= $siswa['id_siswa']; ?>">Edit Data Siswa</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <img src="assets/img/siswa/<?= $siswa['foto']; ?>" alt="Foto" width="100" class="rounded-circle mb-2" style="border: 3px solid #e3e6f0;">
                <p class="text-muted">Foto saat ini</p>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">NISN <span class="text-danger">*</span></label>
                  <input type="text" name="nisn" class="form-control" value="<?= $siswa['nisn']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                  <input type="text" name="nama" class="form-control" value="<?= $siswa['nama']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kelas <span class="text-danger">*</span></label>
                  <input type="text" name="kelas" class="form-control" value="<?= $siswa['kelas']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Konsentrasi <span class="text-danger">*</span></label>
                  <select class="form-select" name="konsentrasi" required>
                    <option value="RPL" <?= $siswa['konsentrasi'] == 'RPL' ? 'selected' : '' ?>>Rekayasa Perangkat Lunak (RPL)</option>
                    <option value="KULINER" <?= $siswa['konsentrasi'] == 'KULINER' ? 'selected' : '' ?>>Tata Boga (KULINER)</option>
                    <option value="BUSANA" <?= $siswa['konsentrasi'] == 'BUSANA' ? 'selected' : '' ?>>Tata Busana (BUSANA)</option>
                    <option value="ATPH" <?= $siswa['konsentrasi'] == 'ATPH' ? 'selected' : '' ?>>Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                  <input type="text" name="no_telepon" class="form-control" value="<?= $siswa['no_telepon']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ganti Foto</label>
                  <input type="hidden" name="fotolama" value="<?= $siswa['foto']; ?>">
                  <input type="file" name="foto" class="form-control" accept="image/*">
                  <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password<?= $siswa['id_siswa']; ?>" placeholder="Kosongkan jika tidak ingin mengubah password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password<?= $siswa['id_siswa']; ?>')">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                  <div class="form-text">Minimal 6 karakter</div>
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
  <?php endforeach; ?>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <!-- <script src="assets/js/scripts.js"></script> -->
  <script>
    // Initialize DataTable dengan responsive
    $(document).ready(function() {
      var $tbl = $('#dataTable');
      if (!$tbl.length) return;
      if (typeof $.fn.DataTable !== 'function' || !$.fn.dataTable.Responsive) {
        console.warn('DataTables or Responsive plugin not loaded.');
        return;
      }

      var actionIndex = -1, nameIndex = -1;
      $tbl.find('thead th').each(function(i){
        var txt = $(this).text().trim().toLowerCase();
        if (txt.indexOf('aksi') !== -1) actionIndex = i;
        if (txt.indexOf('nama') !== -1 && nameIndex === -1) nameIndex = i;
      });

      var columnDefs = [{ responsivePriority: 1, targets: 0 }];
      if (nameIndex > -1) columnDefs.push({ responsivePriority: 2, targets: nameIndex });
      if (actionIndex > -1) columnDefs.push({ className: 'none', orderable: false, targets: actionIndex, responsivePriority: 1000 });

      $tbl.DataTable({
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function(row) {
                var d = row.data();
                return 'Detail: ' + (nameIndex > -1 ? (d[nameIndex] || '') : (d.join(' - ')));
              }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({ tableClass: 'table' })
          }
        },
        autoWidth: false,
        scrollX: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
        columnDefs: columnDefs,
        pageLength: 10
      });
    });

    // Toggle password visibility (safe attach)
    (function () {
      var toggleBtn = document.getElementById('togglePassword');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
          var passwordInput = document.getElementById('password');
          if (!passwordInput) return;
          var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          this.innerHTML = type === 'password' ? '<i class=\"fas fa-eye\"></i>' : '<i class=\"fas fa-eye-slash\"></i>';
        }, false);
      }
    })();

    // Fallback sidebar toggle
    (function () {
      var btn = document.getElementById('sidebarToggle');
      if (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          document.body.classList.toggle('sb-sidenav-toggled');
        }, false);
      }
    })();

    function confirmDelete() {
      return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini?');
    }
 
    // existing togglePassword(id) remains unchanged below
   </script>
   <script src="./assets/template/logout-alert.php"></script>
  </body>
 
  </html>