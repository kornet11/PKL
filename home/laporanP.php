<?php
// Syarat menggunakan session
session_start();

// Cek jika tidak ada session login
if (!isset($_SESSION['login'])) {
    echo "
        <script>
            document.location.href = '../index.php';
        </script>
    ";
}

// Penghubung antar file di PHP
require 'functions.php';



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Perpustakaan | Peminjaman Buku</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">E-Perpus</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item btn-logout" href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <?php include 'menu.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Laporan Peminjaman</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Laporan Peminjaman</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Data Laporan Peminjaman
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="tanggal_pinjam" class="form-label">Pilih Tanggal Pinjam</label>
                                            <input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="tanggal_kembali" class="form-label">Pilih Tanggal Kembali</label>
                                            <input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali" required>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Pilih Status Pinjam</label>
                                            <select class="form-select" aria-label="Default select example" name="status" id="status" required>
                                                <option value="" selected disabled hidden>Pilih Status Pinjam</option>
                                                <option value="1">Dipinjam</option>
                                                <option value="2">Dikembalikan</option>
                                                <option value="3">Telat</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </form>
                            <hr>
                            <?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                // Ambil tanggal dari form
                                $tanggalP = $_POST['tanggal_pinjam'];
                                $tanggalK = $_POST['tanggal_kembali'];
                                $statusP = $_POST['status'];
                            ?>

                                <div class="container">
                                    <a href="cetak.php?tanggalp=<?= $tanggalP; ?>&tanggalk=<?= $tanggalK; ?>&status=<?= $statusP; ?>" class="btn btn-sm btn-success mb-3" target=_blank><i class="fas fa-fw fa-print me-1"></i>Print Semua</a>
                                </div>

                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Kelas</th>
                                            <th>Nama Buku</th>
                                            <th>Tgl Peminjaman</th>
                                            <th>Tgl Pengembalian</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No.</th>
                                            <th>Nama Kelas</th>
                                            <th>Nama Buku</th>
                                            <th>Tgl Peminjaman</th>
                                            <th>Tgl Pengembalian</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $tanggalP = $_POST['tanggal_pinjam'];
                                        $tanggalK = $_POST['tanggal_kembali'];
                                        $statusP = $_POST['status'];
                                        $query = mysqli_query($conn, "SELECT * FROM peminjaman, buku WHERE id_buku = buku_id AND tgl_peminjaman >= '$tanggalP' AND (tgl_pengembalian_r <= '$tanggalK' OR tgl_pengembalian_a <= '$tanggalK') AND status_peminjaman = '$statusP'");
                                        while ($row = mysqli_fetch_assoc($query)) :
                                            $status = $row['status_peminjaman'];
                                        ?>
                                            <tr class="text-center">
                                                <td><?= $no++; ?></td>
                                                <td><?= $row['nama_pinjam']; ?></td>
                                                <td><?= $row['judul_buku']; ?></td>
                                                <td><?= $row['tgl_peminjaman']; ?></td>
                                                <td><?= $row['tgl_pengembalian_a']; ?></td>
                                                <td><?= $row['jumlah_pinjam']; ?></td>
                                                <td class="text-white">
                                                    <?php
                                                    if ($status == 1) {
                                                    ?>
                                                        <span class="badge text-bg-warning text-white">Proses</span>
                                                    <?php
                                                    } else if ($status == 2) {
                                                    ?>
                                                        <span class="badge text-bg-success text-white">Dikembalikan</span>
                                                    <?php
                                                    } else if ($status == 3) {
                                                    ?>
                                                        <span class="badge text-bg-danger text-white">Telat</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <a href="cetak1.php?id=<?= $row['id_peminjaman']; ?>&tanggalp=<?= $tanggalP; ?>&tanggalk=<?= $tanggalK; ?>&status=<?= $statusP; ?>" class="btn btn-sm btn-success mb-3" target=_blank>Print</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <div class="alert alert-info" role="alert">
                                    <center>
                                        <strong>Perhatian!</strong> Silahkan Filter Laporan Peminjaman
                                    </center>
                                </div>
                            <?php } ?>
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



    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/datatables-simple-demo.js"></script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>