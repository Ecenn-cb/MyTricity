<?php
include 'koneksiDB.php';
session_start();
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalQuery = $conn->query("SELECT COUNT(*) as total FROM news");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$result = $conn->query("SELECT * FROM news ORDER BY published_at DESC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Terkini | TRICITY</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0f0f0f;
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        header, .navbar {
            background-color: #111;
            padding: 20px 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Orbitron', sans-serif;
        }

        .logo {
            font-size: 24px;
            color: white;
            display: flex;
            align-items: center;
        }

        .logo i {
            color: #ff0080;
            margin-right: 8px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #ff0080;
        }

        .icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .icons a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .icons i {
            margin-right: 5px;
        }

        h1 {
            text-align: center;
            margin: 2rem 0 1rem;
            color: #ffffff;
            font-size: 2.5rem;
        }

        .news-item {
            background-color: #1e1e1e;
            padding: 20px;
            margin: 20px auto;
            border-radius: 12px;
            width: 80%;
            box-shadow: 0 0 12px rgba(255, 0, 127, 0.3);
        }

        .news-item h2 {
            color: #ff007f;
        }

        .news-item img {
            max-width: 100%;
            border-radius: 10px;
            margin: 10px 0;
        }

        .news-item a {
            color: #00d1ff;
            text-decoration: none;
            font-weight: bold;
        }

        .news-item a:hover {
            text-decoration: underline;
        }

        .pagination {
            text-align: center;
            margin: 30px 0;
        }

        .pagination a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background: #ff007f;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .pagination a:hover {
            background: #00d1ff;
        }

        .pagination .disabled {
            background: #333;
            pointer-events: none;
        }
    </style>
</head>
<body>
<header>
  <div class="navbar" id="mainNavbar">
    <div class="logo">
      <i class="fas fa-gamepad"></i> <span>TRICITY</span>
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="news.php">News</a></li>
      </ul>
    </nav>
    <div class="icons">
      <a href="<?= $username ? 'profile.php' : 'LoginForm.php'; ?>">
        <i class="fas fa-user"></i> <?= $username ? htmlspecialchars($username) : '' ?>
      </a>
      <?php if ($username && $role === 'admin'): ?>
        <a href="admin_dashboard.php" title="Dashboard Admin">
          <i class="fas fa-tachometer-alt"></i>
        </a>
      <?php endif; ?>
      <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
      <a href="produk_saya.php"><i class="fas fa-receipt"></i></a>
      <?php if ($username): ?>
        <a href="logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<h1>Hot News</h1>

<?php while ($news = $result->fetch_assoc()): ?>
    <div class="news-item">
        <h2><?= htmlspecialchars($news['title']) ?></h2>
        <p><small>Diterbitkan pada: <?= $news['published_at'] ?></small></p>
        <?php if ($news['image']): ?>
            <img src="uploads/<?= htmlspecialchars($news['image']) ?>" alt="Thumbnail">
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars(substr($news['content'], 0, 300))) ?>...</p>
        <a href="detailNews.php?id=<?= $news['id_news'] ?>">Baca Selengkapnya</a>
    </div>
<?php endwhile; ?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">← Sebelumnya</a>
    <?php else: ?>
        <a class="disabled">← Sebelumnya</a>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Berikutnya →</a>
    <?php else: ?>
        <a class="disabled">Berikutnya →</a>
    <?php endif; ?>
</div>

</body>
</html>
