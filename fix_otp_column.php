<?php
// Script to add otp column to bookings table
require_once __DIR__ . '/config/database.php';

try {
    $dbObj = Database::getInstance();
    $db = $dbObj->getConnection();

    // Check if otp column exists
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('otp', $columns)) {
        echo "Adding otp column to bookings table...<br>";
        $db->exec("ALTER TABLE bookings ADD COLUMN otp VARCHAR(4) DEFAULT NULL AFTER actual_fare");
        echo "✅ otp column added successfully!<br><br>";
    } else {
        echo "✅ otp column already exists!<br><br>";
    }

    // Now let's also check if existing bookings have otp, and generate one if missing
    echo "Checking existing bookings for otp...<br>";
    $stmt = $db->query("SELECT id FROM bookings WHERE otp IS NULL");
    $bookings = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (count($bookings) > 0) {
        foreach ($bookings as $bookingId) {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $updateStmt = $db->prepare("UPDATE bookings SET otp = ? WHERE id = ?");
            $updateStmt->execute([$otp, $bookingId]);
            echo "✅ Updated booking ID $bookingId with OTP: $otp<br>";
        }
    } else {
        echo "✅ All existing bookings have otp!<br><br>";
    }

    echo "<h3>All done! Now your app should work correctly!</h3>";
    echo "<a href='index.php'>Go to homepage</a>";
} catch (Exception $e) {
    echo "<h1>Error: " . $e->getMessage() . "</h1>";
}
