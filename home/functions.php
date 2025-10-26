<?php
// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "pkl");


function cekAkses($roles = []) {
    // pastikan user login
    if (!isset($_SESSION['login'])) {
        header("Location: ../index.php");
        exit;
    }

    // kalau roles tidak kosong, cek hak akses
    if (!empty($roles) && !in_array($_SESSION['hak_akses'], $roles)) {
        header("Location: error/404.php"); // halaman larangan
        exit;
    }
}


// Function Logika Tambah Admin
function tambahAdmin($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$username = htmlspecialchars($data['username']);
	$password = password_hash($data['password'], PASSWORD_DEFAULT);
	$nama_lengkap = htmlspecialchars($data['nama_lengkap']);
	$alamat = htmlspecialchars($data['alamat']);
	$no_telp = htmlspecialchars($data['no_telp']);
	$hak_akses = htmlspecialchars($data['hak_akses']);

	// Cek apakah ada username yang sama
	$result = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$username'");

	if (mysqli_fetch_assoc($result)) {
		echo "
			<script>
				alert('Username Sudah Terdaftar!')
			</script>
		";
		return false;
	}

	// Jalankan function upload gambar
	$gambar = uploadGambarAdmin();
	// Cek jika hasil dari variabel gambar tidak sama dengan yang ada di function upload gambar
	if (!$gambar) {
		return false;
	}

	// Lakukan query tambah data
	$query = "INSERT INTO admin
				VALUES
				(null, '$username', '$password', '$nama_lengkap', '$alamat', '$no_telp', '$hak_akses', '$gambar')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}



// Function Logika Edit Admin
function editAdmin($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_admin = htmlspecialchars($data['id_admin']);
	$username = htmlspecialchars($data['username']);
	$password = password_hash($data['password'], PASSWORD_DEFAULT);
	$nama_lengkap = htmlspecialchars($data['nama_lengkap']);
	$alamat = htmlspecialchars($data['alamat']);
	$no_telp = htmlspecialchars($data['no_telp']);
	$hak_akses = htmlspecialchars($data['hak_akses']);

	$gambarLama = $data['fotoLama'];

	if ($_FILES['foto']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		// Jalankan function upload gambar
		$gambar = uploadGambarAdmin();
	}

	// Lakukan query edit data
	$query = "UPDATE admin SET
				username = '$username',
				nama_lengkap = '$nama_lengkap',
				alamat = '$alamat',
				no_telp = '$no_telp',
				hak_akses = '$hak_akses',
				foto = '$gambar'
			
			 ";
	if ($password !== "") {
		$query .= ", password = '$password'";
	}

	$query .= " WHERE id_admin = $id_admin";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}







// Function Logika Edit Anggota
function editAnggota($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_anggota = htmlspecialchars($data['id_anggota']);
	$nama_anggota = htmlspecialchars($data['nama_anggota']);
	$nis = htmlspecialchars($data['nis']);
	$nama_kelas = htmlspecialchars($data['nama_kelas']);
	$alamat = htmlspecialchars($data['alamat']);
	$no_telp = htmlspecialchars($data['no_telp']);
	$tgl_bergabung = htmlspecialchars($data['tgl_bergabung']);

	$gambarLama = $data['gambarLama'];

	if ($_FILES['gambar']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		// Jalankan function upload gambar
		$gambar = uploadGambarAnggota();
	}

	// Lakukan query edit data
	$query = "UPDATE anggota SET
				nama_anggota = '$nama_anggota',
				nis = '$nis',
				nama_kelas = '$nama_kelas',
				alamat = '$alamat',
				no_telp = '$no_telp',
				tgl_bergabung = '$tgl_bergabung',
				gambar = '$gambar'
			 WHERE id_anggota = $id_anggota
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

//Function logika Tampil Guru
function tampilGuru($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

//Function logika Tambah Guru
function tambahGuru($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$nip = htmlspecialchars($data['nip']);
	$nama = htmlspecialchars($data['nama']);
	$jurusan = htmlspecialchars($data['jurusan']);
	$jabatan_guru = htmlspecialchars($data['jabatan_guru']);
	$no_telepon = htmlspecialchars($data['no_telepon']);
	$password = password_hash($data['password'] , PASSWORD_DEFAULT);

	// Jalankan function upload gambar
	$foto = uploadFotoGuru();
	// Cek jika hasil dari variabel gambar tidak sama dengan yang ada di function upload gambar
	if (!$foto) {
		return false;
	}
	// Lakukan query tambah data
	$query = "INSERT INTO gurupembimbing
				VALUES
				(null ,'$nip', '$nama', '$jurusan', '$no_telepon', '$password', '$foto', '$jabatan_guru')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

//Function logika Edit Guru
function editGuru($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_gurupem = htmlspecialchars($data['id_gurupem']);
	$nip = htmlspecialchars($data['nip']);
	$nama = htmlspecialchars($data['nama']);
	$jurusan = htmlspecialchars($data['jurusan']);
	$jabatan_guru = htmlspecialchars($data['jabatan_guru']);
	$no_telepon = htmlspecialchars($data['no_telepon']);
	$password = password_hash($data['password'] , PASSWORD_DEFAULT);


	$result = mysqli_query($conn, "SELECT password FROM gurupembimbing WHERE id_gurupem = '$id_gurupem'");
	$row = mysqli_fetch_assoc($result);
	$passwordLama = $row['password'];

	// Gunakan password lama jika tidak diisi
	$password = !empty($data['password']) ? password_hash($data['password'] ,PASSWORD_DEFAULT) : $passwordLama;

	$fotoLama = $data['fotolama'];

	if ($_FILES['foto']['error'] === 4) {
		$foto = $fotoLama;
	} else {
		// Jalankan function upload foto
		$foto = uploadFotoGuru();
	}

	// Lakukan query edit data
	$query = "UPDATE gurupembimbing SET
				nip = '$nip',
				nama = '$nama',
				jurusan = '$jurusan', 
				no_telepon = '$no_telepon',
				foto = '$foto',
				jabatan_guru = '$jabatan_guru'
				";
	if ($password !== "") {
		$query .= ", password = '$password'";
	}

	$query .= " WHERE id_gurupem = $id_gurupem";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Guru
function hapusGuru($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM gurupembimbing WHERE id_gurupem = '$id'");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Tambah Buku
function tambahBuku($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$judul_buku = htmlspecialchars($data['judul_buku']);
	$penulis = htmlspecialchars($data['penulis']);
	$pengarang = htmlspecialchars($data['pengarang']);
	$penerbit = htmlspecialchars($data['penerbit']);
	$thn_terbit = htmlspecialchars($data['thn_terbit']);
	$jumlah_buku = htmlspecialchars($data['jumlah_buku']);
	$jumlah_salinan = htmlspecialchars($data['jumlah_salinan']);
	$kategori_buku = htmlspecialchars($data['kategori_buku']);
	$deskripsi_buku = htmlspecialchars($data['deskripsi_buku']);

	// Jalankan function upload gambar
	$gambar = uploadGambarBuku();
	// Cek jika hasil dari variabel gambar tidak sama dengan yang ada di function upload gambar
	if (!$gambar) {
		return false;
	}

	// Lakukan query tambah data
	$query = "INSERT INTO buku
				VALUES
				(null, '$judul_buku', '$penulis', '$pengarang', '$penerbit', '$thn_terbit', '$jumlah_buku', '$jumlah_salinan', '$kategori_buku', '$deskripsi_buku', '$gambar')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Upload Gambar Anggota
function uploadFotoGuru()
{
	// Ambil beberapa data dari file gambar yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['foto']['name'];
	$ukuranFile = $_FILES['foto']['size'];
	$error = $_FILES['foto']['error'];
	$tmpName = $_FILES['foto']['tmp_name'];

	// Cek apakah tidak ada gambar yang di upload
	if ($error === 4) {
		echo "
            <script>
                alert('Pilih Gambar Anda Terlebih Dahulu!');
            </script>
        ";
		// Kembalikan nilai false yang artinya keluar dari function ini
		return false;
	}

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!');
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, 'assets/img/guru/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}
//Function Logika Tampil Siswa
function tampilData($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

//Function Logika Tambah Anggota
function tambahAnggota($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$nama_anggota = htmlspecialchars($data['nama_anggota']);
	$nis = htmlspecialchars($data['nis']);
	$nama_kelas = htmlspecialchars($data['nama_kelas']);
	$alamat = htmlspecialchars($data['alamat']);
	$no_telp = htmlspecialchars($data['no_telp']);
	$tgl_bergabung = htmlspecialchars($data['tgl_bergabung']);
	// Jalankan function upload gambar
	$gambar = uploadGambarAnggota();
	// Cek jika hasil dari variabel gambar tidak sama dengan yang ada di function upload gambar
	if (!$gambar) {
		return false;
	}

	// Lakukan query tambah data
	$query = "INSERT INTO anggota
				VALUES
				(null, '$nama_anggota', '$nis', '$nama_kelas', '$alamat', '$no_telp', '$tgl_bergabung', '$gambar')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

//Function Logika Tambah Siswa
function tambahSiswa($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$nisn = htmlspecialchars($data['nisn']);
	$nama = htmlspecialchars($data['nama']);
	$kelas = htmlspecialchars($data['kelas']);
	$konsentrasi = htmlspecialchars($data['konsentrasi']);
	$no_telepon = htmlspecialchars($data['no_telepon']);
	$password = password_hash($data['password'], PASSWORD_DEFAULT);

	// Jalankan function upload gambar
	$foto = uploadFotoSiswa();
	// Cek jika hasil dari variabel gambar tidak sama dengan yang ada di function upload gambar
	if (!$foto) {
		return false;
	}

	// Lakukan query tambah data
	$query = "INSERT INTO siswa
				VALUES
				(null ,'$nisn', '$nama', '$kelas', '$konsentrasi', '$no_telepon', '$password', '$foto')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

//Function logika Edit Siswa
function editSiswa($data)
{
    global $conn;

    $id_siswa    = htmlspecialchars($data['id_siswa']);
    $nisn        = htmlspecialchars($data['nisn']);
    $nama        = htmlspecialchars($data['nama']);
    $kelas       = htmlspecialchars($data['kelas']);
    $konsentrasi = htmlspecialchars($data['konsentrasi']);
    $no_telepon  = htmlspecialchars($data['no_telepon']);

    // Ambil password lama
    $result = mysqli_query($conn, "SELECT password FROM siswa WHERE id_siswa = '$id_siswa'");
    $row = mysqli_fetch_assoc($result);
    $passwordLama = $row['password'];

    // Gunakan password lama jika tidak diisi
    $password = !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : $passwordLama;

    // Ambil foto lama (kalau ada)
    $fotoLama = isset($data['fotolama']) ? $data['fotolama'] : '';

    // Cek apakah upload foto baru
    if ($_FILES['foto']['error'] === 4) {
        $foto = $fotoLama;
    } else {
        $foto = uploadFotoSiswa();
    }

    $query = "UPDATE siswa SET
                nisn = '$nisn',
                nama = '$nama',
                kelas = '$kelas',
                konsentrasi = '$konsentrasi',
                no_telepon = '$no_telepon',
                foto = '$foto',
                password = '$password'
              WHERE id_siswa = $id_siswa";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}


// Function Logika Hapus Siswa
function hapusSiswa($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM siswa WHERE id_siswa = '$id'");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}

// Function Logika Upload Gambar Anggota
function uploadFotoSiswa()
{
	// Ambil beberapa data dari file gambar yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['foto']['name'];
	$ukuranFile = $_FILES['foto']['size'];
	$error = $_FILES['foto']['error'];
	$tmpName = $_FILES['foto']['tmp_name'];

	// Cek apakah tidak ada gambar yang di upload
	if ($error === 4) {
		echo "
            <script>
                alert('Pilih Gambar Anda Terlebih Dahulu!');
            </script>
        ";
		// Kembalikan nilai false yang artinya keluar dari function ini
		return false;
	}

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!');
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, 'assets/img/siswa/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}


// Function Logika Edit Buku
function editBuku($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_buku = htmlspecialchars($data['id_buku']);
	$judul_buku = htmlspecialchars($data['judul_buku']);
	$penulis = htmlspecialchars($data['penulis']);
	$pengarang = htmlspecialchars($data['pengarang']);
	$penerbit = htmlspecialchars($data['penerbit']);
	$thn_terbit = htmlspecialchars($data['thn_terbit']);
	$jumlah_buku = htmlspecialchars($data['jumlah_buku']);
	$jumlah_salinan = htmlspecialchars($data['jumlah_salinan']);
	$kategori_buku = htmlspecialchars($data['kategori_buku']);
	$deskripsi_buku = htmlspecialchars($data['deskripsi_buku']);

	$gambarLama = $data['gambar_sampul_lama'];

	if ($_FILES['gambar_sampul']['error'] === 4) {
		$gambar = $gambarLama;
	} else {
		// Jalankan function upload gambar
		$gambar = uploadGambarBuku();
	}

	// Lakukan query edit data
	$query = "UPDATE buku SET
				judul_buku = '$judul_buku',
				penulis = '$penulis',
				pengarang = '$pengarang',
				penerbit = '$penerbit',
				thn_terbit = '$thn_terbit',
				jumlah_buku = '$jumlah_buku',
				jumlah_salinan = '$jumlah_salinan',
				kategori_buku = '$kategori_buku',
				deskripsi_buku = '$deskripsi_buku',
				gambar_sampul = '$gambar'
			 WHERE id_buku = $id_buku
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}






// Function Logika Tambah Peminjaman
function tambahPeminjaman($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$anggota_id = htmlspecialchars($data['anggota_id']);
	$buku_id = htmlspecialchars($data['buku_id']);
	$tgl_peminjaman = htmlspecialchars($data['tgl_peminjaman']);
	$nama_pinjam = htmlspecialchars($data['nama_pinjam']);
	$tgl_pengembalian_r = htmlspecialchars($data['tgl_pengembalian_r']);
	$tgl_pengembalian_a = htmlspecialchars($data['tgl_pengembalian_a']);
	$denda = 0;
	$status_peminjaman = 1;
	$jumlah_pinjam = htmlspecialchars($data['jumlah_pinjam']);
	$catatan = htmlspecialchars($data['catatan']);

	// Lakukan query tambah data
	$query = "INSERT INTO peminjaman
				VALUES
				(null, '$anggota_id', '$buku_id', '$tgl_peminjaman', '$nama_pinjam', '$tgl_pengembalian_r', '$tgl_pengembalian_a', '$denda', '$status_peminjaman', '$jumlah_pinjam', '$catatan')
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	if (mysqli_query($conn, $query)) {
		// Ambil id terakhir dari query tambah peminjaman 
		$id_peminjaman_akhir = mysqli_insert_id($conn);

		// Lakukan query tambah juga ke tabel riwayat peminjaman
		$query_riwayat = "INSERT INTO riwayat_peminjaman
							VALUES
							(null, '$id_peminjaman_akhir', '$anggota_id', '$buku_id', '$tgl_peminjaman', '$tgl_pengembalian_a')
						 ";

		if (mysqli_query($conn, $query_riwayat)) {
			$update_buku = "UPDATE buku SET jumlah_buku = jumlah_buku - $jumlah_pinjam WHERE id_buku = $buku_id";

			mysqli_query($conn, $update_buku);
		}
	}

	return mysqli_affected_rows($conn);
}



// Function Logika Edit Peminjaman
function editPeminjaman($data)
{
	// Variable scope / Lingkup variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_peminjaman = htmlspecialchars($data['id_peminjaman']);
	$anggota_id = htmlspecialchars($data['anggota_id']);
	$buku_id = htmlspecialchars($data['buku_id']);
	$tgl_peminjaman = htmlspecialchars($data['tgl_peminjaman']);
	$nama_pinjam = htmlspecialchars($data['nama_pinjam']);
	$tgl_pengembalian_r = htmlspecialchars($data['tgl_pengembalian_r']);
	$tgl_pengembalian_a = htmlspecialchars($data['tgl_pengembalian_a']);
	$jumlah_pinjam = htmlspecialchars($data['jumlah_pinjam']);
	$catatan = htmlspecialchars($data['catatan']);

	// Lakukan query edit data
	$query = "UPDATE peminjaman SET
				anggota_id = '$anggota_id',
				buku_id = '$buku_id',
				tgl_peminjaman = '$tgl_peminjaman',
				nama_pinjam = '$nama_pinjam',
				tgl_pengembalian_r = '$tgl_pengembalian_r',
				tgl_pengembalian_a = '$tgl_pengembalian_a',
				jumlah_pinjam = '$jumlah_pinjam',
				catatan = '$catatan'
			 WHERE id_peminjaman = $id_peminjaman
			 ";
	// Jalankan function untuk mengeksekusi query database milik PHP
	mysqli_query($conn, $query);

	$query_update_riwayat = "UPDATE riwayat_peminjaman SET
								anggota_id = '$anggota_id',
								buku_id = '$buku_id',
								tgl_peminjaman = '$tgl_peminjaman',
								tgl_pengembalian = '$tgl_pengembalian_a'
							WHERE pinjam_id = '$id_peminjaman'
							";

	mysqli_query($conn, $query_update_riwayat);

	return 1;
}



// Function Logika Upload Gambar Admin
function uploadGambarAdmin()
{
	// Ambil beberapa data dari file foto yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['foto']['name'];
	$ukuranFile = $_FILES['foto']['size'];
	$error = $_FILES['foto']['error'];
	$tmpName = $_FILES['foto']['tmp_name'];

	// Cek apakah tidak ada gambar yang di upload
	if ($error === 4) {
		echo "
            <script>
                alert('Pilih Gambar Anda Terlebih Dahulu!');
            </script>
        ";
		// Kembalikan nilai false yang artinya keluar dari function ini
		return false;
	}

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!);
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, 'assets/img/admin/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}



// Function Logika Upload Gambar Anggota
function uploadGambarAnggota()
{
	// Ambil beberapa data dari file gambar yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['gambar']['name'];
	$ukuranFile = $_FILES['gambar']['size'];
	$error = $_FILES['gambar']['error'];
	$tmpName = $_FILES['gambar']['tmp_name'];

	// Cek apakah tidak ada gambar yang di upload
	if ($error === 4) {
		echo "
            <script>
                alert('Pilih Gambar Anda Terlebih Dahulu!');
            </script>
        ";
		// Kembalikan nilai false yang artinya keluar dari function ini
		return false;
	}

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!);
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, 'assets/img/anggota/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}



// Function Logika Upload Gambar Buku
function uploadGambarBuku()
{
	// Ambil beberapa data dari file foto yang di input dari variabel superglobal PHP yaitu $_FILES
	$namaFile = $_FILES['gambar_sampul']['name'];
	$ukuranFile = $_FILES['gambar_sampul']['size'];
	$error = $_FILES['gambar_sampul']['error'];
	$tmpName = $_FILES['gambar_sampul']['tmp_name'];

	// Cek apakah tidak ada gambar yang di upload
	if ($error === 4) {
		echo "
            <script>
                alert('Pilih Gambar Anda Terlebih Dahulu!');
            </script>
        ";
		// Kembalikan nilai false yang artinya keluar dari function ini
		return false;
	}

	// Cek apakah yang di upload gambar atau bukan

	// Buat array yang berisi ekstensi file yang diperbolehkan
	$ekstensiGambarValid = ["jpg", "jpeg", "png"];
	$ekstensiGambar = explode('.', $namaFile);
	$ekstensiGambar = strtolower(end($ekstensiGambar));

	// Cek jika ekstensi nya tidak sama dengan yang diperbolehkan
	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "
            <script>
                alert('Yang Anda Upload Bukan Gambar!);
            </script>
        ";
		return false;
	}

	// Cek jika ukuran gambar terlalu besar
	if ($ukuranFile > 5000000) {
		echo "
            <script>
                alert('Ukuran Gambar Terlalu Besar!');
            </script>
        ";
		return false;
	}

	// Lolos pengecekan, gambar siap di upload
	// Generate nama gambar baru
	$namaFileBaru = uniqid();
	$namaFileBaru .= '.';
	$namaFileBaru .= $ekstensiGambar;

	// Jalankan function milik PHP untuk mengupload file
	move_uploaded_file($tmpName, 'assets/img/buku/' . $namaFileBaru);

	// Kembalikan nama file baru
	return $namaFileBaru;
}




// Function Logika Tampil Admin
function tampilAdmin($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}



// Function Logika Hapus Admin
function hapusAdmin($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Ambil data foto
	$file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin WHERE id_admin = $id"));

	// Hapus gambar
	unlink('assets/img/admin/' . $file["foto"]);

	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM admin WHERE id_admin = $id");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}




// Function Logika Tampil Buku
function tampilBuku($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}



// Function Logika Hapus Buku
function hapusBuku($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Ambil data foto
	$file = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = $id"));

	// Hapus gambar
	unlink('assets/img/buku/' . $file["gambar_sampul"]);

	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM buku WHERE id_buku = $id");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}







// Function Logika Tampil Peminjaman
function tampilPeminjaman($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}



// Function Logika Tampil Riwayat Peminjaman
function tampilRiwayat($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}



// Function Logika Ubah Status Peminjaman
function ubahStatusPeminjaman($data)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$id_peminjaman = htmlspecialchars($data['id_peminjaman']);
	$id_buku = htmlspecialchars($data['id_buku']);
	$jumlah_pinjam = htmlspecialchars($data['jumlah_pinjam']);
	$status_peminjaman = htmlspecialchars($data['status_peminjaman']);

	// Cek berdasarkan nilai dari status peminjaman
	if ($status_peminjaman == '1') {
		// Lakukan query update data status peminjaman
		mysqli_query($conn, "UPDATE peminjaman SET status_peminjaman = '$status_peminjaman' WHERE id_peminjaman = $id_peminjaman");

		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);
	} else if ($status_peminjaman == '2') {
		// Lakukan query update data status peminjaman
		mysqli_query($conn, "UPDATE peminjaman SET status_peminjaman = '$status_peminjaman', denda = 0 WHERE id_peminjaman = $id_peminjaman");
		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);

		// dan update jumlah kembalikan buku
		mysqli_query($conn, "UPDATE buku SET jumlah_buku = jumlah_buku + $jumlah_pinjam WHERE id_buku = $id_buku");

		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);
	} else if ($status_peminjaman == '3') {
		// Lakukan query update data status peminjaman
		mysqli_query($conn, "UPDATE peminjaman SET status_peminjaman = '$status_peminjaman' WHERE id_peminjaman = $id_peminjaman");

		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);
	}
}



// Function Logika Update Jumlah Pinjam Buku
function updateJumlahPinjamBuku($data)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Tangkap semua data dari form / inputan user
	$buku_id = htmlspecialchars($data['buku_id']);
	$id_peminjaman = htmlspecialchars($data['id_peminjaman']);
	$jumlah_pinjam = htmlspecialchars($data['jumlah_pinjam']);
	$action_pinjam = htmlspecialchars($data['action_pinjam']); // Kembalikan / Tambah

	// Cek action yang di pilih user
	if ($action_pinjam === 'kembalikan') {
		// Lakukan query untuk mengurangi jumlah peminjaman
		mysqli_query($conn, "UPDATE peminjaman SET jumlah_pinjam = jumlah_pinjam - $jumlah_pinjam WHERE id_peminjaman = $id_peminjaman");

		// Lakukan query untuk mengembalikan buku
		mysqli_query($conn, "UPDATE buku SET jumlah_buku = jumlah_buku + $jumlah_pinjam WHERE id_buku = $buku_id");

		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);
	} else if ($action_pinjam === 'tambah') {
		// Lakukan query untuk menambah jumlah peminjaman
		mysqli_query($conn, "UPDATE peminjaman SET jumlah_pinjam = jumlah_pinjam + $jumlah_pinjam WHERE id_peminjaman = $id_peminjaman");

		// Lakukan query untuk mengurangi buku
		mysqli_query($conn, "UPDATE buku SET jumlah_buku = jumlah_buku - $jumlah_pinjam WHERE id_buku = $buku_id");

		// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
		return mysqli_affected_rows($conn);
	}
}



// Function Logika Cek Denda
function cekDenda($data)
{
	global $conn;
	$id_peminjaman = htmlspecialchars($data['id_peminjaman']);
	$tangalPinjaman = htmlspecialchars($data['tgl_peminjaman']);
	$tanggalHarusKembali = htmlspecialchars($data['tgl_harus_kembali']);
	$tanggalKembali = htmlspecialchars($data['tgl_pengembalian_a']);
	$tarifDendaPerHari = htmlspecialchars($data['denda_per_hari']);
	$denda = hitungDenda($tanggalKembali, $tanggalHarusKembali, $tarifDendaPerHari);

	mysqli_query($conn, "UPDATE peminjaman SET denda = '$denda' WHERE id_peminjaman = $id_peminjaman");

	return mysqli_affected_rows($conn);
}



// Function Logika Hitung Denda
function hitungDenda($tanggalKembali, $tanggalHarusKembali, $tarifDendaPerHari)
{
	$selisihHari = strtotime($tanggalKembali) - strtotime($tanggalHarusKembali);
	$selisihHari = floor($selisihHari / (60 * 60 * 24));

	if ($selisihHari <= 0) {
		return 0;
	} else {
		$denda = $selisihHari * $tarifDendaPerHari;
		return $denda;
	}
}

//Function logika Tampil penempatan
function tampilPenempatan($query)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query
	// Simpan hasil query ke variabel
	$result = mysqli_query($conn, $query);

	// Siapkan array kosong sebagai wadah baru dari hasil query
	$rows = [];

	// Lakukan looping ke semua data yang sudah di dapat dari hasil query
	while ($row = mysqli_fetch_assoc($result)) {
		// Isi array kosong tadi dengan data yang sudah di looping
		$rows[] = $row;
	}

	// Kembalikan array kosong tadi yang sekarang sudah terdapat isi data-data dari database
	return $rows;
}

//Function logika Edit Guru
function editPenempatan($data)
{
	global $conn;

	$id_penempatan = $data["id_penempatan"];
	$siswa_id = $data["siswa_id"];
	$dudi_id = $data["dudi_id"];
	$gurupem_id = $data["gurupem_id"];

	$query = "UPDATE penempatan SET 
                siswa_id = '$siswa_id',
                dudi_id = '$dudi_id',
                gurupem_id = '$gurupem_id'
              WHERE id_penempatan = $id_penempatan";

	return mysqli_query($conn, $query);
}

function tambahPenempatan($data)
{
	global $conn;

	$siswa_id = htmlspecialchars($data['siswa_id']);
	$dudi_id = htmlspecialchars($data['dudi_id']);
	$guru_id = htmlspecialchars($data['gurupem_id']);

	$query = "INSERT INTO penempatan (siswa_id, dudi_id, gurupem_id)
              VALUES ('$siswa_id', '$dudi_id', '$guru_id')";

	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Buku
function hapusPenempatan($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;
	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM penempatan WHERE id_penempatan = $id");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}




// Fungsi menampilkan data guru kaprok
function tampilGuruKaprok($query)
{
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

// Fungsi upload gambar
function uploadGambar()
{
	$namaFile = $_FILES['foto']['name'];
	$ukuranFile = $_FILES['foto']['size'];
	$error = $_FILES['foto']['error'];
	$tmpName = $_FILES['foto']['tmp_name'];

	// Cek jika tidak ada gambar yang diupload
	if ($error === 4) {
		echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
		return false;
	}

	// Validasi tipe gambar
	$ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
	$ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "<script>alert('Yang Anda upload bukan gambar!');</script>";
		return false;
	}

	// Batasi ukuran file (misalnya 2MB)
	if ($ukuranFile > 2000000) {
		echo "<script>alert('Ukuran gambar terlalu besar!');</script>";
		return false;
	}

	// Generate nama file unik
	$namaFileBaru = uniqid() . "_" . $namaFile;
	move_uploaded_file($tmpName, 'assets/img/guru/' . $namaFileBaru);

	return $namaFileBaru;
}

// Fungsi tambah guru kaprok
function tambahGuruKaprok($data)
{
	global $conn;

	$nip = htmlspecialchars($data["nip"]);
	$namakaprok = htmlspecialchars($data["namakaprok"]);
	$no_telpon = htmlspecialchars($data["no_telpon"]);
	$jurusan = htmlspecialchars($data["jurusan"]);
	$jabatan = htmlspecialchars($data["jabatan_guru"]);
	$password = password_hash($data["password"], PASSWORD_DEFAULT);

	$foto = uploadGambar();
	if (!$foto) {
		return 0;
	}

	$query = "INSERT INTO gurukaprok (nip, namakaprok,  jurusan, jabatan, no_telpon, foto, password)
              VALUES ('$nip', '$namakaprok', '$jurusan', '$jabatan', '$no_telpon', '$foto', '$password')";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// Fungsi edit guru kaprok
function editGuruKaprok($data)
{
	global $conn;

	$id = $data["id_gurukaprok"];
	$nip = $data["nip"];
	$namakaprok = htmlspecialchars($data["namakaprok"]);
	$no_telpon = htmlspecialchars($data["no_telpon"]);
	$jurusan = htmlspecialchars($data["jurusan"]);
	$jabatan = htmlspecialchars($data["jabatan"]);
	$fotoLama = $data["fotolama"];
	$password = password_hash($data["password"], PASSWORD_DEFAULT);

	// Cek apakah user upload foto baru
	if ($_FILES['foto']['error'] === 4) {
		$foto = $fotoLama;
	} else {
		// Hapus foto lama
		if (file_exists("assets/img/guru/$fotoLama")) {
			unlink("assets/img/guru/$fotoLama");
		}
		$foto = uploadGambar();
	}

	$query = "UPDATE gurukaprok SET 
				nip = '$nip',
				namakaprok = '$namakaprok',
				no_telpon = '$no_telpon',
				jurusan = '$jurusan',
				jabatan = '$jabatan',
				foto = '$foto'";

	if ($password !== "") {
		$query .= ", password = '$password'";
	}

	$query .= " WHERE id_gurukaprok = $id";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// Fungsi hapus guru kaprok
function hapusGuruKaprok($id)
{
	global $conn;

	// Ambil data gambar untuk dihapus dari folder
	$query = mysqli_query($conn, "SELECT foto FROM gurukaprok WHERE id_gurukaprok = $id");
	$data = mysqli_fetch_assoc($query);
	$foto = $data["foto"];
	if (file_exists("assets/img/guru/$foto")) {
		unlink("assets/img/guru/$foto");
	}

	mysqli_query($conn, "DELETE FROM gurukaprok WHERE id_gurukaprok = $id");
	return mysqli_affected_rows($conn);
}

function editDudi($data)
{
    global $conn;

    $id = $data["id_dudi"];
    $nama = htmlspecialchars($data["nama"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $kontak = htmlspecialchars($data["kontak"]);
    $logoLama = htmlspecialchars($data["logolama"]); // pastikan ada
    $owner = htmlspecialchars($data["owner"]);
    $jabatan = htmlspecialchars($data["jabatan"]);
    $pembimbing = htmlspecialchars($data["pembimbing"]);

    // Cek upload baru
    if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === 4) {
        $logo = $logoLama;
    } else {
        if (!empty($logoLama) && is_file("assets/img/dudi/$logoLama")) {
            unlink("assets/img/dudi/$logoLama");
        }
        $logo = uploadLogo();
        if (!$logo) {
            return 0; // gagal upload
        }
    }

    // Tambahkan koma sebelum pembimbing
    $query = "UPDATE dudi SET 
              nama = '$nama',
              alamat = '$alamat',
              kontak = '$kontak',
              logo = '$logo',
              owner = '$owner',
              jabatan = '$jabatan',
              pembimbing = '$pembimbing'
              WHERE id_dudi = $id";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}


// Fungsi hapus guru kaprok
function hapusDudi($id)
{
	global $conn;

	// Ambil data gambar untuk dihapus dari folder
	$query = mysqli_query($conn, "SELECT logo FROM dudi WHERE id_dudi = $id");
	$data = mysqli_fetch_assoc($query);
	$logo = $data["logo"];
	if (file_exists("assets/img/dudi/$logo")) {
		unlink("assets/img/dudi/$logo");
	}

	mysqli_query($conn, "DELETE FROM dudi WHERE id_dudi = $id");
	return mysqli_affected_rows($conn);
}

// Fungsi tambah guru kaprok
function tambahDudi($data)
{
	global $conn;

	$nama = htmlspecialchars($data["nama"]);
	$alamat = htmlspecialchars($data["alamat"]);
	$kontak = htmlspecialchars($data["kontak"]);
	$owner = htmlspecialchars($data["owner"]);
	$jabatan = htmlspecialchars($data["jabatan"]);
	$pembimbing = htmlspecialchars($data["pembimbing"]);


	$logo = uploadLogo();
	if (!$logo) {
		return 0;
	}

	$query = "INSERT INTO dudi (nama, alamat, kontak, logo, owner, jabatan, pembimbing)
              VALUES ('$nama', '$alamat', '$kontak', '$logo', '$owner', '$jabatan', '$pembimbing')";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

// Fungsi upload gambar
function uploadLogo()
{
	$namaFile = $_FILES['logo']['name'];
	$ukuranFile = $_FILES['logo']['size'];
	$error = $_FILES['logo']['error'];
	$tmpName = $_FILES['logo']['tmp_name'];

	// Cek jika tidak ada gambar yang diupload
	if ($error === 4) {
		echo "<script>alert('Pilih gambar terlebih dahulu!');</script>";
		return false;
	}

	// Validasi tipe gambar
	$ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
	$ekstensiGambar = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

	if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
		echo "<script>alert('Yang Anda upload bukan gambar!');</script>";
		return false;
	}

	// Batasi ukuran file (misalnya 2MB)
	if ($ukuranFile > 2000000) {
		echo "<script>alert('Ukuran gambar terlalu besar!');</script>";
		return false;
	}

	// Generate nama file unik
	$namaFileBaru = uniqid() . "_" . $namaFile;
	move_uploaded_file($tmpName, 'assets/img/dudi/' . $namaFileBaru);

	return $namaFileBaru;
}

// Fungsi menampilkan data guru kaprok
function tampilWaktu($query)
{
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

function tambahWaktu($data)
{
	global $conn;
    $id_waktu = htmlspecialchars($data['id_waktu']);
	$siswa_id = htmlspecialchars($data['siswa_id']);
	$tgl_mulai = htmlspecialchars($data["kontak"]);
	$tgl_selesai= htmlspecialchars($data["owner"]);
	$status= htmlspecialchars($data["jabatan"]);
	$laporan = htmlspecialchars($data["pembimbing"]);

	$query = "INSERT INTO waktu (siswa_id, tgl_mulai, tgl_selesai, status, laporan,)
              VALUES ('$id_waktu','$siswa_id', '$tgl_mulai', '$tgl_selesai', '$status', '$laporan')";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function tambahTugas($data) {
    global $conn;
    $judul = htmlspecialchars($data['judul_tugas']);
    $deskripsi = htmlspecialchars($data['deskripsi']);
    $deadline = $data['tanggal_deadline'];

    // Upload file
    $namaFile = $_FILES['file_tugas']['name'];
    $tmp = $_FILES['file_tugas']['tmp_name'];
    $newName = uniqid() . "_" . $namaFile;
    move_uploaded_file($tmp, 'file/tugas/' . $newName);

    $query = "INSERT INTO tugas_pkl VALUES (NULL, '$judul', '$deskripsi', '$newName', NOW(), '$deadline')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}


function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function upload_bukti($file_input_name, $target_dir = 'uploads/') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $f = $_FILES[$file_input_name];

    if ($f['error'] !== UPLOAD_ERR_OK) return null;

    // cek ukuran (maks 5MB)
    if ($f['size'] > 5 * 1024 * 1024) return null;

    // ekstensi aman
    $allowed = ['png','jpg','jpeg','pdf'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;

    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

    $newName = uniqid('bkt_', true) . '.' . $ext;
    $dest = rtrim($target_dir, '/') . '/' . $newName;

    if (move_uploaded_file($f['tmp_name'], $dest)) return $newName;
    return null;
}

// Fungsi Upload File
function uploadBukti($file_input_name)
{
    $fileName = basename($_FILES[$file_input_name]["name"]);
    $targetFilePath = time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Expanded allowed file types
    $allowedTypes = [
        'jpg', 'jpeg', 'png', 'gif',  // Images
        'pdf',  // PDF documents
        'doc', 'docx',  // Word documents
        'xls', 'xlsx',  // Excel spreadsheets
        'ppt', 'pptx'   // PowerPoint presentations
    ];
    
    // Validate file type
    if (!in_array($fileType, $allowedTypes)) {
        return false; // Invalid file type
    }
    
    // Validate file size (max 10MB)
    $maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($_FILES[$file_input_name]["size"] > $maxSize) {
        return false; // File too large
    }
    
    // Additional security check for file content
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES[$file_input_name]["tmp_name"]);
    finfo_close($finfo);
    
    // Allowed MIME types for security
    $allowedMimes = [
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];
    
    if (!in_array($mime, $allowedMimes)) {
        return false; // Invalid MIME type
    }
    
    // Move uploaded file
    if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $targetFilePath)) {
        return $targetFilePath;
    }
    
    return false; // Upload failed
}


function tambahJurnal($data) {
    global $conn;
    // Ambil data dari form
    $nama_siswa    = htmlspecialchars($data["nama_siswa"]);
    $kelas_jurusan = htmlspecialchars($data["kelas_jurusan"]);
    $tempat_pkl    = htmlspecialchars($data["tempat_pkl"]);
    $tanggal       = $data["tanggal"];
    $waktu_mulai   = $data["waktu_mulai"];
    $waktu_selesai = $data["waktu_selesai"];
    $uraian        = htmlspecialchars($data["uraian"]);
    $alat_bahan    = htmlspecialchars($data["alat_bahan"]);
    $hasil_output  = htmlspecialchars($data["hasil_output"]);
    $link_1        = htmlspecialchars($data["link_1"]);
    $link_2        = htmlspecialchars($data["link_2"]);
    
    // Upload file bukti
    $bukti_file = null;
    if (!empty($_FILES['bukti_file']['name'])) {
        $targetDir = "./assets/jurnal/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Validasi tipe file
        $allowedTypes = [
            'image/jpeg', 
            'image/png', 
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        
        $fileType = $_FILES['bukti_file']['type'];
        $fileExtension = strtolower(pathinfo($_FILES['bukti_file']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['bukti_file']['size'];
        
        // Cek tipe file
        if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Tipe file tidak diizinkan! Hanya JPG, PNG, PDF, DOC, DOCX, XLS, XLSX yang diperbolehkan.');</script>";
            return false;
        }
        
        // Cek ukuran file (maksimal 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.');</script>";
            return false;
        }
        
        // Nama file unik
        $fileName = time() . "_" . basename($_FILES['bukti_file']['name']);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['bukti_file']['tmp_name'], $targetFile)) {
            $bukti_file = $fileName;
        } else {
            echo "<script>alert('Gagal mengupload file!');</script>";
            return false;
        }
    }
    
    // Query insert ke tabel
    $query = "INSERT INTO jurnal_pkl 
              (nama_siswa, kelas_jurusan, tempat_pkl, tanggal, waktu_mulai, waktu_selesai, uraian, alat_bahan, hasil_output, bukti_file, link_1, link_2) 
              VALUES 
              ('$nama_siswa', '$kelas_jurusan', '$tempat_pkl', '$tanggal', '$waktu_mulai', '$waktu_selesai', '$uraian', '$alat_bahan', '$hasil_output', '$bukti_file', '$link_1', '$link_2')";
    
    mysqli_query($conn, $query);
    
    // Debug jika ada error
    if (mysqli_error($conn)) {
        echo "<script>alert('Error SQL: " . mysqli_error($conn) . "');</script>";
        return false;
    }
    
    return mysqli_affected_rows($conn);
}
function editJurnal($data)
{
    global $conn;
    $id = $data["id"];
    $nama_siswa = htmlspecialchars($data["nama_siswa"]);
    $kelas_jurusan = htmlspecialchars($data["kelas_jurusan"]);
    $tempat_pkl = htmlspecialchars($data["tempat_pkl"]);
    $tanggal = htmlspecialchars($data["tanggal"]);
    $waktu_mulai = htmlspecialchars($data["waktu_mulai"]);
    $waktu_selesai = htmlspecialchars($data["waktu_selesai"]);
    $uraian = htmlspecialchars($data["uraian"]);
    $alat_bahan = htmlspecialchars($data["alat_bahan"]);
    $hasil_output = htmlspecialchars($data["hasil_output"]);
    $link_1 = htmlspecialchars($data["link_1"]);
    $link_2 = htmlspecialchars($data["link_2"]);
    
    // Perbaikan: Cek jika old_bukti ada dalam array
    $old_bukti = isset($data["old_bukti"]) ? $data["old_bukti"] : "";
    
    // cek jika user upload file baru
    if ($_FILES["bukti_file"]["error"] === 4) {
        $bukti = $old_bukti;
    } else {
        // Validasi tipe file
        $allowedTypes = [
            'image/jpeg', 
            'image/png', 
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        
        $fileType = $_FILES['bukti_file']['type'];
        $fileExtension = strtolower(pathinfo($_FILES['bukti_file']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['bukti_file']['size'];
        
        // Cek tipe file
        if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, $allowedExtensions)) {
            echo "<script>alert('Tipe file tidak diizinkan! Hanya JPG, PNG, PDF, DOC, DOCX, XLS, XLSX yang diperbolehkan.');</script>";
            return false;
        }
        
        // Cek ukuran file (maksimal 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB.');</script>";
            return false;
        }
        
        // Perbaikan path untuk menghapus file lama
        if (!empty($old_bukti) && file_exists("./assets/jurnal/" . $old_bukti)) {
            unlink("./assets/jurnal/" . $old_bukti);
        }
        
        // Pindahkan file yang diupload
        $targetDir = "./assets/jurnal/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Generate nama file unik
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES["bukti_file"]["tmp_name"], $targetFile)) {
            $bukti = $fileName; // Hanya simpan nama file, bukan path lengkap
        } else {
            echo "<script>alert('Gagal mengupload file bukti.');</script>";
            return false;
        }
    }
    
    $query = "UPDATE jurnal_pkl SET 
                nama_siswa='$nama_siswa', 
                kelas_jurusan='$kelas_jurusan', 
                tempat_pkl='$tempat_pkl', 
                tanggal='$tanggal', 
                waktu_mulai='$waktu_mulai', 
                waktu_selesai='$waktu_selesai', 
                uraian='$uraian', 
                alat_bahan='$alat_bahan', 
                hasil_output='$hasil_output', 
                bukti_file='$bukti',
                link_1='$link_1',
                link_2='$link_2'
              WHERE id=$id";
    
    mysqli_query($conn, $query);
    
    if (mysqli_error($conn)) {
        echo "<script>alert('Error SQL: " . mysqli_error($conn) . "');</script>";
        return false;
    }
    
    return mysqli_affected_rows($conn);
}

// Function Logika Tampil Penilaian
function tampilPenilaian($query)
{
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}

// Function Logika Tambah Penilaian
function tambahPenilaian($data)
{
	global $conn;
	$nisn = htmlspecialchars($data['nisn']);
	$nama = htmlspecialchars($data['nama']);
	$kelas = htmlspecialchars($data['kelas']);
	$konsentrasi = htmlspecialchars($data['konsentrasi']);
	$nilai = htmlspecialchars($data['nilai']);
	$keterangan = htmlspecialchars($data['keterangan']);

	$query = "INSERT INTO penilaian VALUES (null, '$nisn', '$nama', '$kelas', '$konsentrasi', '$nilai', '$keterangan')";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

// Function Logika Edit Penilaian
function editPenilaian($data)
{
	global $conn;
	$id_penilaian = htmlspecialchars($data['id_penilaian']);
	$nisn = htmlspecialchars($data['nisn']);
	$nama = htmlspecialchars($data['nama']);
	$kelas = htmlspecialchars($data['kelas']);
	$konsentrasi = htmlspecialchars($data['konsentrasi']);
	$nilai = htmlspecialchars($data['nilai']);
	$keterangan = htmlspecialchars($data['keterangan']);

	$query = "UPDATE penilaian SET
				nisn = '$nisn',
				nama = '$nama',
				kelas = '$kelas',
				konsentrasi = '$konsentrasi',
				nilai = '$nilai',
				keterangan = '$keterangan'
			  WHERE id_penilaian = $id_penilaian";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

// Function Logika Hapus Penilaian
function hapusPenilaian($id)
{
	global $conn;
	mysqli_query($conn, "DELETE FROM penilaian WHERE id_penilaian = '$id'");
	return mysqli_affected_rows($conn);
}



// upload jurnal siswa
function uploadJurnal($conn,$id_siswa,$fileName){
  mysqli_query($conn,"
      INSERT INTO jurnal_siswa (id_siswa,file_jurnal,tanggal_upload)
      VALUES('$id_siswa','$fileName',NOW())
  ");
}

// simpan nilai jurnal
function beriNilai($conn,$id,$nilai){
  mysqli_query($conn,"
      UPDATE jurnal_siswa SET nilai='$nilai' WHERE id='$id'
  ");
}

//tampilkan kode
// Function Logika Tampil Penilaian
function tampilKode($query)
{
	global $conn;
	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}
// Function Logika Hapus Guru
function hapusKode($id)
{
	// Variable Scope / Lingkup Variabel
	global $conn;

	// Jalankan query hapus data
	mysqli_query($conn, "DELETE FROM k WHERE id_gurupem = '$id'");
	// Kembalikan nilai 1 jika berhasil, dan 0 jika gagal
	return mysqli_affected_rows($conn);
}