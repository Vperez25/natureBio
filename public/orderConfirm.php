<?php
session_start();
require_once __DIR__.'/../src/helpers/functions.php';



$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "Error: No se encontró la orden.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="<?= ASSETS_URL?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL?>styles/orderConfirm.css">
</head>
<body>
<?php include_once __DIR__.'/../src/views/layouts/header.php'; ?>

<main>
    <div class="confirmation-container">
        <h2>Order Confirmed</h2>
        <p>Thank you for your purchase! Your order ID is <strong>#<?= htmlspecialchars($order_id) ?></strong>.</p>
        <a href="index.php" class="btn">Return to Home</a>
    </div>
</main>


<?php include_once __DIR__.'/../src/views/layouts/footer.php'; ?>
</body>
</html>
