<?php
$host = "localhost";
$user = "root"; // ganti jika user Anda berbeda
$password = ""; // ganti jika password MySQL Anda tidak kosong
$dbname = "ecommerceku"; // nama database Anda

$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// echo "Berhasil";
?>
