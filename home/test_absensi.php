<?php
session_start();
require_once 'functions.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

include 'header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Test Akses Folder Absensi</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="absensi/generate_qr.php" class="list-group-item list-group-item-action">Generate QR Code</a>
                        <a href="absensi/scan_qr.php" class="list-group-item list-group-item-action">Scan QR Code</a>
                        <a href="absensi/tracking_gps.php" class="list-group-item list-group-item-action">Tracking GPS</a>
                        <a href="absensi/recap_harian.php" class="list-group-item list-group-item-action">Rekap Harian</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>