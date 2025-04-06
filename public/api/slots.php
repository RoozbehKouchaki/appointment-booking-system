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

$doctor_id = $_GET['doctor_id'] ?? null;

if (!$doctor_id) {
    jsonResponse(false, 'Doctor ID is required');
}

try {
    $stmt = $pdo->prepare("
        SELECT id, slot_datetime 
        FROM available_slots 
        WHERE doctor_id = ? AND is_booked = 0
        ORDER BY slot_datetime ASC
    ");
    $stmt->execute([$doctor_id]);
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Slots fetched', ['data' => $slots]);
} catch (PDOException $e) {
    jsonResponse(false, 'DB error', ['error' => $e->getMessage()]);
}