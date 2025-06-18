<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak!");
}

$username = $_SESSION['username'] ?? null;

$news = $conn->query("SELECT * FROM news ORDER BY published_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Berita</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #0d0d0d;
            color: white;
            font-family: 'Orbitron', sans-serif;
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #ff0080;
        }

        a {
            text-decoration: none;
            color: #00d9ff;
            transition: color 0.3s;
        }

        a:hover {
            color: #ff0080;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1a1a1a;
            margin-top: 20px;
            border: 1px solid #333;
        }

        th, td {
            border: 1px solid #333;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background-color: #222;
            color: #ff0080;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tr:nth-child(even) {
            background-color: #151515;
        }

        tr:hover {
            background-color: #2a2a2a;
        }

        .btn-add, .btn-back {
            display: inline-block;
            margin-bottom: 15px;
            background: linear-gradient(90deg, #ff0080, #7928ca);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            margin-right: 10px;
        }

        .btn-add:hover, .btn-back:hover {
            opacity: 0.9;
        }

        .action-links a {
            margin-right: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <h2>Manajemen Berita</h2>

    <a href="admin_dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    <a href="tambah_news.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Berita Baru</a>

    <table>
        <tr>
            <th>Judul</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        <?php while ($n = $news->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($n['title']) ?></td>
                <td><?= $n['published_at'] ?></td>
                <td class="action-links">
                    <a href="edit_news.php?id=<?= $n['id_news'] ?>"><i class="fas fa-edit"></i> Edit</a>
                    <a href="hapus_news.php?id=<?= $n['id_news'] ?>" onclick="return confirm('Yakin ingin menghapus berita ini?')">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
