<?php
require '../config/config.php';
require '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(" Invalid request!");
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// 🔎 Find user by email
$user = User::findByEmail($pdo, $email);

if (!$user) {
    echo " User not found.";
    echo '<br><a href="/login">🔙 Back to Login</a>';
    exit();
}

// 🔑 Verify password
if (!$user->verifyPassword($password)) {
    echo " Incorrect password.";
    echo '<br><a href="/login">🔙 Back to Login</a>';
    exit();
}

//  Successful login
$_SESSION['user_id'] = $user->getId();
$_SESSION['username'] = $user->getUsername();

header('Location: /');
exit();