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

// Tangkap id guru di url dengan $_GET
$id = $_GET['id'];

// Tampilkan halaman konfirmasi SweetAlert
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus Guru</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .container {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .loading {
            display: none;
            margin-top: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Konfirmasi Hapus Data Guru</h1>
        <div class="loading">
            <div class="spinner"></div>
            <p>Sedang memproses...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data guru yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    document.querySelector('.loading').style.display = 'block';
                    
                    // Redirect ke proses hapus dengan parameter konfirmasi
                    window.location.href = 'hapus-data-guru.php?id=<?php echo $id; ?>&confirm=1';
                } else {
                    window.location.href = 'guru-pembimbing.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Proses penghapusan jika ada konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    // Jalankan function hapus guru
    if (hapusGuru($id) > 0) {
        // Jika berhasil
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data guru berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = 'guru-pembimbing.php';
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
                    text: 'Data guru gagal dihapus!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = 'guru-pembimbing.php';
                });
            </script>
        ";
    }
    exit;
}
?>