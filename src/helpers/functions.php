<?php

    $config = require __DIR__ . '/../config/config.php';

    define('BASE_URL', $config['base_url']);
    define('ASSETS_URL', $config['assets_url']);
    define('ADMIN_URL', $config['admin_url']);


try {
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
        $config['db']['user'],
        $config['db']['password']
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}