<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page filename for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
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
          <li><a href="index.php" <?= $current_page == 'index.php' ? 'class="active"' : '' ?>>Home</a></li>
          <li><a href="product_list.php" <?= $current_page == 'product_list.php' || $current_page == 'product_detail.php' ? 'class="active"' : '' ?>>Shop</a></li>
          <li><a href="cart.php" <?= $current_page == 'cart.php' || $current_page == 'checkout.php' ? 'class="active"' : '' ?>>Cart
          <?php if (!empty($_SESSION['cart'])): ?>
            <span class="cart-count"><?= count($_SESSION['cart']) ?></span>
          <?php endif; ?>
          </a></li>
          <li><a href="user_orders.php" <?= $current_page == 'user_orders.php' || $current_page == 'order_detail.php' ? 'class="active"' : '' ?>>My Orders</a></li>
          <li><a href="admin_orders.php" <?= $current_page == 'admin_orders.php' || $current_page == 'admin_order_detail.php' ? 'class="active"' : '' ?>>Admin</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>

<style>
.cart-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background-color: var(--accent);
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  margin-left: 5px;
}
</style>

<script>
// Mobile menu toggle
document.getElementById('mobileMenuBtn').addEventListener('click', function() {
  document.getElementById('navMenu').classList.toggle('show');
});
</script>