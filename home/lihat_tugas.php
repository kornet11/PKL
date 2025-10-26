<?php
session_start();
require 'functions.php';

// Cek login guru
if (!isset($_SESSION['login']) || !isset($_SESSION['id_gurupem'])) {
    echo "<script>document.location.href = '../index.php';</script>";
    exit;
}

$id_tugas = isset($_GET['id_tugas']) ? intval($_GET['id_tugas']) : 0;
if ($id_tugas <= 0) {
    echo "ID tugas tidak valid.";
    exit;
}

// Ubah status submission siswa
if (isset($_POST['update_status'])) {
    $histori_id = intval($_POST['id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE histori_tugas SET status='$new_status' WHERE id='$histori_id'");
    echo "<script>document.location.href='lihat_tugas.php?id_tugas=$id_tugas';</script>";
    exit;
}


// Ambil detail tugas
$tugas_query = mysqli_query($conn, "SELECT * FROM tugas WHERE id_tugas = '$id_tugas'");
if (!$tugas_query || mysqli_num_rows($tugas_query) == 0) {
    echo "Tugas tidak ditemukan.";
    exit;
}
$tugas = mysqli_fetch_assoc($tugas_query);

// Ambil semua submission siswa untuk tugas ini
$sub_q = "
  SELECT h.*, s.nama, s.konsentrasi
  FROM histori_tugas h
  JOIN siswa s ON h.id_siswa = s.id_siswa
  WHERE h.id_tugas = '$id_tugas'
  ORDER BY h.tanggal_upload DESC
";
$subs = mysqli_query($conn, $sub_q);
if (!$subs) {
    die("Query error: " . mysqli_error($conn));
}
$rows = [];
while ($r = mysqli_fetch_assoc($subs)) $rows[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pengumpulan Tugas - <?= htmlspecialchars($tugas['judul']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

  <h3>Pengumpulan: <?= htmlspecialchars($tugas['judul']) ?></h3>
  <p><strong>Deadline:</strong> <?= htmlspecialchars($tugas['tanggal_deadline']) ?></p>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Jurusan</th>
        <th>File (Jawaban)</th>
        <th>Status</th>
        <th>Tanggal Upload</th>
        <th>Aksi (Ubah Status)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($rows) == 0): ?>
        <tr><td colspan="7" class="text-center">Belum ada siswa yang mengumpulkan</td></tr>
      <?php else: ?>
        <?php $no = 1; foreach ($rows as $r): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['konsentrasi']) ?></td>
            <td>
              <?php if (!empty($r['jawaban'])): ?>
                <a href="uploads/tugas/<?= htmlspecialchars($r['jawaban']) ?>" target="_blank" class="btn btn-sm btn-primary">Download</a>
                <br><small><?= htmlspecialchars($r['jawaban']) ?></small>
              <?php else: ?>
                <span class="text-muted">Belum upload</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($r['status'] ?? 'Belum') ?></td>
            <td><?= htmlspecialchars($r['tanggal_upload']) ?></td>
            <td>
              <form method="post" class="d-flex flex-column">
                <input type="hidden" name="id" value="<?= intval($r['id']) ?>">
                <select name="status" class="form-select form-select-sm mb-1">
                  <option value="Belum" <?= (isset($r['status']) && $r['status']=='Belum')? 'selected':'' ?>>Belum</option>
                  <option value="Proses" <?= (isset($r['status']) && $r['status']=='Proses')? 'selected':'' ?>>Proses</option>
                  <option value="Selesai" <?= (isset($r['status']) && $r['status']=='Selesai')? 'selected':'' ?>>Selesai</option>
                </select>
                <button class="btn btn-sm btn-success" name="update_status">Simpan</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="dashboard_guru.php" class="btn btn-secondary">⬅ Kembali</a>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
