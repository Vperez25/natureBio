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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/adminPanel.css">
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/adminStyle.css">
</head>
<body>
<main>
    <div class="window-container">
        <div class="logout-container">
            <h2>Admin Panel</h2>
            <form method="POST" action="adminLogout.php" class="logout-form">
                <button type="submit" class="logout-button">Log Out</button>
            </form>
        </div>
        <div class="window-main-content">
            <div class="admin-info">
                <?php

                if (isset($_SESSION['admin_username'])) {
                    $username = $_SESSION['admin_username'];


                    $stmt = $pdo->prepare("SELECT email FROM admin WHERE username = ?");
                    $stmt->execute([$username]);
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                    $email = $admin ? $admin['email'] : 'Email not found';
                    echo "<p>Username: $username</p>";
                    echo "<p>Email: $email</p>";
                } else {
                    echo "<p>Unable to fetch admin information.</p>";
                }
                ?>
            </div>

            <div class="nav-container">

                <div class="nav-card">
                    <a href="manageOrders.php" >
                        <img src="<?=ASSETS_URL?>img/ordersIcon.png" alt="user icon">
                        <p>Manage Orders</p>
                    </a>
                </div>

                <div class="nav-card">
                    <a href="viewInquiries.php" >
                        <img src="<?=ASSETS_URL?>img/inquiryIcon.png" alt="inquiry icon">
                        <p>Manage Inquiries</p>
                    </a>
                </div>

                <div class="nav-card">
                    <a href="manageProducts.php" >
                        <img src="<?=ASSETS_URL?>img/productsIcon.png" alt="product icon">
                        <p>Manage Products</p>
                    </a>
                </div>

                <div class="nav-card">
                    <a href="manageAdmins.php" >
                        <img src="<?=ASSETS_URL?>img/adminIcon.png" alt="admin icon">
                        <p>Manage Administrators</p>
                    </a>
                </div>

            </div>
        </div>
    </div>
</main>
</body>
</html>
