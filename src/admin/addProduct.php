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
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $image = $_FILES['image'];


    $maxFileSize = 3 * 1024 * 1024;


    if (empty($name) || empty($price) || empty($image['name'])) {
        $errorMessage = "All fields are required.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $errorMessage = "Price must be a positive number.";
    } elseif ($image['size'] > $maxFileSize) {
        $errorMessage = "The image size exceeds the 3MB limit.";
    } else {

        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $checkStmt->execute([$name]);
        if ($checkStmt->fetchColumn() > 0) {
            $errorMessage = "The product name is already in use.";
        } else {

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowedTypes)) {
                $errorMessage = "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
            } else {

                $uploadDir = __DIR__ . '/../../public/assets/img/';
                $filePath = $uploadDir . basename($image['name']);

                if (move_uploaded_file($image['tmp_name'], $filePath)) {
                    try {
                        $imagePath = "img/" . $image['name'];
                        $stmt = $pdo->prepare("INSERT INTO products (name, price, image_path) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $price, $imagePath]);

                        $successMessage = "Product added successfully!";
                    } catch (PDOException $e) {
                        $errorMessage = "Error: " . $e->getMessage();
                    }
                } else {
                    $errorMessage = "Failed to upload the image.";
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
                <h2>Add a Product</h2>
            </div>
        </div>
        <div class="window-main-content">

            <form method="POST" enctype="multipart/form-data" class="form">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" required>
                <br>
                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01" required>
                <br>
                <label for="image">Insert Image:</label>
                <input type="file" name="image" id="image" accept="image/*" required>
                <br>
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
