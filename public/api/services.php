<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/response.php';

try {
    $stmt = $pdo->query("SELECT id, name FROM services");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse(true, 'Services fetched', ['data' => $services]);
} catch (PDOException $e) {
    jsonResponse(false, 'Database error', ['error' => $e->getMessage()]);
}