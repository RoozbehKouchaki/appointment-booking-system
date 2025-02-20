<?php
session_start();
require '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to view your appointments.");
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<a href="../controllers/LogoutController.php">Logout</a>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
</head>
<body>
    <h2>Your Appointments</h2>

    <?php if (count($appointments) > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Date</th>
                <th>Service Type</th>
                <th>Status</th>
            </tr>
            <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                    <td><?= htmlspecialchars($appt['service_type']) ?></td>
                    <td><?= htmlspecialchars($appt['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No appointments found.</p>
    <?php endif; ?>
</body>
</html>