<?php
require_once 'assets/vendor/autoload.php';
require_once 'functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Koneksi ke database


// Ambil data dari database
$query = "SELECT nama, alamat, kontak, owner, jabatan, pembimbing FROM dudi";
$result = mysqli_query($conn, $query);

// Susun data menjadi array untuk diekspor
$data = [];
$data[] = ['No', 'Nama', 'Alamat', 'Kontak', 'Owner', 'Jabatan', 'Pemebimbing']; // Header

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $no++,
        $row['nama'],
        $row['alamat'],
        $row['kontak'],
        $row['owner'],
        $row['jabatan'],
        $row['pembimbing']
    ];
}

// Buat objek spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Isi data ke dalam spreadsheet
foreach ($data as $rowIndex => $row) {
    foreach ($row as $colIndex => $cell) {
        $columnLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
        $cellCoordinate = $columnLetter . ($rowIndex + 1);
        $sheet->setCellValue($cellCoordinate, $cell);

        // Atur lebar kolom agar otomatis menyesuaikan isi
        $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    }
}

// Tambahkan warna latar belakang untuk baris header (baris pertama)
$headerRange = 'A1:' . Coordinate::stringFromColumnIndex(count($data[0])) . '1';
$sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('00FF00');

// Header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_gurupembimbing.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
