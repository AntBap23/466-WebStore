<?php
session_start();
require 'db_config.php';

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid order ID");
}
$order_id = (int)$_GET['id'];

// In a real application, you would check if this order belongs to the logged-in user
$customer_id = $_SESSION['customer_id'] ?? 1;

// Fetch order details
$stmt = $pdo->prepare("
  SELECT o.order_id, o.order_date, o.status, o.total_amount,
         c.first_name, c.last_name, c.email
    FROM `Order` o
    JOIN Customer c ON o.customer_id = c.customer_id
   WHERE o.order_id = ? AND o.customer_id = ?
");
$stmt->execute([$order_id, $customer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
  die("Order not found or access denied");
}

// Fetch order items
$stmt = $pdo->prepare("
  SELECT oi.quantity, oi.price_at_order_time, 
         p.name, p.product_id
    FROM OrderItem oi
    JOIN Product p ON oi.product_id = p.product_id
   WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Fetch shipping address
$stmt = $pdo->prepare("
  SELECT street, city, state, zip
    FROM Address
   WHERE customer_id = ? AND type = 'shipping'
   LIMIT 1
");
$stmt->execute([$customer_id]);
$shipping = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order #<?= $order_id ?> - TechStore</title>
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
      <div style="margin-bottom: 20px;">
        <a href="user_orders.php" class="btn btn-secondary btn-sm">
          <i class="fas fa-arrow-left"></i> Back to My Orders
        </a>
      </div>
      
      <div class="order-item" style="margin-bottom: 30px;">
        <div class="order-header">
          <div class="order-id">Order #<?= htmlspecialchars($order['order_id']) ?></div>
          <div class="order-date"><?= date('F j, Y', strtotime($order['order_date'])) ?></div>
        </div>
        
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
        
        <div style="margin: 20px 0;">
          <span class="order-status <?= $status_class ?>">
            <?= htmlspecialchars($order['status']) ?>
          </span>
        </div>
      </div>
      
      <div class="checkout-grid">
        <div>
          <h2>Order Items</h2>
          <table class="cart-table" style="margin-top: 20px;">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): 
                $subtotal = $item['price_at_order_time'] * $item['quantity'];
              ?>
                <tr>
                  <td data-label="Product">
                    <a href="product_detail.php?id=<?= $item['product_id'] ?>">
                      <?= htmlspecialchars($item['name']) ?>
                    </a>
                  </td>
                  <td data-label="Price">$<?= number_format($item['price_at_order_time'], 2) ?></td>
                  <td data-label="Quantity"><?= $item['quantity'] ?></td>
                  <td data-label="Total">$<?= number_format($subtotal, 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
          <div class="cart-total" style="margin-top: 20px;">
            <div class="cart-total-row grand-total">
              <div class="cart-total-label">Total:</div>
              <div class="cart-total-value">$<?= number_format($order['total_amount'], 2) ?></div>
            </div>
          </div>
        </div>
        
        <div>
          <div class="checkout-summary">
            <h2>Order Information</h2>
            
            <div style="margin: 20px 0;">
              <h3>Customer Details</h3>
              <p>
                <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?><br>
                <?= htmlspecialchars($order['email']) ?>
              </p>
            </div>
            
            <?php if ($shipping): ?>
              <div style="margin: 20px 0;">
                <h3>Shipping Address</h3>
                <p>
                  <?= htmlspecialchars($shipping['street']) ?><br>
                  <?= htmlspecialchars($shipping['city']) ?>, <?= htmlspecialchars($shipping['state']) ?> <?= htmlspecialchars($shipping['zip']) ?>
                </p>
              </div>
            <?php endif; ?>
            
            <div style="margin: 20px 0;">
              <h3>Payment Method</h3>
              <p>Credit Card</p>
            </div>
            
            <?php if ($order['status'] == 'Shipped'): ?>
              <div style="margin: 20px 0;">
                <h3>Tracking Information</h3>
                <p>Tracking #: 1Z999AA10123456784</p>
                <a href="#" class="btn btn-secondary btn-sm" style="margin-top: 10px;">Track Package</a>
              </div>
            <?php endif; ?>
          </div>
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