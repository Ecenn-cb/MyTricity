<?php
session_start();
$error = '';
$success = '';

// Cek apakah form sudah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Validasi sederhana
    if (empty($email)) {
        $error = "Email tidak boleh kosong.";
    } else {
        // Simulasi pengecekan email (nanti bisa dihubungkan ke DB)
        // Misal kita hanya kasih notifikasi berhasil
        $success = "Jika email $email terdaftar, link reset password telah dikirim.";
        
        // Pada sistem nyata, di sini akan ada pengecekan ke database dan pengiriman email
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Tricity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header i {
            font-size: 50px;
            color: #e67e22;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: #e74c3c;
        }

        .success {
            color: #2ecc71;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #e67e22;
            text-decoration: none;
        }
    </style>
<body>
    <div class="container">
        <div class="header">
    </head>
            <i class="fas fa-unlock-alt"></i>
            <h2>Lupa Password</h2>
        </div>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="forgot_password.php" method="post">
            <div class="form-group">
                <label for="email">Masukkan Email Anda</label>
                <input type="email" name="email" id="email" placeholder="contoh@email.com" required>
            </div>

            <button type="submit">Kirim Link Reset</button>
        </form>

        <div class="back-link">
            <a href="loginForm.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </div>
    </div>
</body>
</html>
