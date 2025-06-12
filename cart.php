<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user'])) {
    echo "Anda harus login untuk melihat keranjang.";
    exit;
}

$id_user = $_SESSION['id_user'];

$orderQuery = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending'");
$orderQuery->bind_param("i", $id_user);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

$cart = [];
$total = 0;

if ($orderResult->num_rows > 0) {
    $id_order = $orderResult->fetch_assoc()['id_order'];

    $itemsQuery = $conn->prepare("
        SELECT p.name, p.image, od.quantity, od.price, od.id_product
        FROM order_details od 
        JOIN products p ON od.id_product = p.id_product 
        WHERE od.id_order = ?
    ");
    $itemsQuery->bind_param("i", $id_order);
    $itemsQuery->execute();
    $itemsResult = $itemsQuery->get_result();

    while ($item = $itemsResult->fetch_assoc()) {
        $cart[] = $item;
        $total += $item['price'] * $item['quantity'];
    }
}

// Proses penghapusan item dari keranjang
if (isset($_GET['remove']) && isset($_SESSION['id_user'])) {
    $id_product = intval($_GET['remove']);
    $id_user = $_SESSION['id_user'];

    // Cari id_order user yang status-nya pending
    $orderStmt = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending'");
    $orderStmt->bind_param("i", $id_user);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();

    if ($orderResult->num_rows > 0) {
        $id_order = $orderResult->fetch_assoc()['id_order'];

        // Ambil harga dan jumlah item yang ingin dihapus
        $itemStmt = $conn->prepare("SELECT price, quantity FROM order_details WHERE id_order = ? AND id_product = ?");
        $itemStmt->bind_param("ii", $id_order, $id_product);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();

        if ($itemResult->num_rows > 0) {
            $item = $itemResult->fetch_assoc();
            $subtotal = $item['price'] * $item['quantity'];

            // Hapus item dari order_details
            $deleteStmt = $conn->prepare("DELETE FROM order_details WHERE id_order = ? AND id_product = ?");
            $deleteStmt->bind_param("ii", $id_order, $id_product);
            $deleteStmt->execute();

            // Update total_price
            $updateOrder = $conn->prepare("UPDATE orders SET total_price = GREATEST(total_price - ?, 0) WHERE id_order = ?");
            $updateOrder->bind_param("di", $subtotal, $id_order);
            $updateOrder->execute();

            // Cek apakah total_price menjadi 0, jika ya hapus order-nya
            $checkTotal = $conn->prepare("SELECT total_price FROM orders WHERE id_order = ?");
            $checkTotal->bind_param("i", $id_order);
            $checkTotal->execute();
            $resultTotal = $checkTotal->get_result();
            if ($resultTotal->num_rows > 0) {
                $totalRow = $resultTotal->fetch_assoc();
                if ((float)$totalRow['total_price'] <= 0) {
                    // Hapus order karena sudah tidak ada item dan totalnya 0
                    $deleteOrder = $conn->prepare("DELETE FROM orders WHERE id_order = ?");
                    $deleteOrder->bind_param("i", $id_order);
                    $deleteOrder->execute();
                }
            }
        }

        // Redirect agar tidak terus-menerus menghapus saat refresh
        header("Location: cart.php");
        exit;
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
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
        }

        .cart-item {
            display: flex;
            background-color: #1c1c1c;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 255, 255, 0.05);
            transition: transform 0.2s;
        }

        .cart-item:hover {
            transform: scale(1.01);
        }

        .cart-item img {
            width: 90px;
            height: auto;
            margin-right: 20px;
            border-radius: 8px;
            object-fit: cover;
        }

        .info {
            flex: 1;
        }

        .info h3 {
            margin: 0 0 10px;
            font-size: 1.1em;
            color: #0ff;
        }

        .info p {
            margin: 3px 0;
        }

        .qty {
            font-weight: bold;
        }

        .remove-btn {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            align-self: center;
        }

        .remove-btn:hover {
            background-color: #e60000;
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
            background-color: #00ffff;
            color: #0d0d0d;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            float: right;
        }

        .checkout-btn:hover {
            background-color: #00dddd;
        }

        .back {
            display: inline-block;
            margin-top: 50px;
            color: #00ffff;
            text-decoration: none;
        }

        .back:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cart-item img {
                margin-bottom: 10px;
                width: 100%;
            }

            .checkout-btn {
                width: 100%;
                text-align: center;
                float: none;
            }
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
                </div>
                <a href="cart.php?remove=<?= $item['id_product'] ?>" class="remove-btn" onclick="return confirm('Yakin ingin menghapus item ini?')">Hapus</a>
            </div>
        <?php endforeach; ?>

        <div class="cart-total">Total: Rp <?= number_format($total, 0, ',', '.') ?></div>
        
        <a href="checkout.php" class="checkout-btn">Lanjut ke Checkout</a>
    <?php endif; ?>

    <a href="products.php" class="back">← Kembali ke Produk</a>
</body>
</html>
