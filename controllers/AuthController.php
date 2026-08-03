<?php
// Smart Cab Booking System - Authentication Controller
// Location: controllers/AuthController.php

class AuthController {
    private $userModel;
    private $driverModel;
    private $vehicleModel;
    private $bookingModel;
    private $notificationModel;
    private $adminModel;

    public function __construct($userModel, $driverModel, $vehicleModel, $bookingModel, $notificationModel, $adminModel) {
        $this->userModel = $userModel;
        $this->driverModel = $driverModel;
        $this->vehicleModel = $vehicleModel;
        $this->bookingModel = $bookingModel;
        $this->notificationModel = $notificationModel;
        $this->adminModel = $adminModel;
    }

    // Helper to render templates
    private function render($view, $data = [], $title = "Smart Cab Booking System") {
        extract($data);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    // Index page / Landing Page
    public function index() {
        if (isset($_SESSION['role'])) {
            $this->redirectToDashboard();
        }
        $this->render('home', [], 'Welcome to Smart Cab Booking System');
    }

    // Render Login Page
    public function login() {
        if (isset($_SESSION['role'])) {
            $this->redirectToDashboard();
        }
        $this->render('auth/login', [], 'Sign In');
    }

    // Handle Login Post
    public function postLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=auth&action=login');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Email and Password are required.";
            redirect('index.php?controller=auth&action=login');
        }

        if ($role === 'user') {
            $user = $this->userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] === 'suspended') {
                    $_SESSION['error'] = "Your account has been suspended by the administrator.";
                    redirect('index.php?controller=auth&action=login');
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = 'user';
                redirect('index.php?controller=user&action=dashboard');
            }
        } elseif ($role === 'driver') {
            $driver = $this->driverModel->findByEmail($email);
            if ($driver && password_verify($password, $driver['password'])) {
                if ($driver['status'] === 'suspended') {
                    $_SESSION['error'] = "Your driver account is suspended.";
                    redirect('index.php?controller=auth&action=login');
                }
                $_SESSION['driver_id'] = $driver['id'];
                $_SESSION['name'] = $driver['name'];
                $_SESSION['email'] = $driver['email'];
                $_SESSION['role'] = 'driver';
                $_SESSION['driver_status'] = $driver['status']; // approved / pending_approval
                redirect('index.php?controller=driver&action=dashboard');
            }
        } elseif ($role === 'admin') {
            $admin = $this->adminModel->login($email, $password); // email acts as username for admin form
            if ($admin) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['name'] = $admin['username'];
                $_SESSION['email'] = $admin['email'];
                $_SESSION['role'] = 'admin';
                redirect('index.php?controller=admin&action=dashboard');
            }
        }

        $_SESSION['error'] = "Invalid credentials or account role.";
        redirect('index.php?controller=auth&action=login');
    }

    // Render User Registration
    public function register() {
        if (isset($_SESSION['role'])) {
            $this->redirectToDashboard();
        }
        $this->render('auth/register', [], 'Register Account');
    }

    // Handle User Registration Post
    public function postRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=auth&action=register');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            $_SESSION['error'] = "All fields are required.";
            redirect('index.php?controller=auth&action=register');
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = "Email is already registered.";
            redirect('index.php?controller=auth&action=register');
        }

        if ($this->userModel->register($name, $email, $password, $phone)) {
            $_SESSION['success'] = "Registration successful! You can now log in.";
            redirect('index.php?controller=auth&action=login');
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            redirect('index.php?controller=auth&action=register');
        }
    }

    // Render Driver Registration
    public function driverRegister() {
        if (isset($_SESSION['role'])) {
            $this->redirectToDashboard();
        }
        $this->render('auth/driver-register', [], 'Driver Registration');
    }

    // Handle Driver Registration Post
    public function postDriverRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=auth&action=driverRegister');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $license_no = trim($_POST['license_no'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($license_no) || empty($password)) {
            $_SESSION['error'] = "All fields are required.";
            redirect('index.php?controller=auth&action=driverRegister');
        }

        if ($this->driverModel->findByEmail($email)) {
            $_SESSION['error'] = "Email is already registered.";
            redirect('index.php?controller=auth&action=driverRegister');
        }

        if ($this->driverModel->register($name, $email, $password, $phone, $license_no)) {
            $_SESSION['success'] = "Registration submitted! Please wait for Admin approval before logging in.";
            redirect('index.php?controller=auth&action=login');
        } else {
            $_SESSION['error'] = "Registration failed. Please check details.";
            redirect('index.php?controller=auth&action=driverRegister');
        }
    }

    // Render Forgot Password
    public function forgotPassword() {
        $this->render('auth/forgot-password', [], 'Reset Password');
    }

    // Handle Forgot Password Post
    public function postForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=auth&action=forgotPassword');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';

        if (empty($email)) {
            $_SESSION['error'] = "Please enter your email.";
            redirect('index.php?controller=auth&action=forgotPassword');
        }

        // Simulate sending mail
        // Check if user exists in the chosen role
        $exists = false;
        if ($role === 'user') {
            $exists = $this->userModel->findByEmail($email);
        } else if ($role === 'driver') {
            $exists = $this->driverModel->findByEmail($email);
        }

        if ($exists) {
            // Save dummy notification and alert user of simulation success
            // In a real app we would mail. Here we log it.
            $_SESSION['success'] = "Password reset link sent to: " . htmlspecialchars($email) . " (Simulated notification saved in database). In a real application, an email would be delivered.";
        } else {
            $_SESSION['error'] = "Email address not found in our records.";
        }
        redirect('index.php?controller=auth&action=forgotPassword');
    }

    // Handle Logout
    public function logout() {
        // Don't set driver offline when logging out - keep their online status
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['success'] = "Logged out successfully.";
        redirect('index.php?controller=auth&action=login');
    }

    private function redirectToDashboard() {
        if ($_SESSION['role'] === 'admin') {
            redirect('index.php?controller=admin&action=dashboard');
        } elseif ($_SESSION['role'] === 'driver') {
            redirect('index.php?controller=driver&action=dashboard');
        } else {
            redirect('index.php?controller=user&action=dashboard');
        }
    }
}
