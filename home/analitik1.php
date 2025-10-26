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


// Hitung jumlah guru pembimbing => angka
$dataG = mysqli_query($conn, "SELECT COUNT(*) total FROM gurupembimbing");
$dg = mysqli_fetch_assoc($dataG);

// Hitung jumlah anggota
$dataS = mysqli_query($conn, "SELECT COUNT(*) total FROM siswa");
$ds = mysqli_fetch_assoc($dataS);

// Hitung jumlah user
$dataU = mysqli_query($conn, "SELECT COUNT(*) total FROM admin");
$du = mysqli_fetch_assoc($dataU);

// Hitung jumlah kakomli
$dataU = mysqli_query($conn, "SELECT COUNT(*) total FROM gurukaprok");
$dk = mysqli_fetch_assoc($dataU);

$data123 = [
    ['jumlah' => $dg['total'], 'anggota' => 'guru pembimbing'],
    ['jumlah' => $ds['total'], 'anggota' => 'siswa'],
    ['jumlah' => $du['total'], 'anggota' => 'admin'],
    ['jumlah' => $dk['total'], 'anggota' => 'dudi'],
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>ADMIN_PKL</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index.php">Analitik</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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
                    <h1 class="mt-4">Analitik</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">analitik</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa-solid fa-chart-simple"></i>
                            Data analitik
                        </div>
                        <div style="width: 100%;"><canvas id="acquisitions"></canvas></div>
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


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/datatables-simple-demo.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js" integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script>
        (async function() {
            //   const data = [
            //     { year: 20909, count: 10 },
            //     { year: 2011, count: 20 },
            //     { year: 2012, count: 15 },
            //     { year: 2013, count: 25 },
            //     { year: 2014, count: 22 },
            //     { year: 2015, count: 30 },
            //     { year: 2016, count: 28 },
            //   ];

            // console.log(data)
            const data = <?php echo json_encode($data123); ?>;

            new Chart(
                document.getElementById('acquisitions'), {
                    type: 'bar',
                    data: {
                        labels: data.map(row => row.anggota),
                        datasets: [{
                            label: 'Acquisitions by year',
                            data: data.map(row => row.jumlah)
                        }]
                    }
                }
            );
        })();
    </script>
    <script src="./assets/template/logout-alert.php"></script>
</body>

</html>