<?php
session_start();
if (!isset($_SESSION['login'])) {
    echo "<script>document.location.href = '../index.php';</script>";
}
require 'functions.php';

$id = $_GET['id'];

$bulan = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret",
    "04" => "April", "05" => "Mei", "06" => "Juni",
    "07" => "Juli", "08" => "Agustus", "09" => "September",
    "10" => "Oktober", "11" => "November", "12" => "Desember",
];

$tgl = date("d") . " " . $bulan[date("m")] . " " . date("Y");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cetak Kartu Siswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        margin: 0;
        padding: 0;
        background: #f0f0f0;
    }
    .photo {
        width: 100%;
        max-width: 120px;
        height: auto;
        border-radius: 2px;
        border: 1px solid #ccc;
    }
    .barcode img {
        width: 180px;
        height: auto;
    }
    .card-body {
        background-image: linear-gradient(white 70%, #A0FBCF);
    }
</style>
</head>
<body>

<div class="container my-4">
    <div class="card shadow border">
        <div class="card-header bg-success text-white p-2">
            <div class="row g-0 align-items-center">
                <div class="col-2">
                    <img src="assets/img/logo/logosmkmi.png" alt="Logo SMK MI" class="img-fluid">
                </div>
                <div class="col-10">
                    <span class="fs-5 fw-bold">Kartu Siswa</span><br>
                    <span class="fw-semibold fs-4">SMK Mamba'ul Ihsan</span><br>
                    <span>Jln Banyuurip Ujngpangkah Gresik Jawa Timur</span>
                </div>
            </div>
        </div>
        <div class="card-body p-2">
            <?php
            $a = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa = '$id'");
            while ($dataA = mysqli_fetch_assoc($a)) :
            ?>
            <div class="row g-2">
                <div class="col-3">
                    <img src="assets/img/siswa/<?= $dataA['foto']; ?>" alt="Foto Siswa" class="photo mt-1">
                </div>
                <div class="col-9">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td>NISN</td><td>:</td><td><?= $dataA['nisn']; ?></td></tr>
                        <tr><td>Nama</td><td>:</td><td><?= $dataA['nama']; ?></td></tr>
                        <tr><td>Kelas</td><td>:</td><td><?= $dataA['kelas']; ?></td></tr>
                        <tr><td>Jurusan</td><td>:</td><td><?= $dataA['konsentrasi']; ?></td></tr>
                    </table>
                </div>
            </div>
            <?php endwhile; ?>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="barcode">
                    <img src="assets/barcode/barcode.php?text=<?= $dataA['nisn']; ?>" alt="Barcode">
                </div>
                <div class="text-end small">
                    Banyuurip, <?= $tgl; ?><br>
                    Kepala Perpustakaan<br>
                    <img src="assets/img/tanda-tangan.png" alt="Tanda Tangan" class="mt-1" style="width:60px; height:auto;"><br>
                    <strong>ABID KHALIK ROKO, S.E., M.M.</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3 text-center">
        <a href="cetakKartuS.php?id=<?= $id; ?>" class="btn btn-success" target="_blank">Cetak Kartu</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
