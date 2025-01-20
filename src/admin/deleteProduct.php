<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: adminLogin.php');
    exit;
}

include_once __DIR__ . '/../helpers/functions.php';
include_once __DIR__ . '/../views/layouts/adminHeader.php';

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);

        if ($stmt->rowCount() > 0) {
            $successMessage = "Product deleted successfully!";
        } else {
            $errorMessage = "The selected product does not exist or could not be deleted.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error deleting product: " . $e->getMessage();
    }
}


$products = $pdo->query("SELECT id, name, price, image_path FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/adminStyle.css">
</head>
<style>
    .window-container {
        flex-direction: column;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .window-main-content {
        background: #CBCBCB;
    }
    .form select {
        margin-bottom: 20px;
    }

</style>
<body>
<?php if ($successMessage): ?>
    <div class="success-message">
        <p><?= htmlspecialchars($successMessage) ?></p>
    </div>
<?php elseif ($errorMessage): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<div class="back-to-panel">
    <a href="manageProducts.php">&larr; Return to Products Management</a>
</div>
<main>
    <div class="window-container">
        <div class="labels">
            <div class="admin-panel-label">
                <h2>Admin Panel</h2>
            </div>
            <div class="window-label">
                <h2>Delete a Product</h2>
            </div>
        </div>
        <div class="window-main-content">



            <form method="POST" class="form">
                <label for="product_id">Select Product:</label>
                <select name="product_id" id="product_id" required>
                    <option value="">-- Select a product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>">
                            <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['price']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button type="submit">DELETE PRODUCT</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
