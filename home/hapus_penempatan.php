<?php
// Panggil koneksi ke database
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

// Ambil ID yang dikirimkan melalui URL
$id = $_GET["id"];

// Tampilkan halaman konfirmasi SweetAlert
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus Penempatan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            max-width: 450px;
            width: 90%;
        }
        h1 {
            color: #4a5568;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .loading {
            display: none;
            margin-top: 25px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: #718096;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Konfirmasi Hapus Data Penempatan</h1>
        <div class="loading">
            <div class="spinner"></div>
            <p class="loading-text">Sedang memproses penghapusan...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data penempatan yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#3182ce',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    document.querySelector('.loading').style.display = 'block';
                    
                    // Redirect ke proses hapus dengan parameter konfirmasi
                    window.location.href = 'hapus_penempatan.php?id=<?php echo $id; ?>&confirm=1';
                } else {
                    window.location.href = 'penempatan.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Proses penghapusan jika ada konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    // Panggil fungsi hapus
    if (hapusPenempatan($id) > 0) {
        // Jika berhasil
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data penempatan berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true
                }).then(() => {
                    window.location.href = 'penempatan.php';
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
                    text: 'Data penempatan gagal dihapus!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3182ce',
                    footer: 'Silakan coba lagi atau hubungi administrator'
                }).then(() => {
                    window.location.href = 'penempatan.php';
                });
            </script>
        ";
    }
    exit;
}
?>