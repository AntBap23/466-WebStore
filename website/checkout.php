<?php
session_start();
require 'db_config.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
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
  <title>Checkout - TechStore</title>
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
    <div class="container">
      <h1 class="section-title">Checkout</h1>
      
      <div class="checkout-grid">
        <div>
          <form action="place_order.php" method="post" id="checkoutForm">
            <h2>Shipping Information</h2>
            <div class="form-group">
              <label for="first_name" class="form-label">First Name</label>
              <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label for="last_name" class="form-label">Last Name</label>
              <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label for="phone" class="form-label">Phone</label>
              <input type="tel" id="phone" name="phone" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label for="street" class="form-label">Street Address</label>
              <input type="text" id="street" name="shipping_street" class="form-control" required>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="city" class="form-label">City</label>
                <input type="text" id="city" name="shipping_city" class="form-control" required>
              </div>
              
              <div class="form-group">
                <label for="state" class="form-label">State</label>
                <input type="text" id="state" name="shipping_state" class="form-control" required>
              </div>
            </div>
            
            <div class="form-group">
              <label for="zip" class="form-label">ZIP Code</label>
              <input type="text" id="zip" name="shipping_zip" class="form-control" required>
            </div>
            
            <div class="form-group">
              <label>
                <input type="checkbox" id="sameAsBilling" checked> 
                Billing address same as shipping
              </label>
            </div>
            
            <div id="billingSection" style="display: none;">
              <h2>Billing Information</h2>
              <div class="form-group">
                <label for="billing_street" class="form-label">Street Address</label>
                <input type="text" id="billing_street" name="billing_street" class="form-control">
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="billing_city" class="form-label">City</label>
                  <input type="text" id="billing_city" name="billing_city" class="form-control">
                </div>
                
                <div class="form-group">
                  <label for="billing_state" class="form-label">State</label>
                  <input type="text" id="billing_state" name="billing_state" class="form-control">
                </div>
              </div>
              
              <div class="form-group">
                <label for="billing_zip" class="form-label">ZIP Code</label>
                <input type="text" id="billing_zip" name="billing_zip" class="form-control">
              </div>
            </div>
            
            <h2>Payment Method</h2>
            <div class="form-group">
              <label class="form-label">Select Payment Method</label>
              <div>
                <label>
                  <input type="radio" name="payment_method" value="credit_card" checked> Credit Card
                </label>
                <label style="margin-left: 20px;">
                  <input type="radio" name="payment_method" value="paypal"> PayPal
                </label>
              </div>
            </div>
            
            <div id="creditCardSection">
              <div class="form-group">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456">
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="card_expiry" class="form-label">Expiry Date</label>
                  <input type="text" id="card_expiry" name="card_expiry" class="form-control" placeholder="MM/YY">
                </div>
                
                <div class="form-group">
                  <label for="card_cvv" class="form-label">CVV</label>
                  <input type="text" id="card_cvv" name="card_cvv" class="form-control" placeholder="123">
                </div>
              </div>
            </div>
          </form>
        </div>
        
        <div>
          <div class="checkout-summary">
            <h2>Order Summary</h2>
            
            <?php foreach ($cart_items as $item): ?>
              <div class="checkout-product">
                <div class="checkout-product-name">
                  <?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?>
                </div>
                <div>$<?= number_format($item['subtotal'], 2) ?></div>
              </div>
            <?php endforeach; ?>
            
            <div class="checkout-total">
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
            </div>
            
            <button type="submit" form="checkoutForm" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
              Place Order
            </button>
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
    
    // Billing address toggle
    document.getElementById('sameAsBilling').addEventListener('change', function() {
      document.getElementById('billingSection').style.display = this.checked ? 'none' : 'block';
    });
    
    // Form validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Basic validation
      let valid = true;
      const requiredFields = this.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.style.borderColor = 'var(--danger)';
          valid = false;
        } else {
          field.style.borderColor = '';
        }
      });
      
      if (valid) {
        // In a real application, this would submit the form
        // For this prototype, we'll redirect to a confirmation page
        window.location.href = 'order_confirmation.php';
      }
    });
  </script>
</body>
</html>