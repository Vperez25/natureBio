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
    $adminId = $_POST['admin_id'];

    if ($adminId != 1) { // Prevent deletion of super admin
        try {
            $stmt = $pdo->prepare("DELETE FROM admin WHERE id = ?");
            $stmt->execute([$adminId]);

            $successMessage = "User deleted successfully!";
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "You don't have permission to delete this user!";
    }
}

$admins = $pdo->query("SELECT id, username, email FROM admin")->fetchAll(PDO::FETCH_ASSOC);
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
    .form select{
        margin-bottom: 200px;
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
    <a href="manageAdmins.php">&larr; Return to Admin Panel</a>
</div>

<main>

    <div class="window-container">
        <div class="labels">
            <div class="admin-panel-label">
                <h2>Admin Panel</h2>
            </div>
            <div class="window-label">
                <h2>Delete an Admin</h2>
            </div>

        </div>
        <div class="window-main-content">
            <form method="POST" class="form">
                <label for="admin_id">Select User:</label>
                <select name="admin_id" id="admin_id" required>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?= $admin['id'] ?>"><?= htmlspecialchars($admin['username']) ?> (<?= htmlspecialchars($admin['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>

                <br>
                <button type="submit">DELETE ADMIN</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>