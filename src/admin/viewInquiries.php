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

$inquiries = $pdo->query("SELECT * FROM inquiries")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Enquiries</title>
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
                <h2>Manage Inquiries</h2>
            </div>
        </div>
        <div class="window-main-content">
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($inquiries as $inquiry): ?>
                    <tr>
                        <td><?= htmlspecialchars($inquiry['id']) ?></td>
                        <td><?= htmlspecialchars($inquiry['name']) ?></td>
                        <td><?= htmlspecialchars($inquiry['email']) ?></td>
                        <td><?= htmlspecialchars($inquiry['subject'])?></td>
                        <td><?= htmlspecialchars($inquiry['message']) ?></td>
                        <td><?= htmlspecialchars($inquiry['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</main>

</body>
</html>
