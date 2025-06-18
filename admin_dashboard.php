<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gaming Section</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/adminDashboard.css">

    <style>
        .btn-group a {
            display: inline-block;
            padding: 10px 20px;
            margin-left: 10px;
            border: 2px solid #ff4d8b;
            background-color: #000000;
            color: #ff4d8b;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-group a:hover {
            background-color: #ff4d8b;
            color: #ffffff;
        }
    </style>

</head>
<body>
    <header>
        <h1><i class="fas fa-gamepad"></i> Admin Dashboard</h1>
        <div class="btn-group">
            <a href="index.php" class="kembali-btn">Kembali</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="container">
        <div class="grid">
            <a href="kelolaProduk.php" class="card">
                <div class="icon"><i class="fas fa-box"></i></div>
                <h2>Kelola Sistem</h2>
                <p>Tambah Kategori, Produk, Stok. (Ubah, Hapus)</p>
            </a>

            <a href="info_pembelian.php" class="card">
                <div class="icon"><i class="fas fa-receipt"></i></div>
                <h2>Info Pembelian</h2>
                <p>Mengatur barang yang dibeli konsumen</p>
            </a>

            <a href="adminNews.php" class="card">
                <div class="icon"><i class="fas fa-box"></i></div>
                <h2>Edit News</h2>
                <p>Berita Terbaru Tricity</p>
            </a>
        </div>
    </div>
</body>
</html>
