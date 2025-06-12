<?php
session_start();

$username = $_SESSION['username'] ?? null;
$role = $_SESSION['role'] ?? null;
$cartItems = $_SESSION['cart'] ?? [];
$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tricity</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
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
        <li><a href="#">Blog</a></li>
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


  <main class="hero">
  <video autoplay muted loop class="hero-bg-video">
    <source src="video/gamingBackground.mp4" type="video/mp4" />
    Your browser does not support the video tag.
  </video>

  <div class="hero-text">
    <h1>Elevate Your Experience<br>With Top-Tier Gaming Gear</h1>
    <p>Discover the Cutting-Edge Gear That Will Revolutionize Your Gaming Journey</p>
    <a href="#" class="cta-button">Shop The Collection →</a>
  </div>

  <div class="hero-img">
    <img src="gambar/Logitech_G502_Lightspeed_Wireless_Optical_Gaming_Mouse_with_RGB_Lighting_Wireless_Black_910-005565-removebg-preview.png" alt="Gaming Mouse" />
  </div>
</main>


  <section class="features">
  <div class="feature">
    <i class="fas fa-truck"></i>
    <h3>Free Shipping</h3>
    <p>Free Shipping to Make Your Shopping Experience Seamless.</p>
  </div>
  <div class="feature">
    <i class="fas fa-undo-alt"></i>
    <h3>Return Policy</h3>
    <p>Flexible Returns to Ensure a Positive Shopping Experience.</p>
  </div>
  <div class="feature">
    <i class="fas fa-piggy-bank"></i>
    <h3>Save Money</h3>
    <p>Shop Smarter and Save Big with Our Money-Saving Solutions.</p>
  </div>
  <div class="feature">
    <i class="fas fa-headset"></i>
    <h3>Support 24/7</h3>
    <p>Unparalleled Support, Tailored to Your Needs 24 Hours a Day.</p>
  </div>
</section>

<section class="our-story">
    <div class="video" id="videoTrigger">
      <img src="gambar/Logitech G502 HERO.jpg" alt="Video Thumbnail" class="video-thumbnail" />
      <div class="play-button"><i class="fas fa-play"></i></div>
    </div>
    <div class="story-text">
      <h2>Our Story</h2>
      <p>
        Driven by gaming passion, we craft the finest gear to empower players.
        Our unwavering innovation and user focus make us an integral part of the
        global gaming community.
      </p>
      <a href="#">Read More <i class="fas fa-arrow-up-right-from-square"></i></a>
    </div>
  </section>

  <div id="videoModal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <video id="popupVideo" controls>
        <source src="video/logitech-g502-hero-gaming-mouse-1080-ytshorts.savetube.me.mp4" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
  </div>

  <script>
    const modal = document.getElementById('videoModal');
    const popupVideo = document.getElementById('popupVideo');
    const videoTrigger = document.getElementById('videoTrigger');
    const closeBtn = document.querySelector('.close-btn');

    videoTrigger.addEventListener('click', () => {
      modal.style.display = 'flex';
      popupVideo.play();
    });

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
      popupVideo.pause();
      popupVideo.currentTime = 0;
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
        popupVideo.pause();
        popupVideo.currentTime = 0;
      }
    });
  </script>

  <section class="categories">
  <h2 class="section-title">CATEGORIES</h2>
  <div class="category-container">
    <div class="category-card">
      <img src="gambar/61AnP7HbarL._AC_SL1200_-removebg-preview.png" alt="Keyboards">
      <h3>Keyboards</h3>
      <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="category-card">
      <img src="gambar/Amazon_com__Glorious_Model_O__Model_O__Matte_Black___Computers___Accessories-removebg-preview.png" alt="Gaming Mouse">
      <h3>Gaming Mouse</h3>
      <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="category-card">
      <img src="gambar/download__1_-removebg-preview.png" alt="Headphones">
      <h3>Headphones</h3>
      <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="category-card">
      <img src="gambar/PS5_And_Xbox_Series_X_Are_Likely_Going_To_Keep_Playing__Price_Chicken__Through_June-removebg-preview.png" alt="Gaming Controllers">
      <h3>Gaming Controllers</h3>
      <a href="products.php">Shop Now <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<section class="featured-products">
  <div class="featured-card" style="background-image: url('gambar/proXSuperlight.jpg');">
    <div class="featured-content">
      <h2>PRO X SUPERLIGHT</h2>
      <p>An iconic 60 g pro gaming mouse designed with the world’s top players.</p>
      <a href="products.php">Buy Now <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
  <div class="featured-card" style="background-image: url('gambar/razer-blackshark-v2-pro-black-03.jpg');">
    <div class="featured-content">
      <h2>RAZER BLACKSHARK V2 PRO</h2>
      <p>A next-generation super wideband mic for unrivalled vocal clarity, on-board audio profiles for competitive FPS titles</p>
      <a href="products.php">Buy Now <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<section class="customer-reviews">
  <h2 class="review-title">Top Players Using This Gears</h2>
  <p class="review-subtitle">Tricity has supported by top Valorant Players</p>
  <div class="review-container">
    <div class="review-card">
      <img src="gambar/tenz.jpg" alt="Customer 1">
    </div>
    <div class="review-card">
      <img src="gambar/f0rsakeN-featured-image-1024x1024.webp" alt="Customer 2">
    </div>
    <div class="review-card">
      <img src="gambar/textureGenG.jpg" alt="Customer 3">
    </div>
    <div class="review-card">
      <img src="gambar/aspas1.jpg" alt="Customer 4">
    </div>
    <div class="review-card">
      <img src="gambar/boaster.jpg" alt="Customer 5">
    </div>
  </div>
</section>

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
