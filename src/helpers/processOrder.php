<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__.'/../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'] ?? 0;

    if ($total_amount > 0) {
        try {

            $pdo->beginTransaction();


            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_date, total_amount, status) VALUES (?, NOW(), ?, ?)");
            $stmt->execute([$user_id, $total_amount, 'Pending']);


            $order_id = $pdo->lastInsertId();



            $stmt = $pdo->prepare("
                SELECT c.product_id, c.quantity, p.price, p.name, p.image_path 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                throw new Exception("Cart is empty. Unable to process order.");
            }


            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price, name, image_path) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($cartItems as $item) {
                $stmt->execute([
                    $order_id,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $item['name'],
                    $item['image_path']
                ]);
            }


            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);


            $pdo->commit();


            header("Location: /NatureBio/public/orderConfirm.php?order_id=$order_id");


            exit;

        } catch (Exception $e) {

            $pdo->rollBack();
            echo "Error: Unable to process your order. Please try again later. " . $e->getMessage();
        }
    } else {
        echo "Error: The total amount is invalid.";
    }
}
?>
