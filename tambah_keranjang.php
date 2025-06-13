<?php
session_start();
include 'koneksiDB.php';

// Validasi session dan input
if (!isset($_SESSION['id_user'])) {
    die(json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']));
}

if (!isset($_POST['id_product']) || !is_numeric($_POST['id_product'])) {
    die(json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid.']));
}

$id_user = $_SESSION['id_user'];
$id_product = (int)$_POST['id_product'];
$quantity = 1;

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // 1. Cek/Create Order Pending
    $checkOrder = $conn->prepare("SELECT id_order FROM orders WHERE id_user = ? AND status = 'pending' LIMIT 1");
    $checkOrder->bind_param("i", $id_user);
    $checkOrder->execute();
    $orderResult = $checkOrder->get_result();

    if ($orderResult->num_rows > 0) {
        $order = $orderResult->fetch_assoc();
        $id_order = $order['id_order'];
    } else {
        $createOrder = $conn->prepare("INSERT INTO orders (id_user, total_price, status) VALUES (?, 0, 'pending')");
        $createOrder->bind_param("i", $id_user);
        
        if (!$createOrder->execute()) {
            throw new Exception("Gagal membuat order baru: " . $createOrder->error);
        }
        
        $id_order = $conn->insert_id;
    }

    // 2. Ambil harga produk dan cek stok
    $productQuery = $conn->prepare("SELECT price, stock FROM products WHERE id_product = ?");
    $productQuery->bind_param("i", $id_product);
    $productQuery->execute();
    $productResult = $productQuery->get_result();

    if ($productResult->num_rows === 0) {
        throw new Exception("Produk tidak ditemukan.");
    }

    $product = $productResult->fetch_assoc();
    $price = $product['price'];

    if ($product['stock'] < 1) {
        header("Location: products.php?error=" . urlencode("Stok produk habis."));
        exit();
    }

    // 3. Cek/Tambah Item ke Keranjang
    $checkItem = $conn->prepare("SELECT id_order_detail, quantity FROM order_details WHERE id_order = ? AND id_product = ?");
    $checkItem->bind_param("ii", $id_order, $id_product);
    $checkItem->execute();
    $itemResult = $checkItem->get_result();

    if ($itemResult->num_rows > 0) {
        $item = $itemResult->fetch_assoc();
        $newQuantity = $item['quantity'] + $quantity;
        
        if ($newQuantity > $product['stock']) {
            header("Location: products.php?error=" . urlencode("Stok tidak mencukupi untuk penambahan quantity."));
            exit();
        }
        
        $updateItem = $conn->prepare("UPDATE order_details SET quantity = ? WHERE id_order_detail = ?");
        $updateItem->bind_param("ii", $newQuantity, $item['id_order_detail']);
        
        if (!$updateItem->execute()) {
            throw new Exception("Gagal update quantity: " . $updateItem->error);
        }
    } else {
        $addItem = $conn->prepare("INSERT INTO order_details (id_order, id_product, quantity, price) VALUES (?, ?, ?, ?)");
        $addItem->bind_param("iiid", $id_order, $id_product, $quantity, $price);
        
        if (!$addItem->execute()) {
            throw new Exception("Gagal menambahkan item: " . $addItem->error);
        }
    }

    // 4. Update total harga order
    $updateTotal = $conn->prepare("UPDATE orders SET total_price = (SELECT SUM(quantity * price) FROM order_details WHERE id_order = ?) WHERE id_order = ?");
    $updateTotal->bind_param("ii", $id_order, $id_order);
    
    if (!$updateTotal->execute()) {
        throw new Exception("Gagal update total harga: " . $updateTotal->error);
    }

    $conn->commit();
    header("Location: cart.php?success=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in cart process: " . $e->getMessage());
    header("Location: products.php?error=" . urlencode($e->getMessage()));
    exit();
}
