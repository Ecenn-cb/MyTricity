<?php
session_start();
require 'koneksiDB.php'; // pastikan file koneksi database kamu benar

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: LoginForm.php');
    exit();
}

$username = $_SESSION['username'];

// Ambil data user dari database (jika diperlukan)
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $namaLengkap = $user['full_name']; // sesuaikan dengan nama kolom di tabelmu
} else {
    $namaLengkap = $username; // fallback jika tidak ditemukan
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <h1>Selamat datang, <?php echo htmlspecialchars($namaLengkap); ?>!</h1>

    <a href="index.php">Kembali</a>
</body>
</html>
