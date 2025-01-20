<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__.'/../src/helpers/functions.php';


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


$orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
$orders_stmt->execute([$user_id]);
$orders = $orders_stmt->fetchAll();

$addresses_stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
$addresses_stmt->execute([$user_id]);
$addresses = $addresses_stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>

    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/account.css">
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/style.css">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>
<?php
include_once __DIR__.'/../src/views/layouts/header.php';
?>

<main>
    <div class="account-header">
        <h1><?= htmlspecialchars($user['username']) ?></h1>
        <form action="logout.php" method="POST">
            <button class="logout-btn" type="submit" onclick="clearCart()" >Logout</button>
        </form>
    </div>

    <div class="card-container">
        <a href="orders.php" class="card">
            <h3>Orders</h3>
            <p>Track packages or return orders</p>
        </a>

        <a href="personalInfo.php" class="card">
            <h3>Personal Info</h3>
            <p>Change email, password, and phone number</p>
        </a>

        <a href="addresses.php" class="card">
            <h3>Addresses</h3>
            <p>Edit addresses for orders and gifts</p>
        </a>
    </div>
</main>



<?php
include_once __DIR__.'/../src/views/layouts/footer.php';
?>
<script>

    function clearCart(){
        localStorage.clear();
    }
</script>
</body>
</html>
