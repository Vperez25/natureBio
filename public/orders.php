<?php
session_start();
require_once __DIR__ . '/../src/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, order_date, total_amount, status 
        FROM orders 
        WHERE user_id = ?
        ORDER BY order_date DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/orders.css">
</head>

<body>
<?php include_once __DIR__ . '/../src/views/layouts/header.php'; ?>
<div class="back-to-account">
    <a href="account.php">&larr; Back to Account</a>
</div>
<main class="orders-container">
    <h1>Your Orders</h1>

    <?php if (empty($orders)): ?>
        <p>You have no orders yet.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-summary">
                <h2>Order ID: <?= htmlspecialchars($order['id']) ?></h2>
                <p>
                    <strong>Date:</strong> <?= date("F j, Y", strtotime($order['order_date'])) ?><br>
                    <strong>Total Items:</strong> <?= getOrderItemCount($pdo, $order['id']) ?><br>
                    <strong>Total Price:</strong> $<?= number_format($order['total_amount'], 2) ?>
                </p>
                <a href="orderDetails.php?order_id=<?= $order['id'] ?>" class="view-order-button">View Order</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../src/views/layouts/footer.php'; ?>
</body>
</html>

<?php

function getOrderItemCount($pdo, $order_id) {
    $stmt = $pdo->prepare("
        SELECT SUM(quantity) AS total_items 
        FROM order_items 
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_items'] ?? 0;
}
?>
