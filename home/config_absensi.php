<?php
// Konfigurasi koneksi database
 $host = "localhost";
 $username = "root";
 $password = "";
 $dbname = "pkl";

// Buat koneksi
 $conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set zona waktu
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk membersihkan input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Fungsi untuk generate QR Code
function generate_qr_code($id_siswa) {
    global $conn;
    
    // Cek apakah siswa sudah memiliki QR code aktif hari ini
    $today = date('Y-m-d');
    $check_query = "SELECT * FROM qr_codes WHERE id_siswa = $id_siswa AND tanggal_generate = '$today' AND status = 'Aktif'";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['qr_code'];
    }
    
    // Generate unique QR code
    $qr_code = 'ABS-' . $id_siswa . '-' . date('YmdHis') . '-' . rand(1000, 9999);
    
    // Non-aktifkan QR code sebelumnya jika ada
    $update_query = "UPDATE qr_codes SET status = 'Nonaktif' WHERE id_siswa = $id_siswa";
    $conn->query($update_query);
    
    // Simpan QR code baru
    $insert_query = "INSERT INTO qr_codes (id_siswa, qr_code, tanggal_generate, status) 
                     VALUES ($id_siswa, '$qr_code', '$today', 'Aktif')";
    
    if ($conn->query($insert_query) === TRUE) {
        return $qr_code;
    } else {
        return false;
    }
}

// Fungsi untuk mendapatkan data siswa berdasarkan QR code
function get_siswa_by_qr($qr_code) {
    global $conn;
    
    $query = "SELECT s.*, q.id_qr 
              FROM siswa s 
              JOIN qr_codes q ON s.id_siswa = q.id_siswa 
              WHERE q.qr_code = '$qr_code' AND q.status = 'Aktif' AND q.tanggal_generate = CURDATE()";
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Fungsi untuk menyimpan data absensi
function save_absensi($id_siswa, $id_qr, $latitude, $longitude, $lokasi, $type = 'masuk') {
    global $conn;
    
    $today = date('Y-m-d');
    $now = date('H:i:s');
    
    // Cek apakah siswa sudah absen masuk hari ini
    $check_query = "SELECT * FROM absensi WHERE id_siswa = $id_siswa AND tanggal_absensi = '$today'";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        // Update absensi keluar
        $update_query = "UPDATE absensi SET 
                         jam_keluar = '$now', 
                         lokasi_keluar = '$lokasi', 
                         latitude_keluar = $latitude, 
                         longitude_keluar = $longitude 
                         WHERE id_siswa = $id_siswa AND tanggal_absensi = '$today'";
        
        if ($conn->query($update_query) === TRUE) {
            return array('status' => 'success', 'message' => 'Absensi keluar berhasil!');
        } else {
            return array('status' => 'error', 'message' => 'Gagal menyimpan absensi keluar: ' . $conn->error);
        }
    } else {
        // Insert absensi masuk
        $insert_query = "INSERT INTO absensi (id_siswa, id_qr, tanggal_absensi, jam_masuk, lokasi_masuk, latitude_masuk, longitude_masuk) 
                         VALUES ($id_siswa, $id_qr, '$today', '$now', '$lokasi', $latitude, $longitude)";
        
        if ($conn->query($insert_query) === TRUE) {
            return array('status' => 'success', 'message' => 'Absensi masuk berhasil!');
        } else {
            return array('status' => 'error', 'message' => 'Gagal menyimpan absensi masuk: ' . $conn->error);
        }
    }
}

// Fungsi untuk menyimpan lokasi siswa
function save_location($id_siswa, $id_absensi, $latitude, $longitude) {
    global $conn;
    
    $query = "INSERT INTO lokasi_siswa (id_siswa, id_absensi, latitude, longitude) 
              VALUES ($id_siswa, $id_absensi, $latitude, $longitude)";
    
    if ($conn->query($query) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Fungsi untuk mendapatkan rekap absensi harian
function get_recap_harian($tanggal = null, $jurusan = null) {
    global $conn;
    
    if ($tanggal == null) {
        $tanggal = date('Y-m-d');
    }
    
    $query = "SELECT s.konsentrasi as jurusan, 
                     COUNT(a.id_absensi) as total_siswa,
                     SUM(CASE WHEN a.status_absensi = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                     SUM(CASE WHEN a.status_absensi = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                     SUM(CASE WHEN a.status_absensi = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                     SUM(CASE WHEN a.status_absensi = 'Alpha' THEN 1 ELSE 0 END) as total_alpha
              FROM siswa s
              LEFT JOIN absensi a ON s.id_siswa = a.id_siswa AND a.tanggal_absensi = '$tanggal'
              WHERE 1=1";
    
    if ($jurusan != null) {
        $query .= " AND s.konsentrasi = '$jurusan'";
    }
    
    $query .= " GROUP BY s.konsentrasi";
    
    $result = $conn->query($query);
    
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Fungsi untuk mendapatkan rekap absensi bulanan
function get_recap_bulanan($bulan = null, $tahun = null, $jurusan = null) {
    global $conn;
    
    if ($bulan == null) {
        $bulan = date('m');
    }
    
    if ($tahun == null) {
        $tahun = date('Y');
    }
    
    $query = "SELECT s.konsentrasi as jurusan,
                     COUNT(DISTINCT s.id_siswa) as total_siswa,
                     SUM(CASE WHEN a.status_absensi = 'Hadir' THEN 1 ELSE 0 END) as total_hadir,
                     SUM(CASE WHEN a.status_absensi = 'Izin' THEN 1 ELSE 0 END) as total_izin,
                     SUM(CASE WHEN a.status_absensi = 'Sakit' THEN 1 ELSE 0 END) as total_sakit,
                     SUM(CASE WHEN a.status_absensi = 'Alpha' THEN 1 ELSE 0 END) as total_alpha
              FROM siswa s
              LEFT JOIN absensi a ON s.id_siswa = a.id_siswa 
              WHERE MONTH(a.tanggal_absensi) = $bulan AND YEAR(a.tanggal_absensi) = $tahun";
    
    if ($jurusan != null) {
        $query .= " AND s.konsentrasi = '$jurusan'";
    }
    
    $query .= " GROUP BY s.konsentrasi";
    
    $result = $conn->query($query);
    
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Fungsi untuk mendapatkan lokasi siswa real-time
function get_siswa_locations($jurusan = null) {
    global $conn;
    
    $query = "SELECT s.id_siswa, s.nama, s.konsentrasi as jurusan, 
                     l.latitude, l.longitude, l.timestamp,
                     a.tanggal_absensi, a.jam_masuk, a.jam_keluar, a.status_absensi
              FROM siswa s
              JOIN lokasi_siswa l ON s.id_siswa = l.id_siswa
              LEFT JOIN absensi a ON s.id_siswa = a.id_siswa AND a.tanggal_absensi = CURDATE()
              WHERE DATE(l.timestamp) = CURDATE()";
    
    if ($jurusan != null) {
        $query .= " AND s.konsentrasi = '$jurusan'";
    }
    
    $query .= " ORDER BY l.timestamp DESC";
    
    $result = $conn->query($query);
    
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Fungsi untuk export ke Excel
function export_to_excel($data, $filename) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    $output = fopen("php://output", "w");
    
    // Header
    fputcsv($output, array_keys($data[0]));
    
    // Data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}
?>