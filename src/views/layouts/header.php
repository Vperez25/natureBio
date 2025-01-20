

<header class="header">

    <a href="<?= BASE_URL ?>index.php"><img src="<?= ASSETS_URL ?>img/logo2.png" class="logo" alt=""></a>

    <nav id="navbar" class="nav-links">
        <div class="dropdown">
            <button class="boton">Shop</button>
            <div class="dropdown-content">
                <a href="<?= BASE_URL ?>overallHealth.php" >Overall Health</a>
                <a href="<?= BASE_URL ?>exerciseSupplements.php">Exercise Supplements</a>
            </div>
        </div>
        <button class="boton"><a href="<?= BASE_URL ?>account.php" class="header-a">Account</a></button>
        <button class="boton"><a href="<?= BASE_URL ?>ContactUs.php" class="header-a">Contact</a></button>
        <a href="<?= BASE_URL ?>shoppingCart.php" class=".header-a"><img src="<?= ASSETS_URL ?>img/cart.png" class="carrito"></a>
    </nav>


    <button class="hamburger-menu" id="hamburger">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>
</header>
