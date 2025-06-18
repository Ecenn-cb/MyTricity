<?php
session_start();
include 'koneksiDB.php';

// Batasi akses hanya untuk admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

// Ambil data berita berdasarkan ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid!");
}
$id = $_GET['id'];

$query = $conn->prepare("SELECT * FROM news WHERE id_news = ?");
$query->bind_param("i", $id);
$query->execute();
$news = $query->get_result()->fetch_assoc();

if (!$news) {
    die("Data tidak ditemukan!");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $published_at = $_POST['published_at'];
    $image = $news['image'];

    // Proses upload gambar jika ada
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $newImage = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $newImage;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $newImage;
        }
    }

    $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, image = ?, published_at = NOW(), updated_at = ? WHERE id_news = ?");
    $updated_at = date('Y-m-d H:i:s');
    $stmt->bind_param("ssssi", $title, $content, $image, $updated_at, $id);

    if ($stmt->execute()) {
        header("Location: adminNews.php?success=Berita berhasil diperbarui!");
        exit();
    } else {
        $error = "Gagal memperbarui berita.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Berita</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #0ff3;
        }

        h2 {
            text-align: center;
            color: #0ff;
        }

        label {
            display: block;
            margin-top: 15px;
            color: #ccc;
        }

        input[type="text"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            background-color: #f0f;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0ff;
            color: #111;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
        }

        .back-link:hover {
            color: #f0f;
        }

        .form-preview {
            margin-top: 10px;
        }

        .form-preview img {
            max-width: 150px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Berita</h2>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Judul:</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($news['title']) ?>">

        <label>Isi Berita:</label>
        <textarea name="content" rows="6" required><?= htmlspecialchars($news['content']) ?></textarea>

        <label>Gambar Saat Ini:</label>
        <div class="form-preview">
            <?php if ($news['image']): ?>
                <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="Gambar saat ini">
            <?php else: ?>
                <p><i>Tidak ada gambar</i></p>
            <?php endif; ?>
        </div>

        <label>Ganti Gambar (Opsional):</label>
        <input type="file" name="image" accept="image/*">

        <label>Tanggal Diterbitkan:</label>
        <input type="text" name="published_at" value="<?= htmlspecialchars($news['published_at']) ?>" disabled>

        <button type="submit">Simpan Perubahan</button>
        <br>
        <a href="adminNews.php" class="back-link">← Kembali ke Manajemen Berita</a>
    </form>
</div>

</body>
</html>
