<?php
session_start();
require 'db_config.php';

// In a real application, this would be set when the user logs in
$customer_id = $_SESSION['customer_id'] ?? 1;

// Fetch orders for this customer
$stmt = $pdo->prepare("
  SELECT order_id, order_date, status, total_amount
    FROM `Order`
   WHERE customer_id = ?
ORDER BY order_date DESC
");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - TechStore</title>
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
            <li><a href="user_orders.php" class="active">My Orders</a></li>
            <li><a href="admin_orders.php">Admin</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="section">
    <div class="container">
      <h1 class="section-title">My Orders</h1>
      
      <?php if (empty($orders)): ?>
        <div style="text-align: center; margin: 40px 0;">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px; color: var(--gray);">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
            <path d="M9 12h6"></path>
            <path d="M9 16h6"></path>
            <path d="M9 8h6"></path>
          </svg>
          <p>You haven't placed any orders yet.</p>
          <a href="product_list.php" class="btn btn-primary" style="margin-top: 20px;">Start Shopping</a>
        </div>
      <?php else: ?>
        <ul class="orders-list">
          <?php foreach ($orders as $order): 
            $status_class = '';
            switch ($order['status']) {
              case 'Processing':
                $status_class = 'status-processing';
                break;
              case 'Shipped':
                $status_class = 'status-shipped';
                break;
              case 'Delivered':
                $status_class = 'status-delivered';
                break;
            }
          ?>
            <li class="order-item">
              <div class="order-header">
                <a href="order_detail.php?id=<?= $order['order_id'] ?>" class="order-id">
                  Order #<?= htmlspecialchars($order['order_id']) ?>
                </a>
                <div class="order-date">
                  <?= date('F j, Y', strtotime($order['order_date'])) ?>
                </div>
              </div>
              
              <div class="order-details">
                <div>
                  <strong>Status:</strong>
                  <span class="order-status <?= $status_class ?>">
                    <?= htmlspecialchars($order['status']) ?>
                  </span>
                </div>
                <div>
                  <strong>Total:</strong>
                  $<?= number_format($order['total_amount'], 2) ?>
                </div>
              </div>
              
              <a href="order_detail.php?id=<?= $order['order_id'] ?>" class="btn btn-secondary btn-sm">
                View Order Details
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
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