<?php
// Penghubung antar file pada PHP
require 'home/functions.php';

// Cek apakah tombol ubah ditekan
if (isset($_POST['ubah'])) {
    // Tangkap username dan password dari inputan form user
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Cek ada apa tidak username yang di inputkan dengan yang ada di database
    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) === 1) {
        // Update Password
        $updateP = "UPDATE admin SET password = '$password' WHERE username = '$username'";

        if (mysqli_query($conn, $updateP)) {
            echo "
				<script>
					alert('Password Berhasil Di Reset. Silahkan Login Kembali!');
					document.location.href = 'index.php';
				</script>
			";
        } else {
            echo mysqli_error($conn);
        }
    } else {
        echo "
			<script>
				alert('Username Tidak Di Temukan.');
			</script>
		";
    }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Perpustakaan | Forgot Password</title>
    <link href="home/assets/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="home/assets/css/styles.css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="./home/assets/img/logo/favicon.ico">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Forgot Password</h3>
                                    <p class="text-center">Masukkan Username Anda Untuk Mereset Password Anda Dan Masukkan Password Baru.</p>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name="username" type="text" placeholder="Masukkan Username Saat Login" autocomplete="off" required />
                                            <label for="username">Masukkan Username Saat Login</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" name="password" type="password" placeholder="New Password" autocomplete="off" required />
                                            <label for="password">New Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
                                            <a class="small" href="index.php">Kembali</a>
                                        </div>
                                        <button type="submit" name="ubah" class="btn btn-primary mb-3">Ubah</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="home/assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="home/assets/js/scripts.js"></script>
</body>

</html>