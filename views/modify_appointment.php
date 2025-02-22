<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to modify an appointment.");
}

$appointment_id = $_GET['appointment_id'] ?? null;
$selected_service_id = $_GET['service_id'] ?? null;
$selected_doctor_id = $_GET['doctor_id'] ?? null;

if (!$appointment_id) {
    die("❌ No appointment selected.");
}

// Fetch current appointment details
$stmt = $pdo->prepare("
    SELECT a.id AS appointment_id, a.slot_id, s.id AS service_id, d.id AS doctor_id
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    JOIN available_slots aslots ON a.slot_id = aslots.id
    JOIN doctors d ON aslots.doctor_id = d.id
    WHERE a.id = ?
");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    die("❌ Appointment not found.");
}

// Use appointment data if no new selections are made
$service_id = $selected_service_id ?? $appointment['service_id'];
$doctor_id = $selected_doctor_id ?? $appointment['doctor_id'];

// Fetch all services
$services = $pdo->query("SELECT id, name FROM services")->fetchAll(PDO::FETCH_ASSOC);

// Fetch doctors for the selected service
$doctorStmt = $pdo->prepare("
    SELECT d.id, d.name 
    FROM doctors d
    JOIN doctor_services ds ON d.id = ds.doctor_id
    WHERE ds.service_id = ?
");
$doctorStmt->execute([$service_id]);
$doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available slots for the selected doctor
$slotStmt = $pdo->prepare("
    SELECT id, slot_datetime 
    FROM available_slots 
    WHERE doctor_id = ? AND (is_booked = 0 OR id = ?)
");
$slotStmt->execute([$doctor_id, $appointment['slot_id']]);
$slots = $slotStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Appointment</title>
</head>
<body>
    <h2>✏️ Modify Appointment</h2>

    <form action="../controllers/AppointmentController.php" method="POST">
        <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
        <input type="hidden" name="action" value="modify">

        <label>Service:</label><br>
        <select name="service_id" required onchange="window.location.href='?appointment_id=<?= $appointment_id ?>&service_id=' + this.value;">
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['id'] ?>" <?= $service['id'] == $service_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Doctor:</label><br>
        <select name="doctor_id" required onchange="window.location.href='?appointment_id=<?= $appointment_id ?>&service_id=<?= $service_id ?>&doctor_id=' + this.value;">
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= $doctor['id'] ?>" <?= $doctor['id'] == $doctor_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($doctor['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Available Slots:</label><br>
        <select name="slot_id" required>
            <?php foreach ($slots as $slot): ?>
                <option value="<?= $slot['id'] ?>" <?= $slot['id'] == $appointment['slot_id'] ? 'selected' : '' ?>>
                    <?= date('Y-m-d H:i', strtotime($slot['slot_datetime'])) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">✅ Update Appointment</button>
    </form>

    <br>
    <a href="my_appointments.php">🔙 Back to My Appointments</a>
</body>
</html>