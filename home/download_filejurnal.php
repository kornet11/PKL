<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['file'])) {
    $fileName = $_GET['file'];
    $filePath = './assets/jurnal/' . $fileName;
    
    // Cek apakah file ada
    if (file_exists($filePath)) {
        // Dapatkan tipe file
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);
        
        // Set header
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Output file
        readfile($filePath);
        exit;
    } else {
        // File tidak ditemukan
        header('HTTP/1.0 404 Not Found');
        echo 'File not found';
        exit;
    }
} else {
    // Tidak ada parameter file
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
    exit;
}
?>