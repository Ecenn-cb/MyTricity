<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user'])) {
    echo "Anda harus login terlebih dahulu.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil order pending user
$orderQuery = $conn->prepare("SELECT id_order, total_price FROM orders WHERE id_user = ? AND status = 'pending'");
$orderQuery->bind_param("i", $id_user);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

if ($orderResult->num_rows == 0) {
    echo "Tidak ada pesanan yang bisa dibayar.";
    exit;
}

$orderData = $orderResult->fetch_assoc();
$id_order = $orderData['id_order'];
$amount = $orderData['total_price'];

// Ambil detail produk
$productQuery = $conn->prepare("
    SELECT p.id_product, p.name, p.image, od.quantity, od.price
    FROM order_details od
    JOIN products p ON od.id_product = p.id_product
    WHERE od.id_order = ?
");
$productQuery->bind_param("i", $id_order);
$productQuery->execute();
$productResult = $productQuery->get_result();
$products = $productResult->fetch_all(MYSQLI_ASSOC);

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];

    // 1. Simpan ke tabel payments
    $insertPayment = $conn->prepare("INSERT INTO payments (id_order, payment_method, amount, status) VALUES (?, ?, ?, 'success')");
    $insertPayment->bind_param("isd", $id_order, $payment_method, $amount);
    $insertPayment->execute();

    // 2. Update status order
    $updateOrder = $conn->prepare("UPDATE orders SET status = 'paid' WHERE id_order = ?");
    $updateOrder->bind_param("i", $id_order);
    $updateOrder->execute();

    // 3. Kurangi stok produk
    foreach ($products as $item) {
        $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id_product = ?");
        $updateStock->bind_param("ii", $item['quantity'], $item['id_product']);
        $updateStock->execute();
    }

    echo "<script>alert('Pembayaran berhasil!'); window.location.href='products.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 700px;
            margin: auto;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #ff4d8b;
        }

        form {
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ff4d8b40;
            margin-top: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        select, button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            margin-bottom: 20px;
        }

        select {
            background-color: #292929;
            color: #fff;
        }

        button {
            background-color: #ff4d8b;
            color: #121212;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 0 10px #ff4d8b60;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #e03b77;
        }

        .total-info {
            margin-top: 20px;
            font-size: 1.2em;
            color: #ccc;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            text-decoration: none;
        }

        .back-link:hover {
            color: #fff;
        }

        .product-list {
            margin-top: 30px;
        }

        .product {
            display: flex;
            background-color: #2a2a2a;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            align-items: center;
            box-shadow: 0 0 5px #ff4d8b30;
        }

        .product img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }

        .product-info {
            flex-grow: 1;
        }

        .product-info h4 {
            margin: 0;
            color: #fff;
        }

        .product-info p {
            margin: 3px 0;
            color: #ccc;
        }
    </style>
</head>
<body>
    <h1>💳 Pembayaran</h1>

    <form method="post">
        <label for="payment_method">Pilih Metode Pembayaran:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="">-- Pilih --</option>
            <option value="Transfer Bank">Transfer Bank</option>
            <option value="E-Wallet (OVO/DANA/Gopay)">E-Wallet (OVO/DANA/Gopay)</option>
            <option value="Kartu Kredit">Kartu Kredit</option>
            <option value="COD">Cash On Delivery</option>
        </select>

        <div class="total-info">
            Total yang harus dibayar: <strong>Rp <?= number_format($amount, 0, ',', '.') ?></strong>
        </div>

        <button type="submit">Bayar Sekarang</button>
    </form>

    <div class="product-list">
        <h2>🛍️ Daftar Barang</h2>
        <?php foreach ($products as $item): ?>
            <div class="product">
                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="product-info">
                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                    <p>Jumlah: <?= $item['quantity'] ?> pcs</p>
                    <p>Harga Satuan: Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                    <p>Subtotal: Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="checkout.php" class="back-link">← Kembali ke Checkout</a>
</body>
</html>
