<?php
// Smart Cab Booking System - User Controller
// Location: controllers/UserController.php

class UserController {
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

    private function render($view, $data = [], $title = "User Dashboard") {
        extract($data);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function dashboard() {
        check_auth('user');
        
        $user_id = $_SESSION['user_id'];
        $activeBooking = $this->bookingModel->getActiveForUser($user_id);
        
        // If there is an active booking, redirect to appropriate sub-pages:
        // - if ongoing or accepted: redirect to tracking page
        // - if pending: redirect to book page or show loading state
        
        $fareRates = $this->bookingModel->getFareRates();

        $this->render('user/dashboard', [
            'activeBooking' => $activeBooking,
            'fareRates' => $fareRates
        ], 'Book a Cab');
    }

    public function profile() {
        check_auth('user');
        $user = $this->userModel->findById($_SESSION['user_id']);
        $this->render('user/profile', ['user' => $user], 'My Profile');
    }

    public function postProfile() {
        check_auth('user');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=user&action=profile');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (empty($name) || empty($phone)) {
            $_SESSION['error'] = "Name and Phone fields are required.";
            redirect('index.php?controller=user&action=profile');
        }

        if ($this->userModel->updateProfile($_SESSION['user_id'], $name, $phone)) {
            $_SESSION['name'] = $name; // Update session name
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update profile.";
        }
        redirect('index.php?controller=user&action=profile');
    }

    public function postPassword() {
        check_auth('user');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=user&action=profile');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $old_pass = $_POST['old_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
            $_SESSION['error'] = "All password fields are required.";
            redirect('index.php?controller=user&action=profile');
        }

        if ($new_pass !== $confirm_pass) {
            $_SESSION['error'] = "New passwords do not match.";
            redirect('index.php?controller=user&action=profile');
        }

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (password_verify($old_pass, $user['password'])) {
            if ($this->userModel->updatePassword($_SESSION['user_id'], $new_pass)) {
                $_SESSION['success'] = "Password changed successfully.";
            } else {
                $_SESSION['error'] = "Failed to update password.";
            }
        } else {
            $_SESSION['error'] = "Incorrect old password.";
        }
        redirect('index.php?controller=user&action=profile');
    }

    public function history() {
        check_auth('user');
        $history = $this->bookingModel->getHistoryForUser($_SESSION['user_id']);
        $this->render('user/history', ['history' => $history], 'Booking History');
    }

    public function notifications() {
        check_auth('user');
        $notifications = $this->notificationModel->getForUser($_SESSION['user_id']);
        
        // Mark all as read
        $this->notificationModel->markAllAsRead('user', $_SESSION['user_id']);

        $this->render('user/notifications', ['notifications' => $notifications], 'My Notifications');
    }

    // AJAX Endpoint: Check notifications
    public function getNotifications() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            json_response('error', 'Unauthorized');
        }

        $user_id = $_SESSION['user_id'];
        $unreadCount = $this->notificationModel->getUnreadCount('user', $user_id);
        $list = $this->notificationModel->getForUser($user_id);

        json_response('success', 'Fetched successfully', [
            'unread_count' => $unreadCount,
            'notifications' => array_slice($list, 0, 5) // return top 5
        ]);
    }

    // AJAX Endpoint: Mark notification as read
    public function markNotificationRead() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            json_response('error', 'Unauthorized');
        }

        verify_csrf($_POST['csrf_token'] ?? '');
        
        $notification_id = intval($_POST['notification_id'] ?? 0);
        if ($notification_id > 0) {
            $this->notificationModel->markAsRead($notification_id);
            json_response('success', 'Notification marked as read');
        } else {
            json_response('error', 'Invalid notification ID');
        }
    }
}
