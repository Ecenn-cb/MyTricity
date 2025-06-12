<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user'])) {
    echo "Anda harus login terlebih dahulu.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil daftar review user
$reviewed = [];
$revQuery = $conn->prepare("SELECT id_product FROM reviews WHERE id_user = ?");
$revQuery->bind_param("i", $id_user);
$revQuery->execute();
$resReview = $revQuery->get_result();
while ($row = $resReview->fetch_assoc()) {
    $reviewed[] = $row['id_product'];
}

// Cek apakah user memilih filter status
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$allowedStatus = ['paid', 'confirmed', 'shipped', 'accepted', 'completed'];

if ($statusFilter && in_array($statusFilter, $allowedStatus)) {
    $query = $conn->prepare("
        SELECT o.id_order, o.status AS order_status, od.quantity, od.price, p.name AS product_name, p.image, p.id_product
        FROM orders o
        JOIN order_details od ON o.id_order = od.id_order
        JOIN products p ON od.id_product = p.id_product
        WHERE o.id_user = ? AND o.status = ?
        ORDER BY o.id_order DESC
    ");
    $query->bind_param("is", $id_user, $statusFilter);
} else {
    $query = $conn->prepare("
        SELECT o.id_order, o.status AS order_status, od.quantity, od.price, p.name AS product_name, p.image, p.id_product
        FROM orders o
        JOIN order_details od ON o.id_order = od.id_order
        JOIN products p ON od.id_product = p.id_product
        WHERE o.id_user = ? AND o.status != 'pending'
        ORDER BY o.id_order DESC
    ");
    $query->bind_param("i", $id_user);
}
$query->execute();
$result = $query->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Proses konfirmasi selesai
if (isset($_GET['confirm']) && is_numeric($_GET['confirm'])) {
    $id_order = intval($_GET['confirm']);

    $check = $conn->prepare("SELECT * FROM orders WHERE id_order = ? AND id_user = ? AND status = 'accepted'");
    $check->bind_param("ii", $id_order, $id_user);
    $check->execute();
    $resultCheck = $check->get_result();

    if ($resultCheck->num_rows > 0) {
        $update = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id_order = ?");
        $update->bind_param("i", $id_order);
        $update->execute();
    }

    header("Location: produk_saya.php");
    exit;
}

// Proses kirim review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $id_product = intval($_POST['id_product']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $checkReview = $conn->prepare("SELECT * FROM reviews WHERE id_user = ? AND id_product = ?");
    $checkReview->bind_param("ii", $id_user, $id_product);
    $checkReview->execute();
    $existing = $checkReview->get_result();

    if ($existing->num_rows === 0 && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (id_product, id_user, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $id_product, $id_user, $rating, $comment);
        $stmt->execute();
    }

    header("Location: produk_saya.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk Saya</title>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: auto;
            padding: 40px 20px;
        }

        h1 {
            text-align: center;
            color: #ff4d8b;
            margin-bottom: 30px;
        }

        form select {
            padding: 6px;
            border-radius: 6px;
            border: none;
            background-color: #1e1e1e;
            color: #fff;
        }

        .order-list {
            display: grid;
            gap: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            background-color: #1e1e1e;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 0 10px #ff4d8b40;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .order-details {
            flex-grow: 1;
        }

        .order-details h3 {
            margin: 0;
            color: #fff;
        }

        .order-details p {
            margin: 5px 0;
            color: #ccc;
        }

        .status {
            font-weight: bold;
            color: #ff4d8b;
            text-align: right;
        }

        .no-orders {
            text-align: center;
            color: #ccc;
            margin-top: 50px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            color: #aaa;
        }

        .back-link:hover {
            color: #fff;
        }

        .confirm-button {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #ff4d8b;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9em;
            transition: background-color 0.2s;
        }

        .confirm-button:hover {
            background-color: #e03d75;
        }

        .review-form {
            margin-top: 10px;
            background-color: #2a2a2a;
            padding: 10px;
            border-radius: 8px;
        }

        .review-form label {
            color: #fff;
        }

        .review-form select,
        .review-form textarea {
            width: 100%;
            margin-bottom: 8px;
            border-radius: 6px;
            padding: 5px;
            border: none;
        }

        .review-button {
            background-color: #ff4d8b;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .review-button:hover {
            background-color: #e03d75;
        }
    </style>
</head>
<body>
    <h1>🛒 Produk Saya</h1>

    <!-- Filter Status -->
    <form method="GET" style="text-align:center; margin-bottom: 20px;">
        <label for="status_filter">Filter Status:</label>
        <select name="status" id="status_filter" onchange="this.form.submit()">
            <option value="">Semua</option>
            <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="shipped" <?= $statusFilter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="accepted" <?= $statusFilter === 'accepted' ? 'selected' : '' ?>>Accepted</option>
            <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
    </form>

    <?php if (count($orders) > 0): ?>
        <div class="order-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-item">
                    <img src="uploads/<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
                    <div class="order-details">
                        <h3><?= htmlspecialchars($order['product_name']) ?></h3>
                        <p>Jumlah: <?= $order['quantity'] ?> pcs</p>
                        <p>Harga: Rp <?= number_format($order['price'], 0, ',', '.') ?></p>
                    </div>
                    <div class="status">
                        <?= strtoupper($order['order_status']) ?><br>

                        <?php if ($order['order_status'] === 'accepted'): ?>
                            <a href="?confirm=<?= $order['id_order'] ?>" class="confirm-button" onclick="return confirm('Konfirmasi selesai?')">Konfirmasi Selesai</a>
                        <?php elseif ($order['order_status'] === 'completed' && !in_array($order['id_product'], $reviewed)): ?>
                            <form method="POST" class="review-form">
                                <input type="hidden" name="id_product" value="<?= $order['id_product'] ?>">
                                <label for="rating">Rating:</label>
                                <select name="rating" required>
                                    <option value="">Pilih</option>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ★</option>
                                    <?php endfor; ?>
                                </select><br>
                                <label for="comment">Komentar:</label><br>
                                <textarea name="comment" rows="2" cols="30" required></textarea><br>
                                <button type="submit" name="submit_review" class="review-button">Kirim Review</button>
                            </form>
                        <?php elseif ($order['order_status'] === 'completed'): ?>
                            <div style="color: #4CAF50; margin-top: 10px;">Sudah dinilai</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-orders">Belum ada produk yang Anda beli.</div>
    <?php endif; ?>

    <a href="index.php" class="back-link">← Kembali</a>
</body>
</html>
