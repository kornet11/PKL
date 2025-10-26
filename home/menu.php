<?php
// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Jika belum login -> redirect dengan header
if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
// Pastikan koneksi tersedia (functions.php biasanya menyediakan $conn)
if (!isset($conn)) {
    // functions.php berada di folder home
    require_once 'functions.php';
}

 $akses = $_SESSION['hak_akses'];
 $id_user = $_SESSION['id_admin'] ?? $_SESSION['id_siswa'] ?? $_SESSION['id_gurupem'] ?? $_SESSION['id_gurukaprok'] ?? null;
 $foto = "assets/img/default.png";
 $nama = "User";
 $role = $akses;
// Pastikan id_user adalah integer untuk keamanan
 $id_user = intval($id_user);
// Ambil data profil sesuai role dengan prepared statement
if ($akses == "admin") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE id_admin = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $foto = !empty($row['foto']) ? "assets/img/admin/" . $row['foto'] : "assets/img/default.png";
    $nama = $_SESSION['nama_admin'];
} elseif ($akses == "siswa") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id_siswa = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $foto = !empty($row['foto']) ? "assets/img/siswa/" . $row['foto'] : "assets/img/default.png";
    $nama = $_SESSION['nama_siswa'];
} elseif ($akses == "gurupem") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM gurupembimbing WHERE id_gurupem = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $foto = !empty($row['foto']) ? "assets/img/guru/" . $row['foto'] : "assets/img/default.png";
    $nama = $_SESSION['nama_gurupem'];
} elseif ($akses == "gurukaprok") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM gurukaprok WHERE id_gurukaprok = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $foto = !empty($row['foto']) ? "assets/img/guru/" . $row['foto'] : "assets/img/default.png";
    $nama = $_SESSION['namakaprok'];
}
// Deteksi halaman aktif
 $page = basename($_SERVER['PHP_SELF']);
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion" id="sidenavAccordion">
        <div class="profile mb-3 mt-2 ms-3 d-flex align-items-center">
            <!-- Foto klik menuju biodata -->
            <?php if ($akses == "siswa") { ?>
                <a href="biodata.php?id=<?= $id_user; ?>">
                    <img src="<?= $foto ?>" alt="Profile" class="img-thumbnail rounded-circle me-2" width="75">
                </a>
            <?php } else { ?>
                <img src="<?= $foto ?>" alt="Profile" class="img-thumbnail rounded-circle me-2" width="75">
            <?php } ?>
            <div class="sub-profile d-flex flex-column">
                <strong><?= $nama; ?></strong>
                <small class="<?= $akses ?>"><?= $role; ?></small>
            </div>
        </div>

        <div class="sb-sidenav-menu">
            <div class="nav">
                <?php if ($_SESSION['hak_akses'] == 'admin' || $_SESSION['hak_akses'] == 'gurukaprok'): ?>
                    <a class="nav-link <?= $page == 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="fas fa-fw fa-th-large me-2"></i>Dashboard
                    </a>
                <?php endif; ?>

                <?php if ($akses == "admin") { ?>
                    <a class="nav-link <?= $page == 'analitik1.php' ? 'active' : '' ?>" href="analitik1.php">
                        <i class="fa-solid fa-chart-simple me-2"></i>Analitik
                    </a>

                    <a class="nav-link collapsed <?= in_array($page, ['guru-pembimbing.php', 'siswa.php', 'dudi.php', 'penempatan.php', 'gurukaprok.php', 'kode_register.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false">
                        <i class="fas fa-fw fa-bookmark me-2"></i>Master Data
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-fw fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?= in_array($page, ['guru-pembimbing.php', 'siswa.php', 'dudi.php', 'penempatan.php', 'gurukaprok.php', 'kode_register.php']) ? 'show' : '' ?>" id="collapseLayouts" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= $page == 'guru-pembimbing.php' ? 'active' : '' ?>" href="guru-pembimbing.php">
                                <i class="fas fa-fw fa-chalkboard-user me-2"></i>Guru Pembimbing
                            </a>
                            <a class="nav-link <?= $page == 'siswa.php' ? 'active' : '' ?>" href="siswa.php">
                                <i class="fas fa-fw fa-users me-2"></i>Siswa
                            </a>
                            <a class="nav-link <?= $page == 'dudi.php' ? 'active' : '' ?>" href="dudi.php">
                                <i class="fas fa-solid fa-building-user me-2"></i>DUDI
                            </a>
                            <a class="nav-link <?= $page == 'penempatan.php' ? 'active' : '' ?>" href="penempatan.php">
                                <i class="fas fa-solid fa-building-user me-2"></i>Penempatan
                            </a>
                            <a class="nav-link <?= $page == 'gurukaprok.php' ? 'active' : '' ?>" href="gurukaprok.php">
                                <i class="fas fa-fw fa-chalkboard-user me-2"></i>Guru Kaprok
                            </a>
                            <a class="nav-link <?= $page == 'kode_register.php' ? 'active' : '' ?>" href="kode_register.php">
                                <i class="fa-solid fa-key me-2"></i>Kode Register
                            </a>
                        </nav>
                    </div>

                    <a class="nav-link <?= $page == 'admin.php' ? 'active' : '' ?>" href="admin.php">
                        <i class="fas fa-fw fa-user me-2"></i>Admin
                    </a>

                    <!-- Menu Absensi untuk Admin -->
                    <a class="nav-link collapsed <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseAbsensi" aria-expanded="false">
                        <i class="fas fa-fw fa-qrcode me-2"></i>Absensi
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-fw fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'show' : '' ?>" id="collapseAbsensi" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= $page == 'scan_qr.php' ? 'active' : '' ?>" href="scan_qr.php">
                                <i class="fas fa-qrcode me-2"></i>Scan QR Code
                            </a>
                            <!-- <a class="nav-link <?= $page == 'upload_qr.php' ? 'active' : '' ?>" href="upload_qr.php">
                                <i class="fas fa-upload me-2"></i>Upload QR Code
                            </a> -->
                            <a class="nav-link <?= $page == 'tracking_gps.php' ? 'active' : '' ?>" href="tracking_gps.php">
                                <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS
                            </a>
                            <a class="nav-link <?= $page == 'recap_harian.php' ? 'active' : '' ?>" href="recap_harian.php">
                                <i class="fas fa-chart-bar me-2"></i>Recap Harian
                            </a>
                        </nav>
                    </div>

                    <a class="nav-link collapsed <?= in_array($page, ['uploads.php', 'kegiatan.php', 'siswa_jurnal.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseLaporan" aria-expanded="false">
                        <i class="fas fa-fw fa-file me-2"></i>Laporan
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-fw fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?= in_array($page, ['uploads.php', 'kegiatan.php', 'siswa_jurnal.php']) ? 'show' : '' ?>" id="collapseLaporan" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= $page == 'uploads.php' ? 'active' : '' ?>" href="uploads.php">
                                <i class="fa-solid fa-file-word me-2"></i>Upload
                            </a>
                            <!-- <a class="nav-link <?= $page == 'kegiatan.php' ? 'active' : '' ?>" href="kegiatan.php">
                                <i class="fa-solid fa-book me-2"></i>Jurnal Kegiatan
                            </a>
                            <a class="nav-link <?= $page == 'siswa_jurnal.php' ? 'active' : '' ?>" href="siswa_jurnal.php">
                                <i class="fa-solid fa-book me-2"></i>Jurnal Penilaian
                            </a> -->
                        </nav>
                    </div>
                <?php } ?>

                <?php if ($akses == "siswa") { ?>

                     <a class="nav-link <?= $page == 'dashboard_siswa.php' ? 'active' : '' ?>" href="dashboard_siswa.php">
                        <i class="fas fa-fw fa-chalkboard-user me-2"></i>Dashboard
                    </a>
                    
                    <!-- Menu Absensi untuk Siswa -->
                    <a class="nav-link <?= $page == 'generate_qr.php' ? 'active' : '' ?>" href="generate_qr.php">
                        <i class="fas fa-qrcode me-2"></i>Generate QR Code
                    </a>
                    
                    <a class="nav-link <?= $page == 'guru-pembimbing.php' ? 'active' : '' ?>" href="guru-pembimbing.php">
                        <i class="fas fa-fw fa-chalkboard-user me-2"></i>Guru Pembimbing
                    </a>                   
                    <a class="nav-link <?= $page == 'penempatan.php' ? 'active' : '' ?>" href="penempatan.php">
                        <i class="fas fa-fw fa-building-user me-2"></i>Penempatan
                    </a>
                    <!-- <a class="nav-link <?= $page == 'jurnal_siswa.php' ? 'active' : '' ?>" href="jurnal_siswa.php">
                        <i class="fa-solid fa-book me-2"></i>Jurnal Kegiatan
                    </a>
                    <a class="nav-link <?= $page == 'siswa_jurnal.php' ? 'active' : '' ?>" href="siswa_jurnal.php">
                        <i class="fa-solid fa-book me-2"></i>Jurnal Penilaian
                    </a> -->
                    <a class="nav-link <?= $page == 'uploads.php' ? 'active' : '' ?>" href="uploads.php">
                        <i class="fa-solid fa-file-word me-2"></i>Upload
                    </a>
                <?php } ?>

                <?php if ($akses == "gurupem") { ?>
                    <a class="nav-link <?= $page == 'siswa.php' ? 'active' : '' ?>" href="siswa.php">
                        <i class="fas fa-fw fa-users me-2"></i>Daftar Siswa
                    </a>
                    <a class="nav-link <?= $page == 'dashboard_guru.php' ? 'active' : '' ?>" href="dashboard_guru.php">
                        <i class="fas fa-fw fa-users me-2"></i>Dashboard
                    </a>
                    
                    <!-- Menu Absensi untuk Guru -->
                    <a class="nav-link collapsed <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseAbsensiGuru" aria-expanded="false">
                        <i class="fas fa-fw fa-qrcode me-2"></i>Absensi
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-fw fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'show' : '' ?>" id="collapseAbsensiGuru" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= $page == 'scan_qr.php' ? 'active' : '' ?>" href="scan_qr.php">
                                <i class="fas fa-qrcode me-2"></i>Scan QR Code
                            </a>
                            <a class="nav-link <?= $page == 'upload_qr.php' ? 'active' : '' ?>" href="upload_qr.php">
                                <i class="fas fa-upload me-2"></i>Upload QR Code
                            </a>
                            <a class="nav-link <?= $page == 'tracking_gps.php' ? 'active' : '' ?>" href="tracking_gps.php">
                                <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS
                            </a>
                            <a class="nav-link <?= $page == 'recap_harian.php' ? 'active' : '' ?>" href="recap_harian.php">
                                <i class="fas fa-chart-bar me-2"></i>Recap Harian
                            </a>
                        </nav>
                    </div>
                    
                    <a class="nav-link <?= $page == 'penempatan.php' ? 'active' : '' ?>" href="penempatan.php">
                        <i class="fa-solid fa-book me-2"></i>Penempatan
                    </a>
                    <!-- <a class="nav-link <?= $page == 'siswa_jurnal.php' ? 'active' : '' ?>" href="siswa_jurnal.php">
                        <i class="fa-solid fa-book me-2"></i>Jurnal Penilaian
                    </a> -->
                <?php } ?>

                <?php if ($akses == "gurukaprok") { ?>
                    <a class="nav-link <?= $page == 'siswa.php' ? 'active' : '' ?>" href="siswa.php">
                        <i class="fas fa-fw fa-users me-2"></i>Data Siswa
                    </a>
                    <a class="nav-link <?= $page == 'dudi.php' ? 'active' : '' ?>" href="dudi.php">
                        <i class="fas fa-solid fa-building-user me-2"></i>DUDI
                    </a>
                    <a class="nav-link <?= $page == 'guru-pembimbing.php' ? 'active' : '' ?>" href="guru-pembimbing.php">
                        <i class="fas fa-fw fa-chalkboard-user me-2"></i>Guru Pembimbing
                    </a>
                    
                    <!-- Menu Absensi untuk Kaprok -->
                    <a class="nav-link collapsed <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'active' : '' ?>"
                        href="#" data-bs-toggle="collapse" data-bs-target="#collapseAbsensiKaprok" aria-expanded="false">
                        <i class="fas fa-fw fa-qrcode me-2"></i>Absensi
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-fw fa-angle-down"></i></div>
                    </a>
                    <div class="collapse <?= in_array($page, ['scan_qr.php', 'upload_qr.php', 'tracking_gps.php', 'recap_harian.php']) ? 'show' : '' ?>" id="collapseAbsensiKaprok" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link <?= $page == 'scan_qr.php' ? 'active' : '' ?>" href="scan_qr.php">
                                <i class="fas fa-qrcode me-2"></i>Scan QR Code
                            </a>
                            <a class="nav-link <?= $page == 'upload_qr.php' ? 'active' : '' ?>" href="upload_qr.php">
                                <i class="fas fa-upload me-2"></i>Upload QR Code
                            </a>
                            <a class="nav-link <?= $page == 'tracking_gps.php' ? 'active' : '' ?>" href="tracking_gps.php">
                                <i class="fas fa-map-marker-alt me-2"></i>Tracking GPS
                            </a>
                            <a class="nav-link <?= $page == 'recap_harian.php' ? 'active' : '' ?>" href="recap_harian.php">
                                <i class="fas fa-chart-bar me-2"></i>Recap Harian
                            </a>
                        </nav>
                    </div>
                <?php } ?>

                <div class="sb-sidenav-menu-heading text-light">Lainnya</div>
                <a class="nav-link btn-logout" href="../logout.php">
                    <i class="fas fa-fw fa-sign-out me-2"></i>Keluar
                </a>
            </div>
        </div>
    </nav>
</div>
<style>
    :root {
	/* configurable values */
	--sidebar-width: 250px;
	--sidebar-collapsed-width: 80px;
	--sidebar-bg: linear-gradient(180deg, #1a237e 0%, #0d47a1 100%);
	--sidebar-hover: rgba(255,255,255,0.12);
	--sidebar-active: rgba(255,255,255,0.18);
	--sidebar-text: #fff;
	--sidebar-icon: rgba(255,255,255,0.85);
	--transition-speed: 0.28s;
}

/* Sidebar styling (scoped) - tidak mengubah layout content */
#layoutSidenav_nav {
    box-sizing: border-box;
    width: var(--sidebar-width);
    background: var(--sidebar-bg);
    transition: width var(--transition-speed) ease-in-out;
    overflow-x: hidden;
    overflow-y: auto;
    position: relative;
    z-index: 1030;
}

/* Saat collapsed, kecilkan lebar sidebar saja */
body.sb-sidenav-toggled #layoutSidenav_nav {
    width: var(--sidebar-collapsed-width);
}

/* Style untuk sidebar (tetap seperti sebelumnya) */
    .sb-sidenav {
        display: flex;
        flex-direction: column;
        height: 100%;
        color: var(--sidebar-text);
    }

    .sb-sidenav-menu {
        flex-grow: 1;
        overflow-y: auto;
        padding-bottom: 1rem;
    }

    .profile {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: left;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 0 0 20px 0;
        margin-bottom: 1.5rem;
        backdrop-filter: blur(5px);
    }

    .profile img {
        width: 80px;
        height: 80px;
        border: 3px solid var(--accent-light);
        object-fit: cover;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
        transition: all 0.3s ease;
    }

    .profile img:hover {
        transform: scale(1.05);
        border-color: white;
    }

    .sub-profile strong {
        display: block;
        font-size: 1.15rem;
        margin-bottom: 0.35rem;
        color: white;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .sub-profile small {
        font-size: 0.85rem;
        opacity: 0.95;
        padding: 0.3rem 0.85rem;
        border-radius: 20px;
        display: inline-block;
        color: white;
        font-weight: 500;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
    }

    /* Warna badge berdasarkan role */
    .sub-profile small.admin {
        background-color: var(--admin-color);
    }

    .sub-profile small.siswa {
        background-color: var(--siswa-color);
    }

    .sub-profile small.gurupem,
    .sub-profile small.gurukaprok {
        background-color: var(--guru-color);
    }

    .nav {
        flex-direction: column;
        padding: 0.5rem 0;
    }

    .nav-link {
        color: var(--sidebar-text);
        padding: 0.9rem 1.3rem;
        margin: 0.3rem 0.85rem;
        border-radius: 14px;
        display: flex;
        align-items: center;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        font-weight: 500;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 6px;
        background: var(--accent-medium);
        opacity: 0;
        transition: all 0.25s ease;
        border-radius: 0 6px 6px 0;
    }

    .nav-link:hover {
        background: var(--sidebar-hover);
        color: #fff;
        text-decoration: none;
        transform: translateX(6px);
    }

    .nav-link:hover::before {
        opacity: 1;
        width: 6px;
    }

    .nav-link.active {
        background: var(--sidebar-active);
        color: #fff;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }

    .nav-link.active::before {
        opacity: 1;
        width: 6px;
        background: white;
    }

    .sb-nav-link-icon {
        margin-right: 0.85rem;
        font-size: 1.25rem;
        color: var(--sidebar-icon);
        min-width: 24px;
        text-align: center;
        transition: all 0.25s ease;
    }

    .nav-link:hover .sb-nav-link-icon {
        transform: scale(1.15);
        color: white;
    }

    .nav-link.active .sb-nav-link-icon {
        color: white;
    }

    .sb-sidenav-menu-heading {
        padding: 0.85rem 1.3rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        opacity: 0.75;
        margin-top: 1.8rem;
        margin-bottom: 0.6rem;
        color: white;
        font-weight: 600;
    }

    .sb-sidenav-menu-nested {
        margin-left: 1.8rem;
        padding-left: 0.7rem;
        border-left: 3px solid rgba(255, 255, 255, 0.12);
        border-radius: 0 0 0 15px;
    }

    .sb-sidenav-menu-nested .nav-link {
        font-size: 0.92rem;
        padding: 0.7rem 1.1rem;
        margin: 0.2rem 0.6rem;
    }

    .sb-sidenav-collapse-arrow {
        margin-left: auto;
        transition: transform 0.25s ease;
        font-size: 0.95rem;
    }

    .collapsed .sb-sidenav-collapse-arrow {
        transform: rotate(-90deg);
    }

    /* Scrollbar styling */
    .sb-sidenav-menu::-webkit-scrollbar {
        width: 8px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 10px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background: var(--accent-medium);
        border-radius: 10px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb:hover {
        background: var(--accent-dark);
    }

    /* Efek glow untuk sidebar */
    #layoutSidenav_nav::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 1px;
        height: 100%;
        background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.1), transparent);
        z-index: 1;
    }
</style>

<!-- Simplified script: hanya toggle class + persist, tanpa mengatur margin langsung -->
<script>
/* filepath: c:\xampp\htdocs\PKL1\home\menu.php
   Robust sidebar toggle: pusatkan toggle/save, gunakan delegation + attach langsung saat DOM siap,
   dan dukung beberapa selector umum.
*/
(function () {
    function applySavedState() {
        try {
            var collapsed = localStorage.getItem('sidebarCollapsed');
            if (collapsed === '1') {
                document.body.classList.add('sb-sidenav-toggled');
            } else {
                document.body.classList.remove('sb-sidenav-toggled');
            }
        } catch (e) {}
    }

    function saveState(isCollapsed) {
        try {
            localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
        } catch (e) {}
    }

    function toggleSidebar() {
        var isCollapsed = document.body.classList.toggle('sb-sidenav-toggled');
        saveState(isCollapsed);
    }

    function onDelegatedClick(e) {
        var selector = '#sidebarToggle, .sidebar-toggle, .btn-sidebarToggle, [data-toggle="sidebar"], [data-sidebar-toggle], .navbar-toggler';
        var btn = e.target && e.target.closest ? e.target.closest(selector) : null;
        if (!btn) return;
        // Hanya preventDefault untuk anchor yang mengarah ke '#'
        if (btn.tagName === 'A' && btn.getAttribute('href') === '#') {
            e.preventDefault();
        }
        toggleSidebar();
    }

    // Apply saved state asap
    applySavedState();

    // Global delegation (bekerja walau tombol dimuat belakangan)
    document.addEventListener('click', onDelegatedClick, false);

    // Saat DOM siap, pasang listener langsung jika tombol sudah ada (mengurangi delay)
    document.addEventListener('DOMContentLoaded', function () {
        var direct = document.querySelector('#sidebarToggle, .sidebar-toggle, .btn-sidebarToggle, [data-toggle="sidebar"], [data-sidebar-toggle], .navbar-toggler');
        if (direct) {
            direct.addEventListener('click', function (e) {
                if (direct.tagName === 'A' && direct.getAttribute('href') === '#') {
                    e.preventDefault();
                }
                toggleSidebar();
            }, false);
        }
    });
})();
</script>
<?php include './assets/template/logout-alert.php'; ?>