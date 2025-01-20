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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/adminStyle.css">
</head>
<style>
    .window-container{
        flex-direction: column;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .window-main-content{
        background: #CBCBCB;
    }
</style>
<body>
<div class="back-to-panel">
    <a href="panel.php">&larr; Return to Admin Panel</a>
</div>
<main>


    <div class="window-container">
        <div class="labels">
            <div class="admin-panel-label">
                <h2>Admin Panel</h2>
            </div>
            <div class="window-label">
                <h2>Admins Management</h2>
            </div>

        </div>
        <div class="window-main-content">
            <div class="card-container">
                <a href="registerAdmin.php" class="card">
                    <h3>Register new admin</h3>

                </a>

                <a href="modifyAdmin.php" class="card">
                    <h3>Modify admin information</h3>

                </a>
                <a href="deleteAdmin.php" class="card">
                    <h3>Delete admin</h3>

                </a>

            </div>
        </div>
    </div>
</main>

</body>
</html>