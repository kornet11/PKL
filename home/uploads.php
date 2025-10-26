<?php
session_start();
if (!isset($_SESSION['login'])) {
    echo "<script>document.location.href = '../index.php';</script>";
    exit;
}

require 'functions.php';

$message = '';
if (isset($_POST['upload'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>Berhasil!</strong> File <b>$fileName</b> berhasil diupload.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    } else {
        $message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Gagal!</strong> File tidak dapat diupload.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>ADMIN_PKL - Upload Word</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        /* Styling untuk tampilan responsif */
        .table-responsive {
            border-radius: 0.35rem;
            overflow: hidden;
        }

        /* Untuk child row pada responsivitas */
        .dtr-details {
            width: 100%;
        }

        .dtr-details li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .dtr-title {
            font-weight: bold;
            min-width: 120px;
        }

        .dtr-data {
            flex-grow: 1;
            text-align: right;
        }

        /* Tombol expand/collapse */
        .dtr-control::before {
            margin-right: 8px;
        }

        /* Memastikan tabel tidak melebihi container */
        table.dataTable {
            width: 100% !important;
            margin: 0 !important;
        }

        /* Memperbaiki tampilan pada perangkat mobile */
        @media (max-width: 767.98px) {

            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: center;
                float: none !important;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 15px;
            }

            .table td,
            .table th {
                padding: 0.5rem;
            }
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">Upload</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search..." />
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle active" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
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
            <main class="container-fluid px-4 mt-4">
                <h1 class="mb-4">Upload File Word</h1>

                <?= $message ?>

                <div class="card shadow-sm">
                    <div class="card-header">
                         <?php if ($_SESSION['hak_akses'] !== 'siswa'): ?>
                        <i class="fas fa-file-word me-1 text-primary"></i>
                        Form Upload File Word
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-8">
                                <input type="file" name="file" class="form-control" accept=".doc,.docx" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" name="upload" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                            </div>
                             <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Daftar file yang sudah diupload -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <i class="fas fa-download me-1 text-success"></i>
                        File Word yang Tersedia
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatableUploads" class="table table-bordered table-hover align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-nowrap">No</th>
                                        <th class="text-nowrap">Nama File</th>
                                        <th class="text-nowrap">Ukuran</th>
                                        <th class="text-nowrap">Tanggal Upload</th>
                                        <th class="text-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $folder = "uploads/";
                                    $files = glob($folder . "*.{doc,docx}", GLOB_BRACE);
                                    if ($files) {
                                        $no = 1;
                                        foreach ($files as $file) {
                                            $nama = basename($file);
                                            $ukuran = round(filesize($file) / 1024, 2) . ' KB';
                                            $tanggal = date("d-m-Y H:i", filemtime($file));
                                            echo "<tr>
                                                    <td class='text-nowrap' >$no</td>
                                                    <td class='text-nowrap' >$nama</td>
                                                    <td class='text-nowrap' >$ukuran</td>
                                                    <td class='text-nowrap' >$tanggal</td>
                                                    <td class='text-nowrap' >
                                                        <a href='download_file.php?file=$nama' class='btn btn-success btn-sm'>
                                                            <i class='fas fa-download'></i> Download
                                                        </a>
                                                    </td>
                                                  </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada file diupload.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatableUploads').DataTable({
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
                        targets: 3
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
        });
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>