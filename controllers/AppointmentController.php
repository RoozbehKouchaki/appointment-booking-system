<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to book an appointment.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $service_type = trim($_POST['service_type']);
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO appointments (user_id, appointment_date, service_type, status) VALUES (?, ?, ?, ?)");

    try {
        $stmt->execute([$user_id, $appointment_date, $service_type, $status]);
        echo "✅ Appointment booked successfully!";
    } catch (PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
} else {
    echo "❌ Invalid request!";
}
?>