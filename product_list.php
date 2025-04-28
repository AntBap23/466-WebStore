<?php
session_start();
require 'db_config.php';

$stmt = $pdo->query("
    SELECT product_id, name, price, quantity_in_stock
      FROM Product
     WHERE quantity_in_stock > 0
  ORDER BY name
");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Products</title></head>
<body>
  <?php include 'header.php'; ?>
  <h1>Products In Stock</h1>
  <?php if (empty($products)): ?>
    <p>No products available.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($products as $p): ?>
        <li>
          <strong><?= htmlspecialchars($p['name']) ?></strong> —
          $<?= number_format($p['price'],2) ?> —
          In stock: <?= (int)$p['quantity_in_stock'] ?>
          <a href="product_detail.php?id=<?= $p['product_id'] ?>">View</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>
