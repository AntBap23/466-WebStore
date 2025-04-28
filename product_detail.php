<?php
session_start();
require 'db_config.php';

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid product ID");
}
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
  SELECT name, description, price, quantity_in_stock
    FROM Product
   WHERE product_id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch() ?: die("Not found");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title><?= htmlspecialchars($product['name']) ?></title></head>
<body>
  <?php include 'header.php'; ?>
  <h1><?= htmlspecialchars($product['name']) ?></h1>
  <p>Price: $<?= number_format($product['price'],2) ?></p>
  <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
  <form action="cart.php" method="post">
    <input type="hidden" name="product_id" value="<?= $id ?>">
    <label>Qty <input type="number" name="quantity"
        min="1" max="<?= $product['quantity_in_stock'] ?>" value="1"></label>
    <button>Add to Cart</button>
  </form>
</body>
</html>
