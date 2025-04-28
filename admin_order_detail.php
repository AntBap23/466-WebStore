<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin — Order #456</title>
</head>
<body>
  <nav>
    <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="product_list.php">Shop</a></li>
    <li><a href="cart.php">Cart</a></li>
    <li><a href="user_orders.php">My Orders</a></li>
    <li><a href="admin_orders.php">Admin</a></li>
    </ul>
  </nav>

  <h1>Order #456</h1>
  <p><strong>User:</strong> alice@example.com</p>
  <p><strong>Status:</strong> Processing</p>

  <h2>Items</h2>
  <ul>
    <li>Product A ×2 — $39.98</li>
  </ul>

  <form action="ship_order.php" method="post">
    <input type="hidden" name="order_id" value="456">
    <p>
      <label>
        Tracking Number:<br>
        <input type="text" name="tracking">
      </label>
    </p>
    <button type="submit">Mark as Shipped</button>
  </form>
</body>
</html>
