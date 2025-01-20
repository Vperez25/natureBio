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


$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $recordsPerPage;

try {

    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $totalRecords = $stmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);


    $stmt = $pdo->prepare("
        SELECT 
            o.id AS order_id, 
            o.user_id, 
            o.order_date, 
            o.total_amount, 
            o.status, 
            oi.product_id, 
            oi.name AS product_name, 
            oi.quantity, 
            oi.price 
        FROM 
            orders o 
        LEFT JOIN 
            order_items oi 
        ON 
            o.id = oi.order_id 
        ORDER BY 
            o.order_date DESC 
        LIMIT :offset, :recordsPerPage
    ");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':recordsPerPage', $recordsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error: Unable to fetch orders. " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modify User Info</title>
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
                <h2>Manage Orders</h2>
            </div>
        </div>
        <div class="window-main-content">
            <table>
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['user_id']) ?></td>
                        <td><?= htmlspecialchars($order['order_date']) ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td>
                            <ul>
                                <li>
                                    Product: <?= htmlspecialchars($order['product_name']) ?> |
                                    Quantity: <?= htmlspecialchars($order['quantity']) ?> |
                                    Price: $<?= number_format($order['price'], 2) ?>
                                </li>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-button <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="pagination-button">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>


</body>
</html>
