<?php
// Mulai session jika ingin pakai pesan atau simpan user
session_start();

$host = 'localhost';
$db   = 'ecommerceku';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$error = '';
$success = '';

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $full_name  = trim($_POST['full_name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $role       = 'customer'; // Selalu set ke customer

    // Validasi sederhana
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Username, email, dan password wajib diisi!";
    } else {
        // Cek apakah username atau email sudah digunakan
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username atau email sudah digunakan.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role)
                                   VALUES (:username, :email, :password, :full_name, :phone, :address, :role)");
            $stmt->execute([
                'username'  => $username,
                'email'     => $email,
                'password'  => $hashedPassword,
                'full_name' => $full_name,
                'phone'     => $phone,
                'address'   => $address,
                'role'      => $role
            ]);

            $success = "Registrasi berhasil! Silakan login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Tricity</title>
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

        .register-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #2980b9;
        }

        .btn-login {
            background-color: #2ecc71;
        }

        .btn-login:hover {
            background-color: #27ae60;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 10px;
        }

        .success-message {
            color: #2ecc71;
            text-align: center;
            margin-bottom: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .register-batal{
            padding-top: 20px;
        }

        .register-batal a{
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register Akun Tricity</h2>

        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label>Username*</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Email*</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password*</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name">
            </div>

            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="phone">
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="address" rows="3"></textarea>
            </div>

            <!-- Hapus pilihan role dari form -->
            <input type="hidden" name="role" value="customer">

            <button type="submit">Daftar</button>
            
            <div class="login-link">
                Sudah punya akun? <a href="loginForm.php">Login di sini</a>
            </div>

            <div class="register-batal">
                <a href="index.php">Kembali?</a>
            </div>
        </form>
    </div>
</body>
</html>