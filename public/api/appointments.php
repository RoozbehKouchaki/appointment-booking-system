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

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    jsonResponse(false, 'Unauthorized');
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS appointment_id,
            s.name AS service_name,
            d.name AS doctor_name,
            aslots.slot_datetime AS appointment_time,
            a.status
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        JOIN available_slots aslots ON a.slot_id = aslots.id
        JOIN doctors d ON aslots.doctor_id = d.id
        WHERE a.user_id = ?
        ORDER BY aslots.slot_datetime ASC
    ");
    $stmt->execute([$user_id]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Appointments fetched', ['data' => $appointments]);
} catch (PDOException $e) {
    jsonResponse(false, 'DB error');
}