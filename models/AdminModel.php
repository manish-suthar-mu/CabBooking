<?php
// Smart Cab Booking System - Admin Model
// Location: models/AdminModel.php

class AdminModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }

    public function getDashboardStats() {
        // Collect statistics
        $stats = [];

        // 1. Total Users
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $stats['total_users'] = $stmt->fetch()['count'];

        // 2. Total Drivers
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM drivers");
        $stats['total_drivers'] = $stmt->fetch()['count'];

        // 3. Active Drivers (Online & Approved)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'approved' AND is_online = 1");
        $stats['active_drivers'] = $stmt->fetch()['count'];

        // 4. Total Bookings
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings");
        $stats['total_bookings'] = $stmt->fetch()['count'];

        // 5. Completed Rides
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'");
        $stats['completed_rides'] = $stmt->fetch()['count'];

        // 6. Cancelled Rides
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'");
        $stats['cancelled_rides'] = $stmt->fetch()['count'];

        // 7. Total Earnings & Commission
        $stmt = $this->db->query("SELECT SUM(amount) as revenue, SUM(commission_deducted) as commission FROM earnings");
        $earningsData = $stmt->fetch();
        $stats['total_revenue'] = $earningsData['revenue'] ?? 0.00;
        $stats['total_earnings'] = $earningsData['commission'] ?? 0.00; // Platform earnings

        return $stats;
    }

    public function getEarningsReport() {
        $stmt = $this->db->prepare("
            SELECT e.*, d.name as driver_name, b.pickup_location, b.drop_location, b.distance 
            FROM earnings e
            JOIN drivers d ON e.driver_id = d.id
            JOIN bookings b ON e.booking_id = b.id
            ORDER BY e.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllReviews() {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name as user_name, d.name as driver_name, b.pickup_location, b.drop_location
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            JOIN drivers d ON r.driver_id = d.id
            JOIN bookings b ON r.booking_id = b.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
