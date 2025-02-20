<?php
session_start();
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: ../search_appointments.php');  // Redirect on success
        exit();
    } else {
        echo "❌ Invalid email or password.";
        echo '<br><a href="../views/login.php">Back to Login</a>';
    }
} else {
    echo "❌ Invalid request!";
}
?>