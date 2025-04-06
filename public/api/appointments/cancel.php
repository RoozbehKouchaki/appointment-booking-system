<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request');
}

$appointment_id = $_POST['appointment_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$appointment_id) {
    jsonResponse(false, 'Appointment ID required');
}

try {
    $pdo->beginTransaction();

    $appointment = Appointment::findByIdAndUser($pdo, $appointment_id, $user_id);
    if (!$appointment) {
        throw new Exception("Appointment not found or unauthorized.");
    }

    if ($appointment->getStatus() === 'Cancelled') {
        throw new Exception("Appointment already cancelled.");
    }

    // Cancel the appointment
    $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?")
        ->execute([$appointment_id]);

    // Free the slot
    $slot = AvailableSlot::findById($pdo, $appointment->getSlotId());
    $slot?->unbook($pdo);

    $pdo->commit();
    jsonResponse(true, 'Appointment cancelled');
} catch (Exception $e) {
    $pdo->rollBack();
    jsonResponse(false, $e->getMessage());
}