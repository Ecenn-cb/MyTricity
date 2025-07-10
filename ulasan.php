<?php
session_start();
include 'koneksiDB.php';

if (!isset($_GET['id'])) {
    die("Produk tidak ditemukan!");
}

$id_product = $_GET['id'];

// Ambil nama produk
$stmt = $conn->prepare("SELECT name FROM products WHERE id_product = ?");
$stmt->bind_param("i", $id_product);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    die("Produk tidak ditemukan!");
}

// Ambil semua ulasan
$reviewQuery = $conn->prepare("
    SELECT r.rating, r.comment, r.created_at, u.username
    FROM reviews r
    JOIN users u ON r.id_user = u.id_user
    WHERE r.id_product = ?
    ORDER BY r.created_at DESC
");
$reviewQuery->bind_param("i", $id_product);
$reviewQuery->execute();
$reviews = $reviewQuery->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ulasan - <?= htmlspecialchars($produk['name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f0f0f;
            color: #fff;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #121212;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #0ff3;
        }

        h1 {
            color: #f0f;
            margin-bottom: 20px;
        }

        .review {
            background: #1a1a1a;
            border-left: 5px solid #0ff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .review .username {
            font-weight: bold;
            color: #0ff;
        }

        .review .rating {
            color: #ffd700;
        }

        .review .date {
            font-size: 12px;
            color: #aaa;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
        }

        .back:hover {
            color: #f0f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ulasan untuk: <?= htmlspecialchars($produk['name']) ?></h1>

        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review">
                    <div class="username"><?= htmlspecialchars($review['username']) ?></div>
                    <div class="rating">Rating: <?= $review['rating'] ?> / 5</div>
                    <div class="date"><?= date("d M Y H:i", strtotime($review['created_at'])) ?></div>
                    <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada ulasan untuk produk ini.</p>
        <?php endif; ?>

        <a href="detailProduk.php?id=<?= $id_product ?>" class="back">← Kembali ke Detail Produk</a>
    </div>
</body>
</html>
