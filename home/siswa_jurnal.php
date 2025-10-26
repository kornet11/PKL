<?php
session_start();
require 'functions.php';

// 🔐 Cek login
if (!isset($_SESSION['login'])) {
    echo "<script>alert('Silakan login terlebih dahulu');location='index.php';</script>";
    exit;
}

// 🔐 Ambil role
$hak_akses = $_SESSION['hak_akses'];
$message = "";

// ================== Upload Jurnal (khusus siswa) ==================
if (isset($_POST['upload']) && $hak_akses === 'siswa') {
    $id_siswa = $_SESSION['id_siswa'];
    $nama     = $_FILES['jurnal']['name'];
    $tmp      = $_FILES['jurnal']['tmp_name'];
    $ext      = strtolower(pathinfo($nama, PATHINFO_EXTENSION));

    if ($ext === 'pdf') {
        $newName = uniqid() . '_' . $nama;
        if (!is_dir('assets/jurnal')) mkdir('assets/jurnal', 0777, true);
        move_uploaded_file($tmp, 'assets/jurnal/' . $newName);
        mysqli_query($conn, "INSERT INTO jurnal_siswa (id_siswa,file_jurnal,tanggal_upload) VALUES ('$id_siswa','$newName',NOW())");
        $message = "<div class='alert alert-success'>Jurnal berhasil diupload.</div>";
    } else {
        $message = "<div class='alert alert-danger'>File harus PDF.</div>";
    }
}

// ================== Edit Jurnal (khusus siswa) ==================
if (isset($_POST['edit_jurnal']) && $hak_akses === 'siswa') {
    $id_jurnal = intval($_POST['id_jurnal']);
    $nama     = $_FILES['jurnal']['name'];
    $tmp      = $_FILES['jurnal']['tmp_name'];
    $ext      = strtolower(pathinfo($nama, PATHINFO_EXTENSION));

    // Ambil nama file lama
    $res = mysqli_query($conn, "SELECT file_jurnal FROM jurnal_siswa WHERE id='$id_jurnal' AND id_siswa='{$_SESSION['id_siswa']}'");
    $old = mysqli_fetch_assoc($res);
    if ($old && $ext === 'pdf') {
        $newName = uniqid() . '_' . $nama;
        if (!is_dir('assets/jurnal')) mkdir('assets/jurnal', 0777, true);
        move_uploaded_file($tmp, 'assets/jurnal/' . $newName);
        // Hapus file lama jika ada
        if (!empty($old['file_jurnal']) && file_exists('assets/jurnal/' . $old['file_jurnal'])) {
            unlink('assets/jurnal/' . $old['file_jurnal']);
        }
        mysqli_query($conn, "UPDATE jurnal_siswa SET file_jurnal='$newName', tanggal_upload=NOW() WHERE id='$id_jurnal' AND id_siswa='{$_SESSION['id_siswa']}'");
        $message = "<div class='alert alert-success'>Jurnal berhasil diupdate.</div>";
    } else {
        $message = "<div class='alert alert-danger'>File harus PDF.</div>";
    }
}

// ================== Admin / Guru menilai ==================
if (in_array($hak_akses, ['admin', 'gurupem', 'gurukaprok']) && isset($_POST['beri_nilai'])) {
    $id_jurnal = intval($_POST['id_jurnal']);
    $nilai     = intval($_POST['nilai']);
    mysqli_query($conn, "UPDATE jurnal_siswa SET nilai='$nilai' WHERE id='$id_jurnal'");
    $message = "<div class='alert alert-success'>Nilai berhasil disimpan.</div>";
}

// ================== Hapus Jurnal (khusus siswa) ==================
if (isset($_POST['hapus_jurnal']) && $hak_akses === 'siswa') {
    $id_jurnal = intval($_POST['id_jurnal']);
    // Ambil nama file
    $res = mysqli_query($conn, "SELECT file_jurnal FROM jurnal_siswa WHERE id='$id_jurnal' AND id_siswa='{$_SESSION['id_siswa']}'");
    $old = mysqli_fetch_assoc($res);
    if ($old) {
        // Hapus file fisik
        if (!empty($old['file_jurnal']) && file_exists('assets/jurnal/' . $old['file_jurnal'])) {
            unlink('assets/jurnal/' . $old['file_jurnal']);
        }
        // Hapus dari database
        mysqli_query($conn, "DELETE FROM jurnal_siswa WHERE id='$id_jurnal' AND id_siswa='{$_SESSION['id_siswa']}'");
        $message = "<div class='alert alert-success'>Jurnal berhasil dihapus.</div>";
    }
}

// ================== Ambil data jurnal ==================
$filter_nama = '';
$where_nama = '';
if (isset($_GET['nama']) && $_GET['nama'] !== '') {
    $filter_nama = mysqli_real_escape_string($conn, $_GET['nama']);
    $where_nama = " AND s.nama LIKE '%$filter_nama%'";
}

if ($hak_akses === 'siswa') {
    $idsiswa = $_SESSION['id_siswa'];
    $list = mysqli_query($conn, "
        SELECT js.*, s.nama, s.konsentrasi 
        FROM jurnal_siswa js
        LEFT JOIN siswa s ON js.id_siswa = s.id_siswa
        WHERE js.id_siswa='$idsiswa'
        ORDER BY js.tanggal_upload DESC
    ");
} elseif ($hak_akses === 'admin') {
    // Admin bisa lihat semua jurnal
    $list = mysqli_query($conn, "
        SELECT js.*, s.nama, s.konsentrasi 
        FROM jurnal_siswa js
        LEFT JOIN siswa s ON js.id_siswa = s.id_siswa
        WHERE 1=1 $where_nama
        ORDER BY js.tanggal_upload DESC
    ");
} else {
    // Guru hanya lihat jurnal sesuai jurusan
    $id_guru = $_SESSION['id_gurupem'] ?? $_SESSION['id_gurukaprok'];
    $resGuru = mysqli_query($conn, "SELECT jurusan FROM gurupembimbing WHERE id_gurupem='$id_guru' 
                                     UNION 
                                     SELECT jurusan FROM gurukaprok WHERE id_gurukaprok='$id_guru'");
    $guru = mysqli_fetch_assoc($resGuru);
    $jurusan_guru = $guru['jurusan'];

    $list = mysqli_query($conn, "
        SELECT js.*, s.nama, s.konsentrasi 
        FROM jurnal_siswa js
        LEFT JOIN siswa s ON js.id_siswa = s.id_siswa
        WHERE s.konsentrasi='$jurusan_guru' $where_nama
        ORDER BY js.tanggal_upload DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>PKL - Jurnal Siswa</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .card {
      margin-bottom: 2rem;
    }

    .table th,
    .table td {
      vertical-align: middle;
    }
  </style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Jurnal Siswa</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
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
    <?php include 'menu.php'; ?>
    <div id="layoutSidenav_content">
      <main class="container-fluid px-4">
        <h1 class="mt-4"><?= $hak_akses === 'siswa' ? 'Jurnal Saya' : 'Jurnal Siswa' ?></h1>
        <ol class="breadcrumb mb-4">
          <li class="breadcrumb-item active">Jurnal Siswa</li>
        </ol>

        <?php if ($hak_akses === 'siswa'): ?>
          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <i class="fas fa-upload me-1"></i> Upload Jurnal (PDF)
            </div>
            <div class="card-body">
              <?= $message ?>
              <form method="post" enctype="multipart/form-data" class="row g-2">
                <div class="col-md-8">
                  <input type="file" name="jurnal" class="form-control" required>
                </div>
                <div class="col-md-2">
                  <button name="upload" class="btn btn-success">
                    <i class="fa fa-upload me-1"></i> Upload
                  </button>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($hak_akses !== 'siswa'): ?>
          <div class="card mb-4">
            <div class="card-header bg-dark text-white">
              <i class="fas fa-filter me-1"></i> Filter Nama Siswa
            </div>
            <div class="card-body">
              <form method="get" class="row g-2">
                <div class="col-md-6">
                  <input type="text" name="nama" class="form-control" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($filter_nama) ?>">
                </div>
                <div class="col-md-2">
                  <button class="btn btn-primary">Filter</button>
                </div>
                <div class="col-md-2">
                  <a href="siswa_jurnal.php" class="btn btn-secondary">Reset</a>
                </div>
              </form>
            </div>
          </div>
        <?php endif; ?>

        <div class="card mb-4">
          <div class="card-header bg-success text-white">
            <i class="fas fa-table me-1"></i> <?= $hak_akses === 'siswa' ? 'Jurnal Saya' : 'Daftar Jurnal Siswa' ?>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover table-bordered table-rounded" style="width:100%">
                <thead class="table-dark">
                  <tr>
                    <?php if ($hak_akses !== 'siswa'): ?>
                      <th>Nama Siswa</th>
                    <?php endif; ?>
                    <th>File</th>
                    <th>Nilai</th>
                    <th>Tanggal Upload</th>
                    <?php if ($hak_akses !== 'siswa'): ?>
                      <th>Aksi</th>
                    <?php else: ?>
                      <th>Edit</th>
                    <?php endif; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($list)): ?>
                    <tr>
                      <?php if ($hak_akses !== 'siswa'): ?>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                      <?php endif; ?>
                      <td>
                        <a href="assets/jurnal/<?= $row['file_jurnal'] ?>" target="_blank" class="btn btn-sm btn-info">
                          <i class="fa fa-file-pdf me-1"></i> Lihat
                        </a>
                      </td>
                      <td><?= $row['nilai'] ?? 'Belum dinilai' ?></td>
                      <td><?= date('d-m-Y H:i', strtotime($row['tanggal_upload'])) ?></td>
                      <?php if ($hak_akses !== 'siswa'): ?>
                        <td>
                          <form method="post" class="d-flex">
                            <input type="hidden" name="id_jurnal" value="<?= $row['id'] ?>">
                            <input type="number" name="nilai" min="0" max="100" class="form-control me-2" value="<?= $row['nilai'] ?? '' ?>" required>
                            <button type="submit" name="beri_nilai" class="btn btn-primary btn-sm">
                              <i class="fa fa-save me-1"></i> Simpan
                            </button>
                          </form>
                        </td>
                      <?php else: ?>
                        <td>
                          <form method="post" enctype="multipart/form-data" class="d-flex" style="gap:4px;">
                            <input type="hidden" name="id_jurnal" value="<?= $row['id'] ?>">
                            <input type="file" name="jurnal" class="form-control me-2" accept="application/pdf" required>
                            <button type="submit" name="edit_jurnal" class="btn btn-warning btn-sm">
                              <i class="fa fa-edit me-1"></i> Edit
                            </button>
                          </form>
                          <form method="post" style="display:inline-block;margin-top:4px;" onsubmit="return confirm('Yakin ingin menghapus jurnal ini?');">
                            <input type="hidden" name="id_jurnal" value="<?= $row['id'] ?>">
                            <button type="submit" name="hapus_jurnal" class="btn btn-danger btn-sm">
                              <i class="fa fa-trash me-1"></i> Hapus
                            </button>
                          </form>
                        </td>
                      <?php endif; ?>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/template/logout-alert.php"></script>
</body>

</html>