<?php
require_once __DIR__.'/../src/helpers/functions.php';

$errorMessage = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];


    if (empty($email) || empty($password)) {
        $errorMessage = "Both email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } else {
        try {

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                header("Location: account.php");
                exit;
            } else {
                $errorMessage = "Invalid email or password.";
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

    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/login.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
<?php include_once __DIR__.'/../src/views/layouts/header.php'; ?>
<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>
<main>
    <form action="#" method="post" class="main">
        <h1>Log in</h1>

        <label for="email">E-mail Address:</label>
        <input type="email" id="email" name="email" placeholder="Your E-mail" value="<?= htmlspecialchars($email ?? '') ?>" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Your password" required>

        <button class="formButton" type="submit">Log in</button>
        <a href="register.php" class="form-a">Not registered? Create an account here</a>
    </form>
</main>

<?php include_once __DIR__.'/../src/views/layouts/footer.php'; ?>

</body>
</html>
