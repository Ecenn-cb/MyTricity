<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginForm.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            line-height: 1.6;
        }

        header {
            background-color: #1e1e1e;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #ff4d8b;
            position: relative;
        }

        header h1 {
            color: #ff4d8b;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .kembali-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            background-color: #ff4d8b;
            color: white;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .kembali-btn:hover {
            background-color: #e03e76;
        }

        .container {
            display: flex;
            justify-content: center;
            margin-top: 80px;
        }

        .menu-box {
            display: flex;
            flex-direction: column;
            gap: 25px;
            background: #1c1c1c;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(255, 77, 139, 0.3);
            width: 350px;
            text-align: center;
        }

        .menu-button {
            display: block;
            padding: 15px;
            font-size: 18px;
            background-color: #ff4d8b;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.3s, transform 0.2s;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .menu-button:hover {
            background-color: #e03e76;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 77, 139, 0.4);
        }

        .alert {
            margin-top: 20px;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        .alert.success {
            background-color: #2ecc71;
            color: #fff;
        }

        .alert.error {
            background-color: #e74c3c;
            color: #fff;
        }

        /* Responsif untuk perangkat mobile */
        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }

            .menu-box {
                width: 100%;
            }

            header h1 {
                font-size: 24px;
            }

            .menu-button {
                font-size: 16px;
                padding: 12px;
            }
        }

        .aksi-container {
            text-align: center;
            margin-top: 100px;
            color: white;
        }

        .aksi-container h2 {
            font-size: 26px;
            margin-bottom: 40px;
            color: #fff;
            text-shadow: 1px 1px 4px #ff4d8b;
        }

        .aksi-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .aksi-links a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            background-color: #1e1e1e;
            color: #ff4d8b;
            border: 2px solid #ff4d8b;
            border-radius: 10px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            width: 260px;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .aksi-links a:hover {
            background-color: #ff4d8b;
            color: #fff;
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 77, 139, 0.5);
        }

        .aksi-links a i {
            font-size: 20px;
        }

        .btn-kembali {
            display: inline-block;
            margin: 30px;
            padding: 12px 20px;
            background-color: transparent;
            color: #ff4d8b;
            border: 2px solid #ff4d8b;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-kembali i {
            margin-right: 8px;
        }

        .btn-kembali:hover {
            background-color: #ff4d8b;
            color: #fff;
            box-shadow: 0 0 10px #ff4d8b;
        }
    </style>
</head>
<body>
    <a href="admin_dashboard.php" class="btn-kembali">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
    <div class="aksi-container">
        <h2>Silakan pilih aksi yang ingin dilakukan:</h2>
        <div class="aksi-links">
            <a href="tambahKategori.php"><i class="fas fa-plus-circle"></i> Tambah Kategori</a>
            <a href="tambahProduk.php"><i class="fas fa-shopping-cart"></i> Tambah Produk</a>
            <a href="updateStok.php"><i class="fas fa-tools"></i> Update Stok Produk</a>
        </div>
    </div>
</body>
</html>
