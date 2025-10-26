<?php
// Penghubung antar file di PHP
require 'functions.php';
session_start();

if (!isset($_SESSION['hak_akses']) || $_SESSION['hak_akses'] === 'siswa') {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak!',
            text: 'Anda tidak memiliki hak akses untuk halaman ini.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '/.302.php';
        });
    </script>";
    exit;
}

// Tangkap id admin di url dengan $_GET
$id = $_GET['id'];

// Tampilkan halaman dengan konfirmasi SweetAlert
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus Siswa</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data siswa yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke proses hapus dengan parameter konfirmasi
                    window.location.href = 'hapus_siswa.php?id=<?php echo $id; ?>&confirm=1';
                } else {
                    window.location.href = 'siswa.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Proses penghapusan jika ada konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    // Jalankan function hapus admin
    if (hapusSiswa($id) > 0) {
        // Jika berhasil
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data siswa berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'siswa.php';
                });
            </script>
        ";
    } else {
        // Jika gagal
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Data siswa gagal dihapus!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'siswa.php';
                });
            </script>
        ";
    }
    exit;
}
?>