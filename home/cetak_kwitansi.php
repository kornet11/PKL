<?php
// Syarat menggunakan session
session_start();

// Cek jika tidak ada session login
if( !isset($_SESSION['login']) ) {
    echo "
        <script>
            document.location.href = '../index.php';
        </script>
    ";
}

// Penghubung antar file di PHP
require 'functions.php';

// Tangkap id di url menggunakan $_GET
$id = $_GET['id'];

// Ambil data peminjaman, anggota dan buku kemudian gabung menjadi satu
$data = mysqli_query($conn, "SELECT * FROM peminjaman, anggota, buku WHERE id_anggota = anggota_id AND id_buku = buku_id AND id_peminjaman = $id");
$row = mysqli_fetch_assoc($data);
$tgl_pinjam = $row['tgl_peminjaman'];
$tglA = $row['tgl_pengembalian_a'];
$denda = $row['denda'];
$status_peminjaman = $row['status_peminjaman'];

setlocale(LC_TIME, 'id_ID'); // setel lokal ke bahasa indonesia
$tanggalP = @strftime('%d %B %Y', strtotime($tgl_pinjam));

setlocale(LC_TIME, 'id_ID'); // setel lokal ke bahasa indonesia
$tanggalA = @strftime('%d %B %Y', strtotime($tglA));



?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Kwitansi Peminjaman Buku</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

		.header h1 {
			font-size: 22px;
			margin-bottom: 0;
		}

		.info {
			margin-top: 20px;
			margin-bottom: 20px;
			text-align: center;
		}

		.info hr {
			border-top: 2px solid #333;
		}

		.info table {
			width: 100%;
			border-collapse: collapse;
		}

		.info table th, .info table td {
			border: 1px solid #333;
			padding: 8px;
			text-align: left;
			font-size: 13px;
		}

		.footer {
			margin-top: 20px;
			text-align: right;
		}

	</style>
</head>
<body>

<div class="container info">
	<hr>
	<div class="header text-center">
		<h1>SMK Mamba'ul Ihsan</h1>
		<p class="mb-0">Perpustakaan SMK MI Banyuurip</p>
		<p>6281981657</p>
	</div>
	<div class="info">
		<hr>
		<table>
			<tr>
				<th>Nama Anggota</th>
				<td><?= $row['nama_anggota']; ?></td>
			</tr>
			<tr>
				<th>Tgl. Peminjaman</th>
				<td><?= $tanggalP; ?></td>
			</tr>
			<tr>
				<th>Tgl. Pengembalian Aktual</th>
				<td><?= $tanggalA; ?></td>
			</tr>
		</table>
		<hr>
		<table>
			<tr>
				<th><em>Kelas</em></th>
				<td><?= $row['nama_pinjam']; ?></td>
			</tr>
			<tr>
				<th><em>Judul Buku</em></th>
				<td><?= $row['judul_buku']; ?></td>
			</tr>
			<tr>
				<th><em>Jumlah Pinjam</em></th>
				<td><?= $row['jumlah_pinjam']; ?> Jumlah Buku</td>
			</tr>
			<?php if( $status_peminjaman == 3 ) : ?>
			<tr>
				<th><em>Denda Yang Harus Dibayar</em></th>
				<td>Rp. <?= $row['denda']; ?></td>
			</tr>
			<?php endif; ?>
		</table>
		<hr class="mb-0">
		<p style="font-size: 12px;
		text-align: left; color: red;"><em>*<?= $row['catatan']; ?>.</em></p>
	</div>
	<div class="footer">
		<div class="col-12">
			Dicetak oleh,
			<br><br>
			<br><b><u><?= $_SESSION['nama_admin']; ?></u></b>
		</div>
	</div>
</div>



<script>
	window.print();
</script>

</body>
</html>