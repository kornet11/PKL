-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Okt 2025 pada 06.18
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--aaaaaaa
-- Database: `perpus`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `hak_akses` varchar(50) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `alamat`, `no_telp`, `hak_akses`, `foto`) VALUES
(1, 'admin', '$2y$10$9CBGNzfIJdq.ViqQ9Xx9U.VobYg77px7K2RhiW4AmIcOLQQ8ipV16', 'admin_pkl', 'lowayu\r\n', '081334081074', 'admin', '67dc42cf5a912.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dudi`
--

CREATE TABLE `dudi` (
  `id_dudi` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `pembimbing` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dudi`
--

INSERT INTO `dudi` (`id_dudi`, `nama`, `alamat`, `kontak`, `logo`, `owner`, `jabatan`, `pembimbing`) VALUES
(6, 'dafa', 'g hjwkq', '5647832', '68bcf92226a22_donat.png', 'vfuycdxs', 'vifjcdmks', 'yghcdjs');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gurukaprok`
--

CREATE TABLE `gurukaprok` (
  `id_gurukaprok` int(11) NOT NULL,
  `nip` varchar(255) NOT NULL,
  `namakaprok` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `no_telpon` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gurukaprok`
--

INSERT INTO `gurukaprok` (`id_gurukaprok`, `nip`, `namakaprok`, `jurusan`, `jabatan`, `no_telpon`, `foto`, `password`) VALUES
(5, '12333', 'ainun', 'BUSANA', 'WAKIL_KAPRODI', '0987653332', '68bcf97649b7b_donat.png', '$2y$10$1sE/fVuZw0EzbmALtIp9A.A/GTsdLCr51PEdXGQIoesBE5EwLGrBa'),
(7, '76584930', 'dafa123', 'RPL', 'KAPRODI', '0987653332', '68bd1242212bd_donat.png', '$2y$10$24rQo./UIHCQ/nVAIy2CiOe8PSnppAW9k3QBDXxORqka02bB7zwQ.'),
(8, '76584930', 'yazid', 'RPL', 'KAPRODI', '0987653332', '68d75fd47fc7b_donat.png', '$2y$10$E41JYGJNgU01j2xNr5iqe.ZLQP05piDxX3MLVMPW9GUrQMijfz4BC'),
(9, '12333', 'halo', 'RPL', '', '0987653332', '68d760d2759c5_donat.png', '$2y$10$9JfjafL3J4RWBvtLO80jP.RJExxP63smjwSd0JzqGD5d3/G/ssCwe');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gurupembimbing`
--

CREATE TABLE `gurupembimbing` (
  `id_gurupem` int(11) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `no_telepon` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `jabatan_guru` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `gurupembimbing`
--

INSERT INTO `gurupembimbing` (`id_gurupem`, `nip`, `nama`, `jurusan`, `no_telepon`, `password`, `foto`, `jabatan_guru`) VALUES
(12, '12345', 'udin', 'RPL', '12345', '$2y$10$yRAQBkwfljp55QVsA0Qcb.mKviXnS7Q8iS9cHavmIb2RdjUVFHw8q', '67dd853407232.jpg', 'PRODUKTIF'),
(13, '12333', 'ainun', 'KULINER', '12223', '12345678', '68045ac9a6b03.png', 'PRODUKTIF'),
(14, '12333', 'ainun', 'KULINER', '12223', '12345678', '68045b071e45c.png', 'PRODUKTIF'),
(17, '12333', 'pak husain', 'ATPH', '2734981234', '$2y$10$0EzhOjn9gvrjmBnWdPqFUeiZ7M.li3DOrzxD0wkQcG4hqLzOGpt16', '6818b5333282d.png', 'PRODUKTIF'),
(18, '76584930', 'awik', 'RPL', '098452499494', '$2y$10$BaaqCzYip2yFA836A5IFg.6BL/iwSSG3ckHo9k6tpqCDSBN8y8yXu', '688d78a87fb08.png', 'PRODUKTIF'),
(20, '12345678', 'bu fara', 'ATPH', '08974834874', '$2y$10$jRe/T3t3Ib77OBpXs3Ndr.vTUTAaxFcrOlwksWtxVFJdGv/5gpeLS', '', 'PRODUKTIF'),
(21, '', 'bu nela', 'RPL', '', '$2y$10$yp9eGttXH9BlOz8.GK.ojOuMXEAZwNArCwuerbPEkshyVYpHNd0dK', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `histori_tugas`
--

CREATE TABLE `histori_tugas` (
  `id` int(11) NOT NULL,
  `id_tugas` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `jawaban` varchar(255) DEFAULT NULL,
  `status` enum('Belum','Selesai','Proses') DEFAULT 'Belum',
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `histori_tugas`
--

INSERT INTO `histori_tugas` (`id`, `id_tugas`, `id_siswa`, `jawaban`, `status`, `tanggal_upload`) VALUES
(11, 9, 2, '1758773366_ujian ddpl3.docx', 'Selesai', '2025-09-25 04:09:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_pkl`
--

CREATE TABLE `jurnal_pkl` (
  `id` int(11) NOT NULL,
  `nama_siswa` varchar(200) NOT NULL,
  `kelas_jurusan` varchar(100) DEFAULT NULL,
  `tempat_pkl` varchar(200) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `alat_bahan` text DEFAULT NULL,
  `hasil_output` text DEFAULT NULL,
  `bukti_file` varchar(255) DEFAULT NULL,
  `link_1` varchar(255) DEFAULT NULL,
  `link_2` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurnal_pkl`
--

INSERT INTO `jurnal_pkl` (`id`, `nama_siswa`, `kelas_jurusan`, `tempat_pkl`, `tanggal`, `waktu_mulai`, `waktu_selesai`, `uraian`, `alat_bahan`, `hasil_output`, `bukti_file`, `link_1`, `link_2`, `created_at`) VALUES
(5, 'dafa', 'rpl', 'surabaya', '2025-08-13', '08:31:00', '20:33:00', 'qwfgh', 'waefrgthf', 'dfgh', '1755091907_donat.png', NULL, NULL, '2025-08-13 13:31:47'),
(6, 'AERTYU', 'rpl', 'surabaya', '2000-12-31', '19:34:00', '12:34:00', ',12iu8e3tfce', 'wewrf', 'edwr', '1757295133_donat.png', NULL, NULL, '2025-09-04 04:41:38'),
(7, 'AERTYU', 'rpl', 'surabaya', '2025-12-31', '00:22:00', '02:23:00', ' gcdertyhjnbvf', 'qwertyuikl,mnbvfrt', 'jhgfrtyujnbvfty', '1757294752_JADWAL IMAM SHOLAT WAJIB.pdf', '', '', '2025-09-08 01:25:52'),
(8, 'awik', 'rpl', 'surabaya', '2025-12-31', '00:32:00', '01:32:00', '3123123213we', 'dasdwd', 'dsdwd', '68cea848cee20_1758373960.docx', '', '', '2025-09-08 01:33:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_siswa`
--

CREATE TABLE `jurnal_siswa` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `file_jurnal` varchar(255) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurnal_siswa`
--

INSERT INTO `jurnal_siswa` (`id`, `id_siswa`, `file_jurnal`, `nilai`, `tanggal_upload`) VALUES
(7, 2, '68cf8ff37beb8_JADWAL IMAM SHOLAT WAJIB.pdf', NULL, '2025-09-21 12:41:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi_kategori` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi_kategori`) VALUES
(11, 'romence', 'buku yang menceritakan hubungan'),
(12, 'aksi', 'peperangan, konflik, dan keseriusan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kegiatanpkl`
--

CREATE TABLE `kegiatanpkl` (
  `id_kegiatanpkl` int(11) NOT NULL,
  `pkl_id` varchar(50) NOT NULL,
  `tanggal` varchar(50) NOT NULL,
  `deskripsi` varchar(50) NOT NULL,
  `bukti_kegiatan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kode_register`
--

CREATE TABLE `kode_register` (
  `id` int(11) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `role` enum('gurupem','gurukaprok') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kode_register`
--

INSERT INTO `kode_register` (`id`, `kode`, `role`, `created_at`) VALUES
(24, '76FC68CC98', 'gurupem', '2025-09-25 13:18:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tgl_peminjaman` date NOT NULL,
  `nama_pinjam` varchar(100) NOT NULL,
  `tgl_pengembalian_r` date NOT NULL,
  `tgl_pengembalian_a` date NOT NULL,
  `denda` int(11) NOT NULL,
  `status_peminjaman` int(11) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL,
  `catatan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `anggota_id`, `buku_id`, `tgl_peminjaman`, `nama_pinjam`, `tgl_pengembalian_r`, `tgl_pengembalian_a`, `denda`, `status_peminjaman`, `jumlah_pinjam`, `catatan`) VALUES
(19, 12, 10, '2024-08-08', 'XII RPL', '2024-10-08', '2024-10-10', 0, 2, 0, 'pinjam dulu seratus!!!!'),
(20, 12, 10, '2024-08-08', 'XII RPL', '2024-08-15', '2024-08-14', 3000, 3, 1, 'oke'),
(21, 12, 10, '2024-08-08', 'XII RPL', '2024-08-14', '2024-08-15', 0, 3, 1, 'oke'),
(22, 12, 10, '2024-08-08', 'XII RPL', '2024-08-08', '2024-08-15', 90000, 3, 2, 'oke'),
(23, 12, 11, '2024-08-08', 'XII ATPH', '2024-08-15', '2024-08-23', 0, 1, 1, '-'),
(24, 14, 11, '2024-08-08', 'XII BUSANA', '2024-08-10', '2024-08-13', 15000, 3, 1, '-');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penempatan`
--

CREATE TABLE `penempatan` (
  `id_penempatan` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `dudi_id` int(11) NOT NULL,
  `gurupem_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `penempatan`
--

INSERT INTO `penempatan` (`id_penempatan`, `siswa_id`, `dudi_id`, `gurupem_id`) VALUES
(5, 2, 6, 14);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `nisn` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(30) NOT NULL,
  `konsentrasi` varchar(50) NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penilaian`
--

INSERT INTO `penilaian` (`id_penilaian`, `nisn`, `nama`, `kelas`, `konsentrasi`, `nilai`, `keterangan`) VALUES
(1, '12432443434', 'dafa', 'x', 'rpl', 100.00, 'bagus');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reward`
--

CREATE TABLE `reward` (
  `id_siswa` int(11) NOT NULL,
  `total_tugas_selesai` int(11) DEFAULT 0,
  `sertifikat_terbit` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_peminjaman`
--

CREATE TABLE `riwayat_peminjaman` (
  `id_riwayat` int(11) NOT NULL,
  `pinjam_id` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tgl_peminjaman` date NOT NULL,
  `tgl_pengembalian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `riwayat_peminjaman`
--

INSERT INTO `riwayat_peminjaman` (`id_riwayat`, `pinjam_id`, `anggota_id`, `buku_id`, `tgl_peminjaman`, `tgl_pengembalian`) VALUES
(13, 19, 12, 10, '2024-08-08', '2024-10-10'),
(14, 20, 12, 10, '2024-08-08', '2024-08-14'),
(15, 21, 12, 10, '2024-08-08', '2024-08-15'),
(16, 22, 12, 10, '2024-08-08', '2024-08-15'),
(17, 23, 12, 11, '2024-08-08', '2024-08-23'),
(18, 24, 14, 11, '2024-08-08', '2024-08-13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nisn` varchar(50) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `konsentrasi` varchar(50) NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nisn`, `nama`, `kelas`, `konsentrasi`, `no_telepon`, `password`, `foto`) VALUES
(2, '792387498', 'awik ', 'XI', 'RPL', '083479835', '$2y$10$V91qSQMeiXcOeUQfUbKgUuvVzWq6tdRM.HuR3i3ljTI4gZPjbzDGW', '688b04a79a778.png'),
(4, '124324434342', 'aku', 'XII', 'RPL', '0899477475499', '$2y$10$AaoDxpPO1dLeYMvNQHEh3epAlxSYKgpUHK7wFNG1mujJFs5kQRhm.', '688d785e16cf2.png'),
(11, '124324434342', 'riski', 'XII', 'RPL', '098452499494', '$2y$10$x6UsicSvL5N9mGWR2xw7X.Xxw7X0058WhKG2zCVfiIidXNzDABQ4.', '68baf55e2a01e.png'),
(13, '', 'dafa', '', 'RPL', '', '$2y$10$LoCrScTuPfmcEBZ/HB39buU1JxOVNgo4qgmxaPkwxaywmfi0SRpee', ''),
(14, '124324434342', 'davin', 'XII', 'KULINER', '098452499494', '$2y$10$46W7f9MdmCDmXP3sdG/yj.QnvaFIGIT78wOBDJfhce.UE3HH95V0a', '68cf8a3722f27.png'),
(15, '', 'dafa', '', 'RPL', '', '$2y$10$Ua8fnocgyjI688IyxyOJbOn1nnuQKorXpE5xtW3/lZLYY9PPQGV1m', ''),
(16, '', 'oca', '', 'RPL', '', '$2y$10$UV.tN/.npgYzcTd9fs3tb.oVP6Gjxc4TMqlW7MofKZ3cFaSdBOCx6', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tugas`
--

CREATE TABLE `tugas` (
  `id_tugas` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_deadline` date DEFAULT NULL,
  `jurusan` varchar(50) NOT NULL,
  `id_gurupem` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tugas`
--

INSERT INTO `tugas` (`id_tugas`, `judul`, `deskripsi`, `tanggal_deadline`, `jurusan`, `id_gurupem`) VALUES
(7, 'makala', 'baku', '2025-09-24', 'ATPH', 17),
(8, 'laporan', 'sebisanya', '2025-09-24', 'RPL', 12),
(9, '10001 kisah sedih lagi menyadihkan', 'bagus', '2025-09-26', 'RPL', 21),
(10, 'makala', 'baku', '2025-09-24', 'RPL', 21),
(11, 'makala', 'baku', '2025-09-24', 'RPL', 21);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `dudi`
--
ALTER TABLE `dudi`
  ADD PRIMARY KEY (`id_dudi`);

--
-- Indeks untuk tabel `gurukaprok`
--
ALTER TABLE `gurukaprok`
  ADD PRIMARY KEY (`id_gurukaprok`);

--
-- Indeks untuk tabel `gurupembimbing`
--
ALTER TABLE `gurupembimbing`
  ADD PRIMARY KEY (`id_gurupem`);

--
-- Indeks untuk tabel `histori_tugas`
--
ALTER TABLE `histori_tugas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jurnal_pkl`
--
ALTER TABLE `jurnal_pkl`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jurnal_siswa`
--
ALTER TABLE `jurnal_siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_siswa` (`id_siswa`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kegiatanpkl`
--
ALTER TABLE `kegiatanpkl`
  ADD PRIMARY KEY (`id_kegiatanpkl`);

--
-- Indeks untuk tabel `kode_register`
--
ALTER TABLE `kode_register`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`);

--
-- Indeks untuk tabel `penempatan`
--
ALTER TABLE `penempatan`
  ADD PRIMARY KEY (`id_penempatan`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `dudi_id` (`dudi_id`,`gurupem_id`),
  ADD KEY `siswa_id_2` (`siswa_id`,`dudi_id`,`gurupem_id`),
  ADD KEY `guru_id` (`gurupem_id`);

--
-- Indeks untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id_penilaian`);

--
-- Indeks untuk tabel `reward`
--
ALTER TABLE `reward`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indeks untuk tabel `riwayat_peminjaman`
--
ALTER TABLE `riwayat_peminjaman`
  ADD PRIMARY KEY (`id_riwayat`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- Indeks untuk tabel `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id_tugas`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `dudi`
--
ALTER TABLE `dudi`
  MODIFY `id_dudi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `gurukaprok`
--
ALTER TABLE `gurukaprok`
  MODIFY `id_gurukaprok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `gurupembimbing`
--
ALTER TABLE `gurupembimbing`
  MODIFY `id_gurupem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `histori_tugas`
--
ALTER TABLE `histori_tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `jurnal_pkl`
--
ALTER TABLE `jurnal_pkl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `jurnal_siswa`
--
ALTER TABLE `jurnal_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `kegiatanpkl`
--
ALTER TABLE `kegiatanpkl`
  MODIFY `id_kegiatanpkl` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kode_register`
--
ALTER TABLE `kode_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `penempatan`
--
ALTER TABLE `penempatan`
  MODIFY `id_penempatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `riwayat_peminjaman`
--
ALTER TABLE `riwayat_peminjaman`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id_tugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `jurnal_siswa`
--
ALTER TABLE `jurnal_siswa`
  ADD CONSTRAINT `jurnal_siswa_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penempatan`
--
ALTER TABLE `penempatan`
  ADD CONSTRAINT `penempatan_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penempatan_ibfk_3` FOREIGN KEY (`dudi_id`) REFERENCES `dudi` (`id_dudi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penempatan_ibfk_4` FOREIGN KEY (`gurupem_id`) REFERENCES `gurupembimbing` (`id_gurupem`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
