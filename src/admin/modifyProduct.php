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
$currentProduct = null;


$products = $pdo->query("SELECT id, name, price, image_path FROM products")->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $image = $_FILES['image'];


    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $currentProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentProduct) {
        $errorMessage = "The selected product does not exist.";
    } else {
        $originalName = $currentProduct['name'];
        $originalPrice = $currentProduct['price'];
        $imagePath = $currentProduct['image_path'];


        if (empty($name) || empty($price)) {
            $errorMessage = "All fields are required.";
        } elseif (!is_numeric($price) || $price <= 0) {
            $errorMessage = "Price must be a positive number.";
        } elseif (empty($image['name']) && $name === $originalName && $price == $originalPrice) {
            $errorMessage = "No changes were made.";
        } elseif (!empty($image['name']) && $image['size'] > 3 * 1024 * 1024) {
            $errorMessage = "The image size exceeds the 3MB limit.";
        } else {

            $newImagePath = $imagePath;
            if (!empty($image['name'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($image['type'], $allowedTypes)) {
                    $errorMessage = "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
                } else {
                    $uploadDir = __DIR__ . '/../../public/assets/img/';
                    $newImagePath = $uploadDir . basename($image['name']);
                    $imagePath = "img/" . $image['name'];
                    if (!move_uploaded_file($image['tmp_name'], $newImagePath)) {
                        $errorMessage = "Failed to upload the image.";
                    }
                }
            }

            if (empty($errorMessage)) {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, image_path = ? WHERE id = ?");
                if ($stmt->execute([$name, $price, $imagePath, $productId])) {
                    $successMessage = "Product updated successfully!";
                } else {
                    $errorMessage = "Failed to update the product.";
                }
            }
        }
    }
}
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
                <h2>Modify a Product</h2>
            </div>
        </div>
        <div class="window-main-content">

            <form method="POST" enctype="multipart/form-data" class="form">
                <label for="product_id">Select Product:</label>
                <select name="product_id" id="product_id" onchange="populateFields()" required>
                    <option value="">-- Select a product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['id'] ?>"
                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                data-price="<?= htmlspecialchars($product['price']) ?>">
                            <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['price']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
                <br>
                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01" required>
                <br>
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
                <br>
                <button type="submit">Apply Changes</button>
            </form>
        </div>
    </div>
</main>

<script>
    function populateFields() {
        const select = document.getElementById('product_id');
        const nameField = document.getElementById('name');
        const priceField = document.getElementById('price');

        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption) {
            nameField.value = selectedOption.dataset.name || '';
            priceField.value = selectedOption.dataset.price || '';
        }
    }
</script>
</body>
</html>
