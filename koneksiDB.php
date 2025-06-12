<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';       // Ganti jika user berbeda
$password = '';       // Ganti jika ada password
$dbname = 'ecommerceku'; // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    // Jangan tampilkan detail error ke user di production
    die("Koneksi ke database gagal.");
}

// Set charset UTF-8 (untuk mendukung karakter internasional)
$conn->set_charset('utf8');
?>
