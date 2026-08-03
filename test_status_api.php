<?php
// Test script to check status API response
// First, let's mimic what the status action does
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Driver.php';
require_once __DIR__ . '/models/Vehicle.php';
require_once __DIR__ . '/models/Booking.php';

try {
    $dbObj = Database::getInstance();
    $db = $dbObj->getConnection();

    $bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 5; // Change to your booking ID

    echo "<h1>Testing Status API for Booking ID: $bookingId</h1>";

    // 1. Get booking
    $bookingModel = new Booking($db);
    $booking = $bookingModel->findById($bookingId);
    echo "<h3>Booking Data:</h3><pre>";
    print_r($booking);
    echo "</pre>";

    if (!$booking) {
        echo "<p style='color:red;'>❌ Booking not found!</p>";
        exit;
    }

    // 2. Get driver and vehicle if assigned
    $driverDetails = null;
    if ($booking['driver_id']) {
        $driverModel = new Driver($db);
        $vehicleModel = new Vehicle($db);
        $driver = $driverModel->findById($booking['driver_id']);
        $vehicle = $vehicleModel->findByDriverId($booking['driver_id']);
        $driverDetails = [
            'name' => $driver['name'],
            'phone' => $driver['phone'],
            'latitude' => floatval($driver['latitude']),
            'longitude' => floatval($driver['longitude']),
            'vehicle_model' => $vehicle['model'] ?? 'Standard Vehicle',
            'plate_number' => $vehicle['plate_number'] ?? 'N/A',
            'color' => $vehicle['color'] ?? 'N/A'
        ];
        echo "<h3>Driver Details:</h3><pre>";
        print_r($driver);
        echo "</pre>";
        echo "<h3>Vehicle Details:</h3><pre>";
        print_r($vehicle);
        echo "</pre>";
    } else {
        echo "<p style='color:orange;'>⚠️ No driver assigned to this booking!</p>";
    }

    // 3. Show what the API would return
    $response = [
        'status' => 'success',
        'message' => 'Status retrieved',
        'booking_id' => $booking['id'],
        'booking_status' => $booking['status'],
        'payment_status' => $booking['payment_status'],
        'estimated_fare' => floatval($booking['estimated_fare']),
        'actual_fare' => $booking['actual_fare'] ? floatval($booking['actual_fare']) : null,
        'driver' => $driverDetails,
        'otp' => $booking['otp'] ?? null
    ];
    echo "<h3>Status API JSON Response:</h3><pre>";
    echo json_encode($response, JSON_PRETTY_PRINT);
    echo "</pre>";

} catch (Exception $e) {
    echo "<h1>Error: " . $e->getMessage() . "</h1>";
    echo "<pre>";
    print_r($e->getTraceAsString());
    echo "</pre>";
}
