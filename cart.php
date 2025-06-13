<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user'])) {
    echo "Anda harus login untuk melihat keranjang.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Hapus item dari keranjang jika ada parameter ?remove=
if (isset($_GET['remove'])) {
    $id_product = intval($_GET['remove']);

    // Cari order "pending" milik user
    $orderQuery = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending'");
    $orderQuery->bind_param("i", $id_user);
    $orderQuery->execute();
    $orderResult = $orderQuery->get_result();

    if ($orderResult->num_rows > 0) {
        $id_order = $orderResult->fetch_assoc()['id_order'];

        // Hapus item dari order_details
        $deleteQuery = $conn->prepare("DELETE FROM order_details WHERE id_order = ? AND id_product = ?");
        $deleteQuery->bind_param("ii", $id_order, $id_product);
        $deleteQuery->execute();
    }

    // Redirect agar URL bersih dari ?remove=
    header("Location: cart.php");
    exit;
}

// Ambil isi keranjang
$orderQuery = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending'");
$orderQuery->bind_param("i", $id_user);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

$cart = [];
$total = 0;
$stok_kurang = false;

if ($orderResult->num_rows > 0) {
    $id_order = $orderResult->fetch_assoc()['id_order'];

    $itemsQuery = $conn->prepare("
        SELECT p.name, p.image, p.stock, od.quantity, od.price, od.id_product
        FROM order_details od 
        JOIN products p ON od.id_product = p.id_product 
        WHERE od.id_order = ?
    ");
    $itemsQuery->bind_param("i", $id_order);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();

    while ($item = $itemsResult->fetch_assoc()) {
        $item['stok_kurang'] = $item['quantity'] > $item['stock'];
        if ($item['stok_kurang']) {
            $stok_kurang = true;
        }

        $cart[] = $item;
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Anda</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #0d0d0d;
            color: #f0f0f0;
            padding: 30px;
            max-width: 900px;
            margin: auto;
        }

        h1 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 2em;
            margin-bottom: 30px;
        }

        .cart-item {
            display: flex;
            background-color: #1c1c1c;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            gap: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.4);
            align-items: flex-start;
        }

        .cart-item img {
            width: 120px;
            border-radius: 8px;
            object-fit: cover;
        }

        .info {
            flex-grow: 1;
        }

        .info h3 {
            margin: 0;
            font-size: 1.3em;
            font-weight: 600;
        }

        .info p {
            margin: 5px 0;
        }

        .warning {
            color: #ffcc00;
            font-weight: bold;
            margin-top: 8px;
            font-size: 0.95em;
        }

        .remove-btn {
            background-color: #ff4c4c;
            color: white;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            align-self: flex-start;
            white-space: nowrap;
        }

        .remove-btn:hover {
            background-color: #d93636;
        }

        .cart-total {
            text-align: right;
            font-size: 1.3em;
            font-weight: bold;
            margin-top: 30px;
            color: #00ffff;
        }

        .checkout-btn {
            display: inline-block;
            background-color: <?= $stok_kurang ? '#666' : '#00ffff' ?>;
            color: #0d0d0d;
            font-weight: bold;
            padding: 14px 24px;
            border-radius: 12px;
            text-decoration: none;
            margin-top: 25px;
            float: right;
            pointer-events: <?= $stok_kurang ? 'none' : 'auto' ?>;
            cursor: <?= $stok_kurang ? 'not-allowed' : 'pointer' ?>;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: <?= $stok_kurang ? '#666' : '#00e6e6' ?>;
        }

        .back {
            display: inline-block;
            margin-top: 60px;
            color: #00ffff;
            text-decoration: none;
            font-weight: 500;
        }

        .qty {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <h1>🛒 Keranjang Anda</h1>

    <?php if (empty($cart)): ?>
        <p style="text-align: center;">Keranjang kosong.</p>
    <?php else: ?>
        <?php foreach ($cart as $item): ?>
            <div class="cart-item">
                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="info">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p>Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                    <p class="qty">Jumlah: <?= $item['quantity'] ?></p>
                    <p>Subtotal: Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                    <?php if ($item['stok_kurang']): ?>
                        <p class="warning">⚠️ Stok tidak cukup (tersisa <?= $item['stock'] ?>)</p>
                    <?php endif; ?>
                </div>
                <a href="cart.php?remove=<?= $item['id_product'] ?>" class="remove-btn" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
            </div>
        <?php endforeach; ?>

        <div class="cart-total">Total: Rp <?= number_format($total, 0, ',', '.') ?></div>

        <a href="#" class="checkout-btn" onclick="<?= $stok_kurang ? 'alert(\'Beberapa produk stoknya tidak cukup. Hapus atau sesuaikan jumlahnya.\')' : 'window.location.href=\'checkout.php\'' ?>">Lanjut ke Checkout</a>
    <?php endif; ?>

    <a href="products.php" class="back">← Kembali ke Produk</a>
</body>
</html>
