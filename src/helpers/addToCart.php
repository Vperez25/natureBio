<?php
session_start();
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        header("Location: ../../public/login.php");
        exit();
    }


    $checkQuery = "SELECT * FROM cart WHERE product_id = :product_id AND user_id = :user_id";
    $stmt = $pdo->prepare($checkQuery);
    $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {

        $updateQuery = "UPDATE cart SET quantity = quantity + 1 WHERE product_id = :product_id AND user_id = :user_id";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    } else {

        $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    }


    $_SESSION['flash_message'] = "Item added to cart successfully!";


    header("Location: ../../public/exerciseSupplements.php");
    exit();
}
