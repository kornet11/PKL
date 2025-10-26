<?php
session_start();
require 'functions.php';

// Upload jurnal siswa
$message = '';
if (isset($_POST['upload'])) {
  $id_siswa = $_SESSION['id_siswa'];
  $nama     = $_FILES['jurnal']['name'];
  $tmp      = $_FILES['jurnal']['tmp_name'];
  $ext = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
  if ($ext == 'pdf') {
    $fileBaru = uniqid() . '_' . $nama;
    if (!is_dir('assets/jurnal')) mkdir('assets/jurnal', 0777, true);
    move_uploaded_file($tmp, 'assets/jurnal/' . $fileBaru);
    mysqli_query($conn, "INSERT INTO jurnal_siswa (id_siswa, file_jurnal, tanggal_upload) VALUES('$id_siswa','$fileBaru',NOW())");
    $message = "<div class='alert alert-success'>Jurnal berhasil diupload.</div>";
  } else {
    $message = "<div class='alert alert-danger'>File harus PDF.</div>";
  }
}

// Penilaian jurnal oleh guru
if (isset($_POST['simpan']) && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $nilai = intval($_POST['nilai']);
  mysqli_query($conn, "UPDATE jurnal_siswa SET nilai='$nilai' WHERE id='$id'");
  echo "<script>location='penilaian.php'</script>";
  exit;
}

// Ambil data jurnal
$jurnals = mysqli_query($conn, "SELECT js.*, s.nama FROM jurnal_siswa js LEFT JOIN siswa s ON js.id_siswa = s.id_siswa ORDER BY js.tanggal_upload DESC");

// Jika ada parameter id, tampilkan form penilaian
$showNilaiForm = false;
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM jurnal_siswa WHERE id='$id'"));
  $showNilaiForm = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>PKL - Penilaian Jurnal Siswa</title>
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
  </style>
</head>

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="index.php">Penilaian Jurnal</a>
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

  <div class="container mt-4">
    <h2 class="mb-4">Upload Jurnal Siswa</h2>
    <?= $message ?>
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
          <div class="col-md-6">
            <label class="form-label">Upload Jurnal (PDF)</label>
            <input type="file" name="jurnal" class="form-control" required>
          </div>
          <div class="col-md-2">
            <button name="upload" class="btn btn-success">
              <i class="fa fa-upload me-1"></i> Kirim
            </button>
          </div>
        </form>
      </div>
    </div>

    <?php if ($showNilaiForm): ?>
      <div class="card">
        <div class="card-header bg-primary text-white">Nilai Jurnal</div>
        <div class="card-body">
          <p>File Jurnal: <a href="assets/jurnal/<?= $data['file_jurnal'] ?>" target="_blank">Lihat</a></p>
          <form method="post" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label>Nilai</label>
              <input type="number" name="nilai" class="form-control" required>
            </div>
            <div class="col-md-2">
              <button name="simpan" class="btn btn-success">
                <i class="fa fa-save me-1"></i> Simpan
              </button>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header bg-dark text-white">Daftar Jurnal Siswa</div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Nama Siswa</th>
              <th>File</th>
              <th>Nilai</th>
              <th>Tanggal Upload</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1;
            foreach ($jurnals as $j): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($j['nama'] ?? $j['id_siswa']) ?></td>
                <td>
                  <a target="_blank" href="assets/jurnal/<?= $j['file_jurnal'] ?>">Lihat Jurnal</a>
                </td>
                <td><?= $j['nilai'] ?? "-" ?></td>
                <td><?= date('d-m-Y H:i', strtotime($j['tanggal_upload'])) ?></td>
                <td>
                  <a href="penilaian.php?id=<?= $j['id'] ?>" class="btn btn-primary btn-sm">Beri Nilai</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-secondary text-white">Jurnal yang Sudah Dikirim (Siswa)</div>
      <div class="card-body">
        <?php
        $idSiswa = $_SESSION['id_siswa'];
        $q = mysqli_query($conn, "SELECT * FROM jurnal_siswa WHERE id_siswa='$idSiswa'");
        ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>File</th>
              <th>Nilai</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($q)): ?>
              <tr>
                <td><a href="assets/jurnal/<?= $row['file_jurnal'] ?>" target="_blank">Lihat</a></td>
                <td><?= $row['nilai'] ?? 'Belum dinilai' ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/template/logout-alert.php"></script>
</body>

</html>