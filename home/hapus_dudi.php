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

// Tangkap id DUDI di url dengan $_GET
$id = $_GET['id'];

// Tampilkan halaman konfirmasi SweetAlert
?>
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Hapus DUDI</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            max-width: 550px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        h1 {
            color: #2d3748;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 28px;
        }
        .icon-container {
            margin-bottom: 30px;
        }
        .dudi-icon {
            font-size: 80px;
            color: #667eea;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .loading {
            display: none;
            margin-top: 30px;
        }
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #667eea;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            color: #4a5568;
            font-size: 18px;
            font-weight: 500;
        }
        .info-text {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <i class="fas fa-building dudi-icon"></i>
        </div>
        <h1>Konfirmasi Hapus Data DUDI</h1>
        <p class="info-text">
            Anda akan menghapus data Dunia Usaha/Dunia Industri. 
            Tindakan ini tidak dapat dibatalkan!
        </p>
        <div class="loading">
            <div class="spinner"></div>
            <p class="loading-text">Sedang memproses penghapusan data DUDI...</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Hapus Data DUDI?',
                html: '<strong>Peringatan:</strong> Data DUDI yang dihapus akan hilang secara permanen!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#38a169',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                reverseButtons: true,
                focusCancel: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    document.querySelector('.loading').style.display = 'block';
                    
                    // Redirect ke proses hapus dengan parameter konfirmasi
                    window.location.href = 'hapus_dudi.php?id=<?php echo $id; ?>&confirm=1';
                } else {
                    window.location.href = 'dudi.php';
                }
            });
        });
    </script>
</body>
</html>

<?php
// Proses penghapusan jika ada konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] == '1') {
    // Jalankan function hapus DUDI
    if (hapusDudi($id) > 0) {
        // Jika berhasil
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    html: '<strong>Data DUDI</strong> berhasil dihapus dari sistem.',
                    timer: 2500,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    position: 'top-end',
                    toast: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                }).then(() => {
                    window.location.href = 'dudi.php';
                });
            </script>
        ";
    } else {
        // Jika gagal
        echo "
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menghapus!',
                    html: '<strong>Data DUDI</strong> gagal dihapus!<br>Silakan periksa koneksi database atau hubungi administrator.',
                    confirmButtonText: '<i class=\"fas fa-check\"></i> OK',
                    confirmButtonColor: '#38a169',
                    footer: '<i class=\"fas fa-exclamation-triangle\"></i> Error Code: DUDI-DEL-001',
                    customClass: {
                        confirmButton: 'btn-confirm'
                    }
                }).then(() => {
                    window.location.href = 'dudi.php';
                });
            </script>
        ";
    }
    exit;
}
?>