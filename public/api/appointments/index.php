<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../utils/response.php';
require_once __DIR__ . '/../../../models/Appointment.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, 'Unauthorized');
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

try {
    $appointments = fetchAppointments($pdo, $user_id, $search);
    jsonResponse(true, 'Appointments fetched', ['data' => $appointments]);
} catch (Exception $e) {
    jsonResponse(false, 'Server error', ['error' => $e->getMessage()]);
}

function fetchAppointments($pdo, $userId, $searchTerm = ''): array {
    if ($searchTerm) {
        $searchTerm = '%' . $searchTerm . '%';
        $sql = "
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
            WHERE a.user_id = ? AND (s.name LIKE ? OR d.name LIKE ?)
            ORDER BY aslots.slot_datetime ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $searchTerm, $searchTerm]);
    } else {
        $sql = "
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
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}