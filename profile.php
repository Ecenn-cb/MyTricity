<?php
session_start();
require 'koneksiDB.php'; // pastikan koneksiDB.php berfungsi dengan baik

// Cek login
if (!isset($_SESSION['username'])) {
    header('Location: LoginForm.php');
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $full_name = $user['full_name'];
    $phone = $user['phone'];
    $address = $user['address'];
} else {
    echo "User tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0d0d0d; /* Tetap dark background */
            color: #fff;
            font-family: 'Orbitron', sans-serif;
        }
        .profile-container {
            max-width: 800px;
            margin: 80px auto;
            background: #1c1c1c;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px #ff4d8b; /* Neon glow diubah ke pink (#ff4d8b) */
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header i {
            font-size: 60px;
            color: #ff4d8b; /* Warna icon diubah ke pink (#ff4d8b) */
        }
        .profile-header h2 {
            margin-top: 10px;
            font-size: 28px;
            color: #ff4d8b; /* Judul diubah ke pink (#ff4d8b) */
        }
        .profile-info label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        .profile-info input, .profile-info textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 8px;
        }
        .profile-info input[readonly] {
            background: #222;
            color: #aaa;
        }
        .back-btn {
            margin-top: 25px;
            display: inline-block;
            background: #ff4d8b; /* Tombol diubah ke pink (#ff4d8b) */
            color: #000; /* Teks tombol tetap hitam */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #ff0066; /* Warna hover lebih gelap (#ff0066) */
            color: #fff;
        }

        /* Container untuk mengatur tata letak tombol */
        .button-container {
            display: flex;
            justify-content: space-between; /* Membuat tombol terpisah kiri-kanan */
            margin-top: 25px;
        }

        /* Style tombol "Ubah Profile" (sama seperti back-btn) */
        .ubah-btn {
            margin-top: 25px;
            display: inline-block;
            background: #ff4d8b;
            color: #000;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .ubah-btn:hover {
            background: #ff0066;
            color: #fff;
        }

        /* (Opsional) Jika ingin ikon lebih rapi */
        .ubah-btn i, .back-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <i class="fas fa-user-circle"></i>
        <h2>Profil Pengguna</h2>
    </div>
    <div class="profile-info">
        <label>Username</label>
        <input type="text" value="<?= htmlspecialchars($username) ?>" readonly>

        <label>Nama Lengkap</label>
        <input type="text" value="<?= htmlspecialchars($full_name) ?>" readonly>

        <label>No. Telepon</label>
        <input type="text" value="<?= htmlspecialchars($phone) ?>" readonly>

        <label>Alamat</label>
        <textarea rows="3" readonly><?= htmlspecialchars($address) ?></textarea>

        <div class="button-container">
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            <a href="ubahProfile.php" class="ubah-btn"><i class="fas fa-user-edit"></i> Ubah Profile</a>
        </div>
    </div>
</div>

</body>
</html>
