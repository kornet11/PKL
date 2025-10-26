<?php 
// Syarat menggunakan session
session_start();

// Menghapus session
$_SESSION = [];
session_unset();
session_destroy();

// Menghapus cookie
setcookie('unik', '', time() - 3600);
setcookie('key', '', time() - 3600);

// Pindahkan ke halaman login
header("Location: index.php");

?>