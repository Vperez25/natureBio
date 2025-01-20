<?php
include_once __DIR__ . '/../../helpers/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<header>
    <img src="<?=ASSETS_URL?>img/logo2.png" alt="logo" class="logo">


</header>
