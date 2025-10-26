<?php
echo "<h1>Path Testing</h1>";

// Test current directory
echo "<h2>Current Directory</h2>";
echo "Current working directory: " . getcwd() . "<br>";

// Test possible paths to phpqrcode library
echo "<h2>Testing Paths to phpqrcode library</h2>";
 $possiblePaths = [
    '../../../phpqrcode/qrlib.php',
    '../../phpqrcode/qrlib.php',
    '../phpqrcode/qrlib.php',
    'phpqrcode/qrlib.php'
];

foreach ($possiblePaths as $path) {
    $fullPath = realpath($path);
    echo "Path: $path<br>";
    echo "Full path: " . ($fullPath ? $fullPath : 'Not found') . "<br>";
    echo "File exists: " . (file_exists($path) ? "Yes" : "No") . "<br>";
    echo "<hr>";
}

// Test folder creation
echo "<h2>Testing Folder Creation</h2>";
 $folderPath = 'assets/images/';
echo "Folder path: " . realpath($folderPath) . "<br>";
echo "Folder exists: " . (file_exists($folderPath) ? "Yes" : "No") . "<br>";

if (!file_exists($folderPath)) {
    if (mkdir($folderPath, 0777, true)) {
        echo "Folder created successfully<br>";
    } else {
        echo "Failed to create folder<br>";
    }
}

// Test QR Code generation if library is found
echo "<h2>Testing QR Code Generation</h2>";
 $libraryLoaded = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        include $path;
        if (class_exists('QRcode')) {
            $libraryLoaded = true;
            echo "QRcode class found in: $path<br>";
            
            // Test QR Code generation
            $testQR = "TEST-QR-CODE";
            $testFile = $folderPath . 'test.png';
            QRcode::png($testQR, $testFile, QR_ECLEVEL_H, 10);
            echo "QR Code generated: " . (file_exists($testFile) ? "Yes" : "No") . "<br>";
            echo "QR Code file path: " . realpath($testFile) . "<br>";
            break;
        }
    }
}

if (!$libraryLoaded) {
    echo "PHP QR Code library not found in any of the tested paths.<br>";
}

// List files in current directory
echo "<h2>Files in Current Directory</h2>";
 $files = scandir('.');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo $file . "<br>";
    }
}
?>