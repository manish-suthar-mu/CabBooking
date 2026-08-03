<?php
// Smart Cab Booking System - Driver Controller
// Location: controllers/DriverController.php

class DriverController {
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

    private function render($view, $data = [], $title = "Driver Dashboard") {
        extract($data);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    public function dashboard() {
        check_auth('driver');
        
        $driver_id = $_SESSION['driver_id'];
        $driver = $this->driverModel->findById($driver_id);
        
        // Refresh driver approval status in session
        $_SESSION['driver_status'] = $driver['status'];

        $vehicle = $this->vehicleModel->findByDriverId($driver_id);
        $activeBooking = $this->bookingModel->getActiveForDriver($driver_id);
        
        // If driver has vehicle and is approved, fetch pending requests
        $pendingRequests = [];
        if ($driver['status'] === 'approved' && $vehicle) {
            $pendingRequests = $this->bookingModel->getRequestsForDriver($driver_id);
        }

        $this->render('driver/dashboard', [
            'driver' => $driver,
            'vehicle' => $vehicle,
            'activeBooking' => $activeBooking,
            'pendingRequests' => $pendingRequests
        ], 'Driver Command Center');
    }

    public function vehicle() {
        check_auth('driver');
        $vehicle = $this->vehicleModel->findByDriverId($_SESSION['driver_id']);
        $this->render('driver/vehicle', ['vehicle' => $vehicle], 'Vehicle Management');
    }

    public function postVehicle() {
        check_auth('driver');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=driver&action=vehicle');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $type = $_POST['type'] ?? 'car';
        $model = trim($_POST['model'] ?? '');
        $plate_number = trim($_POST['plate_number'] ?? '');
        $color = trim($_POST['color'] ?? '');

        if (empty($model) || empty($plate_number) || empty($color)) {
            $_SESSION['error'] = "All vehicle fields are required.";
            redirect('index.php?controller=driver&action=vehicle');
        }

        if ($this->vehicleModel->save($_SESSION['driver_id'], $type, $model, $plate_number, $color)) {
            $_SESSION['success'] = "Vehicle specifications saved successfully.";
        } else {
            $_SESSION['error'] = "Failed to update vehicle details. Ensure plate number is unique.";
        }
        redirect('index.php?controller=driver&action=vehicle');
    }

    public function history() {
        check_auth('driver');
        $history = $this->bookingModel->getHistoryForDriver($_SESSION['driver_id']);
        $this->render('driver/history', ['history' => $history], 'Ride History');
    }

    public function earnings() {
        check_auth('driver');
        $driver_id = $_SESSION['driver_id'];
        
        // Fetch earnings logs
        $stmt = $this->db_query_helper("
            SELECT e.*, b.pickup_location, b.drop_location, b.distance, b.created_at as trip_date
            FROM earnings e
            JOIN bookings b ON e.booking_id = b.id
            WHERE e.driver_id = ?
            ORDER BY e.created_at DESC
        ", [$driver_id]);
        $earningsLogs = $stmt->fetchAll();

        // Calculate totals
        $totalEarned = 0;
        $totalCommission = 0;
        $totalNet = 0;
        foreach ($earningsLogs as $log) {
            $totalEarned += $log['amount'];
            $totalCommission += $log['commission_deducted'];
            $totalNet += $log['net_amount'];
        }

        $this->render('driver/earnings', [
            'earningsLogs' => $earningsLogs,
            'totalEarned' => $totalEarned,
            'totalCommission' => $totalCommission,
            'totalNet' => $totalNet
        ], 'My Earnings Wallet');
    }

    // AJAX Endpoint: Toggle Online Status
    public function postOnlineStatus() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $status = isset($_POST['is_online']) ? intval($_POST['is_online']) : 0;
        $driver_id = $_SESSION['driver_id'];

        // Make sure driver is approved
        $driver = $this->driverModel->findById($driver_id);
        if ($driver['status'] !== 'approved') {
            json_response('error', 'Your account is pending administrator approval.');
        }

        // Verify driver has loaded vehicle details
        $vehicle = $this->vehicleModel->findByDriverId($driver_id);
        if (!$vehicle && $status == 1) {
            json_response('error', 'You must save vehicle specifications before going online.');
        }

        if ($this->driverModel->updateOnlineStatus($driver_id, $status)) {
            $msg = $status == 1 ? 'You are now online.' : 'You are now offline.';
            json_response('success', $msg, ['is_online' => $status]);
        } else {
            json_response('error', 'Failed to update online status.');
        }
    }

    // AJAX Endpoint: Update Dummy GPS Coordinates
    public function postLocation() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $lat = floatval($_POST['latitude'] ?? 0);
        $lng = floatval($_POST['longitude'] ?? 0);
        $driver_id = $_SESSION['driver_id'];

        if ($lat == 0 || $lng == 0) {
            json_response('error', 'Invalid coordinates.');
        }

        if ($this->driverModel->updateLocation($driver_id, $lat, $lng)) {
            json_response('success', 'Location updated.', ['latitude' => $lat, 'longitude' => $lng]);
        } else {
            json_response('error', 'Failed to update location.');
        }
    }

    // Helper method to execute queries
    // Show driver's ride receipt after completion
    public function receipt() {
        check_auth('driver');
        $booking_id = intval($_GET['booking_id'] ?? 0);
        $booking = $this->bookingModel->findById($booking_id);
        if (!$booking || $booking['driver_id'] != $_SESSION['driver_id']) {
            $_SESSION['error'] = "Invalid booking ID.";
            redirect('index.php?controller=driver&action=dashboard');
        }
        $user = $this->userModel->findById($booking['user_id']);
        $this->render('driver/receipt', [
            'booking' => $booking,
            'user' => $user
        ], 'Ride Receipt');
    }

    private function db_query_helper($sql, $params = []) {
        $dbObj = Database::getInstance();
        $db = $dbObj->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // AJAX Endpoint: Check notifications
    public function getNotifications() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized');
        }

        $driver_id = $_SESSION['driver_id'];
        $unreadCount = $this->notificationModel->getUnreadCount('driver', $driver_id);
        $list = $this->notificationModel->getForDriver($driver_id);

        json_response('success', 'Fetched successfully', [
            'unread_count' => $unreadCount,
            'notifications' => array_slice($list, 0, 5)
        ]);
    }

    // AJAX Endpoint: Mark notification as read
    public function markNotificationRead() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
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
