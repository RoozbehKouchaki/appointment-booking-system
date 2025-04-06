<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../utils/mailer.php';
require_once __DIR__ . '/../../../utils/response.php';

session_start();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = trim($data['email'] ?? '');

    if (!$email) {
        jsonResponse(false, 'Email is required');
    }

    $stmt = $pdo->prepare("SELECT id, reset_token, reset_expires, reset_requested_at FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Always show generic message to avoid user enumeration
    if (!$user) {
        jsonResponse(true, 'If your email is in our system, a reset link has been sent.');
    }

    // Check cooldown (5 minutes)
    $cooldownMinutes = 5;
    if ($user['reset_requested_at'] && strtotime($user['reset_requested_at']) > strtotime("-{$cooldownMinutes} minutes")) {
        jsonResponse(true, 'A reset link was already sent recently. Please wait a few minutes.');
    }

    // Reuse valid token
    if ($user['reset_token'] && strtotime($user['reset_expires']) > time()) {
        $token = $user['reset_token'];
        $expires = $user['reset_expires'];
    } else {
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    }

    // Update or insert token info
    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ?, reset_requested_at = NOW() WHERE email = ?");
    $stmt->execute([$token, $expires, $email]);

    $resetLink = "http://localhost:8082/reset-password?token=$token";
    $emailSent = sendResetEmail($email, $resetLink);

    jsonResponse(true, $emailSent
        ? 'If your email is in our system, a reset link has been sent.'
        : 'Failed to send reset email.');

} catch (Exception $e) {
    jsonResponse(false, 'Server error', ['error' => $e->getMessage()]);
}