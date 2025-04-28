<?php
session_start();
require 'db_config.php';

// build $cart_items & $total as you have
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Checkout</title></head>
<body>
  <?php include 'header.php'; ?>
  <h1>Checkout</h1>
  <?php if (empty($cart_items)): ?>
    <p>Cart empty—<a href="product_list.php">shop</a>.</p>
  <?php else: ?>
    <form action="place_order.php" method="post">
      <!-- shipping, billing fields -->
      <h2>Order Summary</h2>
      <ul>
        <?php foreach ($cart_items as $it): ?>
          <li><?= htmlspecialchars($it['name']) ?> × <?= $_SESSION['cart'][$it['product_id']] ?>
              @ $<?= number_format($it['price'],2) ?></li>
        <?php endforeach; ?>
      </ul>
      <p><strong>Total: $<?= number_format($total,2) ?></strong></p>
      <button>Place Order</button>
    </form>
  <?php endif; ?>
</body>
</html>
