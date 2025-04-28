<?php
session_start();
require 'db_config.php';


$customer_id = $_SESSION['customer_id'] ?? 1;

// fetch this customer’s orders
$stmt = $pdo->prepare("
  SELECT order_id, order_date, status, total_amount
    FROM `Order`
   WHERE customer_id = ?
ORDER BY order_date DESC
");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Orders — Online Store</title>
</head>
<body>
  <?php include 'header.php'; ?>

  <h1>Order History</h1>

  <?php if (empty($orders)): ?>
    <p>You have placed no orders yet.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($orders as $o): ?>
        <li>
          <a href="order_detail.php?id=<?= $o['order_id'] ?>">
            Order #<?= htmlspecialchars($o['order_id'], ENT_QUOTES) ?>
          </a>
          — <?= htmlspecialchars($o['order_date'], ENT_QUOTES) ?>
          — Status: <?= htmlspecialchars($o['status'], ENT_QUOTES) ?>
          — Total: $<?= number_format($o['total_amount'], 2) ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>
