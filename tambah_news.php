<?php
// tambah_news.php
session_start();
include 'koneksiDB.php';

if ($_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = null;

    if ($_FILES['image']['name']) {
        $targetDir = "uploads/";
        $image = basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $stmt = $conn->prepare("INSERT INTO news (title, content, image, published_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $title, $content, $image);
    $stmt->execute();

    header("Location: adminNews.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Berita</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #121212;
            font-family: 'Orbitron', sans-serif;
            color: white;
            padding: 40px;
        }
        .admin-container {
            max-width: 600px;
            margin: auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 0, 128, 0.2);
        }
        h2 {
            color: #ff00aa;
            text-align: center;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: #2a2a2a;
            border: none;
            color: white;
            border-radius: 6px;
        }
        button {
            background-color: #ff00aa;
            color: white;
            border: none;
            padding: 12px 20px;
            margin-top: 20px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #ff33bb;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            color: #ff00aa;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="admin-container">
    <h2>Tambah Berita Baru</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Judul Berita" required>
        <textarea name="content" placeholder="Isi berita" rows="8" required></textarea>
        <input type="file" name="image">
        <button type="submit">Simpan</button>
    </form>
    <a href="adminNews.php" class="back-button">&larr; Kembali ke Dashboard Berita</a>
</div>
</body>
</html>
