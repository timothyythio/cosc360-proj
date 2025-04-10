<?php
$host = 'localhost';
$db   = 'bloggit_db';
$user = 'webuser';
$pass = 'P@ssw0rd';
$charset = 'utf8mb4';

// Check if user is on the COSC360 server
if (strpos($_SERVER['HTTP_HOST'], 'cosc360.ok.ubc.ca') !== false) {
    $user = 'timnthio';
    $pass = 'cosc360proj';
    $db = 'timnthio';
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
