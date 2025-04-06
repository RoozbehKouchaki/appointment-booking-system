<?php
require '../config/config.php';
require '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request!");
}

$username = trim($_POST['username']);
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password']);

// 🚦 Validate input
if (!$username || !$email || !$password) {
    exit("Please fill out all fields correctly.");
}

try {
    // 🔎 Check if the user already exists
    if (User::findByEmail($pdo, $email)) {
        exit("Email already registered. <a href='../views/login.php'>Login here</a>");
    }

    // Create and save new user
    $user = (new User())
        ->setUsername($username)
        ->setEmail($email)
        ->setPassword($password); // Handles password hashing internally

    $user->save($pdo); // Insert user into the database

    echo "Registration successful! <a href='../views/login.php'>Login now</a>";

} catch (PDOException $e) {
    echo "Registration failed: " . htmlspecialchars($e->getMessage());
}
?>