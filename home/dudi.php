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
  $where[] = "(nama LIKE '%$keyword%' OR alamat LIKE '%$keyword%' OR kontak LIKE '%$keyword%' OR owner LIKE '%$keyword%')";
}
$sql = "SELECT * FROM dudi";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY id_dudi DESC";
$dudis = tampilData($sql);
// Tambah
if (isset($_POST["tambah"])) {
  if (tambahDudi($_POST, $_FILES)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Menambah Data DUDI.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'dudi.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Menambah Data DUDI!',
                confirmButtonText: 'OK'
              });
            });
          </script>";
  }
}
// Edit
if (isset($_POST["edit"])) {
  if (editDudi($_POST, $_FILES)) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Mengubah Data DUDI.',
                showConfirmButton: false,
                timer: 1500
              }).then(function() {
                window.location.href = 'dudi.php';
              });
            });
          </script>";
  } else {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Mengubah Data DUDI!',
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
  <title>DUDI - Sistem PKL</title>

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
  </style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">
      <i class="fas fa-building me-2"></i>DUDI
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <!-- Search Form -->
    <form action="" method="GET" class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
        <input type="text" class="form-control" name="keyword" placeholder="Cari DUDI..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
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
          <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building me-2"></i>Data DUDI</h1>
          <?php if ($_SESSION['hak_akses'] !== 'siswa'): ?>
            <a href="cetak_excel_dudi.php" class="btn btn-success">
              <i class="fa-solid fa-file-excel me-1"></i>Export Excel
            </a>
          <?php endif; ?>
        </div>

        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
          <li class="breadcrumb-item active">DUDI</li>
        </ol>

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center py-3">
            <div>
              <i class="fas fa-table me-1"></i> Data DUDI
            </div>
            <?php if ($_SESSION['hak_akses'] !== 'siswa'): ?>
              <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                  <i class="fas fa-plus me-1"></i>Tambah Data
                </button>
              </div>
            <?php endif; ?>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="datatableDudi" class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-dark">
                  <tr>
                    <th width="50">No</th>
                    <th>Logo</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Owner</th>
                    <th>Jabatan</th>
                    <th>Pembimbing</th>
                    <?php if ($_SESSION['hak_akses'] !== 'siswa'): ?><th width="100">Aksi</th><?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($dudis)): ?>
                    <tr>
                      <td colspan="<?= $_SESSION['hak_akses'] !== 'siswa' ? '9' : '8' ?>" class="text-center py-4">
                        <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data DUDI
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1;
                    foreach ($dudis as $dudi): ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">
                          <img src="assets/img/dudi/<?= $dudi['logo']; ?>" alt="Logo DUDI" class="profile-img">
                        </td>
                        <td><?= $dudi['nama']; ?></td>
                        <td><?= $dudi['alamat']; ?></td>
                        <td><?= $dudi['kontak']; ?></td>
                        <td><?= $dudi['owner']; ?></td>
                        <td><?= $dudi['jabatan']; ?></td>
                        <td><?= $dudi['pembimbing']; ?></td>
                        <?php if ($_SESSION['hak_akses'] !== 'siswa'): ?>
                          <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                              <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $dudi['id_dudi']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                              </a>
                              <a href="hapus_dudi.php?id=<?= $dudi['id_dudi']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirmDelete()" title="Hapus">
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
            <h5 class="modal-title" id="modalTambahLabel">Tambah Data DUDI</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Kontak <span class="text-danger">*</span></label>
                <input type="text" name="kontak" class="form-control" required>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" required></textarea>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Owner <span class="text-danger">*</span></label>
                <input type="text" name="owner" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                <input type="text" name="jabatan" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Pembimbing <span class="text-danger">*</span></label>
                <input type="text" name="pembimbing" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Logo <span class="text-danger">*</span></label>
                <input type="file" name="logo" class="form-control" accept="image/*" required>
                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB.</div>
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
  <?php foreach ($dudis as $dudi): ?>
    <div class="modal fade" id="modalEdit<?= $dudi['id_dudi']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $dudi['id_dudi']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_dudi" value="<?= $dudi['id_dudi']; ?>">
            <input type="hidden" name="logo_lama" value="<?= $dudi['logo']; ?>">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEditLabel<?= $dudi['id_dudi']; ?>">Edit Data DUDI</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="text-center mb-4">
                <img src="assets/img/dudi/<?= $dudi['logo']; ?>" alt="Logo" width="100" class="rounded mb-2" style="border: 3px solid #e3e6f0;">
                <p class="text-muted">Logo saat ini</p>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama <span class="text-danger">*</span></label>
                  <input type="text" name="nama" value="<?= $dudi['nama']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kontak <span class="text-danger">*</span></label>
                  <input type="text" name="kontak" value="<?= $dudi['kontak']; ?>" class="form-control" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Alamat <span class="text-danger">*</span></label>
                  <textarea name="alamat" class="form-control" rows="3" required><?= $dudi['alamat']; ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Owner <span class="text-danger">*</span></label>
                  <input type="text" name="owner" value="<?= $dudi['owner']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                  <input type="text" name="jabatan" value="<?= $dudi['jabatan']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Pembimbing <span class="text-danger">*</span></label>
                  <input type="text" name="pembimbing" value="<?= $dudi['pembimbing']; ?>" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ganti Logo</label>
                  <input type="hidden" name="logo_lama" value="<?= $dudi['logo']; ?>">
                  <input type="file" name="logo" class="form-control" accept="image/*">
                  <div class="form-text">Kosongkan jika tidak ingin mengganti logo.</div>
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
  <script src="./assets/js/scripts.js"></script>

  <script>
    $(document).ready(function() {
      $('#datatableDudi').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        lengthMenu: [5, 10, 25, 50],
        responsive: {
          details: {
            type: 'column',
            target: 'tr'
          }
        },
        columnDefs: [{
            responsivePriority: 1,
            targets: 0
          },
          {
            responsivePriority: 2,
            targets: 8
          },
          {
            responsivePriority: 2,
            targets: 2
          },
          {
            responsivePriority: 3,
            targets: 6
          }
        ],
        language: {
          sProcessing: "Sedang memproses...",
          sLengthMenu: "Tampilkan _MENU_ data",
          sZeroRecords: "Tidak ditemukan data yang sesuai",
          sInfo: "Tampilkan _START_ sampai _END_ dari _TOTAL_ data",
          sInfoEmpty: "Tampilkan 0 sampai 0 dari 0 data",
          sInfoFiltered: "(disaring dari _MAX_ data keseluruhan)",
          sSearch: "Cari:",
          oPaginate: {
            sFirst: "Pertama",
            sPrevious: "<-",
            sNext: "->",
            sLast: "Terakhir"
          }
        }
      });
    });
  </script>
  <!-- <script>
    // Initialize DataTable
    $(document).ready(function() {
      // Tentukan target column berdasarkan hak akses
      var isAdmin = <?php echo ($_SESSION['hak_akses'] == 'admin') ? 'true' : 'false'; ?>;

      var columnDefs = [];
      if (isAdmin) {
        columnDefs = [{
          orderable: false,
          targets: [4]
        }];
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
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    });
  </script> -->
  <script src="./assets/template/logout-alert.php"></script>
</body>

</html>