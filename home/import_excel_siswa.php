<?php
session_start();
require_once __DIR__ . '/functions.php';

// pastikan PhpSpreadsheet terinstall
$autoload1 = __DIR__ . '/../vendor/autoload.php'; // project root vendor
$autoload2 = __DIR__ . '/vendor/autoload.php';    // home/vendor (fallback)
if (file_exists($autoload1)) {
    require_once $autoload1;
} elseif (file_exists($autoload2)) {
    require_once $autoload2;
} else {
    // tampilkan form tapi beri peringatan bahwa PhpSpreadsheet tidak terpasang
    $missingPhpSpreadsheet = true;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

// Unduh template CSV (tetap sediakan)
if (isset($_GET['download']) && $_GET['download'] === 'template') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=template_siswa.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['NISN','Nama','Kelas','Konsentrasi','No Telepon','Password']);
    fclose($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($missingPhpSpreadsheet)) {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Library tidak ditemukan','text'=>'PhpSpreadsheet tidak terpasang. Jalankan: composer require phpoffice/phpspreadsheet'];
        header('Location: siswa.php'); exit;
    }

    if (!isset($_FILES['file_import']) || $_FILES['file_import']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Upload gagal','text'=>'File tidak diunggah atau terjadi error.'];
        header('Location: siswa.php'); exit;
    }

    $tmpFile = $_FILES['file_import']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION));
    // load spreadsheet
    try {
        $reader = IOFactory::createReaderForFile($tmpFile);
        $spreadsheet = $reader->load($tmpFile);
    } catch (\Throwable $e) {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal membaca file','text'=>$e->getMessage()];
        header('Location: siswa.php'); exit;
    }

    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true); // preserve columns A,B,C...
    // cari header: baris pertama non-empty
    $header = [];
    $startRow = 1;
    foreach ($data as $r => $row) {
        $allEmpty = true;
        foreach ($row as $cell) { if (trim((string)$cell) !== '') { $allEmpty = false; break; } }
        if (!$allEmpty) { $header = $row; $startRow = $r + 1; break; }
    }
    if (empty($header)) {
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Gagal','text'=>'Header tidak ditemukan pada file.'];
        header('Location: siswa.php'); exit;
    }

    // normalisasi header -> map kolom ke field
    $normalize = function($s){
        $s = mb_strtolower(trim((string)$s));
        $s = preg_replace('/[\s\-_]+/',' ', $s);
        return $s;
    };
    $headerNorm = array_map($normalize, $header);
    $fieldsMap = [
        'nisn' => ['nisn','nis','no nisn','nomor induk','nomor_induk'],
        'nama' => ['nama','nama siswa','name','student name'],
        'kelas' => ['kelas','class'],
        'konsentrasi' => ['konsentrasi','jurusan','major'],
        'no_telepon' => ['no telepon','telp','hp','phone','no_telp'],
        'password' => ['password','pwd','kata sandi']
    ];
    $colIndex = [];
    foreach ($fieldsMap as $field => $aliases) {
        $found = false;
        foreach ($headerNorm as $col => $h) {
            foreach ($aliases as $a) {
                if ($h === $a || strpos($h, $a) !== false) {
                    $colIndex[$field] = $col; $found = true; break 2;
                }
            }
        }
        if (!$found) {
            // tidak wajib password; jika password tidak ada maka kita buat default
            if ($field === 'password') continue;
            $_SESSION['flash'] = ['icon'=>'error','title'=>'Header tidak sesuai','text'=>"Kolom '{$field}' tidak ditemukan. Pastikan header minimal NISN, Nama, Kelas, Konsentrasi, No Telepon."];
            header('Location: siswa.php'); exit;
        }
    }

    // mulai transaction
    mysqli_begin_transaction($conn);
    $errors = [];
    $inserted = $updated = $skipped = 0;

    // persiapkan statement
    $stmtSelect = mysqli_prepare($conn, "SELECT id_siswa FROM siswa WHERE nisn = ? LIMIT 1");
    $stmtInsert = mysqli_prepare($conn, "INSERT INTO siswa (nisn, nama, kelas, konsentrasi, no_telepon, password, foto) VALUES (?, ?, ?, ?, ?, ?, '')");
    $stmtUpdate = mysqli_prepare($conn, "UPDATE siswa SET nama = ?, kelas = ?, konsentrasi = ?, no_telepon = ?, password = ? WHERE nisn = ?");

    $maxRow = max(array_keys($data));
    for ($r = $startRow; $r <= $maxRow; $r++) {
        $row = $data[$r] ?? [];
        // build sequential cells array
        $cells = array_values($row);
        // read values by header column keys (A,B,C etc). map uses original header keys
        $getCell = function($colKey) use ($row){
            // $row is associative with letters
            return isset($row[$colKey]) ? trim((string)$row[$colKey]) : '';
        };
        // convert header associative keys to letters order
        $headerLetters = array_keys($header);
        // helper to get by mapped index
        $getByMap = function($field) use ($colIndex, $headerLetters, $row) {
            if (!isset($colIndex[$field])) return '';
            $idx = $colIndex[$field]; // numeric index of header array (0-based)
            $letter = $headerLetters[$idx] ?? null;
            return $letter ? trim((string)($row[$letter] ?? '')) : '';
        };

        $nisn = $getByMap('nisn');
        $nama = $getByMap('nama');
        $kelas = $getByMap('kelas');
        $kons = $getByMap('konsentrasi');
        $telp = $getByMap('no_telepon');
        $pass = $getByMap('password');

        // skip empty
        if ($nisn === '' && $nama === '' && $kelas === '' && $kons === '' && $telp === '' && $pass === '') { $skipped++; continue; }
        if ($nisn === '') { $errors[] = "Baris {$r}: NISN kosong"; $skipped++; continue; }

        $pwPlain = $pass !== '' ? $pass : 'password123';
        $pwHash = password_hash($pwPlain, PASSWORD_DEFAULT);

        // check existing
        mysqli_stmt_bind_param($stmtSelect, "s", $nisn);
        if (!mysqli_stmt_execute($stmtSelect)) { $errors[] = "Select error baris {$r}: " . mysqli_error($conn); continue; }
        mysqli_stmt_store_result($stmtSelect);
        $exists = mysqli_stmt_num_rows($stmtSelect) > 0;
        if ($exists) {
            mysqli_stmt_bind_param($stmtUpdate, "ssssss", $nama, $kelas, $kons, $telp, $pwHash, $nisn);
            if (mysqli_stmt_execute($stmtUpdate)) $updated++; else $errors[] = "Update error baris {$r}: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmtInsert, "ssssss", $nisn, $nama, $kelas, $kons, $telp, $pwHash);
            if (mysqli_stmt_execute($stmtInsert)) $inserted++; else $errors[] = "Insert error baris {$r}: " . mysqli_error($conn);
        }
        mysqli_stmt_free_result($stmtSelect);
    }

    // close stmts
    mysqli_stmt_close($stmtSelect);
    mysqli_stmt_close($stmtInsert);
    mysqli_stmt_close($stmtUpdate);

    if (empty($errors)) {
        mysqli_commit($conn);
        $_SESSION['flash'] = ['icon'=>'success','title'=>'Import Sukses','text'=>"Selesai: $inserted ditambah, $updated diupdate, $skipped di-skip."];
    } else {
        mysqli_rollback($conn);
        $_SESSION['flash'] = ['icon'=>'error','title'=>'Import Gagal','text'=>"Terjadi error. Tidak ada perubahan yang disimpan. Lihat log import."];
        $_SESSION['import_errors'] = $errors;
    }
    header('Location: siswa.php'); exit;
}

// tampilkan form upload
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Import Excel Siswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h4>Import Data Siswa (.xlsx / .xls)</h4>
    <?php if (!empty($missingPhpSpreadsheet)): ?>
        <div class="alert alert-warning">PhpSpreadsheet tidak ditemukan. Jalankan: <code>composer require phpoffice/phpspreadsheet</code></div>
    <?php endif; ?>

    <p>Gunakan template: <a href="import_excel_siswa.php?download=template">Unduh CSV template</a></p>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" name="file_import" accept=".xlsx,.xls" class="form-control" required>
        </div>
        <button class="btn btn-primary">Import Excel</button>
        <a href="siswa.php" class="btn btn-secondary">Kembali</a>
    </form>

    <?php if (!empty($_SESSION['import_errors'])): ?>
        <div class="mt-3">
            <h6>Log Error</h6>
            <ul>
                <?php foreach ($_SESSION['import_errors'] as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['import_errors']); ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>