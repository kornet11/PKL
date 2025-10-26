<?php
session_start();
require 'home/functions.php';

// Cek cookie (remember me)
if (isset($_COOKIE['unik']) && isset($_COOKIE['key'])) {
    $id = $_COOKIE['unik'];
    $key = $_COOKIE['key'];

    $result = mysqli_query($conn, "SELECT * FROM admin WHERE id_admin = $id");
    $row = mysqli_fetch_assoc($result);

    if ($key === hash('sha256', $row['username'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_admin'] = $row['id_admin'];
        $_SESSION['nama_admin'] = $row['nama_lengkap'];
        $_SESSION['hak_akses'] = $row['hak_akses'];
        $_SESSION['jurusan'] = NULL; // admin tidak terikat jurusan
    }
}

// Cek apakah sudah ada session login
if (isset($_SESSION['login'])) {
    header('Location: home/index.php');
    exit;
}

if (isset($_POST["login"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query gabungan untuk semua role + jurusan
    $query = "
    (SELECT id_admin AS id,
            username COLLATE utf8mb4_unicode_ci AS username,
            password, hak_akses,
            'admin' AS role,
            NULL AS jurusan
     FROM admin
     WHERE username = '$username')
    UNION
    (SELECT id_siswa AS id,
            nama COLLATE utf8mb4_unicode_ci AS username,
            password, 'siswa' AS hak_akses,
            'siswa' AS role,
            konsentrasi AS jurusan
     FROM siswa
     WHERE nama = '$username')
    UNION
    (SELECT id_gurupem AS id,
            nama COLLATE utf8mb4_unicode_ci AS username,
            password, 'gurupem' AS hak_akses,
            'gurupem' AS role,
            jurusan
     FROM gurupembimbing
     WHERE nama = '$username')
    UNION
    (SELECT id_gurukaprok AS id,
            namakaprok COLLATE utf8mb4_unicode_ci AS username,
            password, 'gurukaprok' AS hak_akses,
            'gurukaprok' AS role,
            jurusan
     FROM gurukaprok
     WHERE namakaprok = '$username')
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['hak_akses'] = $user['hak_akses'];
        $_SESSION['jurusan'] = $user['jurusan']; // simpan jurusan/konsentrasi

        // Simpan identitas sesuai role
        if ($user['role'] === "admin") {
            $_SESSION['id_admin'] = $user['id'];
            $_SESSION['nama_admin'] = $user['username'];
             echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $user["username"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./home/index.php";
                });
            });
        </script>';
        exit();
        } elseif ($user['role'] === "siswa") {
            $_SESSION['id_siswa'] = $user['id'];
            $_SESSION['nama_siswa'] = $user['username'];
             echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $user["username"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./home/dashboard_siswa.php";
                });
            });
        </script>';
        exit();
        } elseif ($user['role'] === "gurupem") {
            $_SESSION['id_gurupem'] = $user['id'];
            $_SESSION['nama_gurupem'] = $user['username'];
             echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $user["username"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./home/dashboard_guru.php";
                });
            });
        </script>';
        exit();
        } elseif ($user['role'] === "gurukaprok") {
            $_SESSION['id_gurukaprok'] = $user['id'];
            $_SESSION['namakaprok'] = $user['username'];
        } echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Welcome!",
                    text: "Selamat Datang ' . $user["username"] . ', Kamu Berhasil Login!",
                    icon: "success",
                }).then(function() {
                    window.location.href = "./home/dasboard_guru.php";
                });
            });
        </script>';
        exit();

        // Remember me
        if (isset($_POST['remember'])) {
            setcookie('unik', $user['id'], time() + 60 * 60 * 24 * 30);
            setcookie('key', hash('sha256', $user['username']), time() + 60 * 60 * 24 * 30);
        }

       
    }

    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>PKL | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4caf50;
            --danger-color: #f44336;
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

        .login-container {
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

        .card-header .login-title {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .form-check {
            margin-bottom: 20px;
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

        .links-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .links-container a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .links-container a:hover {
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

        @media (max-width: 576px) {
            .card-header {
                padding: 20px 15px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .links-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <div class="logo-container">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="system-name">Sistem Aplikasi PKL</h3>
                <p class="login-title">Masuk ke Akun Anda</p>
            </div>
            
            <div class="card-body">
                <?php if (isset($error)) : ?>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: "error",
                                title: "Login Gagal",
                                text: "Username atau Password Salah!",
                                confirmButtonText: "Coba Lagi",
                                confirmButtonColor: "#4361ee"
                            }).then(function() {
                                window.location.href = "index.php";
                            });
                        });
                    </script>
                <?php endif; ?>
                
                <form action="" method="post">
                    <div class="form-floating mb-3">
                        <input class="form-control" id="username" name="username" type="text" placeholder="Username" required />
                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input class="form-control" id="password" name="password" type="password" placeholder="Password" required />
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" id="remember" name="remember" type="checkbox" />
                        <label class="form-check-label" for="remember">
                            <i class="fas fa-check-circle me-1"></i>Ingat saya
                        </label>
                    </div>
                    
                    <div class="links-container">
                        <a href="forgot-password.php"><i class="fas fa-key me-1"></i>Lupa Password?</a>
                        <a href="register.php"><i class="fas fa-user-plus me-1"></i>Daftar Akun</a>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-primary mt-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistem PKL. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efek animasi saat input focus
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>