<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>Choose an option:</p>

    <ul>
        <li><a href="my_appointments.php">📅 View My Appointments</a></li>
        <li><a href="book_appointment.php">📝 Book an Appointment</a></li>
    </ul>

    <hr>
    <form action="../controllers/LogoutController.php" method="POST">
        <button type="submit">🚪 Logout</button>
    </form>
</body>
</html>