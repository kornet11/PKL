<?php
// Test koneksi database
 $host = "localhost";
 $user = "root";
 $pass = "";
 $db = "pkl";
 $koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "<h1>Koneksi Database Berhasil</h1>";

// Test query
 $result = mysqli_query($koneksi, "SELECT * FROM siswa LIMIT 5");
if ($result) {
    echo "<h2>Data Siswa:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Konsentrasi</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_siswa'] . "</td>";
        echo "<td>" . $row['nisn'] . "</td>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['kelas'] . "</td>";
        echo "<td>" . $row['konsentrasi'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Query error: " . mysqli_error($koneksi);
}

// Test cek tabel qr_codes
 $result = mysqli_query($koneksi, "DESCRIBE qr_codes");
if ($result) {
    echo "<h2>Struktur Tabel qr_codes:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Tabel qr_codes tidak ditemukan: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>