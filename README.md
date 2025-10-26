# Aplikasi PKL (Praktik Kerja Lapangan)

Deskripsi singkat
- Aplikasi manajemen PKL untuk sekolah: manajemen admin, guru pembimbing, guru kaprok, siswa, DUDI, absensi (QR & GPS), dan laporan.
- Dibangun dengan PHP (mysqli), HTML/CSS, dan aset statis di folder `assets`.

Fitur utama
- Manajemen user (admin, siswa, guru pembimbing, guru kaprok).
- Absensi via QR code dan upload QR.
- Tracking GPS dan rekap harian.
- Upload dokumen/laporan.
- Dashboard analitik dasar.

Persyaratan
- Windows / Linux dengan PHP 7.x atau 8.x.
- MySQL / MariaDB.
- XAMPP / LAMP / WAMP sesuai kebutuhan.
- Browser modern (Chrome, Firefox, Edge).

Instalasi cepat
1. Clone atau copy folder proyek ke direktori server web, mis.:
   - c:\xampp\htdocs\PKL1
2. Jalankan XAMPP dan aktifkan Apache + MySQL.
3. Import database:
   - Gunakan phpMyAdmin atau mysql CLI untuk mengimport file SQL (jika tersedia di repo, contoh: `database/pkl.sql`).
4. Sesuaikan koneksi database:
   - Buka `c:\xampp\htdocs\PKL1\home\functions.php` dan atur host, user, password, database sesuai environment.
5. Buka aplikasi:
   - Akses http://localhost/PKL1/ (atau path sesuai setup).
6. Buat akun admin awal lewat phpMyAdmin atau skrip migrasi bila belum tersedia.

Struktur direktori penting (singkat)
- home/ — halaman utama, includes, fungsi, dan menu (sidebar).
  - home/menu.php — komponen sidebar dan script toggle.
  - home/functions.php — koneksi DB dan helper (sesuaikan).
- assets/ — CSS, JS, gambar, template (contoh: assets/template/logout-alert.php).
- uploads/ — file yang diupload pengguna.
- index.php, halaman-halaman CRUD dan absensi (scan_qr.php, upload_qr.php, dll).

Penggunaan & catatan
- Role yang tersedia: admin, siswa, gurupem, gurukaprok. Set hak akses pada tabel user.
- Sidebar toggle:
  - Toggle disimpan di localStorage key `sidebarCollapsed` (nilai '1' atau '0').
  - Tombol hamburger harus memiliki salah satu selector yang didukung: `#sidebarToggle`, `.sidebar-toggle`, `.btn-sidebarToggle`, `[data-toggle="sidebar"]`, `[data-sidebar-toggle]`, atau `.navbar-toggler`.
  - Contoh tombol di navbar:
    <button id="sidebarToggle" class="btn btn-sm btn-link"><i class="fas fa-bars"></i></button>
- Foto profil diambil dari folder `assets/img/{admin|siswa|guru}/` sesuai role.

Troubleshooting
- Sidebar hamburger tidak merespon:
  - Pastikan tombol ada di DOM dan memiliki id/class yang cocok (lihat selector di atas).
  - Cek console browser untuk error JS.
  - Pastikan file `home/menu.php` yang memuat script toggle sudah di-include di layout Anda.
- Koneksi database error:
  - Periksa kredensial di `home/functions.php`.
  - Pastikan database ter-import dan tabel ada.
- Gambar default:
  - Jika foto tidak tampil, pastikan path `assets/img/default.png` tersedia dan permission file benar.

Kontribusi
- Buat issue atau pull request dengan deskripsi perubahan.
- Ikuti struktur kode dan naming convention yang sudah ada.

Lisensi
- (Tambahkan lisensi proyek di sini jika ada, mis. MIT)

...end...
