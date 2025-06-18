<?php
include 'koneksiDB.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM news WHERE id_news = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    die("Berita tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($news['title']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
        }

        h1 {
            color: #0ff;
            font-size: 32px;
        }

        .published {
            color: #888;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #333;
        }

        p {
            line-height: 1.8;
            color: #ddd;
            font-size: 16px;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #f0f;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .back-link:hover {
            background-color: #0ff;
            color: #111;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($news['title']) ?></h1>
        <p class="published">Diterbitkan pada: <?= htmlspecialchars($news['published_at']) ?></p>

        <?php if ($news['image']): ?>
            <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="Gambar Berita">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($news['content'])) ?></p>

        <a href="news.php" class="back-link">← Kembali ke Daftar Berita</a>
    </div>
</body>
</html>
