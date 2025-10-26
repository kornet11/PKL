<?php 
// Penghubung antar file pada PHP
require 'functions.php';



?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Laporan Peminjaman Buku</title>
	<!-- Bootstarp CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
        body {
            font-size: 14px;
        }
        table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
	<div class="container mt-4">
		<h2 class="mb-4 text-center" align="center">Laporan Peminjaman Buku</h2>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>No.</th>
                    <th>Nama Kelas</th>
                    <th>Nama Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah</th>
                    <th>Status</th>
				</tr>
			</thead>
			<tbody>
				<!-- Data Peminjaman Buku -->
				<?php 
				$tanggalP = $_GET['tanggalp'];
				$tanggalK = $_GET['tanggalk'];
				$status = $_GET['status'];

				$no = 1;
				$data = mysqli_query($conn, "SELECT * FROM peminjaman, buku WHERE id_buku = buku_id AND tgl_peminjaman >= '$tanggalP' AND (tgl_pengembalian_r <= '$tanggalK' OR tgl_pengembalian_a <= '$tanggalK') AND status_peminjaman = '$status'");

				while( $row = mysqli_fetch_assoc($data) ) :
				?>
				<tr>
					<td><?= $no++; ?>.</td>
                    <td><?= $row['nama_pinjam']; ?></td>
                    <td><?= $row['judul_buku']; ?></td>
                    <td><?= $row['tgl_peminjaman']; ?></td>
                    <td><?= $row['tgl_pengembalian_a']; ?></td>
                    <td><?= $row['jumlah_pinjam']; ?></td>
                    <td>
                        <?php if ($status == 1) { ?>
                            <span>Proses</span>
                        <?php }elseif ($status == 2) { ?>
                            <span>Dikembalikan</span>
                        <?php }elseif ($status == 3) { ?>
                            <span>Telat</span>
                        <?php } ?>
                    </td>
				</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>



	<!-- Bootstrap JS and Popper.js (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    	window.print();
    </script>

</body>
</html>