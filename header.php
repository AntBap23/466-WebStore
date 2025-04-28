<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav>
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="product_list.php">Shop</a></li>
    <li><a href="cart.php">Cart</a></li>
    <li><a href="user_orders.php">My Orders</a></li>
    <li><a href="admin_orders.php">Admin</a></li>
  </ul>
</nav>
