<?php

class SessionManager {

    // ✅ Start the session safely
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 🔑 Log in the user and set session variables
    public static function login(int $userId, string $username): void {
        self::start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
    }

    // 🔒 Check if a user is logged in
    public static function isLoggedIn(): bool {
        self::start();
        return isset($_SESSION['user_id']);
    }

    // 🚪 Logout and destroy the session
    public static function logout(): void {
        self::start();
        session_unset();
        session_destroy();
    }

    // 🆔 Get the current logged-in user's ID (optional)
    public static function getUserId(): ?int {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }

    // 🧑 Get the current logged-in user's username (optional)
    public static function getUsername(): ?string {
        self::start();
        return $_SESSION['username'] ?? null;
    }
}

?>