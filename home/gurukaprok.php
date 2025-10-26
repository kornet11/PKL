<?php
session_start();
// Redirect jika belum login
if (!isset($_SESSION['login'])) {
  echo "<script>document.location.href = '../index.php';</script>";
  exit;
}
// Koneksi ke database
require 'functions.php';
// hanya admin, gurukaprok yang boleh akses
cekAkses(['admin', 'gurukaprok']);
// ======== Gabungan Filter Jurusan + Keyword ========
$where = [];
// Filter jurusan
if (!empty($_GET['jurusan'])) {
  $jurusan = mysqli_real_escape_string($conn, $_GET['jurusan']);
  $where[] = "jurusan = '$jurusan'";
}
// Filter keyword
if (!empty($_GET['keyword'])) {
  $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
  $where[] = "(namakaprok LIKE '%$keyword%' OR nip LIKE '%$keyword%')";
}
// Susun query
$sql = "SELECT * FROM gurukaprok";
if (!empty($where)) {
  $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id_gurukaprok DESC";
$gurukaprok1 = tampilData($sql);
// Tambah guru kaprok
if (isset($_POST["tambah"])) {
  if (tambahGuruKaprok($_POST, $_FILES)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Menambah Guru Kaprok.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'gurukaprok.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Menambah Guru Kaprok!',
                confirmButtonText: 'OK'
              });
            });
          </script>";
  }
}
// Edit guru kaprok
if (isset($_POST['edit'])) {
  if (editGuruKaprok($_POST, $_FILES) !== false) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Guru Kaprok berhasil diubah!',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'gurukaprok.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Guru Kaprok gagal diubah!',
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
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<title>Guru Kaprok - Sistem PKL</title>

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
</style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">
      <i class="fas fa-user-tie me-2"></i>Guru Kaprok
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <!-- Search Form -->
    <form action="" method="GET" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
      <div class="input-group">
        <input type="text" class="form-control" name="keyword" placeholder="Cari nama kaprok..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
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
          <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-tie me-2"></i>Guru Kaprok</h1>
          <a href="cetak_excel_gurukaprok.php" class="btn btn-success">
            <i class="fa-solid fa-file-excel me-1"></i>Export Excel
          </a>
        </div>

        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Guru Kaprok</li>
        </ol>

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fas fa-table me-1"></i> Data Guru Kaprok
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
                  <select class="form-select" onchange="window.location.href='gurukaprok.php?jurusan=' + this.value + '&keyword=<?= isset($_GET['keyword']) ? urlencode($_GET['keyword']) : '' ?>'">
                    <option value="">Semua Jurusan</option>
                    <?php
                    $sql_jurusan = mysqli_query($conn, "SELECT DISTINCT jurusan FROM gurukaprok ORDER BY jurusan ASC");
                    while ($r_jurusan = mysqli_fetch_assoc($sql_jurusan)) :
                      $selected = (isset($_GET['jurusan']) && $_GET['jurusan'] == $r_jurusan["jurusan"]) ? "selected" : "";
                    ?>
                      <option value="<?= $r_jurusan["jurusan"]; ?>" <?= $selected; ?>>
                        <?= $r_jurusan["jurusan"]; ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label"><strong>Pencarian</strong></label>
                  <form action="" method="GET">
                    <input type="hidden" name="jurusan" value="<?= isset($_GET['jurusan']) ? htmlspecialchars($_GET['jurusan']) : '' ?>">
                    <div class="input-group">
                      <input type="text" class="form-control" name="keyword" placeholder="Cari nama kaprok atau NIP..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                      <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                      <a href="gurukaprok.php" class="btn btn-secondary"><i class="fas fa-sync"></i></a>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-hover" id="example" width="100%" cellspacing="0">
                <thead class="thead-dark">
                  <tr>
                    <th width="50">No</th>
                    <th width="80">Foto</th>
                    <th>NIP</th>
                    <th>Nama Kaprok</th>
                    <th>Jurusan</th>
                    <th>Jabatan</th>
                    <th>No Telepon</th>
                    <th width="100">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($gurukaprok1)): ?>
                    <tr>
                      <td colspan="8" class="text-center py-4">
                        <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data guru kaprok
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1;
                    foreach ($gurukaprok1 as $guru) : ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">
                          <img src="assets/img/guru/<?= $guru['foto']; ?>" alt="Foto Guru" class="profile-img">
                        </td>
                        <td><?= $guru['nip']; ?></td>
                        <td><?= $guru['namakaprok']; ?></td>
                        <td>
                          <span class="badge bg-primary"><?= $guru['jurusan']; ?></span>
                        </td>
                        <td><?= $guru['jabatan']; ?></td>
                        <td><?= $guru['no_telpon']; ?></td>
                        <td class="text-center">
                          <div class="d-flex justify-content-center gap-1">
                            <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $guru['id_gurukaprok']; ?>" title="Edit">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a href="hapus_gurukaprok.php?id=<?= $guru['id_gurukaprok']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirmDelete()" title="Hapus">
                              <i class="fas fa-trash"></i>
                            </a>
                            <a href="cetakKartuKaprok.php?id=<?= $guru['id_gurukaprok']; ?>" class="btn btn-sm btn-warning btn-action" title="Kartu Kaprok">
                              <i class="fas fa-address-card"></i>
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

  <!-- Modal Tambah Guru Kaprok -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Data Guru Kaprok</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">NIP <span class="text-danger">*</span></label>
                <input type="text" name="nip" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Kakonli <span class="text-danger">*</span></label>
                <input type="text" name="namakaprok" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                <select class="form-select" name="jurusan" required>
                  <option value="" selected disabled>Pilih Jurusan</option>
                  <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                  <option value="KULINER">Tata Boga (KULINER)</option>
                  <option value="BUSANA">Tata Busana (BUSANA)</option>
                  <option value="ATPH">Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <select class="form-select" name="jabatan" required>
                  <option value="" selected disabled>Pilih Jabatan</option>
                  <option value="KAPRODI">Ketua Program Studi</option>
                  <option value="WAKIL_KAPRODI">Wakil Ketua Program Studi</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                <input type="text" name="no_telpon" class="form-control" required>
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

  <!-- Modal Edit Guru Kaprok -->
  <?php foreach ($gurukaprok1 as $guru) : ?>
    <div class="modal fade" id="modalEdit<?= $guru['id_gurukaprok']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $guru['id_gurukaprok']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_gurukaprok" value="<?= $guru['id_gurukaprok']; ?>">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel<?= $guru['id_gurukaprok']; ?>">Edit Data Guru Kaprok</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <img src="assets/img/guru/<?= $guru['foto']; ?>" alt="Foto" width="100" class="rounded-circle mb-2" style="border: 3px solid #e3e6f0;">
                <p class="text-muted">Foto saat ini</p>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">NIP <span class="text-danger">*</span></label>
                  <input type="text" name="nip" class="form-control" value="<?= $guru['nip']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Kaprok <span class="text-danger">*</span></label>
                  <input type="text" name="namakaprok" class="form-control" value="<?= $guru['namakaprok']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                  <select class="form-select" name="jurusan" required>
                    <option value="RPL" <?= $guru['jurusan'] == 'RPL' ? 'selected' : '' ?>>Rekayasa Perangkat Lunak (RPL)</option>
                    <option value="KULINER" <?= $guru['jurusan'] == 'KULINER' ? 'selected' : '' ?>>Tata Boga (KULINER)</option>
                    <option value="BUSANA" <?= $guru['jurusan'] == 'BUSANA' ? 'selected' : '' ?>>Tata Busana (BUSANA)</option>
                    <option value="ATPH" <?= $guru['jurusan'] == 'ATPH' ? 'selected' : '' ?>>Agribisnis Tanaman Pangan dan Hortikultura (ATPH)</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                  <select class="form-select" name="jabatan" required>
                    <option value="KAPRODI" <?= $guru['jabatan'] == 'KAPRODI' ? 'selected' : '' ?>>Ketua Program Studi</option>
                    <option value="WAKIL_KAPRODI" <?= $guru['jabatan'] == 'WAKIL_KAPRODI' ? 'selected' : '' ?>>Wakil Ketua Program Studi</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                  <input type="text" name="no_telpon" class="form-control" value="<?= $guru['no_telpon']; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ganti Foto</label>
                  <input type="hidden" name="fotolama" value="<?= $guru['foto']; ?>">
                  <input type="file" name="foto" class="form-control" accept="image/*">
                  <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password<?= $guru['id_gurukaprok']; ?>" placeholder="Kosongkan jika tidak ingin mengubah password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password<?= $guru['id_gurukaprok']; ?>')">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/scripts.js"></script>
  <script src="./assets/template/footer.php"></script>
  <script src="./assets/template/logout-alert.php"></script>
</body>

</html>