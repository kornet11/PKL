<?php
session_start();
require_once 'config_absensi.php';

// Cek login - untuk admin dan guru
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['id_gurupem']) && !isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}

// Ambil parameter filter
 $jurusan = isset($_GET['jurusan']) ? clean_input($_GET['jurusan']) : '';

// Dapatkan data lokasi siswa
 $students = get_siswa_locations($jurusan);

// Dapatkan daftar jurusan untuk filter
 $jurusan_query = "SELECT DISTINCT konsentrasi FROM siswa ORDER BY konsentrasi";
 $jurusan_result = $conn->query($jurusan_query);
 $jurusan_list = array();

if ($jurusan_result->num_rows > 0) {
    while ($row = $jurusan_result->fetch_assoc()) {
        $jurusan_list[] = $row['konsentrasi'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem PKL - Tracking GPS Siswa" />
    <meta name="author" content="" />
    <title>Tracking GPS Siswa - Sistem PKL</title>

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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <!-- Custom CSS untuk tracking GPS -->
    <link rel="stylesheet" href="assets/css/absensi.css">

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

        .btn-action {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
        }

        .badge-status {
            padding: 0.35rem 0.5rem;
            border-radius: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-belum {
            background-color: rgba(246, 194, 62, 0.2);
            color: #f6c23e;
        }

        .badge-masuk {
            background-color: rgba(54, 185, 204, 0.2);
            color: #36b9cc;
        }

        .badge-pulang {
            background-color: rgba(28, 200, 138, 0.2);
            color: #1cc88a;
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

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
            cursor: pointer;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header>div {
                margin-top: 10px;
                width: 100%;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .btn-action {
                margin: 2px;
            }

            #map {
                height: 300px !important;
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

        /* Custom map container */
        #map {
            height: 500px;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="">
            <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS Siswa
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
                        <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS Siswa
                    </h1>
                </div>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tracking GPS Siswa</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-map me-1"></i> Peta Lokasi Siswa
                        </div>
                        <div>
                            <select id="jurusan-filter" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Semua Jurusan</option>
                                <?php foreach ($jurusan_list as $j): ?>
                                    <option value="<?php echo $j; ?>" <?php echo ($jurusan == $j) ? 'selected' : ''; ?>><?php echo $j; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="map"></div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <div>
                            <i class="fas fa-table me-1"></i> Data Siswa
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-primary" id="refresh-btn">
                                <i class="fas fa-sync-alt me-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="students-table" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>NISN</th>
                                        <th>Nama</th>
                                        <th>Jurusan</th>
                                        <th>Status</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Terakhir Update</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <?php
                                        $status = 'Belum Absen';
                                        $statusClass = 'badge-belum';
                                        
                                        if ($student['jam_masuk'] && !$student['jam_keluar']) {
                                            $status = 'Sudah Absen Masuk';
                                            $statusClass = 'badge-masuk';
                                        } else if ($student['jam_masuk'] && $student['jam_keluar']) {
                                            $status = 'Sudah Absen Pulang';
                                            $statusClass = 'badge-pulang';
                                        }
                                        ?>
                                        <tr data-lat="<?php echo $student['latitude']; ?>" data-lng="<?php echo $student['longitude']; ?>" data-name="<?php echo $student['nama']; ?>">
                                            <td><?php echo $student['nisn']; ?></td>
                                            <td><?php echo $student['nama']; ?></td>
                                            <td><?php echo $student['jurusan']; ?></td>
                                            <td><span class="badge-status <?php echo $statusClass; ?>"><?php echo $status; ?></span></td>
                                            <td><?php echo $student['jam_masuk'] ? date('H:i', strtotime($student['jam_masuk'])) : '-'; ?></td>
                                            <td><?php echo $student['jam_keluar'] ? date('H:i', strtotime($student['jam_keluar'])) : '-'; ?></td>
                                            <td><?php echo date('d-m-Y H:i', strtotime($student['timestamp'])); ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info btn-action view-location" data-lat="<?php echo $student['latitude']; ?>" data-lng="<?php echo $student['longitude']; ?>" data-name="<?php echo $student['nama']; ?>" title="Lihat Lokasi">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="./assets/js/scripts.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
    $(document).ready(function() {
        // Inisialisasi DataTable
        $('#students-table').DataTable({
            responsive: true,
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
                {
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: 1
                },
                {
                    responsivePriority: 3,
                    targets: 7
                },
                {
                    orderable: false,
                    targets: [7]
                }
            ],
            order: [
                [0, 'asc']
            ],
            pageLength: 10,
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "Semua"]
            ]
        });
        
        // Inisialisasi peta
        const map = L.map('map').setView([-7.2575, 112.7521], 10); // Default ke Surabaya
        
        // Tambahkan tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Array untuk menyimpan marker
        const markers = [];
        
        // Fungsi untuk menambahkan marker ke peta
        function addMarker(lat, lng, name, status) {
            // Tentukan warna marker berdasarkan status
            let markerColor = 'orange';
            if (status === 'Sudah Absen Masuk') {
                markerColor = 'blue';
            } else if (status === 'Sudah Absen Pulang') {
                markerColor = 'green';
            }
            
            // Buat custom icon
            const icon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            
            // Tambahkan marker
            const marker = L.marker([lat, lng], { icon: icon }).addTo(map);
            marker.bindPopup(`<b>${name}</b><br>Status: ${status}<br>Lat: ${lat}<br>Lng: ${lng}`);
            
            return marker;
        }
        
        // Tambahkan marker untuk setiap siswa
        <?php foreach ($students as $student): ?>
            <?php
            $status = 'Belum Absen';
            if ($student['jam_masuk'] && !$student['jam_keluar']) {
                $status = 'Sudah Absen Masuk';
            } else if ($student['jam_masuk'] && $student['jam_keluar']) {
                $status = 'Sudah Absen Pulang';
            }
            ?>
            markers.push(addMarker(
                <?php echo $student['latitude']; ?>, 
                <?php echo $student['longitude']; ?>, 
                '<?php echo $student['nama']; ?>',
                '<?php echo $status; ?>'
            ));
        <?php endforeach; ?>
        
        // Atur view peta untuk menampilkan semua marker
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
        
        // Event listener untuk filter jurusan
        document.getElementById('jurusan-filter').addEventListener('change', function() {
            const jurusan = this.value;
            window.location.href = 'tracking_gps.php?jurusan=' + encodeURIComponent(jurusan);
        });
        
        // Event listener untuk tombol refresh
        document.getElementById('refresh-btn').addEventListener('click', function() {
            const jurusan = document.getElementById('jurusan-filter').value;
            window.location.href = 'tracking_gps.php?jurusan=' + encodeURIComponent(jurusan);
        });
        
        // Event listener untuk tombol lihat lokasi
        document.querySelectorAll('.view-location').forEach(button => {
            button.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
                
                // Pusatkan peta ke lokasi yang dipilih
                map.setView([lat, lng], 15);
                
                // Buka popup marker
                markers.forEach(marker => {
                    const markerLat = marker.getLatLng().lat;
                    const markerLng = marker.getLatLng().lng;
                    
                    if (Math.abs(markerLat - lat) < 0.0001 && Math.abs(markerLng - lng) < 0.0001) {
                        marker.openPopup();
                    }
                });
            });
        });
        
        // Event listener untuk baris tabel
        document.querySelectorAll('#students-table tbody tr').forEach(row => {
            row.addEventListener('click', function() {
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
                
                // Pusatkan peta ke lokasi yang dipilih
                map.setView([lat, lng], 15);
                
                // Buka popup marker
                markers.forEach(marker => {
                    const markerLat = marker.getLatLng().lat;
                    const markerLng = marker.getLatLng().lng;
                    
                    if (Math.abs(markerLat - lat) < 0.0001 && Math.abs(markerLng - lng) < 0.0001) {
                        marker.openPopup();
                    }
                });
            });
        });
        
        // Auto refresh setiap 30 detik
        setInterval(function() {
            const jurusan = document.getElementById('jurusan-filter').value;
            window.location.href = 'tracking_gps.php?jurusan=' + encodeURIComponent(jurusan);
        }, 30000);
    });
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>