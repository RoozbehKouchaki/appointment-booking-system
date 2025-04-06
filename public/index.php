<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../router/Router.php';

$router = new Router();

// Define your routes
$router->add('/', 'views/dashboard.php', true);
$router->add('/login', 'views/login.php');
$router->add('/register', 'views/register.php');
$router->add('/book', 'views/book_appointment.php', true);
$router->add('/appointments', 'views/my_appointments.php', true);
$router->add('/appointments/cancel', 'controllers/AppointmentController.php', true);
$router->add('/appointments/update', 'controllers/AppointmentController.php', true);
$router->add('/appointments/modify', 'views/modify_appointment.php', true);
$router->add('/search-appointments', 'views/search_appointments.php', true);
$router->add('/logout', 'controllers/LogoutController.php');
$router->add('/reset-password', 'views/reset-password.php', false);
$router->add('/forgot-password', 'views/forgot-password.php', false);

// ✅ Resolve the path first
$file_path = $router->resolve($_SERVER['REQUEST_URI']);

// ✅ Then use it
if (file_exists($file_path)) {
    require $file_path;
} else {
    http_response_code(500);
    echo "Error: Route file not found.";
}