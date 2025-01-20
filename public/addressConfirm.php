<?php
session_start();
require_once __DIR__ . '/../src/helpers/functions.php';

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

    $selected_address_id = $_POST['selected_address_id'] ?? null;
    $save_as_default = isset($_POST['save_as_default']) ? true : false;

    if ($save_as_default) {

        if (!empty($selected_address_id)) {

            $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$selected_address_id, $user_id]);
            $selected_address = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($selected_address) {

                $_SESSION['selected_address'] = [
                    'street_address' => $selected_address['street_address'],
                    'city' => $selected_address['city'],
                    'state' => $selected_address['state'],
                    'postal_code' => $selected_address['postal_code'],
                    'country' => $selected_address['country'],
                    'save_as_default' => true
                ];

                header("Location: paymentConfirm.php");
                exit;
            } else {
                $errorMessage = "Selected address not found.";
            }
        } else {
            $errorMessage = "Please select an address before proceeding.";
        }
    } else {

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

            $_SESSION['selected_address'] = [
                'street_address' => $street_address,
                'city' => $city,
                'state' => $state,
                'postal_code' => $postal_code,
                'country' => $country,
                'save_as_default' => false
            ];

            header("Location: paymentConfirm.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Selection</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/addresses.css">
</head>
<style>
    .divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 20px 0;
    }

    .line {
        flex-grow: 1;
        border: none;
        border-top: 1px solid #ccc;
    }

    .or-text {
        margin: 0 10px;
        font-weight: bold;
    }
    .addresses-page form button{
        background: darkorange;
    }
    .addresses-page form button:hover {
        background: darkorange;
    }
</style>
<body>
<?php include_once __DIR__ . '/../src/views/layouts/header.php'; ?>

<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>

<main class="addresses-page">
    <h1>Select or Add Address</h1>

    <h2>Select an Existing Address</h2>
    <form action="addressConfirm.php" method="POST">
        <?php if (empty($addresses)): ?>
            <p>No addresses added yet.</p>
        <?php else: ?>
            <label for="selected_address_id">Select Address</label>
            <select name="selected_address_id" id="selected_address_id">
                <option value="">Select an Address</option>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?= $address['id'] ?>">
                        <?= htmlspecialchars($address['street_address']) ?>,
                        <?= htmlspecialchars($address['city']) ?>,
                        <?= htmlspecialchars($address['state']) ?>,
                        <?= htmlspecialchars($address['postal_code']) ?>,
                        <?= htmlspecialchars($address['country']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <div class="divider">
            <hr class="line">
            <span class="or-text">OR</span>
            <hr class="line">
        </div>

        <h2>Add a New Address</h2>
        <label for="street_address">Street Address</label>
        <input type="text" id="street_address" name="street_address">

        <label for="city">City</label>
        <input type="text" id="city" name="city">

        <label for="state">State</label>
        <input type="text" id="state" name="state">

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code">

        <label for="country">Country</label>
        <input type="text" id="country" name="country">

        <label for="save_as_default">
            <input type="checkbox" name="save_as_default" id="save_as_default"> Use selected address
        </label>

        <button type="submit">Proceed to Payment</button>
    </form>
</main>

<?php include_once __DIR__ . '/../src/views/layouts/footer.php'; ?>

</body>
</html>
