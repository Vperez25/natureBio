<?php
require_once __DIR__ . '/../src/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $subject = $_POST['subject'];
    $message = trim($_POST['mensaje']);


    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMessage = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    } elseif (strlen($message) > 1000) {
        $errorMessage = "Message cannot exceed 1000 characters.";
    } else {

        $sanitizedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        try {

            $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $sanitizedMessage]);

            $successMessage = "Thank you for contacting us. Your inquiry has been sent successfully!";
        } catch (PDOException $e) {
            $errorMessage = "Error: Unable to process your inquiry. Please try again later.";
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

    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/ContactUsStyle.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
<?php
include_once __DIR__ . '/../src/views/layouts/header.php';
?>
<?php if (isset($errorMessage)): ?>
    <div class="error-message">
        <p><?= htmlspecialchars($errorMessage) ?></p>
    </div>
<?php elseif (isset($successMessage)): ?>
    <div class="success-message">
        <p><?= htmlspecialchars($successMessage) ?></p>
    </div>
<?php endif; ?>
<main>
    <form action="#" method="post" class="main">
        <h1>Contact</h1>



        <label for="nombre">Name:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Your Name" required>

        <label for="email">E-mail Address:</label>
        <input type="email" id="email" name="email" placeholder="Your E-mail" required>

        <label for="subject">Subject:</label>
        <select id="subject" name="subject" required>
            <option value="" disabled selected>Select a subject</option>
            <option value="Sales">Sales</option>
            <option value="Products">Products</option>
            <option value="Shipping">Shipping</option>
            <option value="Other">Other</option>
        </select>

        <label for="mensaje">Message:</label>
        <textarea id="mensaje" name="mensaje" rows="4" placeholder="Tell us about it" required></textarea>

        <button class="formButton" type="submit">Send</button>
    </form>
</main>
<?php
include_once __DIR__ . '/../src/views/layouts/footer.php';
?>
