<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to book an appointment.");
}

$service_id = $_POST['service_id'] ?? null;
$doctor_id = $_POST['doctor_id'] ?? null;

// ✅ Fetch services
$serviceStmt = $pdo->query("SELECT DISTINCT name, MIN(id) AS id FROM services GROUP BY name");
$services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch doctors for the selected service
$doctors = [];
if ($service_id) {
    $doctorStmt = $pdo->prepare("
        SELECT d.id, d.name
        FROM doctors d
        JOIN doctor_services ds ON d.id = ds.doctor_id
        WHERE ds.service_id = ?
    ");
    $doctorStmt->execute([$service_id]);
    $doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Fetch available slots for the selected doctor
$slots = [];
if ($doctor_id) {
    $slotStmt = $pdo->prepare("
        SELECT id, slot_datetime
        FROM available_slots
        WHERE doctor_id = ? AND is_booked = 0
        ORDER BY slot_datetime ASC
    ");
    $slotStmt->execute([$doctor_id]);
    $slots = $slotStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; }
        label { font-weight: bold; }
        select, button { padding: 5px; margin: 5px 0; width: 100%; max-width: 300px; }
        h2 { color: #333; }
    </style>
</head>
<body>
    <h2>📝 Book Appointment</h2>

    <!-- Step 1: Select Service -->
    <form method="POST" action="">
        <label>Choose Service:</label><br>
        <select name="service_id" required onchange="this.form.submit()">
            <option value="">-- Select Service --</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['id'] ?>" <?= $service_id == $service['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Step 2: Select Doctor (after service is selected) -->
    <?php if ($service_id): ?>
        <form method="POST" action="">
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
            <label>Choose Doctor:</label><br>
            <select name="doctor_id" required onchange="this.form.submit()">
                <option value="">-- Select Doctor --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= $doctor['id'] ?>" <?= $doctor_id == $doctor['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doctor['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>

    <!-- Step 3: Select Time Slot and Book Appointment -->
    <?php if ($doctor_id): ?>
        <form method="POST" action="../controllers/AppointmentController.php">
            <input type="hidden" name="service_id" value="<?= $service_id ?>">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="action" value="book">

            <label>Choose Available Slot:</label><br>
            <select name="slot_id" required>
                <option value="">-- Select Time Slot --</option>
                <?php foreach ($slots as $slot): ?>
                    <option value="<?= $slot['id'] ?>">
                        <?= date('Y-m-d H:i', strtotime($slot['slot_datetime'])) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <button type="submit">✅ Book Appointment</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">🔙 Back to Dashboard</a> |
    <a href="../controllers/LogoutController.php">🚪 Logout</a>
</body>
</html>