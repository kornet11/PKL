<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login'])) {
  echo "<script>document.location.href = '../index.php';</script>";
  exit;
}

if(isset($_POST['judul'])){
    $nama = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $jurusan = $_POST['jurusan'];
    $deadline = $_POST['tanggal_deadline'];
    $id_gurupem = $_SESSION['id_gurupem'];

    mysqli_query($conn, "INSERT INTO tugas (judul, deskripsi, jurusan, tanggal_deadline, id_gurupem) 
                        VALUES ('$nama','$deskripsi','$jurusan','$deadline','$id_gurupem')");
    header("Location: dashboard_guru.php");
}
?>
