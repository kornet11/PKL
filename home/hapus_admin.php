<?php
// Penghubung antar file di PHP
require 'functions.php';
session_start();
if (!isset($_SESSION['hak_akses']) || $_SESSION['hak_akses'] === 'siswa') {
    echo "<script>alert('Akses ditolak!');location.href='/.302.php';</script>";
    exit;
}
// Tangkap id admin di url dengan $_GET
$id = $_GET['id'];

// Jalankan function hapus admin
if( hapusAdmin($id) > 0 ) {
	// Jika berhasil
    echo "
        <script>
            alert('Berhasil Menghapus Data Admin.');
            document.location.href = 'admin.php';
        </script>
    ";
}else {
	// Jika gagal
    echo "
        <script>
            alert('Gagal Menghapus Data Admin!');
            document.location.href = 'admin.php';
        </script>
    ";
}

?>