<?php
require_once __DIR__.'/../src/helpers/functions.php';

$registrationSuccess = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $confirmEmail = trim($_POST['confirm-email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $phone = trim($_POST['phone']);

    if (empty($username) || empty($email) || empty($password)) {
        $errorMessage = "All fields are required.";
    }
    elseif ($email !== $confirmEmail) {
        $errorMessage = "Emails do not match.";
    }
    elseif ($password !== $confirmPassword) {
    $errorMessage = "Passwords do not match.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please provide a valid email address.";
    } else {
        try {

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $usernameExists = $stmt->fetchColumn();

            if ($usernameExists > 0) {
                $errorMessage = "The username is already in use.";
            } else {

                $normalizedEmail = strtolower(trim($email));
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE LOWER(email) = ?");
                $stmt->execute([$normalizedEmail]);
                $emailExists = $stmt->fetchColumn();

                if ($emailExists > 0) {
                    $errorMessage = "The email is already in use.";
                } else {

                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$username, $email, $hashedPassword, $phone]);


                    $registrationSuccess = true;
                    header("refresh:3;url=login.php");

                }
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
    <title>NatureBio</title>

    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/login.css">
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>
<?php
include_once __DIR__.'/../src/views/layouts/header.php';
?>
<?php if ($registrationSuccess): ?>
    <div class="success-message">
        <p>Registration successful! You will be redirected to the login page.</p>
        <p>Or you can <a href="login.php">log in here</a> immediately.</p>
    </div>
<?php elseif ($errorMessage): ?>
    <div class="error-message">
        <p><?php echo $errorMessage; ?></p>
    </div>
<?php endif; ?>
<main>

    <form action="#" method="post" class="main">
        <h1>Register</h1>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" required>

        <label for="email">E-mail Address:</label>
        <input type="email" id="email" name="email" placeholder="Your E-mail" required>

        <label for="confirm-email">Confirm E-mail Address:</label>
        <input type="email" id="confirm-email" name="confirm-email" placeholder="Confirm E-mail" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password" required>

        <label for="confirm-password">Confirm Password</label>
        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm password" required>

        <label for="phone">Telephone Number</label>
        <input type="number" id="phone" name="phone" placeholder="Your phone" required>

        <button class="formButton" type="submit">Send</button>
        <a href="<?=BASE_URL?>login.php" class="form-a">Registered already? Sign in here</a>
    </form>
</main>

<?php
include_once __DIR__.'/../src/views/layouts/footer.php';
?>
