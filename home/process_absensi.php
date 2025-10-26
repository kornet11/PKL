<?php
// Perbaiki path untuk memanggil config_absensi.php
require_once 'config_absensi.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek action
 $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($action) {
    case 'validate':
        validate_qr_code();
        break;
    case 'process':
        process_attendance();
        break;
    default:
        echo json_encode(array('status' => 'error', 'message' => 'Action tidak valid'));
        break;
}

// Fungsi untuk validasi QR Code
function validate_qr_code() {
    $qr_code = isset($_GET['qr_code']) ? clean_input($_GET['qr_code']) : '';
    
    if (empty($qr_code)) {
        echo json_encode(array('status' => 'error', 'message' => 'QR Code tidak boleh kosong'));
        return;
    }
    
    // Dapatkan data siswa berdasarkan QR code
    $student = get_siswa_by_qr($qr_code);
    
    if ($student) {
        // Cek apakah siswa sudah absen hari ini
        $today = date('Y-m-d');
        $query = "SELECT * FROM absensi WHERE id_siswa = " . $student['id_siswa'] . " AND tanggal_absensi = '$today'";
        $result = $GLOBALS['conn']->query($query);
        
        if ($result->num_rows > 0) {
            $attendance = $result->fetch_assoc();
            $student['jam_masuk'] = $attendance['jam_masuk'];
            $student['jam_keluar'] = $attendance['jam_keluar'];
            $student['lokasi_masuk'] = $attendance['lokasi_masuk'];
            $student['status_absensi'] = $attendance['status_absensi'];
        } else {
            $student['jam_masuk'] = null;
            $student['jam_keluar'] = null;
            $student['lokasi_masuk'] = null;
            $student['status_absensi'] = 'Hadir';
        }
        
        echo json_encode(array('status' => 'success', 'data' => $student));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'QR Code tidak valid atau sudah kadaluarsa'));
    }
}

// Fungsi untuk memproses absensi
function process_attendance() {
    $id_siswa = isset($_POST['id_siswa']) ? (int)$_POST['id_siswa'] : 0;
    $id_qr = isset($_POST['id_qr']) ? (int)$_POST['id_qr'] : 0;
    $latitude = isset($_POST['latitude']) ? (float)$_POST['latitude'] : 0;
    $longitude = isset($_POST['longitude']) ? (float)$_POST['longitude'] : 0;
    $lokasi = isset($_POST['lokasi']) ? clean_input($_POST['lokasi']) : '';
    $type = isset($_POST['type']) ? clean_input($_POST['type']) : 'masuk';
    
    if ($id_siswa <= 0 || $id_qr <= 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Data siswa tidak valid'));
        return;
    }
    
    // Simpan data absensi
    $result = save_absensi($id_siswa, $id_qr, $latitude, $longitude, $lokasi, $type);
    
    if ($result['status'] === 'success') {
        // Dapatkan ID absensi terakhir
        $today = date('Y-m-d');
        $query = "SELECT id_absensi FROM absensi WHERE id_siswa = $id_siswa AND tanggal_absensi = '$today' ORDER BY id_absensi DESC LIMIT 1";
        $result_query = $GLOBALS['conn']->query($query);
        
        if ($result_query->num_rows > 0) {
            $row = $result_query->fetch_assoc();
            $id_absensi = $row['id_absensi'];
            
            // Simpan lokasi siswa
            save_location($id_siswa, $id_absensi, $latitude, $longitude);
        }
        
        echo json_encode($result);
    } else {
        echo json_encode($result);
    }
}
?>