<?php
$folder = "uploads/";
$file = basename($_GET['file']); // Amankan dari path traversal
$filePath = $folder . $file;

if (file_exists($filePath)) {
    header("Content-Description: File Transfer");
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment; filename=\"$file\"");
    header("Content-Length: " . filesize($filePath));
    readfile($filePath);
    exit;
} else {
    echo "File tidak ditemukan.";
}
