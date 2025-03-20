<?php
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// URL routing based on .htaccess
$request = isset($_GET['adresse']) ? $_GET['adresse'] : '';
$params = explode('/', $request);

// Default controller and action
$controller = !empty($params[0]) ? $params[0] : 'home';
$action = isset($params[1]) ? $params[1] : 'index';
$id = isset($params[2]) ? $params[2] : null;

// Authentication check
require_once 'controllers/AuthController.php';
$authController = new AuthController();

// Protected routes
$protected_routes = ['vendor', 'admin', 'customer', 'cart', 'checkout', 'orders'];
if (in_array($controller, $protected_routes) && !$authController->isLoggedIn()) {
    header('Location: /auth/login');
    exit;
}

// Role-based access control
$vendor_routes = ['vendor'];
$admin_routes = ['admin'];
$customer_routes = ['customer', 'cart', 'checkout', 'orders'];

if (in_array($controller, $vendor_routes) && $authController->getUserRole() !== 'vendor') {
    header('Location: /error/unauthorized');
    exit;
}

if (in_array($controller, $admin_routes) && $authController->getUserRole() !== 'admin') {
    header('Location: /error/unauthorized');
    exit;
}

if (in_array($controller, $customer_routes) && $authController->getUserRole() !== 'customer' && $authController->getUserRole() !== 'admin') {
    header('Location: /error/unauthorized');
    exit;
}

// Load appropriate controller
$controllerName = ucfirst($controller) . 'Controller';
$controllerFile = 'controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerInstance = new $controllerName();
    
    // Check if action exists
    if (method_exists($controllerInstance, $action)) {
        // Call the action with parameter if provided
        if ($id !== null) {
            $controllerInstance->$action($id);
        } else {
            $controllerInstance->$action();
        }
    } else {
        // Action not found
        require_once 'controllers/ErrorController.php';
        $errorController = new ErrorController();
        $errorController->notFound();
    }
} else {
    // Controller not found
    require_once 'controllers/ErrorController.php';
    $errorController = new ErrorController();
    $errorController->notFound();
}
?>

