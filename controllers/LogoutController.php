<?php
session_start();
session_unset();  // Clear session variables
session_destroy();  // Destroy the session
header("Location: ../views/login.php");  // Redirect to login page
exit();
?>