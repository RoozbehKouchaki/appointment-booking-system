<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to view your appointments.");
}

$user_id = $_SESSION['user_id'];

// Fetch appointments for the logged-in user
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        button { padding: 5px 10px; cursor: pointer; margin: 0 5px; }
        a { text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>📅 My Appointments</h2>

    <?php if (empty($appointments)): ?>
        <p>🙁 You have no appointments booked.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Service</th>
                    <th>Doctor</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $index => $appointment): ?>
                    <?php
                        $appointment_time = strtotime($appointment['appointment_time']);
                        $is_future = $appointment_time > time();
                        $is_not_cancelled = strtolower($appointment['status']) !== 'cancelled';
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                        <td><?= htmlspecialchars($appointment['doctor_name']) ?></td>
                        <td><?= date('Y-m-d H:i', $appointment_time) ?></td>
                        <td><?= htmlspecialchars($appointment['status']) ?></td>
                        <td>
                            <?php if ($is_future && $is_not_cancelled): ?>
                                <form action="../controllers/AppointmentController.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                    <input type="hidden" name="action" value="cancel">
                                    <button type="submit">❌ Cancel</button>
                                </form>
                                <form action="modify_appointment.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?= $appointment['appointment_id'] ?>">
                                    <button type="submit">✏️ Modify</button>
                                </form>
                            <?php else: ?>
                                <span style="color: gray;">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <br>
    <a href="dashboard.php">🔙 Back to Dashboard</a> | <a href="../controllers/LogoutController.php">🚪 Logout</a>
</body>
</html>