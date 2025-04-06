<?php

require_once __DIR__ . '/../utils/SessionManager.php';

//  Logout and clear session
SessionManager::logout();

//  Redirect to login page
header("Location: /login");
exit();
?>