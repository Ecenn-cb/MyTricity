<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user'])) {
    echo "Anda harus login terlebih dahulu untuk melakukan checkout.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil order dengan status pending
$orderQuery = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending'");
$orderQuery->bind_param("i", $id_user);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

$cart = [];
$total = 0;

if ($orderResult->num_rows > 0) {
    $id_order = $orderResult->fetch_assoc()['id_order'];

    // Ambil detail produk dalam keranjang
    $itemsQuery = $conn->prepare("
        SELECT p.name, p.image, od.quantity, od.price, (od.quantity * od.price) AS subtotal
        FROM order_details od
        JOIN products p ON od.id_product = p.id_product
        WHERE od.id_order = ?
    ");
    $itemsQuery->bind_param("i", $id_order);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();

    while ($item = $itemsResult->fetch_assoc()) {
        $cart[] = $item;
        $total += $item['subtotal'];
    }
} else {
    echo "Keranjang Anda kosong.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            padding: 30px;
            max-width: 960px;
            margin: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.2em;
            color: #ff4d8b;
            text-shadow: 0 0 5px #ff4d8b99;
        }

        .item {
            display: flex;
            background-color: #1e1e1e;
            border: 1px solid #2c2c2c;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ff4d8b10;
            overflow: hidden;
        }

        .item img {
            width: 120px;
            height: auto;
            object-fit: cover;
            border-right: 1px solid #2c2c2c;
        }

        .item-details {
            padding: 20px;
            flex: 1;
        }

        .item-details h3 {
            margin: 0 0 10px;
            font-size: 1.3em;
            color: #ff4d8b;
        }

        .item-details p {
            margin: 4px 0;
            color: #ccc;
        }

        .total {
            text-align: right;
            font-size: 1.6em;
            font-weight: bold;
            margin-top: 40px;
            color: #ff4d8b;
            text-shadow: 0 0 4px #ff4d8b99;
        }

        .confirm-btn {
            display: block;
            background-color: #ff4d8b;
            color: #121212;
            font-weight: bold;
            text-align: center;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            margin: 30px auto 15px;
            width: 260px;
            box-shadow: 0 0 15px #ff4d8b50;
            transition: background 0.3s;
        }

        .confirm-btn:hover {
            background-color: #e83c79;
        }

        .back-btn {
            display: block;
            text-align: center;
            color: #888;
            text-decoration: none;
            font-size: 0.95em;
            margin-top: 10px;
        }

        .back-btn:hover {
            color: #fff;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .item {
                flex-direction: column;
            }

            .item img {
                width: 100%;
                height: auto;
                border: none;
                border-bottom: 1px solid #2c2c2c;
            }

            .total {
                text-align: center;
            }

            .confirm-btn {
                width: 100%;
            }
        }

        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap');
    </style>
</head>
<body>

    <h1>🛒 Konfirmasi Checkout</h1>

    <?php foreach ($cart as $item): ?>
        <div class="item">
            <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
            <div class="item-details">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p>Harga: Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                <p>Jumlah: <?= $item['quantity'] ?></p>
                <p>Subtotal: Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="total">Total: Rp <?= number_format($total, 0, ',', '.') ?></div>

    <a href="pembayaran.php" class="confirm-btn" onclick="return confirm('Yakin ingin melakukan checkout?')">🚀 Checkout Sekarang</a>
    <a href="cart.php" class="back-btn">← Kembali ke Keranjang</a>

</body>
</html>
