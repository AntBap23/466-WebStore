<?php
session_start();
require 'db_config.php';

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Validate required fields
$required_fields = [
    'first_name', 'last_name', 'email', 'phone',
    'shipping_street', 'shipping_city', 'shipping_state', 'shipping_zip',
    'payment_method'
];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Missing required field: $field");
    }
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // 1. Create/update customer
    $email = $_POST['email'];
    
    // Check if customer exists
    $stmt = $pdo->prepare("SELECT customer_id FROM Customer WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();
    
    if ($customer) {
        $customer_id = $customer['customer_id'];
        
        // Update customer info
        $stmt = $pdo->prepare("
            UPDATE Customer 
               SET first_name = ?, last_name = ?, phone_number = ?
             WHERE customer_id = ?
        ");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['phone'],
            $customer_id
        ]);
    } else {
        // Create new customer
        $stmt = $pdo->prepare("
            INSERT INTO Customer (first_name, last_name, email, phone_number)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone']
        ]);
        $customer_id = $pdo->lastInsertId();
    }
    
    // Save customer ID in session
    $_SESSION['customer_id'] = $customer_id;
    
    // 2. Add/update shipping address
    // Check if address exists
    $stmt = $pdo->prepare("
        SELECT address_id FROM Address 
         WHERE customer_id = ? AND type = 'shipping'
    ");
    $stmt->execute([$customer_id]);
    $address = $stmt->fetch();
    
    if ($address) {
        // Update address
        $stmt = $pdo->prepare("
            UPDATE Address
               SET street = ?, city = ?, state = ?, zip = ?
             WHERE address_id = ?
        ");
        $stmt->execute([
            $_POST['shipping_street'],
            $_POST['shipping_city'],
            $_POST['shipping_state'],
            $_POST['shipping_zip'],
            $address['address_id']
        ]);
    } else {
        // Create new address
        $stmt = $pdo->prepare("
            INSERT INTO Address (customer_id, type, street, city, state, zip)
            VALUES (?, 'shipping', ?, ?, ?, ?)
        ");
        $stmt->execute([
            $customer_id,
            $_POST['shipping_street'],
            $_POST['shipping_city'],
            $_POST['shipping_state'],
            $_POST['shipping_zip']
        ]);
    }
    
    // 3. Handle billing address if different
    if (!isset($_POST['sameAsBilling']) && isset($_POST['billing_street'])) {
        $stmt = $pdo->prepare("
            SELECT address_id FROM Address 
             WHERE customer_id = ? AND type = 'billing'
        ");
        $stmt->execute([$customer_id]);
        $billing = $stmt->fetch();
        
        if ($billing) {
            // Update billing address
            $stmt = $pdo->prepare("
                UPDATE Address
                   SET street = ?, city = ?, state = ?, zip = ?
                 WHERE address_id = ?
            ");
            $stmt->execute([
                $_POST['billing_street'],
                $_POST['billing_city'],
                $_POST['billing_state'],
                $_POST['billing_zip'],
                $billing['address_id']
            ]);
        } else {
            // Create new billing address
            $stmt = $pdo->prepare("
                INSERT INTO Address (customer_id, type, street, city, state, zip)
                VALUES (?, 'billing', ?, ?, ?, ?)
            ");
            $stmt->execute([
                $customer_id,
                $_POST['billing_street'],
                $_POST['billing_city'],
                $_POST['billing_state'],
                $_POST['billing_zip']
            ]);
        }
    }
    
    // 4. Calculate order total and create order
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
    
    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO `Order` (customer_id, status, total_amount)
        VALUES (?, 'Processing', ?)
    ");
    $stmt->execute([$customer_id, $total]);
    $order_id = $pdo->lastInsertId();
    
    // 5. Add order items
    $stmt = $pdo->prepare("
        INSERT INTO OrderItem (order_id, product_id, quantity, price_at_order_time)
        VALUES (?, ?, ?, ?)
    ");
    
    foreach ($cart_items as $item) {
        $stmt->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
        
        // Update product stock
        $pdo->prepare("
            UPDATE Product
               SET quantity_in_stock = quantity_in_stock - ?
             WHERE product_id = ?
        ")->execute([
            $item['quantity'],
            $item['id']
        ]);
    }
    
    // 6. Create payment record
    $stmt = $pdo->prepare("
        INSERT INTO Payment (order_id, payment_method, amount)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $order_id,
        $_POST['payment_method'],
        $total
    ]);
    
    // 7. Clear the cart
    $_SESSION['cart'] = [];
    
    // Commit the transaction
    $pdo->commit();
    
    // Save order ID in session for confirmation page
    $_SESSION['last_order_id'] = $order_id;
    
    // Redirect to confirmation page
    header('Location: order_confirmation.php');
    exit;
    
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    die("Error processing order: " . $e->getMessage());
}