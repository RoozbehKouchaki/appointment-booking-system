<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("❌ You must be logged in to book an appointment.");
}
?>
<a href="../controllers/LogoutController.php">Logout</a>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
</head>
<body>
    <h2>Book Appointment</h2>
    <form action="../controllers/AppointmentController.php" method="POST">
        <label>Appointment Date:</label><br>
        <input type="datetime-local" name="appointment_date" required><br><br>

        <label>Service Type:</label><br>
        <input type="text" name="service_type" required><br><br>

        <label>Status:</label><br>
        <select name="status" required>
            <option value="Pending">Pending</option>
            <option value="Confirmed">Confirmed</option>
        </select><br><br>

        <button type="submit">Book</button>
        <a href="../controllers/LogoutController.php">Logout</a>
    </form>
</body>
</html>