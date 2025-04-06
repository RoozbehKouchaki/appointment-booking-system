<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php'); // Redirect if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Appointments</title>
</head>
<body>
    <h2>Search Appointments</h2>
    <form action="search_appointments.php" method="GET">

        <label for="service">Service:</label>
        <input type="text" name="service" id="service"><br><br>

        <label for="date">Appointment Date:</label>
        <input type="date" name="date" id="date"><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">--Select Status--</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Pending">Pending</option>
            <option value="Cancelled">Cancelled</option>
        </select><br><br>

        <input type="submit" value="Search">
    </form>
</body>
</html>