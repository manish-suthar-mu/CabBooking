<?php
// Smart Cab Booking System - Admin Controller
// Location: controllers/AdminController.php

class AdminController {
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

    private function render($view, $data = [], $title = "Admin Dashboard") {
        extract($data);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function dashboard() {
        check_auth('admin');
        
        $stats = $this->adminModel->getDashboardStats();
        $latestBookings = array_slice($this->bookingModel->getAllBookings(), 0, 5); // Latest 5 bookings
        
        // Fetch driver locations for the live dummy GPS tracking map for admins
        $drivers = $this->driverModel->getAll();

        $this->render('admin/dashboard', [
            'stats' => $stats,
            'latestBookings' => $latestBookings,
            'drivers' => $drivers
        ], 'Administration System Overview');
    }

    public function users() {
        check_auth('admin');
        $users = $this->userModel->getAll();
        $this->render('admin/users', ['users' => $users], 'Manage Users');
    }

    public function drivers() {
        check_auth('admin');
        $drivers = $this->driverModel->getAll();
        $this->render('admin/drivers', ['drivers' => $drivers], 'Manage Drivers');
    }

    public function bookings() {
        check_auth('admin');
        $bookings = $this->bookingModel->getAllBookings();
        $this->render('admin/bookings', ['bookings' => $bookings], 'All Bookings');
    }

    public function vehicles() {
        check_auth('admin');
        $vehicles = $this->vehicleModel->getAll();
        $this->render('admin/vehicles', ['vehicles' => $vehicles], 'Manage Vehicles');
    }

    public function fares() {
        check_auth('admin');
        $rates = $this->bookingModel->getFareRates();
        $this->render('admin/fares', ['rates' => $rates], 'Manage Fare Rates');
    }

    public function postFare() {
        check_auth('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=admin&action=fares');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $type = $_POST['vehicle_type'] ?? '';
        $base = floatval($_POST['base_fare'] ?? 0);
        $per_km = floatval($_POST['per_km_rate'] ?? 0);

        if (empty($type) || $base <= 0 || $per_km <= 0) {
            $_SESSION['error'] = "Invalid fare parameters. Values must be positive.";
            redirect('index.php?controller=admin&action=fares');
        }

        if ($this->bookingModel->updateFareRate($type, $base, $per_km)) {
            $_SESSION['success'] = "Fare configuration for " . ucfirst($type) . " updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update fare rate.";
        }
        redirect('index.php?controller=admin&action=fares');
    }

    public function reviews() {
        check_auth('admin');
        $reviews = $this->adminModel->getAllReviews();
        $this->render('admin/reviews', ['reviews' => $reviews], 'Customer Reviews & Feedback');
    }

    public function earnings() {
        check_auth('admin');
        $earnings = $this->adminModel->getEarningsReport();
        
        $stats = $this->adminModel->getDashboardStats();
        
        $this->render('admin/earnings', [
            'earnings' => $earnings,
            'total_revenue' => $stats['total_revenue'],
            'total_commission' => $stats['total_earnings']
        ], 'Financial & Earnings Statement');
    }

    // AJAX Endpoint: Toggle User Suspended status
    public function toggleUserStatus() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $user_id = intval($_POST['user_id'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if (!in_array($status, ['active', 'suspended'])) {
            json_response('error', 'Invalid status.');
        }

        if ($this->userModel->updateStatus($user_id, $status)) {
            $msg = $status === 'suspended' ? 'User account suspended.' : 'User account activated.';
            
            // Create notification for user
            $dbObj = Database::getInstance();
            create_notification($dbObj->getConnection(), "Account Status Update", "Your account status has been changed to " . $status, "user", $user_id);
            
            json_response('success', $msg);
        } else {
            json_response('error', 'Failed to update user status.');
        }
    }

    // AJAX Endpoint: Approve Driver
    public function approveDriver() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $driver_id = intval($_POST['driver_id'] ?? 0);

        if ($this->driverModel->updateStatus($driver_id, 'approved')) {
            $dbObj = Database::getInstance();
            create_notification($dbObj->getConnection(), "Profile Approved", "Your driver application has been approved by the administrator. You can now go online.", "driver", $driver_id);
            json_response('success', 'Driver account approved successfully.');
        } else {
            json_response('error', 'Failed to approve driver.');
        }
    }

    // AJAX Endpoint: Reject Driver
    public function rejectDriver() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $driver_id = intval($_POST['driver_id'] ?? 0);

        if ($this->driverModel->updateStatus($driver_id, 'pending_approval')) {
            $dbObj = Database::getInstance();
            create_notification($dbObj->getConnection(), "Profile Rejected", "Your driver profile was rejected or set to pending verification.", "driver", $driver_id);
            json_response('success', 'Driver set back to pending status.');
        } else {
            json_response('error', 'Failed to reject driver.');
        }
    }

    // AJAX Endpoint: Suspend Driver
    public function suspendDriver() {
        if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $driver_id = intval($_POST['driver_id'] ?? 0);
        $status = $_POST['status'] ?? 'suspended'; // suspended or approved

        if (!in_array($status, ['approved', 'suspended'])) {
            json_response('error', 'Invalid status.');
        }

        if ($this->driverModel->updateStatus($driver_id, $status)) {
            $msg = $status === 'suspended' ? 'Driver account suspended.' : 'Driver account activated.';
            
            // Set offline if suspended
            if ($status === 'suspended') {
                $this->driverModel->updateOnlineStatus($driver_id, 0);
            }

            $dbObj = Database::getInstance();
            create_notification($dbObj->getConnection(), "Account Status Update", "Your driver account status is now: " . $status, "driver", $driver_id);
            
            json_response('success', $msg);
        } else {
            json_response('error', 'Failed to update driver status.');
        }
    }
}
