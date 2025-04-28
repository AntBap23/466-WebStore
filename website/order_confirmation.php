<?php
session_start();
require 'db_config.php';

// Redirect if no order ID in session
if (empty($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_SESSION['last_order_id'];

// Fetch order details
$stmt = $pdo->prepare("
  SELECT o.order_id, o.order_date, o.status, o.total_amount,
         c.first_name, c.last_name, c.email
    FROM `Order` o
    JOIN Customer c ON o.customer_id = c.customer_id
   WHERE o.order_id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation - TechStore</title>
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
            <li><a href="product_list.php">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="user_orders.php">My Orders</a></li>
            <li><a href="admin_orders.php">Admin</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="section">
    <div class="container" style="max-width: 800px; margin: 0 auto; text-align: center;">
      <div style="margin-bottom: 40px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="color: var(--success);">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        
        <h1 style="margin-top: 20px;">Thank You for Your Order!</h1>
        <p style="margin-top: 10px; font-size: 1.1rem;">Your order has been received and is being processed.</p>
      </div>
      
      <div style="background-color: #f8fafc; padding: 30px; border-radius: 10px; text-align: left; margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">Order Summary</h2>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
          <span><strong>Order Number:</strong></span>
          <span>#<?= htmlspecialchars($order['order_id']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
          <span><strong>Date:</strong></span>
          <span><?= date('F j, Y', strtotime($order['order_date'])) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
          <span><strong>Status:</strong></span>
          <span class="order-status status-processing">Processing</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--light-gray);">
          <span><strong>Total:</strong></span>
          <span style="font-size: 1.2rem; font-weight: 700; color: var(--primary);">$<?= number_format($order['total_amount'], 2) ?></span>
        </div>
      </div>
      
      <p>An email with your order details has been sent to <?= htmlspecialchars($order['email']) ?>.</p>
      <p>You can track your order status in the <a href="user_orders.php">My Orders</a> section.</p>
      
      <div style="margin-top: 40px;">
        <a href="product_list.php" class="btn btn-primary">Continue Shopping</a>
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