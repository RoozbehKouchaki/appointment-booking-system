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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$input = json_decode(file_get_contents("php://input"), true);

$appointment_id = $input['appointment_id'] ?? null;
$service_id     = $input['service_id'] ?? null;
$doctor_id      = $input['doctor_id'] ?? null;
$slot_id        = $input['slot_id'] ?? null;
$user_id        = $_SESSION['user_id'];

if (!$appointment_id || !$service_id || !$slot_id) {
    jsonResponse(false, 'Missing required fields');
}

try {
    $pdo->beginTransaction();

    $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
    if (!$appointment) {
        throw new Exception('Appointment not found or unauthorized');
    }

    $newSlot = AvailableSlot::findById($pdo, $slot_id);
    if (!$newSlot || ($newSlot->isBooked() && $appointment->getSlotId() !== $slot_id)) {
        throw new Exception('Selected slot is unavailable');
    }

    $old_slot_id = $appointment->getSlotId();

    $appointment->setServiceId($service_id)
                ->setSlotId($slot_id)
                ->save($pdo);

    if ($old_slot_id !== $slot_id) {
        $oldSlot = AvailableSlot::findById($pdo, $old_slot_id);
        $oldSlot?->unbook($pdo);
        $newSlot->book($pdo);
    }

    $pdo->commit();
    jsonResponse(true, 'Appointment updated');
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(false, $e->getMessage());
}