<?php
session_start();
include 'koneksiDB.php';

// Jika belum login
if (!isset($_SESSION['id_user'])) {
    $redirect = getRedirectUrl("Silakan login terlebih dahulu untuk menambahkan ke keranjang.", false);
    header("Location: $redirect");
    exit();
}

// Validasi input produk
if (!isset($_POST['id_product']) || !is_numeric($_POST['id_product'])) {
    $redirect = getRedirectUrl("ID Produk tidak valid.", false);
    header("Location: $redirect");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_product = (int)$_POST['id_product'];
$quantity = 1;

try {
    $conn->begin_transaction();

    // Cek order dengan status 'pending'
    $stmt = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending' LIMIT 1");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $id_order = $result->fetch_assoc()['id_order'];
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (id_user, total_price, status) VALUES (?, 0, 'pending')");
        $stmt->bind_param("i", $id_user);
        if (!$stmt->execute()) throw new Exception("Gagal membuat order.");
        $id_order = $conn->insert_id;
    }

    // Ambil data produk
    $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id_product = ?");
    $stmt->bind_param("i", $id_product);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        throw new Exception("Produk tidak ditemukan.");
    }

    if ($product['stock'] < 1) {
        throw new Exception("Stok produk habis.");
    }

    // Cek apakah produk sudah ada di keranjang
    $stmt = $conn->prepare("SELECT id_order_detail, quantity FROM order_details WHERE id_order = ? AND id_product = ?");
    $stmt->bind_param("ii", $id_order, $id_product);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if ($item) {
        $newQty = $item['quantity'] + $quantity;
        if ($newQty > $product['stock']) {
            throw new Exception("Stok tidak mencukupi untuk quantity baru.");
        }

        $stmt = $conn->prepare("UPDATE order_details SET quantity = ? WHERE id_order_detail = ?");
        $stmt->bind_param("ii", $newQty, $item['id_order_detail']);
        if (!$stmt->execute()) throw new Exception("Gagal update quantity.");
    } else {
        $stmt = $conn->prepare("INSERT INTO order_details (id_order, id_product, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $id_order, $id_product, $quantity, $product['price']);
        if (!$stmt->execute()) throw new Exception("Gagal menambahkan item ke keranjang.");
    }

    // Update total harga order
    $stmt = $conn->prepare("
        UPDATE orders 
        SET total_price = (
            SELECT SUM(quantity * price) 
            FROM order_details 
            WHERE id_order = ?
        ) 
        WHERE id_order = ?
    ");
    $stmt->bind_param("ii", $id_order, $id_order);
    if (!$stmt->execute()) throw new Exception("Gagal update total harga.");

    $conn->commit();

    // Redirect sukses
    $redirect = getRedirectUrl("Produk berhasil ditambahkan ke keranjang.", true);
    header("Location: $redirect");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Cart error: " . $e->getMessage());

    $redirect = getRedirectUrl($e->getMessage(), false);
    header("Location: $redirect");
    exit();
}

// 🔧 Fungsi bantu untuk menghindari masalah URL double parameter
function getRedirectUrl($message, $isSuccess = true) {
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    $param = $isSuccess ? 'success' : 'error';

    // Cek apakah URL sudah memiliki query string
    if (strpos($redirect, '?') !== false) {
        return $redirect . '&' . $param . '=' . urlencode($message);
    } else {
        return $redirect . '?' . $param . '=' . urlencode($message);
    }
}
?>
