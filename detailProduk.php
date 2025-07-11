<?php
session_start();
include 'koneksiDB.php';

if (!isset($_GET['id'])) {
    die("Produk tidak ditemukan!");
}

$id = $_GET['id'];
$user_id = $_SESSION['id_user'] ?? null;

// Ambil data produk
$query = $conn->prepare("SELECT * FROM products WHERE id_product = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    die("Produk tidak ditemukan!");
}

// Ambil kategori
$kategoriQuery = $conn->prepare("
    SELECT c.name 
    FROM product_categories pc
    JOIN categories c ON pc.id_category = c.id_category
    WHERE pc.id_product = ?
");
$kategoriQuery->bind_param("i", $id);
$kategoriQuery->execute();
$kategoriResult = $kategoriQuery->get_result();
$kategoriList = [];
while ($kat = $kategoriResult->fetch_assoc()) {
    $kategoriList[] = $kat['name'];
}

// Ambil rating
$ratingQuery = $conn->prepare("
    SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews 
    FROM reviews 
    WHERE id_product = ?
");
$ratingQuery->bind_param("i", $id);
$ratingQuery->execute();
$ratingResult = $ratingQuery->get_result()->fetch_assoc();
$avgRating = $ratingResult['avg_rating'] ? round($ratingResult['avg_rating'], 1) : null;
$totalReviews = $ratingResult['total_reviews'];

// Cek wishlist jika user login
$isInWishlist = false;
if ($user_id !== null) {
    $wishlistQuery = $conn->prepare("SELECT 1 FROM wishlist WHERE id_user = ? AND id_product = ?");
    $wishlistQuery->bind_param("ii", $user_id, $id);
    $wishlistQuery->execute();
    $isInWishlist = $wishlistQuery->get_result()->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($produk['name']) ?> - Detail Produk</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap');

        body {
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(120deg, #0f0f0f, #1a1a1a);
            color: #fff;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #121212;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #0ff3;
        }

        img {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px #0ff5;
        }

        h1 {
            color: #f0f;
            margin-bottom: 10px;
            font-size: 32px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        .harga {
            color: #0ff;
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
        }

        .kategori, .rating, .stok {
            margin-top: 10px;
            font-size: 16px;
        }

        .rating {
            color: #ffd700;
        }

        a.back {
            display: inline-block;
            margin-top: 20px;
            color: #0ff;
            text-decoration: none;
            transition: color 0.3s;
        }

        a.back:hover {
            color: #f0f;
        }

        .btn {
            display: inline-block;
            background: #f0f;
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 20px;
            transition: background 0.3s, transform 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn:hover:enabled {
            background: #0ff;
            color: #111;
            transform: scale(1.05);
        }

        .btn:disabled {
            background: #555;
            color: #999;
            cursor: not-allowed;
        }

        .product-details {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        .product-image {
            flex: 1;
            min-width: 300px;
        }

        .product-info {
            flex: 2;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-ulasan {
            background: #0ff;
            color: #111;
            transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
        }

        .btn-ulasan:hover {
            background: #0cf;
            box-shadow: 0 0 12px #0ff;
            transform: scale(1.05);
        }

    </style>
</head>
<body>

<?php if (isset($_GET['error'])): ?>
    <script>alert(<?= json_encode($_GET['error']); ?>);</script>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <script>alert(<?= json_encode($_GET['success']); ?>);</script>
<?php endif; ?>

<div class="container">
    <div class="product-details">
        <div class="product-image">
            <img src="uploads/<?= htmlspecialchars($produk['image']) ?>" alt="<?= htmlspecialchars($produk['name']) ?>">
        </div>
        <div class="product-info">
            <h1><?= htmlspecialchars($produk['name']) ?></h1>
            <p class="harga">Rp <?= number_format($produk['price'], 0, ',', '.') ?></p>
            <p class="stok">Stok: <?= $produk['stock'] ?></p>
            <p><?= nl2br(htmlspecialchars($produk['description'])) ?></p>

            <?php if (!empty($kategoriList)): ?>
                <p class="kategori">Kategori: <?= implode(', ', array_map('htmlspecialchars', $kategoriList)) ?></p>
            <?php endif; ?>

            <?php if ($totalReviews > 0): ?>
                <p class="rating">Rating: <?= $avgRating ?> / 5 (<?= $totalReviews ?> ulasan)</p>
            <?php else: ?>
                <p class="rating">Belum ada ulasan</p>
            <?php endif; ?>

            <div class="button-group">
                <form action="tambah_keranjang.php" method="post" onsubmit="return confirmLogin();">
                    <input type="hidden" name="id_product" value="<?= $produk['id_product'] ?>">
                    <button type="submit" class="btn" <?= $produk['stock'] < 1 ? 'disabled' : '' ?>>
                        <?= $produk['stock'] < 1 ? 'Stok Habis' : 'Tambah ke Keranjang 🛒' ?>
                    </button>
                </form>

                <a href="ulasan.php?id=<?= $produk['id_product'] ?>" class="btn btn-ulasan">
                    Lihat Ulasan
                </a>
            </div>

            <br>
            <a href="products.php" class="back">← Kembali ke Daftar Produk</a>
        </div>
    </div>
</div>

<script>
function confirmLogin() {
    const isLoggedIn = <?= json_encode($user_id !== null) ?>;
    if (!isLoggedIn) {
        alert("Silakan login terlebih dahulu untuk menambahkan ke keranjang.");
        return false;
    }
    return true;
}
</script>
</body>
</html>
