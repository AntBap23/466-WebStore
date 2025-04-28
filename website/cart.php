<?php
session_start();
require 'db_config.php';

// Init cart if needed
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Handle POST requests (add/update/remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Add item to cart
  if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
      // If already in cart, update quantity
      if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
      } else {
        $_SESSION['cart'][$product_id] = $quantity;
      }
    }
  }
  
  // Update cart
  if (isset($_POST['update'])) {
    foreach ($_POST['items'] as $product_id => $quantity) {
      $product_id = (int)$product_id;
      $quantity = (int)$quantity;
      
      if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
      } else {
        unset($_SESSION['cart'][$product_id]);
      }
    }
  }
  
  // Remove item
  if (isset($_POST['remove'])) {
    $product_id = (int)$_POST['remove'];
    unset($_SESSION['cart'][$product_id]);
  }
  
  // Redirect back to cart (to avoid form resubmission)
  header('Location: cart.php');
  exit;
}

// Fetch cart items
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
  $ids = array_keys($_SESSION['cart']);
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  
  $stmt = $pdo->prepare("
    SELECT product_id, name, price, quantity_in_stock 
      FROM Product 
     WHERE product_id IN ($placeholders)
  ");
  $stmt->execute($ids);
  $products = $stmt->fetchAll();
  
  // Format cart items and calculate total
  foreach ($products as $product) {
    $product_id = $product['product_id'];
    $quantity = min($_SESSION['cart'][$product_id], $product['quantity_in_stock']);
    $subtotal = $product['price'] * $quantity;
    
    $cart_items[] = [
      'id' => $product_id,
      'name' => $product['name'],
      'price' => $product['price'],
      'quantity' => $quantity,
      'max_quantity' => $product['quantity_in_stock'],
      'subtotal' => $subtotal
    ];
    
    $total += $subtotal;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - TechStore</title>
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
            <li><a href="cart.php" class="active">Cart</a></li>
            <li><a href="user_orders.php">My Orders</a></li>
            <li><a href="admin_orders.php">Admin</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="section">
    <div class="container">
      <h1 class="section-title">Your Shopping Cart</h1>
      
      <?php if (empty($cart_items)): ?>
        <div style="text-align: center; margin: 40px 0;">
          <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px; color: var(--gray);">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <p>Your cart is empty.</p>
          <a href="product_list.php" class="btn btn-primary" style="margin-top: 20px;">Continue Shopping</a>
        </div>
      <?php else: ?>
        <form method="post" action="cart.php">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart_items as $item): ?>
                <tr>
                  <td data-label="Product">
                    <div class="cart-product">
                      <div class="cart-product-img">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                          <line x1="8" y1="21" x2="16" y2="21"></line>
                          <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                      </div>
                      <div>
                        <a href="product_detail.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                      </div>
                    </div>
                  </td>
                  <td data-label="Price">$<?= number_format($item['price'], 2) ?></td>
                  <td data-label="Quantity">
                    <input type="number" name="items[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" 
                           min="0" max="<?= $item['max_quantity'] ?>" class="cart-quantity">
                  </td>
                  <td data-label="Subtotal">$<?= number_format($item['subtotal'], 2) ?></td>
                  <td data-label="Action">
                    <button type="submit" name="remove" value="<?= $item['id'] ?>" class="btn btn-danger btn-sm">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
          <div class="cart-actions">
            <a href="product_list.php" class="btn btn-secondary">Continue Shopping</a>
            <button type="submit" name="update" value="1" class="btn btn-primary">Update Cart</button>
          </div>
        </form>
        
        <div class="cart-total">
          <div class="cart-total-row">
            <div class="cart-total-label">Subtotal:</div>
            <div class="cart-total-value">$<?= number_format($total, 2) ?></div>
          </div>
          
          <div class="cart-total-row">
            <div class="cart-total-label">Shipping:</div>
            <div class="cart-total-value">Free</div>
          </div>
          
          <div class="cart-total-row grand-total">
            <div class="cart-total-label">Total:</div>
            <div class="cart-total-value">$<?= number_format($total, 2) ?></div>
          </div>
          
          <div style="text-align: right; margin-top: 20px;">
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
          </div>
        </div>
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