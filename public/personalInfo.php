<?php
session_start();
require_once __DIR__.'/../src/helpers/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$successMessage = '';
$errorMessage = '';
try {
    $stmt = $pdo->prepare("SELECT username, email, phone FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    }
    elseif (empty($phone)) {
        $errorMessage = "Phone number is required.";
    } elseif (!ctype_digit($phone)) {
        $errorMessage = "Phone number must contain only digits.";
    } elseif (strlen($phone) < 10 || strlen($phone) > 15) {
        $errorMessage = "Phone number must be between 10 and 15 digits.";
    }
    elseif (empty($errorMessage)) {
        try {

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $currentPasswordHash = $stmt->fetchColumn();


            if (!$currentPasswordHash) {
                $errorMessage = "Unable to verify current password. Please try again.";
            }

            elseif (password_verify($password, $currentPasswordHash)) {
                $errorMessage = "The new password cannot be the same as the current password.";
            } else {

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("UPDATE users SET email = ?, phone = ?, password = ? WHERE id = ?");
                $stmt->execute([$email, $phone, $hashedPassword, $user_id]);

                $successMessage = "Personal information updated successfully!";
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
    <title>Personal Info</title>
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/style.css">
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/personalInfo.css">
</head>
<body>
<?php include_once __DIR__.'/../src/views/layouts/header.php'; ?>

<?php if ($successMessage): ?>
    <div class="success-message">
        <p><?= htmlspecialchars($successMessage) ?></p>
    </div>
<?php elseif ($errorMessage): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<div class="back-to-account">
    <a href="account.php">&larr; Back to Account</a>
</div>
<main class="personal-info-page">
    <h1>Update Personal Information</h1>

    <form action="personalInfo.php" method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        <label for="password">New Password</label>
        <input type="password" id="password" name="password">

        <button type="submit">Update Info</button>
    </form>
</main>

<?php include_once __DIR__.'/../src/views/layouts/footer.php'; ?>
</body>
</html>
