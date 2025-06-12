<?php
session_start();
require 'koneksiDB.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header('Location: LoginForm.php');
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// Ambil data user saat ini
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $current_full_name = $user['full_name'];
    $current_address = $user['address'];
} else {
    $error = "User tidak ditemukan!";
}

// Proses form jika ada data dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password jika diisi
    if (!empty($new_password)) {
        if ($new_password !== $confirm_password) {
            $error = "Password baru dan konfirmasi password tidak cocok!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET full_name = ?, address = ?, password = ? WHERE username = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssss", $full_name, $address, $hashed_password, $username);
        }
    } else {
        // Update tanpa password
        $update_query = "UPDATE users SET full_name = ?, address = ? WHERE username = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sss", $full_name, $address, $username);
    }

    if (empty($error)) {
        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui!";
            // Update data current
            $current_full_name = $full_name;
            $current_address = $address;

        } else {
            $error = "Gagal memperbarui profil: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #0d0d0d;
            color: #fff;
            font-family: 'Orbitron', sans-serif;
        }
        .profile-container {
            max-width: 800px;
            margin: 80px auto;
            background: #1c1c1c;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px #ff4d8b;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header i {
            font-size: 60px;
            color: #ff4d8b;
        }
        .profile-header h2 {
            margin-top: 10px;
            font-size: 28px;
            color: #ff4d8b;
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
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }
        .back-btn, .save-btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .back-btn {
            display: inline-block;
            background: #ff4d8b;
            color: #000;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }
        .back-btn:hover {
            background: #ff0066;
            color: #fff;
        }
        .save-btn {
            background: #ff4d8b;
            color: #000;
        }
        .save-btn:hover {
            background: #ff0066;
            color: #fff;
        }
        .error {
            color: #ff4d8b;
            margin-top: 10px;
        }
        .success {
            color: #0ff;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <i class="fas fa-user-edit"></i>
        <h2>Ubah Profil</h2>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="profile-info">
        <label>Nama Lengkap</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($current_full_name) ?>" required>

        <label>Alamat</label>
        <textarea name="address" rows="3" required><?= htmlspecialchars($current_address) ?></textarea>

        <label>Password Baru (Kosongkan jika tidak ingin mengubah)</label>
        <input type="password" name="new_password">

        <label>Konfirmasi Password Baru</label>
        <input type="password" name="confirm_password">

        <div class="button-container">
            <a href="profile.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            <button type="submit" class="save-btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>