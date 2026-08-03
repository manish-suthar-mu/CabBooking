<?php
// Smart Cab Booking System - Booking Controller
// Location: controllers/BookingController.php

class BookingController {
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

    private function render($view, $data = [], $title = "Ride Tracking") {
        extract($data);
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/' . $view . '.php';
        include __DIR__ . '/../views/layout/footer.php';
    }

    // AJAX Endpoint: Estimate Fare
    public function estimate() {
        if (!isset($_SESSION['user_id'])) {
            json_response('error', 'Unauthorized access.');
        }

        $vehicle_type = $_POST['vehicle_type'] ?? 'car';
        $plat = floatval($_POST['pickup_lat'] ?? 0);
        $plng = floatval($_POST['pickup_lng'] ?? 0);
        $dlat = floatval($_POST['drop_lat'] ?? 0);
        $dlng = floatval($_POST['drop_lng'] ?? 0);

        if ($plat == 0 || $plng == 0 || $dlat == 0 || $dlng == 0) {
            json_response('error', 'Please select valid pickup and drop-off locations on the map.');
        }

        // Calculate straight-line distance in kilometers (approx 111.32 km per degree)
        $distance = sqrt(pow($dlat - $plat, 2) + pow($dlng - $plng, 2)) * 111.32;
        if ($distance < 0.5) {
            $distance = 0.5; // Minimum distance
        }

        $rates = $this->bookingModel->getFareRateByType($vehicle_type);
        if (!$rates) {
            json_response('error', 'Fare configuration not found.');
        }

        $base = floatval($rates['base_fare']);
        $per_km = floatval($rates['per_km_rate']);
        $estimated_fare = $base + ($distance * $per_km);

        json_response('success', 'Estimation successful', [
            'distance' => round($distance, 2),
            'base_fare' => $base,
            'per_km_rate' => $per_km,
            'estimated_fare' => round($estimated_fare, 2)
        ]);
    }

    // AJAX Endpoint: Book Ride
    public function book() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $user_id = $_SESSION['user_id'];
        $pickup_loc = trim($_POST['pickup_location'] ?? '');
        $drop_loc = trim($_POST['drop_location'] ?? '');
        $plat = floatval($_POST['pickup_lat'] ?? 0);
        $plng = floatval($_POST['pickup_lng'] ?? 0);
        $dlat = floatval($_POST['drop_lat'] ?? 0);
        $dlng = floatval($_POST['drop_lng'] ?? 0);
        $vehicle_type = $_POST['vehicle_type'] ?? 'car';

        if (empty($pickup_loc) || empty($drop_loc) || $plat == 0 || $plng == 0 || $dlat == 0 || $dlng == 0) {
            json_response('error', 'Please fill pickup and drop locations.');
        }

        // Check if user already has an active ride
        $active = $this->bookingModel->getActiveForUser($user_id);
        if ($active) {
            json_response('error', 'You already have an active ride request.', ['booking_id' => $active['id']]);
        }

        // Calculate distance & fare again to verify
        $distance = sqrt(pow($dlat - $plat, 2) + pow($dlng - $plng, 2)) * 111.32;
        if ($distance < 0.5) $distance = 0.5;

        $rates = $this->bookingModel->getFareRateByType($vehicle_type);
        $estimated_fare = floatval($rates['base_fare']) + ($distance * floatval($rates['per_km_rate']));

        // Check if any online approved driver exists
        $drivers = $this->driverModel->getAvailableDrivers($vehicle_type);
        if (empty($drivers)) {
            json_response('error', 'No online drivers available for ' . ucfirst($vehicle_type) . ' at the moment.');
        }

        $booking_id = $this->bookingModel->create($user_id, $pickup_loc, $drop_loc, $plat, $plng, $dlat, $dlng, $vehicle_type, $distance, $estimated_fare);

        if ($booking_id) {
            json_response('success', 'Searching for a driver...', ['booking_id' => $booking_id]);
        } else {
            json_response('error', 'Booking failed to initialize.');
        }
    }

    // AJAX Endpoint: Check Booking Status
    public function status() {
        $booking_id = intval($_GET['booking_id'] ?? 0);
        if ($booking_id == 0) {
            json_response('error', 'Invalid booking ID.');
        }

        $booking = $this->bookingModel->findById($booking_id);
        if (!$booking) {
            json_response('error', 'Booking details not found.');
        }

        // Fetch driver & vehicle details if assigned
        $driverDetails = null;
        if ($booking['driver_id']) {
            $driver = $this->driverModel->findById($booking['driver_id']);
            $vehicle = $this->vehicleModel->findByDriverId($booking['driver_id']);
            $driverDetails = [
                'name' => $driver['name'],
                'phone' => $driver['phone'],
                'latitude' => floatval($driver['latitude']),
                'longitude' => floatval($driver['longitude']),
                'vehicle_model' => $vehicle['model'] ?? 'Standard Vehicle',
                'plate_number' => $vehicle['plate_number'] ?? 'N/A',
                'color' => $vehicle['color'] ?? 'N/A'
            ];
        }

        json_response('success', 'Status retrieved', [
            'booking_id' => $booking['id'],
            'booking_status' => $booking['status'], // Renamed to avoid conflict with response status!
            'payment_status' => $booking['payment_status'],
            'estimated_fare' => floatval($booking['estimated_fare']),
            'actual_fare' => $booking['actual_fare'] ? floatval($booking['actual_fare']) : null,
            'driver' => $driverDetails,
            'otp' => $booking['otp'] ?? null
        ]);
    }

    // AJAX Endpoint: Driver Accepts Ride
    public function accept() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $driver_id = $_SESSION['driver_id'];

        // Make sure driver has vehicle and is online/approved
        $driver = $this->driverModel->findById($driver_id);
        if ($driver['status'] !== 'approved' || $driver['is_online'] != 1) {
            json_response('error', 'You must be approved and online to accept rides.');
        }

        if ($this->bookingModel->acceptRequest($booking_id, $driver_id)) {
            json_response('success', 'Booking accepted successfully.', ['booking_id' => $booking_id]);
        } else {
            json_response('error', 'This ride has already been accepted by another driver or was cancelled.');
        }
    }

    // AJAX Endpoint: Driver Rejects Ride
    public function reject() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $driver_id = $_SESSION['driver_id'];

        if ($this->bookingModel->rejectRequest($booking_id, $driver_id)) {
            json_response('success', 'Booking request rejected.');
        } else {
            json_response('error', 'Failed to reject request.');
        }
    }

    // AJAX Endpoint: Verify OTP
    public function verifyOtp() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');
        
        $booking_id = intval($_POST['booking_id'] ?? 0);
        $otp = trim($_POST['otp'] ?? '');

        if ($this->bookingModel->verifyOtp($booking_id, $otp)) {
            json_response('success', 'OTP Verified!');
        } else {
            json_response('error', 'Invalid OTP!');
        }
    }

    // AJAX Endpoint: Start Ride (Driver)
    public function start() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);

        if ($this->bookingModel->startRide($booking_id)) {
            json_response('success', 'Ride started. Drive safely!');
        } else {
            json_response('error', 'Failed to start ride.');
        }
    }

    // AJAX Endpoint: Complete Ride (Driver)
    public function complete() {
        if (!isset($_SESSION['driver_id']) || $_SESSION['role'] !== 'driver') {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $booking = $this->bookingModel->findById($booking_id);

        if (!$booking) {
            json_response('error', 'Booking not found.');
        }

        // Actual fare is set as estimated fare for simulation, or could add minor toll/traffic variation
        $actual_fare = $booking['estimated_fare'];

        if ($this->bookingModel->completeRide($booking_id, $actual_fare)) {
            json_response('success', 'Ride completed successfully.', ['actual_fare' => $actual_fare]);
        } else {
            json_response('error', 'Failed to complete ride.');
        }
    }

    // AJAX Endpoint: Cancel Ride (User or Driver)
    public function cancel() {
        if (!isset($_SESSION['role'])) {
            json_response('error', 'Unauthorized.');
        }

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $role = $_SESSION['role'];

        if ($this->bookingModel->cancel($booking_id, $role)) {
            json_response('success', 'Ride cancelled.');
        } else {
            json_response('error', 'Unable to cancel ride. It may be completed or already cancelled.');
        }
    }

    // Render Ride Tracking View
    public function tracking() {
        if (!isset($_SESSION['role'])) {
            redirect('index.php?controller=auth&action=login');
        }

        $booking_id = intval($_GET['booking_id'] ?? 0);
        $booking = $this->bookingModel->findById($booking_id);

        if (!$booking) {
            $_SESSION['error'] = "Booking tracking expired or not found.";
            redirect('index.php');
        }

        $user = $this->userModel->findById($booking['user_id']);
        $driver = $booking['driver_id'] ? $this->driverModel->findById($booking['driver_id']) : null;
        $vehicle = $booking['driver_id'] ? $this->vehicleModel->findByDriverId($booking['driver_id']) : null;

        $this->render('booking/tracking', [
            'booking' => $booking,
            'user' => $user,
            'driver' => $driver,
            'vehicle' => $vehicle
        ], 'Live Trip Tracking');
    }

    // Render Dummy Payment View
    public function payment() {
        check_auth('user');

        $booking_id = intval($_GET['booking_id'] ?? 0);
        $booking = $this->bookingModel->findById($booking_id);

        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Invalid booking ID.";
            redirect('index.php');
        }

        if ($booking['status'] !== 'completed') {
            $_SESSION['error'] = "This ride is not completed yet.";
            redirect('index.php?controller=booking&action=tracking&booking_id=' . $booking_id);
        }

        if ($booking['payment_status'] === 'paid') {
            $_SESSION['success'] = "Payment was already completed for this trip.";
            redirect('index.php?controller=user&action=history');
        }

        $driver = $booking['driver_id'] ? $this->driverModel->findById($booking['driver_id']) : null;

        $this->render('booking/payment', [
            'booking' => $booking,
            'driver' => $driver
        ], 'Payment Gateway Gateway');
    }

    // AJAX Endpoint: Process Payment
    public function pay() {
        check_auth('user');

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $method = $_POST['payment_method'] ?? 'cash';
        
        // Generate simulated transaction ID
        $tx_id = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));

        if ($this->bookingModel->processPayment($booking_id, $method, $tx_id)) {
            json_response('success', 'Payment successful!', [
                'transaction_id' => $tx_id,
                'booking_id' => $booking_id
            ]);
        } else {
            json_response('error', 'Payment processing failed or already paid.');
        }
    }

    // AJAX Endpoint: Rate and Review
    public function submitReview() {
        check_auth('user');

        verify_csrf($_POST['csrf_token'] ?? '');

        $booking_id = intval($_POST['booking_id'] ?? 0);
        $rating = intval($_POST['rating'] ?? 5);
        $review_text = trim($_POST['review_text'] ?? '');

        if ($rating < 1 || $rating > 5) {
            json_response('error', 'Rating must be between 1 and 5 stars.');
        }

        $booking = $this->bookingModel->findById($booking_id);
        if (!$booking || !$booking['driver_id'] || $booking['user_id'] != $_SESSION['user_id']) {
            json_response('error', 'Invalid booking details.');
        }

        if ($this->bookingModel->addReview($booking_id, $_SESSION['user_id'], $booking['driver_id'], $rating, $review_text)) {
            json_response('success', 'Thank you for your rating!');
        } else {
            json_response('error', 'Failed to submit rating. You may have reviewed this ride already.');
        }
    }
}
