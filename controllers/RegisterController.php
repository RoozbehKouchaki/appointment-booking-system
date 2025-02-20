<?php
require '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['password']);

    if (!$username || !$email || !$password) {
        exit("❌ Please fill out all fields correctly.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            exit("⚠️ Email already registered. <a href='../views/login.php'>Login here</a>");
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);

        echo "✅ Registration successful! <a href='../views/login.php'>Login now</a>";
    } catch (PDOException $e) {
        echo "❌ Registration failed: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "❌ Invalid request!";
}
?>