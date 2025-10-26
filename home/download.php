<?php
// Folder tempat file disimpan
$folder = "uploads/";

// Ambil daftar file Word
$files = glob($folder . "*.{doc,docx}", GLOB_BRACE);

echo "<h2>Daftar File Word</h2><ul>";
foreach ($files as $file) {
    $nama = basename($file);
    echo "<li><a href='download_file.php?file=$nama'>$nama</a></li>";
}
echo "</ul>";
