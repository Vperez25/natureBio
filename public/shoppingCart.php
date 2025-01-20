<?php
session_start();
require_once __DIR__.'/../src/helpers/functions.php';

$errorMessage = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];


    try {
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);



    } catch (PDOException $e) {
        $errorMessage = "Error fetching cart data: " . $e->getMessage();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!empty($cart)) {

        header('Location: addressConfirm.php');
        exit;
    } else {

        $errorMessage = "Your cart is empty. Please add items to your cart before proceeding.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="<?= ASSETS_URL?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL?>styles/cart.css">
</head>

<body>
<?php
include_once __DIR__.'/../src/views/layouts/header.php';
?>
<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<main class="cart-container">
    <h2>Shopping Cart</h2>
    <div id="cart-items" class="cart-items">

    </div>
    <div class="cart-total">
        <h5>Total: <span id="total-amount">$0.00</span></h5>
        <form id="order-form" action="#" method="POST">
            <input type="hidden" name="total_amount" id="total-amount-input" value="0">
            <button class="pay-button" type="submit">Proceed to Pay</button>
        </form>
    </div>

</main>

<?php
$cartItems = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.image_path, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<script>
    let cart = <?= json_encode($cartItems) ?> || [];
    if (cart.length === 0) {
        cart = JSON.parse(localStorage.getItem('cart') || '[]');
    }

    const cartContainer = document.getElementById('cart-items');
    let totalAmount = 0;

    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.classList.add('cart-item');
        cartItem.innerHTML = `
            <img src="<?=ASSETS_URL?>${item.image_path}" alt="${item.name}" class="product-image">
            <div class="item-details">
                <h3>${item.name}</h3>
                <p>$${item.price} x ${item.quantity}</p>
                <p class="item-subtotal">Subtotal: $${(item.price * item.quantity).toFixed(2)}</p>
            </div>
        `;
        cartContainer.appendChild(cartItem);
        totalAmount += item.price * item.quantity;
    });


    document.getElementById('total-amount').innerText = `$${totalAmount.toFixed(2)}`;


    document.getElementById('total-amount-input').value = totalAmount.toFixed(2);
</script>


<?php
include_once __DIR__.'/../src/views/layouts/footer.php';
?>
</body>
</html>
