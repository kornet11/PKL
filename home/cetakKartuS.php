<?php
// Syarat menggunakan session
session_start();

// Cek jika tidak ada session login
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Penghubung antar file di PHP
require 'functions.php';

// Tangkap id di url menggunakan $_GET
$id = $_GET['id'];

$bulan = [
    "01" => "Januari",
    "02" => "Februari",
    "03" => "Maret",
    "04" => "April",
    "05" => "Mei",
    "06" => "Juni",
    "07" => "Juli",
    "08" => "Agustus",
    "09" => "September",
    "10" => "Oktober",
    "11" => "November",
    "12" => "Desember",
];

$tgl = date("d") . " " . $bulan[date("m")] . " " . date("Y");



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card">
            <div class="card-header bg-success text-center text-white">
                <div class="row">
                    <div class="col-2">
                        <img src="https://smkmambaulihsan.sch.id/wp-content/uploads/2023/04/logo-smk-mi.png" alt="Logo SMK MI" width="75">
                    </div>
                    <div class="col-10">
                        <span class="fs-5">Kartu Siswa</span><br>
                        <span class="fw-semibold fs-4">SMK Mamba'ul Ihsan</span><br>
                        <span>Jln Banyuurip Ujngpangkah Gresik Jawa Timur</span>
                    </div>
                </div>
            </div>
            <div class="card-body" style="background-image: linear-gradient(white 70%,#A0FBCF);">
                <?php
                $a = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa = '$id'");
                while ($dataA = mysqli_fetch_assoc($a)) :
                ?>
                    <div class="row">
                        <div class="col-3">
                            <img src="assets/img/anggota/<?= $dataA['foto']; ?>" width="100" class="mt-2">
                        </div>
                        <div class="col-9">
                            <table cellpadding="5" cellspacing="0">
                                <tr>
                                    <td>NISN</td>
                                    <td>:</td>
                                    <td><?= $dataA['nisn']; ?></td>
                                </tr>
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td><?= $dataA['nama']; ?></td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>:</td>
                                    <td><?= $dataA['kelas']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jurusan</td>
                                    <td>:</td>
                                    <td><?= $dataA['konsentrasi']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                <?php endwhile; ?>
                <div class="box-f d-flex align-items-center justify-content-center mt-3">
                    <div class="bar me-4">
                        <img alt="testing" src="assets/barcode/barcode.php?text=testing" width="230" />
                    </div>
                    <div class="tanda-tangan">
                        <p style="font-size: 14px;">Banyuurip, <?= $tgl; ?><br>Kepala Perustakaan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        window.print();
    </script>

</body>

</html>