<?php
// Database configuration for PDO in Docker
$host = 'db';          // Docker service name for the MySQL container
$db = 'appointment_system';
$user = 'root';
$pass = 'root';
$port = '3306';        // Use MySQL's container port

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>