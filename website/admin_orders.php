<?php
session_start();
require 'db_config.php';

// Get filter status if provided
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;

// Build query based on filter
$query = "
    SELECT o.order_id, o.order_date, o.status, o.total_amount,
           c.first_name, c.last_name, c.email
      FROM `Order` o
      JOIN Customer c ON o.customer_id = c.customer_id
";

// Add WHERE clause for status filtering
$params = [];
if ($status_filter) {
    $query .= " WHERE o.status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - TechStore</title>
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
            <li><a href="admin_orders.php" class="active">Admin</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="section">
    <div class="container">
      <div class="admin-header">
        <h1>Admin Dashboard</h1>
        
        <div class="admin-filter">
          <label for="statusFilter">Filter by Status:</label>
          <select id="statusFilter" class="filter-select">
            <option value="">All Orders</option>
            <option value="Processing" <?= $status_filter == 'Processing' ? 'selected' : '' ?>>Processing</option>
            <option value="Shipped" <?= $status_filter == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="Delivered" <?= $status_filter == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
          </select>
        </div>
      </div>
      
      <?php if (empty($orders)): ?>
        <p>No orders found.</p>
      <?php else: ?>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                <td>
                  <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
                  <small><?= htmlspecialchars($order['email']) ?></small>
                </td>
                <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                <td>
                  <?php 
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
                  <span class="order-status <?= $status_class ?>">
                    <?= htmlspecialchars($order['status']) ?>
                  </span>
                </td>
                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                <td>
                  <a href="admin_order_detail.php?id=<?= $order['order_id'] ?>" class="btn btn-primary btn-sm">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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
    
    // Status filter handler
    document.getElementById('statusFilter').addEventListener('change', function() {
      const status = this.value;
      if (status) {
        window.location.href = 'admin_orders.php?status=' + status;
      } else {
        window.location.href = 'admin_orders.php';
      }
    });
  </script>
</body>
</html>