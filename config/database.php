<?php
// Smart Cab Booking System - Database Configuration & Helpers
// Location: config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smart_cab_db');
define('SITE_URL', 'http://localhost/CabBooking'); // Adjust if directory name is different

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 30px; border: 1px solid #ffccd5; background-color: #fff0f3; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                    <h2 style='color: #d90429; margin-top: 0;'>Database Connection Error</h2>
                    <p style='color: #2b2d42;'>Unable to connect to the database. Please ensure that:</p>
                    <ul style='color: #2b2d42; line-height: 1.6;'>
                        <li>Your MySQL local server (XAMPP / WAMP / MAMP) is running.</li>
                        <li>You have imported the database schema from the <strong>schema.sql</strong> file.</li>
                        <li>Database credentials in <code>config/database.php</code> match your local environment.</li>
                    </ul>
                    <hr style='border: 0; border-top: 1px solid #ffccd5; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #8d99ae;'>Technical details: " . htmlspecialchars($e->getMessage()) . "</p>
                </div>
            ");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Start PHP Session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize CSRF Token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Global Sanitization Helper (XSS Protection)
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Verify CSRF Token
function verify_csrf($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die("CSRF token validation failed. Unauthorized request.");
    }
    return true;
}

// Helper to redirect
function redirect($path) {
    header("Location: " . SITE_URL . "/" . ltrim($path, '/'));
    exit;
}

// Helper for JSON Responses (AJAX calls)
function json_response($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'status' => $status,
        'message' => $message
    ], $data));
    exit;
}

// Check logged in roles
function check_auth($role = null) {
    if (!isset($_SESSION['role'])) {
        redirect('index.php?controller=auth&action=login');
    }
    
    // Check corresponding session id for role
    if ($_SESSION['role'] === 'user' && !isset($_SESSION['user_id'])) {
        redirect('index.php?controller=auth&action=login');
    }
    if ($_SESSION['role'] === 'driver' && !isset($_SESSION['driver_id'])) {
        redirect('index.php?controller=auth&action=login');
    }
    if ($_SESSION['role'] === 'admin' && !isset($_SESSION['admin_id'])) {
        redirect('index.php?controller=auth&action=login');
    }

    if ($role !== null && $_SESSION['role'] !== $role) {
        redirect('index.php?controller=auth&action=login');
    }
}

// Log notification helper
function create_notification($db, $title, $message, $type, $target_id) {
    try {
        $stmt = null;
        if ($type === 'user') {
            $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'user')");
        } elseif ($type === 'driver') {
            $stmt = $db->prepare("INSERT INTO notifications (driver_id, title, message, type) VALUES (?, ?, ?, 'driver')");
        } else {
            $stmt = $db->prepare("INSERT INTO notifications (admin_id, title, message, type) VALUES (?, ?, ?, 'admin')");
        }
        $stmt->execute([$target_id, $title, $message]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
