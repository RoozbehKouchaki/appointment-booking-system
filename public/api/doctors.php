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

// Validate query param
$service_id = $_GET['service_id'] ?? null;
if (!$service_id) {
    jsonResponse(false, 'Missing service_id');
}

try {
    $stmt = $pdo->prepare("
        SELECT d.id, d.name 
        FROM doctors d
        JOIN doctor_services ds ON d.id = ds.doctor_id
        WHERE ds.service_id = ?
    ");
    $stmt->execute([$service_id]);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Doctors fetched', ['data' => $doctors]);
} catch (Exception $e) {
    jsonResponse(false, 'Database error', ['error' => $e->getMessage()]);
}