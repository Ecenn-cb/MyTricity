<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksiDB.php';

$message = "";
$alert = "";

// Ambil semua produk
$products = $conn->query("SELECT id_product, name, stock FROM products");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $amount = (int)$_POST['stock_change'];

    $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id_product = ?");
    $stmt->bind_param("ii", $amount, $product_id);
    if ($stmt->execute()) {
        $message = "Stok produk berhasil diperbarui.";
        $alert = "success";
    } else {
        $message = "Gagal memperbarui stok.";
        $alert = "error";
    }
    $stmt->close();

    // Ambil ulang produk setelah update
    $products = $conn->query("SELECT id_product, name, stock FROM products");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Stok Produk</title>
    <link rel="stylesheet" href="style/adminKelolaProduk.css">
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 60vh;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        select, input[type="number"], button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .alert {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .alert.success {
            background-color: #2ecc71;
            color: white;
        }

        .alert.error {
            background-color: #e74c3c;
            color: white;
        }
    </style>

</head>
<body>
    <header>
        <h1>Update Stok Produk</h1>
        <a href="kelolaProduk.php" class="kembali-btn">Kembali ke Menu</a>
    </header>
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert <?= $alert ?>"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return confirm('Yakin ingin mengubah stok produk ini? Tambah atau kurangi sesuai input.')">
            <select name="product_id" required>
                <option value="">Pilih Produk</option>
                <?php while ($prod = $products->fetch_assoc()): ?>
                    <option value="<?= $prod['id_product'] ?>">
                        <?= htmlspecialchars($prod['name']) ?> (Stok: <?= $prod['stock'] ?>)
                    </option>
                <?php endwhile; ?>
            </select><br>

            <input type="number" name="stock_change" placeholder="Jumlah (+/-)" required><br>
            <button type="submit" name="update_stock">Update Stok</button>
        </form>
    </div>
</body>
</html>
