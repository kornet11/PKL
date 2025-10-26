<?php
session_start();
require 'functions.php';
// cek hak akses admin
if (!isset($_SESSION['hak_akses']) || $_SESSION['hak_akses'] !== 'admin') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak!',
                text: 'Anda tidak memiliki hak akses untuk halaman ini.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>";
    exit;
}
// fungsi buat kode acak
function generateKode($length = 8)
{
    return strtoupper(bin2hex(random_bytes($length / 2)));
}

// =============================
// LOGIKA HAPUS - Dipindahkan ke paling atas
// =============================
if (isset($_GET['hapus']) && !empty($_GET['hapus'])) {
    $id = intval($_GET['hapus']);

    // Debug - Tampilkan ID yang diterima
    error_log("Mencoba menghapus ID: " . $id);

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Cek apakah ID ada di database
    $check_query = "SELECT id FROM kode_register WHERE id = $id";
    $check_result = mysqli_query($conn, $check_query);

    if (!$check_result) {
        die("Query check error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($check_result) > 0) {
        // ID ditemukan, lakukan penghapusan
        $delete_query = "DELETE FROM kode_register WHERE id = $id";
        $delete_result = mysqli_query($conn, $delete_query);

        if ($delete_result) {
            // Debug
            error_log("Berhasil menghapus ID: " . $id);

            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Notifikasi</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>
                    body {
                        background-color: #f8f9fa;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    }
                    .success-container {
                        text-align: center;
                        background: white;
                        padding: 40px;
                        border-radius: 10px;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        max-width: 400px;
                        width: 90%;
                    }
                    .success-icon {
                        font-size: 64px;
                        color: #28a745;
                        margin-bottom: 20px;
                    }
                    .success-title {
                        font-size: 24px;
                        font-weight: 600;
                        margin-bottom: 10px;
                        color: #333;
                    }
                    .success-message {
                        font-size: 16px;
                        color: #666;
                        margin-bottom: 30px;
                    }
                    .progress-bar {
                        height: 6px;
                        background-color: #e9ecef;
                        border-radius: 3px;
                        overflow: hidden;
                        margin-bottom: 20px;
                    }
                    .progress {
                        height: 100%;
                        background-color: #28a745;
                        border-radius: 3px;
                        animation: progress 2s linear forwards;
                    }
                    @keyframes progress {
                        0% { width: 0%; }
                        100% { width: 100%; }
                    }
                    .redirect-text {
                        font-size: 14px;
                        color: #6c757d;
                    }
                </style>
            </head>
            <body>
                <div class='success-container'>
                    <div class='success-icon'>✓</div>
                    <div class='success-title'>Berhasil Dihapus!</div>
                    <div class='success-message'>Kode registrasi berhasil dihapus dari sistem.</div>
                    <div class='progress-bar'>
                        <div class='progress'></div>
                    </div>
                    <div class='redirect-text'>Mengalihkan ke halaman utama...</div>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'kode_register.php';
                    }, 2000);
                </script>
            </body>
            </html>";
            exit;
        } else {
            // Gagal hapus
            $error = mysqli_error($conn);
            error_log("Error menghapus: " . $error);

            echo "<!DOCTYPE html>
            <html>
            <head>
                <title>Error</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menghapus kode registrasi: $error',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'kode_register.php';
                    });
                </script>
            </body>
            </html>";
            exit;
        }
    } else {
        // ID tidak ditemukan
        error_log("ID tidak ditemukan: " . $id);

        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Data Tidak Ditemukan!',
                    text: 'Kode registrasi dengan ID tersebut tidak ada.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'kode_register.php';
                });
            </script>
        </body>
        </html>";
        exit;
    }
}

// =============================
// LOGIKA TAMBAH KODE
// =============================
if (isset($_POST['buat_kode'])) {
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $kode_baru = generateKode(10);
    $query = "INSERT INTO kode_register (kode, role, created_at) 
              VALUES ('$kode_baru', '$role', NOW())";

    if (mysqli_query($conn, $query)) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Notifikasi</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: 'Kode berhasil dibuat: <strong>$kode_baru</strong>',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'kode_register.php';
                });
            </script>
        </body>
        </html>";
        exit;
    } else {
        die("Query insert error: " . mysqli_error($conn));
    }
}

// =============================
// AMBIL DATA KODE
// =============================
// Cek apakah fungsi tampilKode ada
if (!function_exists('tampilKode')) {
    die("Fungsi tampilKode tidak ditemukan di file functions.php");
}

$kode = tampilKode("SELECT * FROM kode_register ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>PKL</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #4e73df;
            --secondary: #6c757d;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .table th {
            border-top: none;
            font-weight: 700;
            color: var(--dark);
            padding: 1rem 0.75rem;
            background-color: #f8f9fc;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
        }

        .kode-badge {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            padding: 0.35rem 0.65rem;
            background-color: #f8f9fc;
            border: 1px dashed #d1d3e2;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .kode-badge:hover {
            background-color: #eaecf4;
            border-color: #b7b9cc;
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 0.35rem;
        }

        .bg-gurupem {
            background-color: rgba(78, 115, 223, 0.2);
            color: #4e73df;
        }

        .bg-gurukaprok {
            background-color: rgba(28, 200, 138, 0.2);
            color: #1cc88a;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
            transform: scale(0.95);
        }

        .table-row-transition {
            transition: all 0.5s ease-out;
        }

        .form-select:focus,
        .btn:focus {
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            border-color: #4e73df;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 5px !important;
            margin: 0 3px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
        }

        .copy-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--success);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .copy-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-key me-2"></i>Kode Registrasi
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item btn-logout" href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <?php include 'menu.php'; ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-key me-2"></i>Manajemen Kode Registrasi
                        </h1>
                    </div>

                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Kode Registrasi</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <div><i class="fas fa-table me-1"></i> Data Kode Registrasi</div>
                        </div>
                        <div class="card-body">
                            <!-- Form buat kode -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">
                                                <i class="fas fa-plus-circle me-1"></i>Buat Kode Baru
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="mb-3">
                                                    <label class="form-label">Pilih Role <span class="text-danger">*</span></label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="">-- Pilih Role --</option>
                                                        <option value="gurupem">Guru Pembimbing</option>
                                                        <option value="gurukaprok">Guru Kaprok</option>
                                                    </select>
                                                </div>
                                                <button type="submit" name="buat_kode" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>Buat Kode Acak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">
                                                <i class="fas fa-info-circle me-1"></i>Informasi
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                Kode registrasi digunakan untuk mendaftarkan akun guru pembimbing dan guru kaprok.
                                                Setiap kode hanya dapat digunakan sekali dan akan kadaluarsa setelah 24 jam.
                                            </p>
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                <strong>Perhatian:</strong> Simpan kode dengan aman dan jangan bagikan kepada pihak yang tidak berwenang.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel kode -->
                            <div class="table-responsive">
                                <table id="datatablekode" class="table table-bordered table-hover table-rounded" style="width:100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Kode</th>
                                            <th>Role</th>
                                            <th>Dibuat</th>
                                            <th width="100">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($kode) > 0): ?>
                                            <?php $no = 1;
                                            foreach ($kode as $row): ?>
                                                <tr id="row-<?= $row['id'] ?>" class="table-row-transition">
                                                    <td class="text-center"><?= $no++; ?></td>
                                                    <td class="text-nowrap">
                                                        <span class="kode-badge" data-kode="<?= htmlspecialchars($row['kode']) ?>">
                                                            <i class="fas fa-copy me-1"></i><?= htmlspecialchars($row['kode']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap">
                                                        <?php
                                                        $role_class = $row['role'] == 'gurupem' ? 'bg-gurupem' : 'bg-gurukaprok';
                                                        $role_text = $row['role'] == 'gurupem' ? 'Guru Pembimbing' : 'Guru Kaprok';
                                                        ?>
                                                        <span class="role-badge <?= $role_class ?>">
                                                            <?= $role_text ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-nowrap"><?= $row['created_at'] ?></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-danger btn-hapus btn-action"
                                                            data-id="<?= $row['id'] ?>"
                                                            data-kode="<?= htmlspecialchars($row['kode']) ?>"
                                                            title="Hapus Kode">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data kode registrasi
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-center small">
                        <?php $date = date('Y'); ?>
                        <div class="text-muted">Copyright &copy; Web PKL By Kornet <?= $date ?>.</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Copy Notification -->
    <div class="copy-notification" id="copyNotification">
        <i class="fas fa-check-circle me-2"></i> Kode disalin ke clipboard!
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="./assets/js/scripts.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#datatablekode').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthMenu: [5, 10, 25, 50],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: 4
                    },
                    {
                        responsivePriority: 3,
                        targets: 2
                    },
                    {
                        responsivePriority: 2,
                        targets: 1
                    }
                ],
                language: {
                    sProcessing: "Sedang memproses...",
                    sLengthMenu: "Tampilkan _MENU_ data",
                    sZeroRecords: "Tidak ditemukan data yang sesuai",
                    sInfo: "Tampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    sInfoEmpty: "Tampilkan 0 sampai 0 dari 0 data",
                    sInfoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                    sSearch: "Cari:",
                    oPaginate: {
                        sFirst: "Pertama",
                        sPrevious: "<-",
                        sNext: "->",
                        sLast: "Terakhir"
                    }
                }
            });

            // Copy to clipboard functionality
            $('.kode-badge').click(function() {
                const kode = $(this).data('kode');
                navigator.clipboard.writeText(kode).then(() => {
                    const notification = document.getElementById('copyNotification');
                    notification.classList.add('show');

                    setTimeout(() => {
                        notification.classList.remove('show');
                    }, 2000);
                }).catch(err => {
                    console.error('Gagal menyalin teks: ', err);
                });
            });

            // Handle delete button click with event delegation
            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');
                const kode = $(this).data('kode');
                const rowId = `row-${id}`;
                const row = document.getElementById(rowId);

                Swal.fire({
                    title: 'Hapus Kode Registrasi?',
                    html: `Anda akan menghapus kode <strong>${kode}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="fas fa-times"></i> Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button to prevent multiple clicks
                        $(this).prop('disabled', true);

                        if (row) {
                            // Add fade-out effect
                            $(row).addClass('fade-out');

                            // Redirect to delete URL after animation
                            setTimeout(() => {
                                window.location.href = `kode_register.php?hapus=${id}`;
                            }, 500);
                        } else {
                            // If row element doesn't exist, redirect immediately
                            window.location.href = `kode_register.php?hapus=${id}`;
                        }
                        
                    }
                    
                });
            });
        });
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>