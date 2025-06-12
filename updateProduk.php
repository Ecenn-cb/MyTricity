<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "Akses ditolak. Halaman ini hanya untuk admin.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID produk tidak ditemukan.";
    exit;
}

$id_product = intval($_GET['id']);

// Ambil data produk
$stmt = $conn->prepare("SELECT * FROM products WHERE id_product = ?");
$stmt->bind_param("i", $id_product);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produk tidak ditemukan.";
    exit;
}

$product = $result->fetch_assoc();

// Ambil semua kategori
$categoryQuery = $conn->query("SELECT * FROM categories");

// Ambil kategori yang dimiliki produk ini
$currentCategories = [];
$catStmt = $conn->prepare("SELECT id_category FROM product_categories WHERE id_product = ?");
$catStmt->bind_param("i", $id_product);
$catStmt->execute();
$catRes = $catStmt->get_result();
while ($row = $catRes->fetch_assoc()) {
    $currentCategories[] = $row['id_category'];
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $description = $_POST['description'];
    $categories  = $_POST['categories'] ?? [];

    $image = $product['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $newName = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $newName);
        $image = $newName;
    }

    $update = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, description=?, image=? WHERE id_product=?");
    $update->bind_param("sdissi", $name, $price, $stock, $description, $image, $id_product);
    $update->execute();

    $delStmt = $conn->prepare("DELETE FROM product_categories WHERE id_product = ?");
    $delStmt->bind_param("i", $id_product);
    $delStmt->execute();

    $insertCat = $conn->prepare("INSERT INTO product_categories (id_product, id_category) VALUES (?, ?)");
    foreach ($categories as $cat_id) {
        $cat_id = intval($cat_id);
        $insertCat->bind_param("ii", $id_product, $cat_id);
        $insertCat->execute();
    }

    echo "<script>alert('Produk berhasil diperbarui!'); window.location.href='products.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #121212;
            color: #fff;
            max-width: 600px;
            margin: auto;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #ff4d8b;
            margin-bottom: 30px;
        }

        form {
            background: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ff4d8b40;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: none;
            background-color: #292929;
            color: #fff;
        }

        select[multiple] {
            height: 100px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ff4d8b;
            border: none;
            font-weight: bold;
            color: #121212;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1em;
        }

        button:hover {
            background: #e03b77;
        }

        .img-preview {
            width: 120px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #333;
        }
    </style>
</head>
<body>

    <h1>✏️ Edit Produk</h1>

    <form method="post" enctype="multipart/form-data">
        <label for="name">Nama Produk:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="price">Harga:</label>
        <input type="number" name="price" id="price" value="<?= $product['price'] ?>" step="0.01" required>

        <label for="stock">Stok:</label>
        <input type="number" name="stock" id="stock" value="<?= $product['stock'] ?>" required>

        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="categories">Kategori:</label>
        <select name="categories[]" id="categories" multiple>
            <?php
            $categoryQuery->data_seek(0);
            while ($cat = $categoryQuery->fetch_assoc()):
                $selected = in_array($cat['id_category'], $currentCategories) ? 'selected' : '';
            ?>
                <option value="<?= $cat['id_category'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="image">Gambar Produk:</label>
        <?php if ($product['image']): ?>
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>" class="img-preview" alt="Preview">
        <?php endif; ?>
        <input type="file" name="image" id="image" accept="image/*">

        <button type="submit">💾 Simpan Perubahan</button>
    </form>

</body>
</html>
