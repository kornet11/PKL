<?php
require 'home/functions.php';

// Inisialisasi variabel error
 $error = '';
 $kodetdkada = false;

if (isset($_POST['register'])) {
    $role = $_POST['role'];
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jurusan = $_POST['jurusan'] ?? '';
    $kode_register = $_POST['kode_register'] ?? '';

    // Validasi role guru harus pakai kode
    if ($role == "gurupem" || $role == "gurukaprok") {
        if (empty($kode_register)) {
            $error = "Kode registrasi diperlukan untuk role ini";
        } else {
            $cek = mysqli_query($conn, "SELECT * FROM kode_register WHERE kode='$kode_register' AND role='$role' LIMIT 1");
            if (mysqli_num_rows($cek) === 0) {
                $kodetdkada = true;
            } else {
                // hapus kode setelah dipakai biar tidak bisa dipakai lagi
                $row = mysqli_fetch_assoc($cek);
                mysqli_query($conn, "DELETE FROM kode_register WHERE id='{$row['id']}'");
            }
        }
    }

    // Jika tidak ada error, lakukan registrasi
    if (!$kodetdkada && empty($error)) {
        // Insert sesuai role
        if ($role == "siswa") {
            $query = "INSERT INTO siswa (nisn, nama, kelas, konsentrasi, no_telepon, password, foto) 
                      VALUES ('', '$username', '', '$jurusan', '', '$password', '')";
        } elseif ($role == "gurupem") {
            $query = "INSERT INTO gurupembimbing (nip, nama, jurusan, no_telepon, password, foto, jabatan_guru) 
                      VALUES ('', '$username', '$jurusan', '', '$password', '', '')";
        } elseif ($role == "gurukaprok") {
            $query = "INSERT INTO gurukaprok (nip, namakaprok, jurusan, jabatan, no_telpon, foto, password) 
                      VALUES ('', '$username', '$jurusan', '', '', '', '$password')";
        }

        if (mysqli_query($conn, $query)) {
            echo '
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    Swal.fire({
                        title: "Registrasi Berhasil!",
                        text: "Akun Anda telah dibuat, Silahkan Login!",
                        icon: "success",
                        confirmButtonText: "Login Sekarang",
                        confirmButtonColor: "#4361ee"
                    }).then(function() {
                        window.location.href = "./index.php";
                    });
                });
            </script>';
            exit; // Keluar setelah menampilkan alert
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>PKL | Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --info-color: #03a9f4;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff; /* Diubah menjadi putih */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            max-width: 450px;
            width: 100%;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-bottom: none;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .card-header .system-name {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .card-header .register-title {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .form-floating label {
            padding: 12px 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }

        .alert-danger {
            border-radius: 10px;
            border: none;
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-color);
            font-weight: 500;
        }

        .text-muted {
            font-size: 0.85rem;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color); /* Diubah menjadi warna gelap agar kontras dengan background putih */
        }

        .login-link a {
            color: var(--primary-color); /* Diubah menjadi warna primer agar kontras dengan background putih */
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-container i {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .swal2-popup {
            border-radius: 15px;
        }

        .swal2-styled.swal2-confirm {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 10px;
        }

        .form-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
            z-index: 10;
            pointer-events: none;
            opacity: 0.7;
            transition: all 0.3s;
        }

        .form-floating:focus-within .form-icon {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            transition: all 0.3s;
            background-color: #e9ecef;
        }

        .password-strength.weak {
            background-color: var(--danger-color);
            width: 33%;
        }

        .password-strength.medium {
            background-color: var(--warning-color);
            width: 66%;
        }

        .password-strength.strong {
            background-color: var(--success-color);
            width: 100%;
        }

        @media (max-width: 576px) {
            .card-header {
                padding: 20px 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .form-control, .form-select {
                padding: 12px 15px;
            }
            
            .form-floating label {
                padding: 12px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <div class="logo-container">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="system-name">Sistem Aplikasi PKL</h3>
                <p class="register-title">Buat Akun Baru</p>
            </div>
            
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                        <div class="password-strength" id="password-strength"></div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select name="jurusan" class="form-select" id="jurusan" required>
                            <option value="" selected disabled>-- Pilih jurusan --</option>
                            <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                            <option value="ATPH">Agribisnis Tanaman Pangan dan Holtikultura (ATPH)</option>
                            <option value="KULINER">Kuliner</option>
                            <option value="BUSANA">Busana</option>
                        </select>
                        <label for="jurusan"><i class="fas fa-school me-2"></i>Jurusan</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select name="role" id="role" class="form-select" onchange="toggleKodeField()" required>
                            <option value="" selected disabled>-- Pilih Role --</option>
                            <option value="siswa">Siswa</option>
                            <option value="gurupem">Guru Pembimbing</option>
                            <option value="gurukaprok">Guru Kaprok</option>
                        </select>
                        <label for="role"><i class="fas fa-user-tag me-2"></i>Role</label>
                    </div>

                    <!-- Kode Registrasi -->
                    <div class="form-floating mb-3" id="kodeDiv" style="display:none;">
                        <input type="text" name="kode_register" class="form-control" placeholder="Kode Registrasi">
                        <label for="kode_register"><i class="fas fa-key me-2"></i>Kode Registrasi</label>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Hanya untuk Guru Pembimbing / Kaprok
                        </small>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary mt-3">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </button>
                </form>
                
                <div class="login-link">
                    <p>Sudah punya akun? <a href="./index.php">Login di sini</a></p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistem PKL. All rights reserved.</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function toggleKodeField() {
            let role = document.getElementById("role").value;
            let kodeDiv = document.getElementById("kodeDiv");
            let kodeInput = document.querySelector('input[name="kode_register"]');
            
            if (role === "gurupem" || role === "gurukaprok") {
                kodeDiv.style.display = "block";
                kodeInput.setAttribute('required', 'required');
            } else {
                kodeDiv.style.display = "none";
                kodeInput.removeAttribute('required');
            }
        }
        
        // Panggil fungsi saat halaman dimuat untuk mengatur state awal
        document.addEventListener("DOMContentLoaded", function() {
            toggleKodeField();
            
            // Efek animasi saat input focus
            document.querySelectorAll('.form-control, .form-select').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const passwordStrength = document.getElementById('password-strength');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Check password strength
                if (password.length >= 8) strength += 1;
                if (password.match(/[a-z]+/)) strength += 1;
                if (password.match(/[A-Z]+/)) strength += 1;
                if (password.match(/[0-9]+/)) strength += 1;
                if (password.match(/[$@#&!]+/)) strength += 1;
                
                // Update strength indicator
                passwordStrength.className = 'password-strength';
                if (password.length > 0) {
                    if (strength < 3) {
                        passwordStrength.classList.add('weak');
                    } else if (strength < 4) {
                        passwordStrength.classList.add('medium');
                    } else {
                        passwordStrength.classList.add('strong');
                    }
                }
            });
        });
        
        // Tampilkan alert jika kode tidak valid
        <?php if ($kodetdkada): ?>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "error",
                title: "Kode Tidak Valid",
                text: "Kode registrasi yang Anda masukkan tidak ditemukan atau sudah digunakan!",
                confirmButtonText: "Coba Lagi",
                confirmButtonColor: "#4361ee"
            });
        });
        <?php endif; ?>
    </script>
</body>

</html>