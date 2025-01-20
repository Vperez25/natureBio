<?php
require_once __DIR__.'/../src/helpers/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NatureBio</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>styles/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
<?php
    include_once __DIR__.'/../src/views/layouts/header.php';
?>


<section class="nature-section">
    <div class="shop-now">
        <h1>Welcome to NatureBio</h1>
        <p>Your journey to better health begins here.</p>
        <button class="shop-now-button"><a href="exerciseSupplements.php">Shop Now</a></button>
    </div>
</section>

<main >
    <div class="row1">
        <img src="<?= ASSETS_URL ?>img/mainImg.jpg" class="mainImg">
        <div class="p-row">
            <p>Discover a healthier way of life
                with our natural products.
                From the careful selection of
                organic ingredients to the
                packaging, we are proud to
                deliver the best natural
                products on the market.</p>
        </div>

    </div>
    <div class="row2">
        <div class="p-row">
            With our 2-day shipping within
            major U.S. cities and availability
            in more than 50 countries, we
            have a reach around the globe
            as never before.
        </div>
        <img src="<?= ASSETS_URL ?>img/img_1.png" class="mainImg">
    </div>
</main>

<?php
include_once __DIR__.'/../src/views/layouts/footer.php';
?>
