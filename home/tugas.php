<?php
require 'functions.php';
session_start();

// Cek login
if (!isset($_SESSION['login'])) {
  header("Location: ../index.php");
  exit;
}

// Ambil semua tugas
$tugas = tampilData("SELECT * FROM tugas_pkl ORDER BY tanggal_diberikan DESC");

// Proses tambah tugas
if (isset($_POST['tambah'])) {
  if (tambahTugas($_POST) > 0) {
    echo "<script>alert('Tugas berhasil ditambahkan!'); location.href='tugas_pkl.php';</script>";
  } else {
    echo "<script>alert('Tugas gagal ditambahkan.');</script>";
  }
}
?>

<!-- HTML Tugas -->
<h1>Daftar Tugas PKL</h1>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Tugas</button>
<table class="table mt-3">
  <thead>
    <tr>
      <th>No</th>
      <th>Judul</th>
      <th>Deskripsi</th>
      <th>File</th>
      <th>Deadline</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no = 1;
    foreach ($tugas as $t): ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $t['judul_tugas'] ?></td>
        <td><?= $t['deskripsi'] ?></td>
        <td><a href="file/tugas/<?= $t['file_tugas'] ?>" target="_blank">Download</a></td>
        <td><?= $t['tanggal_deadline'] ?></td>
        <td>
          <a href="daftar_jawaban.php?id=<?= $t['id_tugas'] ?>" class="btn btn-sm btn-success">Lihat Jawaban</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Modal Tambah Tugas -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Tugas PKL</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="judul_tugas" class="form-control mb-2" placeholder="Judul Tugas" required>
        <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi Tugas"></textarea>
        <input type="date" name="tanggal_deadline" class="form-control mb-2" required>
        <input type="file" name="file_tugas" class="form-control" required>
      </div>
      <div class="modal-footer">
        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>
