<?php
// Mulai session lebih awal tanpa output
session_start();
require_once 'koneksiDB.php';

// Inisialisasi variabel session
$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['id_user'] ?? null;

$cartItems = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Ambil filter pencarian dan kategori dari query string
$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';

// Ambil semua kategori
$categoriesResult = $conn->query("SELECT * FROM categories");

// Query produk berdasarkan filter
$query = "
    SELECT DISTINCT p.* 
    FROM products p
    LEFT JOIN product_categories pc ON p.id_product = pc.id_product
    WHERE 1
";
$params = [];
$types = '';

if ($search !== '') {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($category) && is_numeric($category)) {
    $query .= " AND pc.id_category = ?";
    $params[] = $category;
    $types .= 'i';
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$products = $stmt->get_result();

// Ambil data rating produk
$ratingData = $conn->query("
    SELECT id_product, AVG(rating) AS avg_rating, COUNT(*) AS review_count
    FROM reviews
    GROUP BY id_product
");

$productRatings = [];
while ($row = $ratingData->fetch_assoc()) {
    $productRatings[$row['id_product']] = [
        'avg' => round($row['avg_rating'], 1),
        'count' => $row['review_count']
    ];
}

// Ambil kategori produk
$categoryMapResult = $conn->query("
    SELECT pc.id_product, c.name AS category_name
    FROM product_categories pc
    JOIN categories c ON pc.id_category = c.id_category
");

$productCategories = [];
while ($row = $categoryMapResult->fetch_assoc()) {
    $productCategories[$row['id_product']][] = $row['category_name'];
}

// Ambil wishlist user
$wishlistStmt = $conn->prepare("SELECT id_product FROM wishlist WHERE id_user = ?");
$wishlistStmt->bind_param("i", $user_id);
$wishlistStmt->execute();
$wishlistRes = $wishlistStmt->get_result();

$userWishlist = [];
while ($row = $wishlistRes->fetch_assoc()) {
    $userWishlist[] = $row['id_product'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Produk - TRICITY</title>
    <link rel="stylesheet" href="style/products.css">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
</head>
<body>

<header>
  <div class="navbar visible" id="mainNavbar">
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
      <div class="user-info">
        <a href="<?php echo $username ? 'profile.php' : 'LoginForm.php'; ?>" style="text-decoration: none;">
          <i class="fas fa-user" style="color: #FFFFFF;"></i>
          <?php if ($username): ?>
            <span class="username" style="color: #FFFFFF;"><?php echo htmlspecialchars($username); ?></span>
          <?php endif; ?>
        </a>
      </div>

      <?php if ($username && isset($role) && $role === 'admin'): ?>
        <a href="admin_dashboard.php" title="Dashboard Admin">
          <i class="fas fa-tachometer-alt" style="color: #FFFFFF;"></i>
        </a>
      <?php endif; ?>

        <a href="cart.php"><i class="fas fa-shopping-cart" style="color: #FFFFFF;"></i></a>
        <a href="produk_saya.php"><i class="fas fa-receipt" style="color: #FFFFFF;"></i></a>

      <?php if ($username): ?>
        <a href="logout.php" style="color: #FFFFFF; text-decoration: none;">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<br>
<br>
<div class="filter-container">
    <form method="GET">
        <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">Semua Kategori</option>
            <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
                <option value="<?= $cat['id_category'] ?>" <?= $cat['id_category'] == $category ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit"><i class="fas fa-search"></i> Cari</button>
    </form>
</div>

<div class="product-container">
    <?php if ($products->num_rows > 0): ?>
        <?php while ($prod = $products->fetch_assoc()): ?>
            <div class="product-card">
                <a href="detailProduk.php?id=<?= $prod['id_product'] ?>">
                    <img src="uploads/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                    <h2><?= htmlspecialchars($prod['name']) ?></h2>
                    <p class="desc"><?= nl2br(htmlspecialchars($prod['description'])) ?></p>
                    <p class="price">Rp <?= number_format($prod['price'], 0, ',', '.') ?></p>
                    <p class="stock">Stok: <?= $prod['stock'] ?></p>
                    <br>
                    <?php if (!empty($productCategories[$prod['id_product']])): ?>
                        <div>
                            <?php foreach ($productCategories[$prod['id_product']] as $catName): ?>
                                <span class="category-tag"><?= htmlspecialchars($catName) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <br>
                    <?php if (isset($productRatings[$prod['id_product']])): ?>
                        <p class="rating">
                            Rating: <?= $productRatings[$prod['id_product']]['avg'] ?> / 5
                            (<?= $productRatings[$prod['id_product']]['count'] ?> ulasan)
                        </p>
                    <?php else: ?>
                        <p class="rating">Belum ada ulasan</p>
                    <?php endif; ?>
                </a>

                <div class="product-actions">
                    <form action="<?= in_array($prod['id_product'], $userWishlist) ? 'hapus_wishlist.php' : 'tambah_wishlist.php' ?>" method="post">
                        <input type="hidden" name="id_product" value="<?= $prod['id_product'] ?>">
                    </form>

                    <?php if ($role === 'admin'): ?>
                        <a href="updateProduk.php?id=<?= $prod['id_product'] ?>" class="edit-button">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="hapusProduk.php?id=<?= $prod['id_product'] ?>" class="delete-button" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="not-found">Produk tidak ditemukan.</p>
    <?php endif; ?>
</div>

<footer class="footer">
  <div class="footer-top">
    <div class="footer-columns">
      <div class="footer-column">
        <h3>Contact us</h3>
        <p>Indonesia, Cianjur</p>
        <p>+62 895-3952-51660</p>
        <p><a href="mailto:tricityawesome@gmail.com">tricityawesome@gmail.com</a></p>
        <p>@tricity_gears</p>
      </div>

      <div class="footer-column">
        <h3>Let us help</h3>
        <ul>
          <li><a href="#">Track My Order</a></li>
          <li><a href="#">Cancel My Order</a></li>
          <li><a href="#">Return My Order</a></li>
          <li><a href="#">Search</a></li>
        </ul>
      </div>

      <div class="footer-column">
        <h3>Our policies</h3>
        <ul>
          <li><a href="#">Shipping & Delivery</a></li>
          <li><a href="#">Returns & Cancellations</a></li>
          <li><a href="#">Terms & Conditions</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>

      <div class="footer-column">
        <h3>My Account</h3>
        <ul>
          <li><a href="#">Store Location</a></li>
          <li><a href="#">Order History</a></li>
          <li><a href="#">Wish List</a></li>
          <li><a href="#">Gift Cards</a></li>
        </ul>
      </div>
    </div>
  </div>

  <hr />

  <div class="footer-bottom">
    <div class="brand">
      <i class="fa-solid fa-gamepad"></i> <span>Tricity</span>
    </div>
    <p>Copyright © 2024 Tricity. All Rights Reserved.</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-x-twitter"></i></a>
      <a href="#"><i class="fab fa-tiktok"></i></a>
    </div>
  </div>
</footer>
</body>
</html>
