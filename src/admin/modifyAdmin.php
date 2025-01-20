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

$registrationSuccess = false;
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $confirmEmail = trim($_POST['confirm-email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

   
    if ($email !== $confirmEmail) {
        $errorMessage = "Emails do not match.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errorMessage = "Password must be at least 8 characters long.";
    } else {

        try {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {

                $userCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = ?");
                $userCheckStmt->execute([$username]);
                if ($userCheckStmt->fetchColumn() > 0) {
                    $errorMessage = "Username is already in use.";
                }

                $emailCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE email = ?");
                $emailCheckStmt->execute([$email]);
                if ($emailCheckStmt->fetchColumn() > 0) {
                    $errorMessage = "Email is already in use.";
                }
            } else {

                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("INSERT INTO admin (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword]);

                $registrationSuccess = true;


                header("refresh:3;url=manageAdmins.php");
                exit;
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
    <title>Register Admin</title>
    <link rel="stylesheet" href="<?=ASSETS_URL?>styles/adminStyle.css">
</head>
<style>
    .window-container {
        flex-direction: column;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }
    .window-main-content {
        background: #CBCBCB;

    }

</style>
<body>
<?php if ($registrationSuccess): ?>
    <div class="success-message">
        <p>Registration successful! Redirecting to the admin management page...</p>
    </div>
<?php elseif ($errorMessage): ?>
    <div class="error-message">
        <p><?php echo htmlspecialchars($errorMessage); ?></p>
    </div>
<?php endif; ?>
<div class="back-to-panel">
    <a href="manageAdmins.php">&larr; Return to Admin Panel</a>
</div>
<main>

    <div class="window-container">

        <div class="labels">
            <div class="admin-panel-label">
                <h2>Admin Panel</h2>
            </div>
            <div class="window-label">
                <h2>Register an Admin</h2>
            </div>

        </div>
        <div class="window-main-content">
            <form action="#" method="post" class="form">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Username" required>

                <label for="email">E-mail Address:</label>
                <input type="email" id="email" name="email" placeholder="Admin E-mail" required>

                <label for="confirm-email">Confirm E-mail Address:</label>
                <input type="email" id="confirm-email" name="confirm-email" placeholder="Confirm E-mail" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Admin Password" required>

                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>

                <button type="submit">Register</button>
            </form>
        </div>
    </div>
</main>
</body>
</html>
