<?php
// Syarat menggunakan session
session_start();

// Cek jika tidak ada session login
if (!isset($_SESSION['login'])) {
    echo "
        <script>
            document.location.href = '../index.php';
        </script>
    ";
}

// Penghubung antar file di PHP
require 'functions.php';

// Hanya admin yang boleh masuk
cekAkses(['admin']);

// Buat variabel untuk menampung hasil query
$admins = tampilAdmin("SELECT * FROM admin ORDER BY id_admin DESC");

// Cek apakah tombol tambah di klik
if (isset($_POST["tambah"])) {
    // Jika di klik, jalankan function tambah data dan cek apakah berhasil atau tidak
    if (tambahAdmin($_POST) > 0) {
        // Jika berhasil
        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Berhasil Menambahkan Data Admin Baru',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'admin.php';
            });
        </script>
    ";
    } else {
        // Jika gagal
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal Menambahkan Data Admin Baru',
                confirmButtonText: 'OK'
            });
        </script>
    ";
    }
}

// Cek apakah tombol edit di klik
if (isset($_POST["edit"])) {
    // Jika di klik, jalankan function edit data dan cek apakah berhasil atau tidak
    if (editAdmin($_POST) > 0) {
        // Jika berhasil
        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data Admin berhasil diubah!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'admin.php';
            });
        </script>
    ";
    } else {
        // Jika gagal
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Data Admin gagal diubah!',
                confirmButtonText: 'OK'
            });
        </script>
    ";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem PKL - Manajemen Admin" />
    <meta name="author" content="" />
    <title>Manajemen Admin - Sistem PKL</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
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

        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e3e6f0;
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

        .badge-role {
            padding: 0.35rem 0.5rem;
            border-radius: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-admin {
            background-color: rgba(78, 115, 223, 0.2);
            color: #4e73df;
        }

        .badge-user {
            background-color: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .modal-header {
            background: linear-gradient(90deg, var(--primary) 0%, #224abe 100%);
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header>div {
                margin-top: 10px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin: 2px;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 5px !important;
            margin: 0 3px;
            padding: 5px 10px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ced4da;
        }

        /* Improve table wrapping & responsive */
        /* Allow table cells to wrap long content on small screens */
        table.dataTable td,
        table.dataTable th {
            white-space: normal !important;
            word-break: break-word;
        }

        /* Ensure responsive container allows horizontal scroll if needed on very small screens */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Shrink profile images and action buttons on small screens */
        @media (max-width: 576px) {
            .profile-img {
                width: 36px;
                height: 36px;
            }

            .btn-action {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 0.45rem 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">
            <i class="fas fa-user-shield me-2"></i>Manajemen Admin
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- User Menu -->
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
        <div id="layoutSidenav_nav">
            <?php include 'menu.php'; ?>
        </div>

        <div id="layoutSidenav_content">
            <main class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-shield me-2"></i>Manajemen Admin
                    </h1>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Manajemen Admin</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-table me-1"></i> Data Admin
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                <i class="fas fa-plus me-1"></i>Tambah Data
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTableAdmin" class="table table-bordered table-hover" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Username</th>
                                        <th>Nama Lengkap</th>
                                        <th>Alamat</th>
                                        <th>No Telp</th>
                                        <th>Hak Akses</th>
                                        <th width="80">Foto</th>
                                        <th width="100">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($admins)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data admin
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $no = 1;
                                        foreach ($admins as $admin) : ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= $admin['username']; ?></td>
                                                <td><?= $admin['nama_lengkap']; ?></td>
                                                <td><?= $admin['alamat']; ?></td>
                                                <td><?= $admin['no_telp']; ?></td>
                                                <td>
                                                    <?php
                                                    $role_class = $admin['hak_akses'] == 'admin' ? 'badge-admin' : 'badge-user';
                                                    ?>
                                                    <span class="badge-role <?= $role_class ?>">
                                                        <?= ucfirst($admin['hak_akses']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <img src="assets/img/admin/<?= $admin['foto']; ?>" alt="Foto Admin" class="profile-img">
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center flex-wrap gap-1">
                                                        <a href="#" class="btn btn-sm btn-success btn-action" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $admin['id_admin']; ?>" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus_admin.php?id=<?= $admin['id_admin']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirmDelete()" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahLabel">Tambah Data Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" placeholder="Username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_telp" placeholder="No Telepon" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="passwordTambah" placeholder="Password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordTambah')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hak Akses <span class="text-danger">*</span></label>
                                <select class="form-select" name="hak_akses" required>
                                    <option value="" selected disabled>Pilih Hak Akses</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto <span class="text-danger">*</span></label>
                                <input class="form-control" type="file" name="foto" accept="image/*" required>
                                <div class="form-text">Format: JPG, PNG, JPEG. Maksimal 2MB.</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alamat" rows="3" placeholder="Alamat" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Admin -->
    <?php
    $dataEdit = mysqli_query($conn, "SELECT * FROM admin");
    while ($rowA = mysqli_fetch_assoc($dataEdit)) :
    ?>
        <div class="modal fade" id="modalEdit<?= $rowA['id_admin']; ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $rowA['id_admin']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_admin" value="<?= $rowA['id_admin']; ?>">
                        <input type="hidden" name="fotoLama" value="<?= $rowA['foto']; ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditLabel<?= $rowA['id_admin']; ?>">Edit Data Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <img src="assets/img/admin/<?= $rowA['foto']; ?>" alt="Foto Admin" width="100" class="rounded-circle mb-2" style="border: 3px solid #e3e6f0;">
                                <p class="text-muted">Foto saat ini</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="username" value="<?= $rowA['username']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No Telepon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="no_telp" value="<?= $rowA['no_telp']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="passwordEdit<?= $rowA['id_admin']; ?>" placeholder="Kosongkan jika tidak ingin mengubah password">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('passwordEdit<?= $rowA['id_admin']; ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hak Akses <span class="text-danger">*</span></label>
                                    <select class="form-select" name="hak_akses" required>
                                        <option value="admin" <?= $rowA['hak_akses'] == "admin" ? "selected" : "" ?>>Admin</option>
                                        <option value="user" <?= $rowA['hak_akses'] == "user" ? "selected" : "" ?>>User</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_lengkap" value="<?= $rowA['nama_lengkap']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ganti Foto</label>
                                    <input class="form-control" type="file" name="foto" accept="image/*">
                                    <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat" rows="3" required><?= $rowA['alamat']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="./assets/js/scripts.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#dataTableAdmin').length) {
                $('#dataTableAdmin').DataTable({
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Detail: ' + (data[2] || '');
                                }
                            }),
                            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                tableClass: 'table'
                            })
                        }
                    },
                    autoWidth: false,
                    scrollX: true,
                    language: {
                        processing: "Memproses...",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                        search: "Cari:",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 },   // No
                        { responsivePriority: 2, targets: 1 },   // Username
                        { responsivePriority: 3, targets: 2 },   // Nama Lengkap
                        { responsivePriority: 4, targets: 5 },   // Hak Akses
                        { responsivePriority: 5, targets: 6 },   // Foto
                        { orderable: false, targets: -1, responsivePriority: 1000 } // Aksi (silang ke detail dulu)
                    ],
                    pageLength: 10,
                    lengthMenu: [[5,10,25,50,-1],[5,10,25,50,"Semua"]]
                });
            }
        });

        function confirmDelete() {
            return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini?');
        }

        function togglePassword(id) {
            const passwordInput = document.getElementById(id);
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = passwordInput.nextElementSibling.querySelector('i');
            icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        }

        // Inisialisasi tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>