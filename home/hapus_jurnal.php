<?php
// ====== KONEKSI DATABASE ======
$host = "localhost"; // ganti jika perlu
$user = "root"; // username MySQL
$pass = ""; // password MySQL
$db   = "pkl"; // nama database

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ====== FUNGSI HAPUS JURNAL ======
function hapusfromjurnal($id) {
    global $conn;

    // Cek apakah ada file bukti, jika ada hapus fisiknya
    $q = mysqli_query($conn, "SELECT bukti_file FROM jurnal_pkl WHERE id = $id");
    $d = mysqli_fetch_assoc($q);
    if ($d && !empty($d['bukti_file']) && file_exists("uploads/" . $d['bukti_file'])) {
        unlink("uploads/" . $d['bukti_file']);
    }

    // Hapus data di database
    mysqli_query($conn, "DELETE FROM jurnal_pkl WHERE id = $id");

    return mysqli_affected_rows($conn);
}

// ====== PROSES HAPUS ======
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if (hapusfromjurnal($id) > 0) {
        echo "
            <script>
                alert('Berhasil menghapus data jurnal.');
                document.location.href = 'kegiatan.php';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('Gagal menghapus data jurnal!');
                document.location.href = 'kegiatan.php';
            </script>
        ";
    }
} else {
    echo "
        <script>
            alert('ID jurnal tidak ditemukan.');
            document.location.href = 'kegiatan.php';
        </script>
    ";
}
?>
