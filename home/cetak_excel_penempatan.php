<?php
require_once 'assets/vendor/autoload.php';
require_once 'functions.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Koneksi ke database


// Ambil data dari database
$query = "
SELECT 
    s.`nama` AS nama_siswa,
    d.`nama` AS nama_dudi,
    g.`nama` AS nama_gurupembimbing,
FROM penempatan p
JOIN siswa s ON p.`siswa_id` = s.`id_siswa`
JOIN dudi d ON p.`dudi_id` = d.`id_dudi`
JOIN gurupembimbing g ON p.`gurupem_id` = g.`id_gurupem`
";
$result = mysqli_query($conn, $query);

// Susun data menjadi array untuk diekspor
$data = [];
$data[] = ['No', 'Nama siswa', 'Nama dudi', 'Nama gurupembimbing']; // Header

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        $no++,
        $row['nama_siswa'],
        $row['nama_dudi'],
        $row['nama_gurupembimbing'],
        
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
header('Content-Disposition: attachment;filename="data_gurukaprok.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
