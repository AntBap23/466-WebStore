<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin — Outstanding Orders</title>
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

  <h1>Outstanding Orders</h1>
  <table border="1" cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>Order #</th><th>User</th><th>Status</th><th>Total</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
    
      <tr>
        <td>456</td>
        <td>alice@example.com</td>
        <td>Processing</td>
        <td>$39.98</td>
        <td><a href="admin_order_detail.html?id=456">View</a></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
