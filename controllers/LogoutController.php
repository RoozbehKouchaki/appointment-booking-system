<?php
session_start();
require '../models/SessionManager.php';

// 🔒 Logout and clear session
SessionManager::logout();

// 🔄 Redirect to login page
header("Location: ../views/login.php");
exit();
?>