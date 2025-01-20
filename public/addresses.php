<?php
session_start();
require_once __DIR__.'/../src/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $street_address = trim($_POST['street_address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);


    if (empty($street_address) || empty($city) || empty($state) || empty($postal_code) || empty($country)) {
        $errorMessage = "All fields are required.";
    } elseif (!ctype_digit($postal_code)) {
        $errorMessage = "Postal code must contain only digits.";
    } elseif (strlen($postal_code) < 4 || strlen($postal_code) > 10) {
        $errorMessage = "Postal code must be between 4 and 10 characters.";
    } else {
        try {

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM addresses WHERE user_id = ? AND street_address = ? AND city = ? AND state = ? AND postal_code = ? AND country = ?");
            $stmt->execute([$user_id, $street_address, $city, $state, $postal_code, $country]);
            $addressExists = $stmt->fetchColumn();

            if ($addressExists > 0) {
                $errorMessage = "This address is already in your list.";
            } else {

                $stmt = $pdo->prepare("INSERT INTO addresses (user_id, street_address, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $street_address, $city, $state, $postal_code, $country]);
               
                header("Location: addresses.php");

            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addresses</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/addresses.css">
</head>
<body>
<?php include_once __DIR__.'/../src/views/layouts/header.php'; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<div class="back-to-account">
    <a href="account.php">&larr; Back to Account</a>
</div>
<main class="addresses-page">
    <h1>Your Addresses</h1>




    <form action="addresses.php" method="POST" class="address-form">
        <label for="street_address">Street Address</label>
        <input type="text" id="street_address" name="street_address" required>

        <label for="city">City</label>
        <input type="text" id="city" name="city" required>

        <label for="state">State</label>
        <input type="text" id="state" name="state" required>

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" required>

        <label for="country">Country</label>
        <input type="text" id="country" name="country" required>

        <button type="submit">Add Address</button>
    </form>

    <h2>Existing Addresses</h2>
    <?php if (empty($addresses)): ?>
        <p class="empty-message">No addresses added yet.</p>
    <?php else: ?>
        <ul class="address-list">
            <?php foreach ($addresses as $address): ?>
                <li class="address-item">
                    <strong><?= htmlspecialchars($address['street_address']) ?></strong><br>
                    <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?>,
                    <?= htmlspecialchars($address['postal_code']) ?><br>
                    <?= htmlspecialchars($address['country']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php include_once __DIR__.'/../src/views/layouts/footer.php'; ?>
</body>
</html>
