<?php
session_start();
require 'functions.php';

// Cek hak akses
if (!isset($_SESSION['hak_akses'])) {
    echo "<script>alert('Akses ditolak!');location.href='/.302.php';</script>";
    exit;
}

// Tentukan ID siswa
if ($_SESSION['hak_akses'] == 'siswa') {
    if (!isset($_SESSION['id_siswa'])) {
        die("ID siswa tidak ditemukan di session.");
    }
    $id = $_SESSION['id_siswa']; // siswa hanya boleh lihat dirinya sendiri
} else {
    if (!isset($_GET['id'])) {
        die("ID siswa tidak ditemukan di URL.");
    }
    $id = $_GET['id']; // admin/guru bisa akses semua siswa
}

// Query data siswa
$query = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa = '$id' LIMIT 1");
$siswa = mysqli_fetch_assoc($query);

if (!$siswa) {
    die("Data siswa tidak ditemukan.");
}

// ======================
// Update Username
// ======================
if (isset($_POST['update_username'])) {
    $username_baru = mysqli_real_escape_string($conn, $_POST['nama']);
    mysqli_query($conn, "UPDATE siswa SET nama='$username_baru' WHERE id_siswa='$id'");
    echo "<script>alert('Nama berhasil diperbarui');location.href='biodata.php?id=$id';</script>";
}

// ======================
// Update Password
// ======================
if (isset($_POST['update_password'])) {
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];

    // cek password lama
    if (password_verify($pass_lama, $siswa['password'])) {
        $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE siswa SET password='$hash' WHERE id_siswa='$id'");
        echo "<script>alert('Password berhasil diganti');location.href='biodata.php?id=$id';</script>";
    } else {
        echo "<script>alert('Password lama salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #7209b7;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background-color: white;
            flex: 1;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }
        
        .profile-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,133.3C960,128,1056,96,1152,90.7C1248,85,1344,107,1392,117.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
        }
        
        .profile-header img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        
        .profile-header h3 {
            margin-top: 1.5rem;
            font-weight: 700;
            font-size: 1.8rem;
            position: relative;
            z-index: 1;
        }
        
        .profile-header p {
            margin-top: 0.5rem;
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .profile-info {
            padding: 2rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            font-size: 1.5rem;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .action-buttons {
            padding: 0 2rem 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .btn-custom {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, #f7b733, #fc4a1a);
            color: white;
        }
        
        .btn-warning-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(252, 74, 26, 0.3);
            background: linear-gradient(135deg, #fc4a1a, #e63946);
            color: white;
        }
        
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 16px 16px 0 0;
            border: none;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        .modal-footer {
            border: none;
        }
        
        /* Footer Styles */
        .footer {
            background-color: var(--dark-color);
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: auto;
        }
        
        .footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .footer .social-icons {
            margin-top: 1rem;
        }
        
        .footer .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        
        .footer .social-icons a:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard_siswa.php">
                <i class="bi bi-mortarboard-fill me-2"></i>
                Sistem Informasi Akademik
            </a>
            <div class="d-flex">
                <span class="navbar-text">
                    <i class="bi bi-person-circle me-2"></i>
                    <?= $siswa['nama']; ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <div class="profile-header">
            <img src="assets/img/siswa/<?= $siswa['foto'] ?: 'default.png'; ?>" alt="Foto Siswa">
            <h3><?= $siswa['nama']; ?></h3>
            <p class="badge bg-light text-dark fs-6"><?= $siswa['kelas']; ?> - <?= $siswa['konsentrasi']; ?></p>
        </div>
        
        <div class="profile-info">
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-card-text"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">NISN</div>
                    <div class="info-value"><?= $siswa['nisn']; ?></div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">No HP</div>
                    <div class="info-value"><?= $siswa['no_telepon']; ?></div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#editUsernameModal">
                <i class="bi bi-pencil-square"></i> Edit Username
            </button>
            <button class="btn btn-custom btn-warning-custom" data-bs-toggle="modal" data-bs-target="#editPasswordModal">
                <i class="bi bi-key-fill"></i> Ganti Password
            </button>
        </div>
    </div>

    <!-- Modal Edit Username -->
    <div class="modal fade" id="editUsernameModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Username</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= $siswa['nama']; ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_username" class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Password -->
    <div class="modal fade" id="editPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key-fill me-2"></i>Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password_lama" class="form-label">Password Lama</label>
                        <div class="input-group">
                            <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleOldPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password_baru" class="form-label">Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password_baru" id="password_baru" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password minimal 8 karakter</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update_password" class="btn btn-warning-custom">
                        <i class="bi bi-shield-lock me-1"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Template -->
    <?php include 'assets/template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('toggleOldPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password_lama');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password_baru');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>