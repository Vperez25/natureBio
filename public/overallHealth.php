<?php
session_start();

require_once __DIR__ . '/../src/helpers/functions.php';

$productsPerPage = 10;


$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}


$totalProductsSql = "SELECT COUNT(*) AS total FROM products";
$totalResult = $pdo->query($totalProductsSql);
$totalRow = $totalResult->fetch(PDO::FETCH_ASSOC);
$totalProducts = (int)$totalRow['total'];


$totalPages = ceil($totalProducts / $productsPerPage);


$offset = ($page - 1) * $productsPerPage;


$sql = "SELECT * FROM products LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NatureBio</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/shopStyle.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include_once __DIR__ . '/../src/views/layouts/header.php'; ?>

<main>
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $row): ?>
            <?php
            $productId = $row["id"];
            $productName = htmlspecialchars($row["name"], ENT_QUOTES);
            $productPrice = $row["price"];
            $productImagePath = $row["image_path"];
            ?>
            <div class="contenedorTarjeta">
                <img class="img" src="<?= ASSETS_URL . htmlspecialchars($productImagePath, ENT_QUOTES) ?>" alt="<?= $productName ?>">
                <div class="contenidoTarjeta">
                    <h2 class="h2"><?= $productName ?></h2>
                </div>
                <p>$<?= number_format($productPrice, 2) ?></p>
                <div class="contenedorBoton">
                    <form action="<?= BASE_URL ?>cartController.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                        <button class="botonTarjeta" type="submit">Add to cart</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products found!</p>
    <?php endif; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="pagination-button">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="pagination-button <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="pagination-button">Next</a>
        <?php endif; ?>
    </div>

</main>

<?php include_once __DIR__ . '/../src/views/layouts/footer.php'; ?>
</body>
</html>
