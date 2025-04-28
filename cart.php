<?php
session_start();
require 'db_config.php';

// init cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// handle POST (add/update/remove)
if ($_SERVER['REQUEST_METHOD']==='POST') {
  
  header("Location: cart.php");
  exit;
}

// fetch items for display
$cart_items = [];
if ($_SESSION['cart']) {
  $ids = array_keys($_SESSION['cart']);
  $ph  = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT product_id,name,price FROM Product WHERE product_id IN ($ph)");
  $stmt->execute($ids);
  $cart_items = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Your Cart</title></head>
<body>
  <?php include 'header.php'; ?>
  <h1>Your Cart</h1>
  <?php if (!$cart_items): ?>
    <p>Cart is empty.</p>
  <?php else: ?>
    <form method="post">
      <table border="1"><tr>
        <th>Name</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th>
      </tr>
      <?php foreach($cart_items as $it): 
        $pid = $it['product_id'];
        $qty = $_SESSION['cart'][$pid];
        $sub = $it['price']*$qty;
      ?>
      <tr>
        <td><?= htmlspecialchars($it['name']) ?></td>
        <td>$<?= number_format($it['price'],2) ?></td>
        <td>
          <input type="number" name="quantity" value="<?= $qty ?>" min="0">
          <input type="hidden" name="product_id" value="<?= $pid ?>">
        </td>
        <td>$<?= number_format($sub,2) ?></td>
        <td><button name="remove" value="<?= $pid ?>">Remove</button></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <button>Update Cart</button>
    <a href="checkout.php">Checkout</a>
    </form>
  <?php endif; ?>
</body>
</html>
