<?php
require_once 'config_absensi.php';

// Cek apakah user adalah guru atau admin
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil data user
 $username = $_SESSION['username'];
 $userQuery = "SELECT * FROM admin WHERE username = '$username'";
 $userResult = mysqli_query($koneksi, $userQuery);

if (mysqli_num_rows($userResult) > 0) {
    $user = mysqli_fetch_assoc($userResult);
    $role = $user['hak_akses'];
} else {
    // Cek di tabel guru pembimbing
    $guruQuery = "SELECT * FROM gurupembimbing WHERE nip = '$username'";
    $guruResult = mysqli_query($koneksi, $guruQuery);
    
    if (mysqli_num_rows($guruResult) > 0) {
        $guru = mysqli_fetch_assoc($guruResult);
        $role = 'guru';
        $jurusan = $guru['jurusan'];
    } else {
        // Cek di tabel guru kaprok
        $kaprokQuery = "SELECT * FROM gurukaprok WHERE nip = '$username'";
        $kaprokResult = mysqli_query($koneksi, $kaprokQuery);
        
        if (mysqli_num_rows($kaprokResult) > 0) {
            $kaprok = mysqli_fetch_assoc($kaprokResult);
            $role = 'guru';
            $jurusan = $kaprok['jurusan'];
        } else {
            header("Location: ../login.php");
            exit();
        }
    }
}

// Proses upload QR Code jika ada file yang diupload
if (isset($_POST['submit']) && isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] == 0) {
    $targetDir = "assets/uploads/";
    $fileName = basename($_FILES["qr_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg','png','jpeg','gif');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES["qr_image"]["tmp_name"], $targetFilePath)) {
            // Placeholder untuk proses membaca QR Code dari gambar
            // Untuk contoh ini, kita asumsikan QR Code berhasil dibaca
            $qrCode = "QR-" . rand(1000, 9999) . "-" . date('YmdHis');
            
            // Cek QR Code di database
            $qrQuery = "SELECT qr.*, s.nama, s.kelas, s.konsentrasi 
                       FROM qr_codes qr 
                       JOIN siswa s ON qr.id_siswa = s.id_siswa 
                       WHERE qr.qr_code = '$qrCode' AND qr.status = 'Aktif'";
            $qrResult = mysqli_query($koneksi, $qrQuery);
            
            if (mysqli_num_rows($qrResult) > 0) {
                $qrData = mysqli_fetch_assoc($qrResult);
                $id_siswa = $qrData['id_siswa'];
                $id_qr = $qrData['id_qr'];
                $nama_siswa = $qrData['nama'];
                $kelas = $qrData['kelas'];
                $jurusan_siswa = $qrData['konsentrasi'];
                
                // Cek apakah guru/admin berhak melihat siswa ini
                if ($role != 'admin' && $jurusan != $jurusan_siswa) {
                    $message = "Anda tidak berhak melakukan absensi untuk siswa ini.";
                    $alertClass = "alert-danger";
                } else {
                    // Cek apakah siswa sudah absen hari ini
                    $today = date('Y-m-d');
                    $checkAbsensi = "SELECT * FROM absensi WHERE id_siswa = $id_siswa AND tanggal_absensi = '$today'";
                    $absensiResult = mysqli_query($koneksi, $checkAbsensi);
                    
                    if (mysqli_num_rows($absensiResult) > 0) {
                        $absensiData = mysqli_fetch_assoc($absensiResult);
                        
                        // Jika sudah absen masuk tapi belum keluar
                        if ($absensiData['jam_keluar'] == NULL) {
                            // Update absensi keluar
                            $jam_keluar = date('H:i:s');
                            
                            // Ambil lokasi dari form
                            $latitude_keluar = isset($_POST['latitude']) ? $_POST['latitude'] : NULL;
                            $longitude_keluar = isset($_POST['longitude']) ? $_POST['longitude'] : NULL;
                            $lokasi_keluar = isset($_POST['lokasi']) ? $_POST['lokasi'] : NULL;
                            
                            $updateAbsensi = "UPDATE absensi SET 
                                             jam_keluar = '$jam_keluar',
                                             lokasi_keluar = '$lokasi_keluar',
                                             latitude_keluar = $latitude_keluar,
                                             longitude_keluar = $longitude_keluar
                                             WHERE id_absensi = " . $absensiData['id_absensi'];
                            mysqli_query($koneksi, $updateAbsensi);
                            
                            // Simpan lokasi ke tabel lokasi_siswa
                            if ($latitude_keluar && $longitude_keluar) {
                                $insertLokasi = "INSERT INTO lokasi_siswa (id_siswa, id_absensi, latitude, longitude) 
                                                VALUES ($id_siswa, " . $absensiData['id_absensi'] . ", $latitude_keluar, $longitude_keluar)";
                                mysqli_query($koneksi, $insertLokasi);
                            }
                            
                            $message = "Absensi keluar berhasil untuk $nama_siswa.";
                            $alertClass = "alert-success";
                        } else {
                            $message = "$nama_siswa sudah melakukan absensi masuk dan keluar hari ini.";
                            $alertClass = "alert-warning";
                        }
                    } else {
                        // Jika belum absen sama sekali, lakukan absensi masuk
                        $jam_masuk = date('H:i:s');
                        
                        // Ambil lokasi dari form
                        $latitude_masuk = isset($_POST['latitude']) ? $_POST['latitude'] : NULL;
                        $longitude_masuk = isset($_POST['longitude']) ? $_POST['longitude'] : NULL;
                        $lokasi_masuk = isset($_POST['lokasi']) ? $_POST['lokasi'] : NULL;
                        
                        $insertAbsensi = "INSERT INTO absensi (id_siswa, id_qr, tanggal_absensi, jam_masuk, lokasi_masuk, latitude_masuk, longitude_masuk) 
                                         VALUES ($id_siswa, $id_qr, '$today', '$jam_masuk', '$lokasi_masuk', $latitude_masuk, $longitude_masuk)";
                        mysqli_query($koneksi, $insertAbsensi);
                        
                        // Ambil ID absensi yang baru dibuat
                        $id_absensi = mysqli_insert_id($koneksi);
                        
                        // Simpan lokasi ke tabel lokasi_siswa
                        if ($latitude_masuk && $longitude_masuk) {
                            $insertLokasi = "INSERT INTO lokasi_siswa (id_siswa, id_absensi, latitude, longitude) 
                                            VALUES ($id_siswa, $id_absensi, $latitude_masuk, $longitude_masuk)";
                            mysqli_query($koneksi, $insertLokasi);
                        }
                        
                        $message = "Absensi masuk berhasil untuk $nama_siswa.";
                        $alertClass = "alert-success";
                    }
                }
            } else {
                $message = "QR Code tidak valid atau sudah tidak aktif.";
                $alertClass = "alert-danger";
            }
        } else {
            $message = "Maaf, terjadi kesalahan saat mengupload file.";
            $alertClass = "alert-danger";
        }
    } else {
        $message = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $alertClass = "alert-danger";
    }
}

// Tampilkan halaman upload QR Code
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload QR Code - Absensi Siswa</title>
    <!-- Custom CSS for absensi -->
<link rel="stylesheet" href="assets/css/absensi.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .qr-upload {
            text-align: center;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .preview-container {
            margin-top: 15px;
            text-align: center;
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Upload QR Code Absensi</h2>
        
        <?php if (isset($message)): ?>
        <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="qr_image">Upload QR Code:</label>
                        <input type="file" class="form-control-file" id="qr_image" name="qr_image" accept="image/*" required>
                        <div class="preview-container">
                            <img id="preview" class="preview-image" style="display: none;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="lokasi">Lokasi:</label>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" placeholder="Masukkan lokasi (opsional)">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="latitude">Latitude:</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude GPS">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="longitude">Longitude:</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude GPS">
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary">Proses Absensi</button>
                        <a href="<?php echo ($role == 'admin') ? '../index.php' : '../dashboard_guru.php'; ?>" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </div>
                </form>
                
                <hr>
                
                <div class="qr-upload">
                    <h5>Cara Upload QR Code</h5>
                    <ol class="text-left">
                        <li>Pilih gambar QR Code yang ingin diupload</li>
                        <li>Sistem akan otomatis membaca QR Code dari gambar</li>
                        <li>Isi lokasi dan koordinat GPS jika diperlukan</li>
                        <li>Klik "Proses Absensi" untuk menyelesaikan proses absensi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Preview image before upload
            const qrImage = document.getElementById('qr_image');
            const preview = document.getElementById('preview');
            
            qrImage.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.addEventListener('load', function() {
                        preview.setAttribute('src', this.result);
                        preview.style.display = 'block';
                    });
                    
                    reader.readAsDataURL(file);
                } else {
                    preview.setAttribute('src', '');
                    preview.style.display = 'none';
                }
            });
            
            // Get GPS location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                });
            }
        });
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>