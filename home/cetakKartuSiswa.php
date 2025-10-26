<?php
// Pastikan session aktif sebelum mengecek session vars
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php'; // pakai functions.php di folder home
// Pastikan user login dan punya hak akses guru/admin/kaprodi
if (!isset($_SESSION['login']) || !in_array($_SESSION['hak_akses'] ?? '', ['gurupem','gurukaprok','admin','siswa'])) {
    header('Location: ../index.php');
    exit;
}

// Ambil koneksi mysqli dari functions.php
// Ambil id siswa dari query string
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'ID siswa tidak valid.';
    header('Location: ../siswa.php');
    exit;
}

// Ambil data siswa dari tabel siswa (menggunakan mysqli)
$stmt = mysqli_prepare($conn, "SELECT * FROM siswa WHERE id_siswa = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

if (!$siswa) {
    $_SESSION['error'] = 'Siswa tidak ditemukan.';
    header('Location: ../siswa.php');
    exit;
}

// Buat data QR (tanpa menyimpan ke DB) — gunakan id_siswa + nama
$qr_data = 'siswa:' . $siswa['id_siswa'] . '|' . $siswa['nama'];
$qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($qr_data);

?>

<style>
    /* Kartu Siswa Styles */
    :root {
        --primary-color: #2E8B57; /* SeaGreen */
        --secondary-color: #3CB371; /* MediumSeaGreen */
        --accent-color: #90EE90; /* LightGreen */
        --text-color: #333;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s ease;
    }
    
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .student-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        position: relative;
        animation: fadeInUp 0.6s ease-out;
        margin-bottom: 2rem;
    }
    
    /* Header Section - Green Area */
    .card-header {
        background: var(--primary-color);
        color: white;
        padding: 2rem;
        position: relative;
        text-align: center;
    }
    
    .card-header::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="%232E8B57" opacity="0.1"/><circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="0.5" opacity="0.3"/></svg>');
        opacity: 0.1;
    }
    
    .school-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .card-subtitle {
        font-size: 1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    
    /* Body Section */
    .card-body {
        padding: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }
    
    /* Photo Section */
    .photo-section {
        flex: 0 0 180px;
        text-align: center;
    }
    
    .student-photo {
        width: 180px;
        height: 220px;
        border-radius: 1rem;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        margin-bottom: 1rem;
        background-color: #f8f9fa;
    }
    
    .photo-frame {
        position: relative;
        display: inline-block;
    }
    
    .photo-frame::after {
        content: "";
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border: 2px solid var(--primary-color);
        border-radius: 1rem;
        z-index: -1;
    }
    
    /* Info Section */
    .info-section {
        flex: 1;
        min-width: 250px;
    }
    
    .student-info {
        background: #f8f9fa;
        border-radius: 1rem;
        padding: 1.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .info-row {
        display: flex;
        margin-bottom: 1rem;
        align-items: flex-start;
    }
    
    .info-row:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 120px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-value {
        flex: 1;
        color: var(--text-color);
    }
    
    .info-label i {
        color: var(--primary-color);
    }
    
    /* QR Code Section */
    .qr-section {
        flex: 0 0 150px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .qr-code {
        width: 120px;
        height: 120px;
        background: white;
        border: 2px solid var(--primary-color);
        border-radius: 0.5rem;
        padding: 0.5rem;
        display: inline-block;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 0.5rem;
    }
    
    .qr-code img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .qr-label {
        font-size: 0.8rem;
        color: var(--text-color);
        font-weight: 600;
    }
    
    /* Action Buttons */
    .action-section {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
    
    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        border: none;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(46, 139, 87, 0.3);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: white;
    }
    
    .btn-warning:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            flex-direction: column;
            text-align: center;
        }
        
        .photo-section {
            margin: 0 auto;
        }
        
        .info-row {
            flex-direction: column;
            text-align: left;
        }
        
        .info-label {
            min-width: auto;
            margin-bottom: 0.25rem;
        }
        
        .qr-section {
            margin-top: 1rem;
        }
        
        .action-section {
            flex-direction: column;
        }
    }
    
    /* Print Styles */
    @media print {
        body {
            background: white;
            padding: 0;
        }
        
        .student-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .action-section {
            display: none;
        }
    }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-lines-fill me-2"></i>Detail Siswa</h2>
        <div>
            <a href="../edit_siswa.php?id=<?php echo $siswa['id_siswa']; ?>" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <a href="../siswa.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Student Card -->
    <div class="student-card">
        <!-- Header Section -->
        <div class="card-header">
            <div class="school-name">YAYASAN PONDOK PESANTREN MAMBA'UL IHSAN</div>
            <div class="card-title">KARTU PESERTA DIDIK</div>
            <div class="card-subtitle">TAHUN PELAJARAN <?= date('Y') ?>/<?= date('Y') + 1 ?></div>
        </div>
        
        <!-- Body Section -->
        <div class="card-body">
            <!-- Photo Section -->
            <div class="photo-section">
                <div class="photo-frame">
                    <?php if (!empty($siswa['foto'])): ?>
                        <img src="./assets/img/siswa/<?= htmlspecialchars($siswa['foto']) ?>" alt="Foto Siswa" class="student-photo">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/180x220?text=Foto+Siswa" alt="Foto Siswa" class="student-photo">
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Info Section -->
            <div class="info-section">
                <div class="student-info">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-person-fill"></i> Nama
                        </div>
                        <div class="info-value">
                            <strong><?= htmlspecialchars($siswa['nama']) ?></strong>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-hash"></i> Username
                        </div>
                        <div class="info-value">
                            <?= htmlspecialchars($siswa['nisn'] ?: '-') ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-building"></i> Jurusan
                        </div>
                        <div class="info-value">
                            <?= htmlspecialchars($siswa['konsentrasi']) ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-people-fill"></i> Kelas
                        </div>
                        <div class="info-value">
                            <?= htmlspecialchars($siswa['kelas']) ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-label">
                            <i class="bi bi-envelope-fill"></i> Email
                        </div>
                        <div class="info-value">
                            <?= htmlspecialchars($siswa['no_telepon'] ?? '-') ?>
                        </div>
                    </div>
                    
                    <!-- Jika ingin menambah status, sesuaikan di DB siswa -->
                </div>
            </div>
            
            <!-- QR Code Section -->
            <div class="qr-section">
                <div class="qr-code">
                    <img src="<?= $qr_url ?>" alt="QR Code">
                </div>
                <div class="qr-label">QR Code Identitas</div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="action-section">
        <a href="siswa.php" class="btn-action btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Siswa
        </a>
        <a href="../edit_siswa.php?id=<?= $siswa['id_siswa'] ?>" class="btn-action btn-warning">
            <i class="bi bi-pencil-square"></i> Edit Data Siswa
        </a>
        <button type="button" onclick="window.print()" class="btn-action btn-primary">
            <i class="bi bi-printer"></i> Cetak Kartu Siswa
        </button>
    </div>
</div>
