<?php
// Perbaiki path untuk memanggil config_absensi.php
require_once 'config_absensi.php';

// Set header untuk Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_absensi_" . date('YmdHis') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil parameter
 $type = isset($_GET['type']) ? clean_input($_GET['type']) : 'harian';
 $jurusan = isset($_GET['jurusan']) ? clean_input($_GET['jurusan']) : '';

// Dapatkan data rekap
if ($type === 'harian') {
    $tanggal = isset($_GET['tanggal']) ? clean_input($_GET['tanggal']) : date('Y-m-d');
    $data = get_recap_harian($tanggal, $jurusan);
    $title = 'Rekap Absensi Harian - ' . date('d-m-Y', strtotime($tanggal));
} else {
    $bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
    $tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
    $data = get_recap_bulanan($bulan, $tahun, $jurusan);
    $title = 'Rekap Absensi Bulanan - ' . date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun));
}

// Buat output Excel
echo "<html>";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<title>$title</title>";
echo "</head>";
echo "<body>";

// Tampilkan judul
echo "<h2>$title</h2>";
echo "<p>Dibuat pada: " . date('d-m-Y H:i:s') . "</p>";

// Tampilkan filter
if ($jurusan) {
    echo "<p>Filter Jurusan: $jurusan</p>";
}

// Tampilkan tabel
echo "<table border=\"1\">";
echo "<tr>";
echo "<th>No</th>";
echo "<th>Jurusan</th>";
echo "<th>Total Siswa</th>";
echo "<th>Hadir</th>";
echo "<th>Izin</th>";
echo "<th>Sakit</th>";
echo "<th>Alpha</th>";
echo "<th>Persentase Kehadiran</th>";
echo "</tr>";

 $no = 1;
foreach ($data as $row) {
    $total = $row['total_siswa'];
    $hadir = $row['total_hadir'];
    $izin = $row['total_izin'];
    $sakit = $row['total_sakit'];
    $alpha = $row['total_alpha'];
    
    $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;
    
    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>" . $row['jurusan'] . "</td>";
    echo "<td>$total</td>";
    echo "<td>$hadir</td>";
    echo "<td>$izin</td>";
    echo "<td>$sakit</td>";
    echo "<td>$alpha</td>";
    echo "<td>$persentase%</td>";
    echo "</tr>";
    
    $no++;
}

echo "</table>";

echo "</body>";
echo "</html>";
?>