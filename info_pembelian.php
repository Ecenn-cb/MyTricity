<?php
session_start();
include 'koneksiDB.php';

// Cek apakah admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "Akses ditolak. Halaman ini hanya untuk admin.";
    exit;
}

// Proses update status jika ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_order = intval($_POST['id_order']);
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id_order = ?");
    $stmt->bind_param("si", $new_status, $id_order);
    $stmt->execute();
}

// Status yang valid
$valid_statuses = ['pending', 'paid', 'confirmed', 'shipped', 'accepted', 'completed', 'cancelled'];

// Ambil filter dari query string
$filter_status = isset($_GET['status']) && in_array($_GET['status'], $valid_statuses)
    ? $_GET['status']
    : 'paid'; // default

// Ambil order berdasarkan status yang dipilih
$stmt = $conn->prepare("
    SELECT o.id_order, o.status, u.username AS buyer_name, u.address, p.name AS product_name, p.image, od.quantity, od.price
    FROM orders o
    JOIN users u ON o.id_user = u.id_user
    JOIN order_details od ON o.id_order = od.id_order
    JOIN products p ON od.id_product = p.id_product
    WHERE o.status = ?
    ORDER BY o.id_order DESC
");
$stmt->bind_param("s", $filter_status);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Info Pembelian (Admin)</title>
    <style>
        body {
            background-color: #0f0f0f;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }

        h1 {
            color: #ff4d8b;
            text-align: center;
        }

        .filter-form {
            text-align: center;
            margin-bottom: 30px;
        }

        .filter-form select {
            padding: 8px 12px;
            font-size: 16px;
            border-radius: 6px;
            border: none;
            margin-right: 10px;
        }

        .filter-form button {
            background-color: #ff4d8b;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
        }

        .order {
            background: #1b1b1b;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 0 8px #ff4d8b44;
            display: flex;
            align-items: center;
        }

        .order img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .order-info {
            flex-grow: 1;
        }

        .order-info h3 {
            margin: 0;
            color: #fff;
        }

        .order-info p {
            margin: 5px 0;
            color: #ccc;
        }

        form {
            text-align: right;
        }

        select[name="new_status"], button[name="update_status"] {
            padding: 6px 10px;
            border-radius: 6px;
            border: none;
            margin-left: 10px;
        }

        button[name="update_status"] {
            background-color: #ff4d8b;
            color: white;
            cursor: pointer;
        }

        button[name="update_status"]:hover {
            background-color: #e63c77;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            color: #ccc;
            text-decoration: none;
        }

        .back-link a:hover {
            color: #fff;
        }

        h2 {
            margin-top: 30px;
            color: #ff4d8b;
        }
    </style>
</head>
<body>
    <h1>📦 Info Pembelian</h1>

    <div class="filter-form">
        <form method="GET" action="">
            <select name="status">
                <?php foreach ($valid_statuses as $status): ?>
                    <option value="<?= $status ?>" <?= $filter_status === $status ? 'selected' : '' ?>>
                        <?= ucfirst($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Tampilkan</button>
        </form>
    </div>

    <h2>Status: <?= strtoupper($filter_status) ?></h2>

    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <img src="uploads/<?= htmlspecialchars($order['image']) ?>" alt="<?= htmlspecialchars($order['product_name']) ?>">
                <div class="order-info">
                    <h3><?= htmlspecialchars($order['product_name']) ?></h3>
                    <p>Jumlah: <?= $order['quantity'] ?> pcs</p>
                    <p>Harga: Rp <?= number_format($order['price'], 0, ',', '.') ?></p>
                    <p>Pembeli: <strong><?= htmlspecialchars($order['buyer_name']) ?></strong></p>
                    <p>Alamat: <?= htmlspecialchars($order['address']) ?></p>
                    <p>Status: <span style="color: #ff4d8b"><?= strtoupper($order['status']) ?></span></p>
                </div>
                <form method="POST">
                    <input type="hidden" name="id_order" value="<?= $order['id_order'] ?>">
                    <select name="new_status">
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="shipped">Dikirim</option>
                        <option value="accepted">Diterima</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center; color: #ccc;">Belum ada pesanan dengan status ini.</p>
    <?php endif; ?>

    <div class="back-link">
        <a href="admin_dashboard.php">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
