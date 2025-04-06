<?php
// File: router/Router.php

class Router {
    private array $routes = [];
    private array $protected = [];

    public function add(string $path, string $file, bool $isProtected = false): void {
        $this->routes[$path] = $file;
        if ($isProtected) {
            $this->protected[] = $path;
        }
    }

    public function resolve(string $requestUri): string {
        $uri = rtrim(parse_url($requestUri, PHP_URL_PATH), '/');
        $uri = $uri === '' ? '/' : $uri;
        $uri = str_replace('/public', '', $uri);

        // Handle POST login
        if ($uri === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            return __DIR__ . '/../controllers/LoginController.php';
        }

        // Clean URL like /appointments/modify/5
        if (preg_match('#^/appointments/modify/(\d+)$#', $uri, $matches)) {
            $_GET['appointment_id'] = $matches[1];
            return __DIR__ . '/../views/modify_appointment.php';
        }

        // Root redirect
        if ($uri === '/') {
            return isset($_SESSION['user_id'])
                ? __DIR__ . '/../views/dashboard.php'
                : __DIR__ . '/../views/login.php';
        }

        //  Normal route handling
        if (isset($this->routes[$uri])) {
            if (in_array($uri, $this->protected) && !isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit();
            }
            return __DIR__ . '/../' . $this->routes[$uri];
        }

        // ❌ Not found
        http_response_code(404);
        return __DIR__ . '/../views/inaccess.php';
    }
}