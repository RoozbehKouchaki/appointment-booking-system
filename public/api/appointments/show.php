<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../utils/response.php';
require_once __DIR__ . '/../../../models/Appointment.php';
require_once __DIR__ . '/../../../models/AvailableSlot.php';

session_start();

// Auth check
if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized');
}

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, 'Invalid request');
}

// Validate appointment ID
$appointment_id = $_GET['id'] ?? null;
if (!$appointment_id) {
    jsonResponse(false, 'Appointment ID required');
}

$user_id = $_SESSION['user_id'];

// Find appointment
$appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
if (!$appointment) {
    jsonResponse(false, 'Appointment not found');
}

$slot = AvailableSlot::findById($pdo, $appointment->getSlotId());

jsonResponse(true, 'Appointment fetched', [
    'data' => [
        'id' => $appointment->getId(),
        'service_id' => $appointment->getServiceId(),
        'slot_id' => $appointment->getSlotId(),
        'status' => $appointment->getStatus(),
        'datetime' => $slot ? $slot->getSlotDatetime() : null
    ]
]);