<?php
include "koneksi.php"; // koneksi database

// Notifikasi pesan
$notif = "";

// ============ Tambah Jurnal ============
if (isset($_POST['tambah_jurnal'])) {
    $nama_siswa    = mysqli_real_escape_string($conn, $_POST['nama_siswa']);
    $kelas_jurusan = mysqli_real_escape_string($conn, $_POST['kelas_jurusan']);
    $tempat_pkl    = mysqli_real_escape_string($conn, $_POST['tempat_pkl']);
    $tanggal       = $_POST['tanggal'];
    $waktu_mulai   = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $uraian        = mysqli_real_escape_string($conn, $_POST['uraian']);
    $alat_bahan    = mysqli_real_escape_string($conn, $_POST['alat_bahan']);
    $hasil         = mysqli_real_escape_string($conn, $_POST['hasil']);

    $dokumentasi = "";
    if (!empty($_FILES['dokumentasi']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES["dokumentasi"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','bmp','webp'];
        if (in_array($ext, $allowed)) {
            $dokumentasi = time() . "_" . uniqid() . "." . $ext;
            if (!move_uploaded_file($_FILES["dokumentasi"]["tmp_name"], $target_dir . $dokumentasi)) {
                $notif = "<div class='alert alert-danger'>Gagal upload dokumentasi.</div>";
                $dokumentasi = "";
            }
        } else {
            $notif = "<div class='alert alert-danger'>File dokumentasi harus gambar (jpg/png/gif/webp).</div>";
            $dokumentasi = "";
        }
    }

    if (!$notif) {
        $query = "INSERT INTO jurnal_pkl 
            (nama_siswa, kelas_jurusan, tempat_pkl, tanggal, waktu_mulai, waktu_selesai, uraian, alat_bahan, hasil, dokumentasi) 
            VALUES 
            ('$nama_siswa', '$kelas_jurusan', '$tempat_pkl', '$tanggal', '$waktu_mulai', '$waktu_selesai', '$uraian', '$alat_bahan', '$hasil', '$dokumentasi')";
        mysqli_query($conn, $query);
        header("Location: jurnal.php");
        exit;
    }
}

// ============ Edit Jurnal ============
if (isset($_POST['edit_jurnal'])) {
    $id            = intval($_POST['id']);
    $nama_siswa    = mysqli_real_escape_string($conn, $_POST['nama_siswa']);
    $kelas_jurusan = mysqli_real_escape_string($conn, $_POST['kelas_jurusan']);
    $tempat_pkl    = mysqli_real_escape_string($conn, $_POST['tempat_pkl']);
    $tanggal       = $_POST['tanggal'];
    $waktu_mulai   = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $uraian        = mysqli_real_escape_string($conn, $_POST['uraian']);
    $alat_bahan    = mysqli_real_escape_string($conn, $_POST['alat_bahan']);
    $hasil         = mysqli_real_escape_string($conn, $_POST['hasil']);

    if (!empty($_FILES['dokumentasi']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES["dokumentasi"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','bmp','webp'];
        if (in_array($ext, $allowed)) {
            $dokumentasi = time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($_FILES["dokumentasi"]["tmp_name"], $target_dir . $dokumentasi)) {
                $update = "UPDATE jurnal_pkl SET 
                    nama_siswa='$nama_siswa', kelas_jurusan='$kelas_jurusan', tempat_pkl='$tempat_pkl',
                    tanggal='$tanggal', waktu_mulai='$waktu_mulai', waktu_selesai='$waktu_selesai',
                    uraian='$uraian', alat_bahan='$alat_bahan', hasil='$hasil', 
                    dokumentasi='$dokumentasi'
                    WHERE id='$id'";
            } else {
                $notif = "<div class='alert alert-danger'>Gagal upload dokumentasi.</div>";
            }
        } else {
            $notif = "<div class='alert alert-danger'>File dokumentasi harus gambar (jpg/png/gif/webp).</div>";
        }
    } else {
        $update = "UPDATE jurnal_pkl SET 
            nama_siswa='$nama_siswa', kelas_jurusan='$kelas_jurusan', tempat_pkl='$tempat_pkl',
            tanggal='$tanggal', waktu_mulai='$waktu_mulai', waktu_selesai='$waktu_selesai',
            uraian='$uraian', alat_bahan='$alat_bahan', hasil='$hasil'
            WHERE id='$id'";
    }

    if (!$notif) {
        mysqli_query($conn, $update);
        header("Location: jurnal.php");
        exit;
    }
}

// ============ Hapus Jurnal ============
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jurnal_pkl WHERE id='$id'");
    header("Location: jurnal.php");
    exit;
}

// ============ Ambil Data ============
$data = mysqli_query($conn, "SELECT * FROM jurnal_pkl ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Jurnal PKL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0adcd65.js" crossorigin="anonymous"></script>
</head>
<body class="container py-4">

    <h3 class="mb-3">📘 Manajemen Jurnal PKL</h3>
    <?= $notif ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="fas fa-plus"></i> Tambah Jurnal
    </button>

    <!-- Tabel Jurnal -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas/Jurusan</th>
                <th>Tempat PKL</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Uraian</th>
                <th>Dokumentasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                <td><?= htmlspecialchars($row['kelas_jurusan']) ?></td>
                <td><?= htmlspecialchars($row['tempat_pkl']) ?></td>
                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                <td><?= htmlspecialchars($row['waktu_mulai']." - ".$row['waktu_selesai']) ?></td>
                <td><?= htmlspecialchars($row['uraian']) ?></td>
                <td>
                    <?php if($row['dokumentasi']) { ?>
                        <a href="uploads/<?= htmlspecialchars($row['dokumentasi']) ?>" target="_blank">📎 Lihat</a>
                    <?php } else { echo "-"; } ?>
                </td>
                <td>
                    <!-- Tombol Edit -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>
                    <!-- Tombol Hapus -->
                    <a href="jurnal.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Jurnal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="row mb-2">
                                    <div class="col">
                                        <label>Nama Siswa</label>
                                        <input type="text" name="nama_siswa" value="<?= htmlspecialchars($row['nama_siswa']) ?>" class="form-control" required>
                                    </div>
                                    <div class="col">
                                        <label>Kelas/Jurusan</label>
                                        <input type="text" name="kelas_jurusan" value="<?= htmlspecialchars($row['kelas_jurusan']) ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label>Tempat PKL</label>
                                    <input type="text" name="tempat_pkl" value="<?= htmlspecialchars($row['tempat_pkl']) ?>" class="form-control">
                                </div>
                                <div class="row mb-2">
                                    <div class="col">
                                        <label>Tanggal</label>
                                        <input type="date" name="tanggal" value="<?= htmlspecialchars($row['tanggal']) ?>" class="form-control" required>
                                    </div>
                                    <div class="col">
                                        <label>Waktu Mulai</label>
                                        <input type="time" name="waktu_mulai" value="<?= htmlspecialchars($row['waktu_mulai']) ?>" class="form-control">
                                    </div>
                                    <div class="col">
                                        <label>Waktu Selesai</label>
                                        <input type="time" name="waktu_selesai" value="<?= htmlspecialchars($row['waktu_selesai']) ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label>Uraian</label>
                                    <textarea name="uraian" class="form-control"><?= htmlspecialchars($row['uraian']) ?></textarea>
                                </div>
                                <div class="mb-2">
                                    <label>Alat & Bahan</label>
                                    <textarea name="alat_bahan" class="form-control"><?= htmlspecialchars($row['alat_bahan']) ?></textarea>
                                </div>
                                <div class="mb-2">
                                    <label>Hasil</label>
                                    <textarea name="hasil" class="form-control"><?= htmlspecialchars($row['hasil']) ?></textarea>
                                </div>
                                <div class="mb-2">
                                    <label>Upload Dokumentasi (Opsional)</label>
                                    <input type="file" name="dokumentasi" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="edit_jurnal" class="btn btn-success"><i class="fas fa-save"></i> Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
        </tbody>
    </table>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Jurnal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col">
                                <label>Nama Siswa</label>
                                <input type="text" name="nama_siswa" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Kelas/Jurusan</label>
                                <input type="text" name="kelas_jurusan" class="form-control">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Tempat PKL</label>
                            <input type="text" name="tempat_pkl" class="form-control">
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" class="form-control">
                            </div>
                            <div class="col">
                                <label>Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" class="form-control">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Uraian</label>
                            <textarea name="uraian" class="form-control"></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Alat & Bahan</label>
                            <textarea name="alat_bahan" class="form-control"></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Hasil</label>
                            <textarea name="hasil" class="form-control"></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Upload Dokumentasi</label>
                            <input type="file" name="dokumentasi" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah_jurnal" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
