<?php
session_start();
if (!isset($_SESSION['login'])) {
  echo "<script>document.location.href = '../index.php';</script>";
  exit;
}
require 'functions.php';
// Filter keyword
$where = [];
if (!empty($_GET['keyword'])) {
  $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
  $where[] = "(nama_siswa LIKE '%$keyword%' OR tempat_pkl LIKE '%$keyword%' OR kelas_jurusan LIKE '%$keyword%')";
}
$sql = "SELECT * FROM jurnal_pkl";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id DESC";
$jurnals = tampilData($sql);
// Tambah
if (isset($_POST["tambah"])) {
  if (tambahJurnal($_POST, $_FILES)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Menambah Data Jurnal PKL.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'kegiatan.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Menambah Data Jurnal PKL! Pastikan file yang diupload adalah JPG, PNG, PDF, DOC, DOCX, XLS, atau XLSX dan ukuran maksimal 2MB.',
                confirmButtonText: 'OK'
              });
            });
          </script>";
  }
}
// Edit
if (isset($_POST["edit"])) {
  if (editJurnal($_POST, $_FILES)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Mengubah Data Jurnal PKL.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'kegiatan.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Mengubah Data Jurnal PKL! Pastikan file yang diupload adalah JPG, PNG, PDF, DOC, DOCX, XLS, atau XLSX dan ukuran maksimal 2MB.',
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Jurnal PKL - Sistem PKL</title>

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

    /* Custom styling untuk file badges */
    .file-badge {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 600;
      margin-right: 0.5rem;
    }

    .file-badge i {
      margin-right: 0.25rem;
    }

    .file-badge.pdf {
      background-color: rgba(231, 76, 60, 0.1);
      color: #e74c3c;
    }

    .file-badge.word {
      background-color: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }

    .file-badge.excel {
      background-color: rgba(39, 174, 96, 0.1);
      color: #27ae60;
    }

    .file-badge.image {
      background-color: rgba(155, 89, 182, 0.1);
      color: #9b59b6;
    }
  </style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">
      <i class="fas fa-book me-2"></i>Jurnal PKL
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <!-- Search Form -->
    <form action="" method="GET" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
        <input type="text" class="form-control" name="keyword" placeholder="Cari Jurnal..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
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
          <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book me-2"></i>Data Jurnal PKL</h1>
          <?php if ($_SESSION['hak_akses'] == 'admin'): ?>
            <a href="cetak_excel_jurnal.php" class="btn btn-success">
              <i class="fa-solid fa-file-excel me-1"></i>Export Excel
            </a>
          <?php endif; ?>
        </div>

        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Jurnal PKL</li>
        </ol>

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fas fa-table me-1"></i> Data Jurnal PKL
            </div>
            <?php if ($_SESSION['hak_akses'] == 'admin'): ?>
              <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                  <i class="fas fa-plus me-1"></i>Tambah Data
                </button>
              </div>
            <?php endif; ?>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="example" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-dark">
                  <tr>
                    <th width="50">No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas/Jurusan</th>
                    <th>Tempat PKL</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Uraian</th>
                    <th>Bukti</th>
                    <th>Youtube</th>
                    <th>Website</th>
                    <?php if ($_SESSION['hak_akses'] == 'admin'): ?><th width="100">Aksi</th><?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($jurnals)): ?>
                    <tr>
                      <td colspan="<?= $_SESSION['hak_akses'] == 'admin' ? '11' : '10' ?>" class="text-center py-4">
                        <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data Jurnal PKL
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1;
                    foreach ($jurnals as $jurnal): ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= $jurnal['nama_siswa']; ?></td>
                        <td><?= $jurnal['kelas_jurusan']; ?></td>
                        <td><?= $jurnal['tempat_pkl']; ?></td>
                        <td><?= $jurnal['tanggal']; ?></td>
                        <td><?= $jurnal['waktu_mulai'] . ' - ' . $jurnal['waktu_selesai']; ?></td>
                        <td><?= nl2br(htmlspecialchars(substr($jurnal['uraian'], 0, 50) . (strlen($jurnal['uraian']) > 50 ? '...' : ''))); ?></td>
                        <td class="text-center">
                          <?php if ($jurnal['bukti_file']) :
                            $ext = strtolower(pathinfo($jurnal['bukti_file'], PATHINFO_EXTENSION));
                            $iconClass = "fas fa-file";
                            $btnClass = "btn-info";
                            $badgeClass = "";

                            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                              $iconClass = "fas fa-image";
                              $badgeClass = "image";
                            } elseif (in_array($ext, ['pdf'])) {
                              $iconClass = "fas fa-file-pdf";
                              $btnClass = "btn-danger";
                              $badgeClass = "pdf";
                            } elseif (in_array($ext, ['doc', 'docx'])) {
                              $iconClass = "fas fa-file-word";
                              $btnClass = "btn-primary";
                              $badgeClass = "word";
                            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                              $iconClass = "fas fa-file-excel";
                              $btnClass = "btn-success";
                              $badgeClass = "excel";
                            }
                          ?>
                            <div class="d-flex flex-column align-items-center">
                              <span class="file-badge mb-1 <?= $badgeClass ?>">
                                <i class="<?= $iconClass; ?>"></i> <?= strtoupper($ext) ?>
                              </span>
                              <a href='./assets/jurnal/<?= $jurnal['bukti_file']; ?>' target='_blank' class='btn btn-sm <?= $btnClass; ?>'>
                                <i class="fas fa-eye"></i>
                              </a>
                            </div>
                          <?php else : ?>
                            -
                          <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $jurnal['link_1'] ? "<a href='" . $jurnal['link_1'] . "' target='_blank' class='btn btn-sm btn-danger'><i class='fab fa-youtube'></i></a>" : "-"; ?></td>
                        <td class="text-center"><?= $jurnal['link_2'] ? "<a href='" . $jurnal['link_2'] . "' target='_blank' class='btn btn-sm btn-warning'><i class='fas fa-globe'></i></a>" : "-"; ?></td>
                        <?php if ($_SESSION['hak_akses'] == 'admin'): ?>
                          <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                              <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $jurnal['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="hapus_jurnal.php?id=<?= $jurnal['id']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirmDelete()" title="Hapus">
                                <i class="fas fa-trash"></i>
                              </a>
                            </div>
                          </td>
                        <?php endif; ?>
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

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Data Jurnal PKL</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                <input type="text" name="nama_siswa" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Kelas / Jurusan</label>
                <input type="text" name="kelas_jurusan" class="form-control">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Tempat PKL <span class="text-danger">*</span></label>
                <input type="text" name="tempat_pkl" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                <input type="date" name="tanggal" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" class="form-control">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" class="form-control">
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Uraian <span class="text-danger">*</span></label>
                <textarea name="uraian" class="form-control" rows="3" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Alat & Bahan</label>
                <textarea name="alat_bahan" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Hasil Output</label>
                <textarea name="hasil_output" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Link Youtube (Opsional)</label>
                <input type="url" name="link_1" class="form-control" placeholder="https://...">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Link Web (Opsional)</label>
                <input type="url" name="link_2" class="form-control" placeholder="https://...">
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Bukti File</label>
                <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                <div class="form-text">Format: JPG, PNG, PDF, DOC, DOCX, XLS, XLSX. Maksimal 2MB.</div>
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
  <?php foreach ($jurnals as $jurnal): ?>
    <div class="modal fade" id="modalEdit<?= $jurnal['id']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $jurnal['id']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $jurnal['id']; ?>">
            <input type="hidden" name="bukti_file_lama" value="<?= $jurnal['bukti_file']; ?>">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel<?= $jurnal['id']; ?>">Edit Data Jurnal PKL</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php if ($jurnal['bukti_file']): ?>
                <div class="text-center mb-4">
                  <?php
                  $ext = strtolower(pathinfo($jurnal['bukti_file'], PATHINFO_EXTENSION));
                  if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    echo '<img src="./assets/jurnal/' . $jurnal['bukti_file'] . '" alt="Bukti" width="100" class="rounded mb-2" style="border: 3px solid #e3e6f0;">';
                  } else {
                    echo '<div class="file-preview-placeholder rounded mb-2 d-flex align-items-center justify-content-center" style="height: 100px; border: 3px solid #e3e6f0; background-color: #f8f9fc;">';
                    echo '<i class="fas fa-file fa-3x text-secondary"></i>';
                    echo '</div>';
                  }
                  ?>
                  <p class="text-muted">File saat ini: <?= $jurnal['bukti_file']; ?></p>
                </div>
              <?php endif; ?>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                  <input type="text" name="nama_siswa" value="<?= $jurnal['nama_siswa']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kelas / Jurusan</label>
                  <input type="text" name="kelas_jurusan" value="<?= $jurnal['kelas_jurusan']; ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tempat PKL <span class="text-danger">*</span></label>
                  <input type="text" name="tempat_pkl" value="<?= $jurnal['tempat_pkl']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                  <input type="date" name="tanggal" value="<?= $jurnal['tanggal']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Waktu Mulai</label>
                  <input type="time" name="waktu_mulai" value="<?= $jurnal['waktu_mulai']; ?>" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Waktu Selesai</label>
                  <input type="time" name="waktu_selesai" value="<?= $jurnal['waktu_selesai']; ?>" class="form-control">
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Uraian <span class="text-danger">*</span></label>
                  <textarea name="uraian" class="form-control" rows="3" required><?= $jurnal['uraian']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Alat & Bahan</label>
                  <textarea name="alat_bahan" class="form-control" rows="2"><?= $jurnal['alat_bahan']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Hasil Output</label>
                  <textarea name="hasil_output" class="form-control" rows="2"><?= $jurnal['hasil_output']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Link Youtube (Opsional)</label>
                  <input type="url" name="link_1" value="<?= $jurnal['link_1']; ?>" class="form-control" placeholder="https://...">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Link Web (Opsional)</label>
                  <input type="url" name="link_2" value="<?= $jurnal['link_2']; ?>" class="form-control" placeholder="https://...">
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Ganti Bukti File</label>
                  <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
                  <div class="form-text">Kosongkan jika tidak ingin mengganti file.</div>
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
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="./assets/template/footer.php"></script>
  <!-- <script>
    // Initialize DataTable
    $(document).ready(function() {
        // Tentukan target column berdasarkan hak akses
        var isAdmin = <?php echo ($_SESSION['hak_akses'] == 'admin') ? 'true' : 'false'; ?>;
        
        var columnDefs = [];
        if (isAdmin) {
            columnDefs = [{ orderable: false, targets: [10] }];
        }
        
        $('#dataTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            columnDefs: columnDefs
        });
    });
    
    function confirmDelete() {
        return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini?');
    }
    
    // Inisialisasi tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script> -->
  <script src="./assets/template/logout-alert.php"></script>
</body>

</html>