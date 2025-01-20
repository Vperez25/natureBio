<?php
session_start();
require_once __DIR__ . '/../src/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$selected_address = $_SESSION['selected_address'] ?? [];

$total_amount = 0;

$errorMessage = '';
$successMessage = '';

try {
    $stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.price, p.name, p.image_path 
                           FROM cart c
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($cartItems as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $save_address = isset($_POST['save_address']) ? true : false;
    $save_card = isset($_POST['save_card']) ? true : false;

    $card_name = $_POST['card_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $security_code = $_POST['security_code'] ?? '';

    if (!empty($card_name) && !empty($card_number) && !empty($expiry_date) && !empty($security_code)) {
        if (!preg_match("/^[a-zA-Z\s]+$/", $card_name)) {
            $errorMessage = "Invalid card name. Only letters and spaces are allowed.";
        } elseif (!validateCardNumber($card_number)) {
            $errorMessage = "Invalid card number.";
        } elseif (!validateExpiryDate($expiry_date)) {
            $errorMessage = "Invalid or expired card expiry date.";
        } elseif (!preg_match("/^\d{3,4}$/", $security_code)) {
            $errorMessage = "Invalid security code. Must be 3 or 4 digits.";
        }
    } else {
        $errorMessage = "All fields for card details must be filled.";
    }

    if ($save_address && !empty($selected_address)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO saved_addresses (user_id, street_address, city, state, postal_code, country) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $selected_address['street_address'],
                $selected_address['city'],
                $selected_address['state'],
                $selected_address['postal_code'],
                $selected_address['country']
            ]);
        } catch (PDOException $e) {
            $errorMessage = "Error saving address: " . $e->getMessage();
        }
    }

    if ($save_card && empty($errorMessage)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO saved_cards (user_id, card_name, card_number, expiry_date, security_code) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $card_name,
                $card_number,
                $expiry_date,
                $security_code
            ]);
        } catch (PDOException $e) {
            $errorMessage = "Error saving card: " . $e->getMessage();
        }
    }

    if (empty($errorMessage)) {
        require_once __DIR__ . '/../src/helpers/processOrder.php';
    }
}

function validateCardNumber($card_number) {

    $card_number = str_replace(' ', '', $card_number);


    if (!ctype_digit($card_number)) {
        return false;
    }

    $sum = 0;
    $shouldDouble = false;


    for ($i = strlen($card_number) - 1; $i >= 0; $i--) {
        $digit = (int)$card_number[$i];

        if ($shouldDouble) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }

        $sum += $digit;
        $shouldDouble = !$shouldDouble;
    }

    return $sum % 10 === 0;
}
function validateExpiryDate($expiry_date) {
    if (preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $expiry_date)) {
        $current_date = date('m/y');
        return $expiry_date >= $current_date;
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/paymentConfirm.css">
</head>
<body>
<?php include_once __DIR__ . '/../src/views/layouts/header.php'; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<main class="payment-confirmation">
    <h1>Payment Confirmation</h1>

    <form action="paymentConfirm.php" method="POST" id="payment-form">
        <div class="container">

                <div class="form-section">
                    <h2>Card Details</h2>
                    <label for="card_name">Name on Card</label>
                    <input type="text" id="card_name" name="card_name" required>

                    <label for="card_number">Card Number</label>
                    <input type="text" id="card_number" name="card_number" required>

                    <label for="expiry_date">Expiry Date (MM/YY)</label>
                    <input type="text" id="expiry_date" name="expiry_date" required>

                    <label for="security_code">Security Code (CVV)</label>
                    <input type="text" id="security_code" name="security_code" required>

                    <div class="checkbox">
                        <input type="checkbox" id="save_card" name="save_card">
                        <label for="save_card">Save this card for future transactions</label>
                    </div>
                </div>
                <div class="form-section address-section">
                    <h2>Shipping Address</h2>
                    <p><strong>Address:</strong> <?= htmlspecialchars($selected_address['street_address']) ?>,
                        <?= htmlspecialchars($selected_address['city']) ?>,
                        <?= htmlspecialchars($selected_address['state']) ?>,
                        <?= htmlspecialchars($selected_address['postal_code']) ?>,
                        <?= htmlspecialchars($selected_address['country']) ?></p>

                    <div class="checkbox">
                        <input type="checkbox" id="save_address" name="save_address">
                        <label for="save_address">Save this address for future transactions</label>
                    </div>
                </div>


            <div class="form-section cart-section">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?= ASSETS_URL. htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img">
                            <div class="cart-item-details">
                                <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                                <p>Price: $<?= number_format($item['price'], 2) ?></p>
                                <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="total">Total: $<?= number_format($total_amount, 2) ?></div>
            </div>
        </div>
        <input type="hidden" name="total_amount" value="<?= number_format($total_amount, 2) ?>">

        <button type="submit" class="payment-button">Make Payment</button>
    </form>
</main>


<?php include_once __DIR__ . '/../src/views/layouts/footer.php'; ?>

</body>
</html>
