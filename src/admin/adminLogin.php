<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../helpers/functions.php';
include_once __DIR__ . '/../views/layouts/adminHeader.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);


    if (empty($username) || empty($password)) {
        $errorMessage = "Both username and password are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($admin && password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                header('Location: panel.php');
                exit;
            } else {
                $errorMessage = "Invalid username or password.";
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
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/adminLogin.css">
</head>
<body>
<?php if (!empty($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php endif; ?>

<main>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form method="POST" action="adminLogin.php" class="login-form">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>

    </div>
</main>

</body>
</html>
