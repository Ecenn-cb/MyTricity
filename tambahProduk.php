<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginForm.php");
    exit();
}

require_once 'koneksiDB.php';

$upload_dir = "uploads/";
$message = "";
$alert = "";

// Ambil kategori untuk dropdown
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];

    if (empty($name) || empty($price) || empty($stock) || empty($category_id)) {
        $message = "Semua field harus diisi!";
        $alert = "error";
    } else {
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid("prod_") . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image_name);
        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;
            $stmt->close();

            $stmt2 = $conn->prepare("INSERT INTO product_categories (id_product, id_category) VALUES (?, ?)");
            $stmt2->bind_param("ii", $product_id, $category_id);
            if ($stmt2->execute()) {
                $message = "Produk <strong>$name</strong> berhasil ditambahkan.";
                $alert = "success";
            } else {
                $message = "Gagal menghubungkan produk ke kategori.";
                $alert = "error";
            }
            $stmt2->close();
        } else {
            $message = "Gagal menambahkan produk.";
            $alert = "error";
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
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
        <h1>Tambah Produk</h1>
        <a href="kelolaProduk.php" class="kembali-btn">Kembali ke Menu</a>
    </header>
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert <?= $alert ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('Yakin ingin menambahkan produk ini?')">
            <input type="text" name="product_name" placeholder="Nama Produk" required><br>
            <textarea name="description" placeholder="Deskripsi Produk"></textarea><br>
            <input type="number" step="0.01" name="price" placeholder="Harga" required><br>
            <input type="number" name="stock" placeholder="Stok" required><br>
            <input type="file" name="image" accept="image/*" required><br>
            <select name="category_id" required>
                <option value="">Pilih Kategori</option>
                <?php while($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id_category'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select><br>
            <button type="submit" name="add_product">Tambah Produk</button>
        </form>
    </div>
</body>
</html>
