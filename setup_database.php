<?php
require_once __DIR__ . '/config/database.php';

try {
    $dbObj = Database::getInstance();
    $db = $dbObj->getConnection();
    echo "<h1 style='color: #059669; text-align: center;'>Smart Cab Booking System - Database Setup</h1>";
    echo "<div style='max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif;'>";

    // Step 1: Check if otp column exists in bookings table
    $stmt = $db->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bookings' AND COLUMN_NAME = 'otp'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $otpExists = $result['count'] > 0;

    if (!$otpExists) {
        echo "<p style='background: #fef3c7; padding: 10px; border-left: 5px solid #f59e0b;'>Adding <code>otp</code> column to <code>bookings</code> table...</p>";
        $db->exec("ALTER TABLE `bookings` ADD COLUMN `otp` VARCHAR(4) DEFAULT NULL AFTER `actual_fare`");
        echo "<p style='color: #059669;'><strong>✅ Success:</strong> <code>otp</code> column added!</p>";
    } else {
        echo "<p style='color: #059669;'><strong>✅ Okay:</strong> <code>otp</code> column already exists!</p>";
    }

    // Step 2: Check if there are any bookings without otp, and populate them
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE otp IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $bookingsWithoutOtp = $result['count'];

    if ($bookingsWithoutOtp > 0) {
        echo "<p style='background: #fffbeb; padding: 10px; border-left: 5px solid #f59e0b;'>Populating OTP for $bookingsWithoutOtp existing bookings...</p>";
        $stmt = $db->query("SELECT id FROM bookings WHERE otp IS NULL");
        $bookings = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $updateStmt = $db->prepare("UPDATE bookings SET otp = ? WHERE id = ?");

        foreach ($bookings as $id) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $updateStmt->execute([$otp, $id]);
        }
        echo "<p style='color: #059669;'><strong>✅ Success:</strong> OTP populated for all bookings!</p>";
    } else {
        echo "<p style='color: #059669;'><strong>✅ Okay:</strong> All bookings have OTP!</p>";
    }

    echo "<h2 style='margin-top: 30px;'>Setup Complete!</h2>";
    echo "<p>Your database is now ready to use! You can now:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Go to the Smart Cab Booking System homepage</a></li>";
    echo "</ul>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h1 style='color: #dc2626; text-align: center;'>Error!</h1>";
    echo "<div style='max-width: 800px; margin: 0 auto; font-family: Arial, sans-serif;'>";
    echo "<p style='background: #fef2f2; padding: 10px; border: 1px solid #dc2626; color: #dc2626;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please make sure you have imported the <code>schema.sql</code> file first!</p>";
    echo "</div>";
}
