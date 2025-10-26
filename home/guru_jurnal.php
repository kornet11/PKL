<?php
session_start();
require 'functions.php';

// jika form modal disubmit
if (isset($_POST['simpan_nilai'])) {
    $id    = intval($_POST['jurnal_id']);
    $nilai = intval($_POST['nilai']);
    beriNilai($conn, $id, $nilai);
}

// ambil semua jurnal
$jurnals = mysqli_query($conn, "
   SELECT js.*, s.nama 
   FROM jurnal_siswa js 
   LEFT JOIN siswa s ON js.id_siswa = s.id_siswa 
   ORDER BY js.tanggal_upload DESC
");

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
                <h1 class="mt-4">Jurnal Siswa</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Jurnal Siswa</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-table me-1"></i> Daftar Jurnal Siswa
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered table-rounded" style="width:100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>File</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($jurnals as $row) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td>
                                                <a href="assets/jurnal/<?= $row['file_jurnal'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fa fa-file-pdf me-1"></i> Lihat
                                                </a>
                                            </td>
                                            <td><?= $row['nilai'] ?? '-' ?></td>
                                            <td>
                                                <button
                                                    class="btn btn-primary btn-sm btn-berinilai"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-file="<?= $row['file_jurnal'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalNilai">
                                                    <i class="fa fa-edit me-1"></i> Beri Nilai
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modalNilai" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="post" class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Penilaian Jurnal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="jurnal_id" id="jurnal_id">
                                <p>File Jurnal :
                                    <a href="#" target="_blank" id="link_jurnal">Lihat</a>
                                </p>
                                <div class="mb-3">
                                    <label>Nilai</label>
                                    <input type="number" name="nilai" class="form-control" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="simpan_nilai" class="btn btn-success">
                                    <i class="fa fa-save me-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('.btn-berinilai').on('click', function() {
            let id = $(this).data('id');
            let file = $(this).data('file');
            $('#jurnal_id').val(id);
            $('#link_jurnal').attr('href', 'assets/jurnal/' + file);
        });
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>