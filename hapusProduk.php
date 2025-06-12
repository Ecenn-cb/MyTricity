<?php
session_start();
require_once 'koneksiDB.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak!');
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data terkait terlebih dahulu agar tidak melanggar constraint
    $conn->query("DELETE FROM order_details WHERE id_product = $id");
    $conn->query("DELETE FROM wishlist WHERE id_product = $id");
    $conn->query("DELETE FROM reviews WHERE id_product = $id");
    $conn->query("DELETE FROM product_categories WHERE id_product = $id");

    // Sekarang aman untuk hapus produk
    $conn->query("DELETE FROM products WHERE id_product = $id");

    header('Location: products.php?hapus=berhasil');
    exit();
} else {
    echo "ID tidak valid.";
}
