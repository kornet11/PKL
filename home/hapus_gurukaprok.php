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

// Tangkap id guru kaprokom di url dengan $_GET
$id = $_GET['id'];

// Tampilkan halaman konfirmasi SweetAlert
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus Guru Kaprokom</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .container {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #4a5568;
            margin-bottom: 25px;
            font-weight: 700;
        }
        .loading {
            display: none;
            margin-top: 25px;
        }
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #f093fb;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: #718096;
            font-size: 18px;
            font-weight: 500;
        }
        .warning-icon {
            font-size: 64px;
            color: #f56565;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon">⚠️</div>
        <h1>Konfirmasi Hapus Data Guru Kaprokom</h1>
        <div class="loading">
            <div class="spinner"></div>
            <p class="loading-text">Sedang memproses penghapusan...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: 'Data guru kaprokom yang dihapus <strong>tidak dapat dikembalikan!</strong>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#38a169',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    title: 'custom-title',
                    content: 'custom-content',
                    confirmButton: 'custom-confirm-button',
                    cancelButton: 'custom-cancel-button'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    document.querySelector('.loading').style.display = 'block';
                    
                    // Redirect ke proses hapus dengan parameter konfirmasi
                    window.location.href = 'hapus_gurukaprok.php?id=<?php echo $id; ?>&confirm=1';
                } else {
                    window.location.href = 'gurukaprok.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Proses penghapusan jika ada konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    // Jalankan function hapus guru kaprokom
    if (hapusGuruKaprok($id) > 0) {
        // Jika berhasil
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data guru kaprokom berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true,
                    customClass: {
                        popup: 'colored-toast'
                    }
                }).then(() => {
                    window.location.href = 'gurukaprok.php';
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
                    text: 'Data guru kaprokom gagal dihapus!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#38a169',
                    footer: '<strong>Periksa koneksi database atau hubungi administrator</strong>',
                    customClass: {
                        confirmButton: 'custom-confirm-button'
                    }
                }).then(() => {
                    window.location.href = 'gurukaprok.php';
                });
            </script>
        ";
    }
    exit;
}
?>