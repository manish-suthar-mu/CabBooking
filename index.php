<?php
// Smart Cab Booking System - Front Controller Router
// Location: index.php

// Enable error reporting for debugging simulation
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include configuration and database helpers
require_once __DIR__ . '/config/database.php';

// Instantiate Database and PDO Connection
$dbObj = Database::getInstance();
$db = $dbObj->getConnection();

// Autoload Models
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Driver.php';
require_once __DIR__ . '/models/Vehicle.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/Notification.php';
require_once __DIR__ . '/models/AdminModel.php';

// Instantiate Models
$userModel = new User($db);
$driverModel = new Driver($db);
$vehicleModel = new Vehicle($db);
$bookingModel = new Booking($db);
$notificationModel = new Notification($db);
$adminModel = new AdminModel($db);

// Parse Route parameters (default to home/index)
$controllerName = isset($_GET['controller']) ? trim($_GET['controller']) : 'home';
$actionName = isset($_GET['action']) ? trim($_GET['action']) : 'index';

// Map controllers to files
$controllers = [
    'home' => 'AuthController', // AuthController handles landing page as well
    'auth' => 'AuthController',
    'user' => 'UserController',
    'driver' => 'DriverController',
    'booking' => 'BookingController',
    'admin' => 'AdminController'
];

if (!array_key_exists($controllerName, $controllers)) {
    // Route not found, default to home
    $controllerName = 'home';
}

$controllerClass = $controllers[$controllerName];
$controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Inject models into the controller constructor
    $controllerInstance = new $controllerClass(
        $userModel,
        $driverModel,
        $vehicleModel,
        $bookingModel,
        $notificationModel,
        $adminModel
    );
    
    // Call the action method if it exists
    if (method_exists($controllerInstance, $actionName)) {
        $controllerInstance->$actionName();
    } else {
        // Fallback to home/index if action doesn't exist
        header("HTTP/1.0 404 Not Found");
        die("404 - Page Action not found: " . htmlspecialchars($actionName));
    }
} else {
    die("Controller file not found: " . htmlspecialchars($controllerFile));
}
