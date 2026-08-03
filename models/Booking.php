<?php
// Smart Cab Booking System - Booking Model
// Location: models/Booking.php

class Booking {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 1. Get current fare rates
    public function getFareRates() {
        $stmt = $this->db->query("SELECT * FROM fare_rates");
        return $stmt->fetchAll();
    }

    public function getFareRateByType($vehicle_type) {
        $stmt = $this->db->prepare("SELECT * FROM fare_rates WHERE vehicle_type = ? LIMIT 1");
        $stmt->execute([$vehicle_type]);
        return $stmt->fetch();
    }

    public function updateFareRate($vehicle_type, $base_fare, $per_km_rate) {
        $stmt = $this->db->prepare("UPDATE fare_rates SET base_fare = ?, per_km_rate = ? WHERE vehicle_type = ?");
        return $stmt->execute([$base_fare, $per_km_rate, $vehicle_type]);
    }

    // 2. Create booking and broadcast to available drivers
    public function create($user_id, $pickup_loc, $drop_loc, $pickup_lat, $pickup_lng, $drop_lat, $drop_lng, $vehicle_type, $distance, $estimated_fare) {
        try {
            $this->db->beginTransaction();

            // Generate OTP
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Insert booking
            $stmt = $this->db->prepare("
                INSERT INTO bookings (user_id, pickup_location, drop_location, pickup_lat, pickup_lng, drop_lat, drop_lng, vehicle_type, distance, estimated_fare, status, payment_status, otp) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?)
            ");
            $stmt->execute([$user_id, $pickup_loc, $drop_loc, $pickup_lat, $pickup_lng, $drop_lat, $drop_lng, $vehicle_type, $distance, $estimated_fare, $otp]);
            $booking_id = $this->db->lastInsertId();

            // Find all available online drivers of this vehicle type
            $driverStmt = $this->db->prepare("
                SELECT d.id 
                FROM drivers d 
                JOIN vehicles v ON d.id = v.driver_id 
                WHERE d.status = 'approved' AND d.is_online = 1 AND v.type = ?
            ");
            $driverStmt->execute([$vehicle_type]);
            $drivers = $driverStmt->fetchAll();

            // Broadcast request to all matching drivers
            if (!empty($drivers)) {
                $reqStmt = $this->db->prepare("INSERT INTO booking_requests (booking_id, driver_id, status) VALUES (?, ?, 'pending')");
                foreach ($drivers as $driver) {
                    $reqStmt->execute([$booking_id, $driver['id']]);
                    
                    // Create notification for driver
                    create_notification($this->db, "New Ride Request", "You have a new ride request from {$pickup_loc} to {$drop_loc}.", "driver", $driver['id']);
                }
            }

            // Create notification for user
            create_notification($this->db, "Booking Initiated", "We are searching for available cabs nearby.", "user", $user_id);

            $this->db->commit();
            return $booking_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 3. Find active booking for user (where trip isn't finished or cancelled)
    public function getActiveForUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT b.*, d.name as driver_name, d.phone as driver_phone, d.latitude as driver_lat, d.longitude as driver_lng, v.model, v.plate_number, v.color
            FROM bookings b
            LEFT JOIN drivers d ON b.driver_id = d.id
            LEFT JOIN vehicles v ON d.id = v.driver_id
            WHERE b.user_id = ? AND b.status IN ('pending', 'accepted', 'ongoing')
            ORDER BY b.created_at DESC LIMIT 1
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    // 4. Find active booking for driver
    public function getActiveForDriver($driver_id) {
        $stmt = $this->db->prepare("
            SELECT b.*, u.name as user_name, u.phone as user_phone
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            WHERE b.driver_id = ? AND b.status IN ('accepted', 'ongoing')
            ORDER BY b.created_at DESC LIMIT 1
        ");
        $stmt->execute([$driver_id]);
        return $stmt->fetch();
    }

    // 5. Retrieve pending requests for driver
    public function getRequestsForDriver($driver_id) {
        $stmt = $this->db->prepare("
            SELECT br.id as request_id, br.status as request_status, b.*, u.name as user_name, u.phone as user_phone
            FROM booking_requests br
            JOIN bookings b ON br.booking_id = b.id
            JOIN users u ON b.user_id = u.id
            WHERE br.driver_id = ? AND br.status = 'pending' AND b.status = 'pending'
            ORDER BY br.created_at DESC
        ");
        $stmt->execute([$driver_id]);
        return $stmt->fetchAll();
    }

    // 6. Accept booking request (Safe Transaction: first driver wins)
    public function acceptRequest($booking_id, $driver_id) {
        try {
            $this->db->beginTransaction();

            // Select booking and lock for updates
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ? FOR UPDATE");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch();

            if (!$booking || $booking['status'] !== 'pending') {
                // Booking already taken or cancelled
                // Mark this driver's request as expired
                $expStmt = $this->db->prepare("UPDATE booking_requests SET status = 'expired' WHERE booking_id = ? AND driver_id = ?");
                $expStmt->execute([$booking_id, $driver_id]);
                $this->db->commit();
                return false;
            }

            // Update booking to accepted and assign driver
            $updBooking = $this->db->prepare("UPDATE bookings SET driver_id = ?, status = 'accepted' WHERE id = ?");
            $updBooking->execute([$driver_id, $booking_id]);

            // Update current driver's request to accepted
            $updReq = $this->db->prepare("UPDATE booking_requests SET status = 'accepted' WHERE booking_id = ? AND driver_id = ?");
            $updReq->execute([$booking_id, $driver_id]);

            // Expire all other requests for this booking
            $expOthers = $this->db->prepare("UPDATE booking_requests SET status = 'expired' WHERE booking_id = ? AND driver_id != ?");
            $expOthers->execute([$booking_id, $driver_id]);

            // Notify user
            create_notification($this->db, "Driver Assigned", "A driver has accepted your booking and is on the way.", "user", $booking['user_id']);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 7. Reject booking request
    public function rejectRequest($booking_id, $driver_id) {
        $stmt = $this->db->prepare("UPDATE booking_requests SET status = 'rejected' WHERE booking_id = ? AND driver_id = ?");
        return $stmt->execute([$booking_id, $driver_id]);
    }

    // 8. Verify OTP
    public function verifyOtp($booking_id, $otp) {
        $stmt = $this->db->prepare("SELECT otp FROM bookings WHERE id = ? LIMIT 1");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        return $booking && $booking['otp'] === $otp;
    }

    // 9. Start Ride
    public function startRide($booking_id) {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'ongoing' WHERE id = ?");
        $res = $stmt->execute([$booking_id]);
        if ($res) {
            // Get user_id to send notification
            $booking = $this->findById($booking_id);
            create_notification($this->db, "Ride Started", "Your ride has started. Have a safe journey!", "user", $booking['user_id']);
        }
        return $res;
    }

    // 9. Complete Ride & Calculate Earnings / Payment
    public function completeRide($booking_id, $actual_fare, $payment_method = 'cash') {
        try {
            $this->db->beginTransaction();

            $booking = $this->findById($booking_id);
            if (!$booking) {
                throw new Exception("Booking not found");
            }

            // Update booking details
            $stmt = $this->db->prepare("
                UPDATE bookings 
                SET status = 'completed', actual_fare = ?, payment_status = ?, payment_method = ? 
                WHERE id = ?
            ");
            // If payment method is cash, it can stay pending until paid, but requirements say "Mark payment as Successful automatically"
            // So we will set payment_status as 'paid' upon completion or direct to payment page.
            // Let's set payment_status = 'pending' first, and then payment page handles marking it 'paid' and creating payments / earnings logs.
            $stmt->execute([$actual_fare, 'pending', $payment_method, $booking_id]);

            // Create notification for user to pay
            create_notification($this->db, "Ride Completed", "Your ride has completed. Please complete your payment of ₹" . $actual_fare, "user", $booking['user_id']);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 10. Process Payment & Distribute Earnings
    public function processPayment($booking_id, $payment_method, $transaction_id) {
        try {
            $this->db->beginTransaction();

            // Fetch booking
            $booking = $this->findById($booking_id);
            if (!$booking || $booking['payment_status'] === 'paid') {
                return false;
            }

            $amount = $booking['actual_fare'] ?? $booking['estimated_fare'];

            // Create payment log
            $payStmt = $this->db->prepare("
                INSERT INTO payments (booking_id, user_id, amount, payment_method, transaction_id, status) 
                VALUES (?, ?, ?, ?, ?, 'successful')
            ");
            $payStmt->execute([$booking_id, $booking['user_id'], $amount, $payment_method, $transaction_id]);

            // Update booking status
            $updStmt = $this->db->prepare("UPDATE bookings SET payment_status = 'paid', payment_method = ? WHERE id = ?");
            $updStmt->execute([$payment_method, $booking_id]);

            // Calculate earnings (15% platform commission, 85% driver earnings)
            if ($booking['driver_id']) {
                $commission = $amount * 0.15;
                $net_amount = $amount - $commission;
                
                $earnStmt = $this->db->prepare("
                    INSERT INTO earnings (driver_id, booking_id, amount, commission_deducted, net_amount) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $earnStmt->execute([$booking['driver_id'], $booking_id, $amount, $commission, $net_amount]);

                // Notify driver
                create_notification($this->db, "Payment Received", "Earnings of ₹" . number_format($net_amount, 2) . " credited to your wallet for Booking #" . $booking_id, "driver", $booking['driver_id']);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 11. Cancel Booking
    public function cancel($booking_id, $role) {
        try {
            $this->db->beginTransaction();

            $booking = $this->findById($booking_id);
            if (!$booking || in_array($booking['status'], ['completed', 'cancelled'])) {
                return false;
            }

            // Update status
            $stmt = $this->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$booking_id]);

            // Mark any pending booking requests as expired
            $reqStmt = $this->db->prepare("UPDATE booking_requests SET status = 'expired' WHERE booking_id = ? AND status = 'pending'");
            $reqStmt->execute([$booking_id]);

            // Send notification to other party
            if ($role === 'user' && $booking['driver_id']) {
                create_notification($this->db, "Ride Cancelled", "Customer has cancelled the ride.", "driver", $booking['driver_id']);
            } elseif ($role === 'driver') {
                create_notification($this->db, "Ride Cancelled", "Driver has cancelled the ride.", "user", $booking['user_id']);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // 12. Rating and Reviews
    public function addReview($booking_id, $user_id, $driver_id, $rating, $review_text) {
        $stmt = $this->db->prepare("INSERT INTO reviews (booking_id, user_id, driver_id, rating, review_text) VALUES (?, ?, ?, ?, ?)");
        $res = $stmt->execute([$booking_id, $user_id, $driver_id, $rating, $review_text]);
        if ($res) {
            // Notify driver
            create_notification($this->db, "New Rating Received", "A user rated you " . $rating . " stars.", "driver", $driver_id);
        }
        return $res;
    }

    // Helpers
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getHistoryForUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT b.*, d.name as driver_name, r.rating, r.review_text
            FROM bookings b
            LEFT JOIN drivers d ON b.driver_id = d.id
            LEFT JOIN reviews r ON b.id = r.booking_id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getHistoryForDriver($driver_id) {
        $stmt = $this->db->prepare("
            SELECT b.*, u.name as user_name, r.rating
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            LEFT JOIN reviews r ON b.id = r.booking_id
            WHERE b.driver_id = ?
            ORDER BY b.created_at DESC
        ");
        $stmt->execute([$driver_id]);
        return $stmt->fetchAll();
    }

    public function getAllBookings() {
        $stmt = $this->db->prepare("
            SELECT b.*, u.name as user_name, d.name as driver_name 
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            LEFT JOIN drivers d ON b.driver_id = d.id
            ORDER BY b.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings");
        return $stmt->fetch()['total'];
    }

    public function countCompleted() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'completed'");
        return $stmt->fetch()['total'];
    }

    public function countCancelled() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'cancelled'");
        return $stmt->fetch()['total'];
    }
}
