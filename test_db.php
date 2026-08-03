<?php
// Test script to check DB and OTP column
require_once __DIR__ . '/config/database.php';

try {
    $dbObj = Database::getInstance();
    $db = $dbObj->getConnection();
    echo "<h1>Database Connection Successful!</h1>";

    // Check if otp column exists in bookings table
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<h2>Bookings Table Columns:</h2><ul>";
    foreach ($columns as $col) {
        echo "<li>$col" . (in_array('otp', $columns) && $col === 'otp' ? " <strong>(✓ FOUND!)</strong>" : "") . "</li>";
    }
    echo "</ul>";

    if (!in_array('otp', $columns)) {
        echo "<div style='background: #ffcccc; padding: 10px; border: 1px solid #ff6666;'>";
        echo "<strong>⚠️ OTP column is missing in bookings table! Please import schema.sql or run this query:</strong><br>";
        echo "<code>ALTER TABLE bookings ADD COLUMN otp VARCHAR(4) DEFAULT NULL AFTER actual_fare;</code>";
        echo "</div>";
    }

    // Check sample booking (if exists)
    $bookingId = 4; // User mentioned booking_id=4
    $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h2>Sample Booking (ID=$bookingId):</h2>";
    if ($booking) {
        echo "<pre>" . print_r($booking, true) . "</pre>";
    } else {
        echo "<p>No booking found with ID=$bookingId</p>";
    }
} catch (Exception $e) {
    echo "<h1>Error: " . $e->getMessage() . "</h1>";
}
