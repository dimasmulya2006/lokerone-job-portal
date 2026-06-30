<?php
// config/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lokerone');

try {
    // Set DSN (Data Source Name)
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    
    // Set Options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    // Create a new PDO instanace
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch(PDOException $e) {
    // Catch any errors
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
