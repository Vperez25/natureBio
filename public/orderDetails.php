<?php
session_start();
require_once __DIR__ . '/../src/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: orders.php");
    exit;
}

$order_id = (int)$_GET['order_id'];

try {

    $stmt = $pdo->prepare("
        SELECT oi.product_id, oi.quantity, oi.price, oi.name, oi.image_path 
        FROM order_items oi 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/orderDetails.css">
</head>

<body>
<?php include_once __DIR__ . '/../src/views/layouts/header.php'; ?>
<div class="back-to-account">
    <a href="account.php">&larr; Back to Account</a>
</div>
<main class="order-details-container">
    <h1>Order Details (ID: <?= htmlspecialchars($order_id) ?>)</h1>

    <?php if (empty($orderItems)): ?>
        <p>No items found for this order.</p>
    <?php else: ?>
        <?php foreach ($orderItems as $item): ?>
            <div class="order-item">
                <img src="<?= ASSETS_URL . htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                <div class="item-details">
                    <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <p>Price: $<?= number_format($item['price'], 2) ?></p>
                    <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                    <p>Subtotal: $<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../src/views/layouts/footer.php'; ?>
</body>
</html>
