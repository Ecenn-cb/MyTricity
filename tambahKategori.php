<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'koneksiDB.php';

$message = "";
$alert = "";

// Proses Menambah Kategori
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    $check = $conn->prepare("SELECT * FROM categories WHERE name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Kategori <strong>$name</strong> sudah ada!";
        $alert = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = "Kategori <strong>$name</strong> berhasil ditambahkan.";
            $alert = "success";
        } else {
            $message = "Gagal menambahkan kategori.";
            $alert = "error";
        }
        $stmt->close();
    }
    $check->close();
}

// Proses hapus kategori
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_category'])) {
    $id = $_POST['category_id'];

    // Cek apakah kategori digunakan di tabel product_categories
    $check_use = $conn->prepare("SELECT * FROM product_categories WHERE id_category = ?");
    $check_use->bind_param("i", $id);
    $check_use->execute();
    $res = $check_use->get_result();

    if ($res->num_rows > 0) {
        $message = "Kategori tidak bisa dihapus karena masih digunakan oleh produk!";
        $alert = "error";
    } else {
        $delete = $conn->prepare("DELETE FROM categories WHERE id_category = ?");
        $delete->bind_param("i", $id);
        if ($delete->execute()) {
            $message = "Kategori berhasil dihapus.";
            $alert = "success";
        } else {
            $message = "Gagal menghapus kategori.";
            $alert = "error";
        }
        $delete->close();
    }
    $check_use->close();
}

// Ambil semua kategori untuk ditampilkan di tabel
$categories = $conn->query("SELECT * FROM categories");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori</title>
    <link rel="stylesheet" href="style/adminKelolaProduk.css">

    <style>
        .kategori-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .kategori-table th,
        .kategori-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #555;
            color: #fff;
        }

        .kategori-table th {
            background-color: #ff4d8b;
            color: #fff;
        }

        .hapus-btn {
            background-color: #ff4d8b;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .hapus-btn:hover {
            background-color: #e13a75;
        }


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
        <h1>Tambah Kategori</h1>
        <a href="kelolaProduk.php" class="kembali-btn">Kembali ke Menu</a>
    </header>
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert <?= $alert ?>"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST" onsubmit="return confirm('Yakin ingin menambahkan kategori ini?')">
            <input type="text" name="category_name" placeholder="Nama Kategori" required>
            <button type="submit" name="add_category">Tambah</button>
        </form>
    </div>

    <br>
    <br>
    <h2>Daftar Kategori</h2>
        <table class="kategori-table">
            <thead>
                <tr>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            <input type="hidden" name="category_id" value="<?= $cat['id_category'] ?>">
                            <button type="submit" name="delete_category" class="hapus-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
</body>
</html>
