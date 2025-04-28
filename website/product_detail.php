<?php
session_start();
require 'db_config.php';

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid product ID");
}
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
  SELECT name, description, price, quantity_in_stock
    FROM Product
   WHERE product_id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch() ?: die("Product not found");

// Determine stock status
$stock_status = "In Stock";
$status_class = "in-stock";
if ($product['quantity_in_stock'] < 10 && $product['quantity_in_stock'] > 0) {
  $stock_status = "Low Stock";
  $status_class = "low-stock";
} elseif ($product['quantity_in_stock'] <= 0) {
  $stock_status = "Out of Stock";
  $status_class = "out-of-stock";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> - TechStore</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="container">
      <div class="header-content">
        <a href="index.php" class="logo">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
          </svg>
          TechStore
        </a>
        
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <i class="fas fa-bars"></i>
        </button>
        
        <nav>
          <ul id="navMenu">
            <li><a href="index.php">Home</a></li>
            <li><a href="product_list.php" class="active">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="user_orders.php">My Orders</a></li>
            <li><a href="admin_orders.php">Admin</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="section">
    <div class="container">
      <div class="product-detail">
        <div class="product-detail-img">
          <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
            <line x1="8" y1="21" x2="16" y2="21"></line>
            <line x1="12" y1="17" x2="12" y2="21"></line>
          </svg>
        </div>
        
        <div class="product-detail-info">
          <h1><?= htmlspecialchars($product['name']) ?></h1>
          <p class="product-detail-price">$<?= number_format($product['price'], 2) ?></p>
          <span class="stock-status <?= $status_class ?>"><?= $stock_status ?> (<?= $product['quantity_in_stock'] ?> items available)</span>
          
          <div class="product-description">
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
          </div>
          
          <?php if ($product['quantity_in_stock'] > 0): ?>
            <form action="cart.php" method="post">
              <input type="hidden" name="product_id" value="<?= $id ?>">
              
              <div class="quantity-selector">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" class="quantity-input" 
                      min="1" max="<?= $product['quantity_in_stock'] ?>" value="1">
              </div>
              
              <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
          <?php else: ?>
            <p>We're sorry, this item is currently out of stock.</p>
            <button class="btn btn-primary" disabled>Out of Stock</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container">
      <div class="footer-grid">
        <div>
          <div class="footer-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            TechStore
          </div>
          <p class="footer-about">We provide the latest technology products to enhance your digital lifestyle.</p>
        </div>
        
        <div>
          <h3 class="footer-title">Shop</h3>
          <ul class="footer-links">
            <li><a href="product_list.php">All Products</a></li>
            <li><a href="#">New Arrivals</a></li>
            <li><a href="#">Best Sellers</a></li>
            <li><a href="#">On Sale</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="footer-title">Account</h3>
          <ul class="footer-links">
            <li><a href="user_orders.php">My Orders</a></li>
            <li><a href="cart.php">My Cart</a></li>
            <li><a href="#">My Wishlist</a></li>
            <li><a href="#">My Account</a></li>
          </ul>
        </div>
        
        <div>
          <h3 class="footer-title">Information</h3>
          <ul class="footer-links">
            <li><a href="#">About Us</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms & Conditions</a></li>
            <li><a href="#">Contact Us</a></li>
          </ul>
        </div>
      </div>
      
      <div class="copyright">
        &copy; 2025 TechStore. All rights reserved. CSCI 466 Group Project.
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
      document.getElementById('navMenu').classList.toggle('show');
    });
  </script>
</body>
</html>