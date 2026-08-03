<?php
// Smart Cab Booking System - Driver Model
// Location: models/Driver.php

class Driver {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($name, $email, $password, $phone, $license_no) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO drivers (name, email, password, phone, license_no, status, is_online) VALUES (?, ?, ?, ?, ?, 'pending_approval', 0)");
        return $stmt->execute([$name, $email, $hashedPassword, $phone, $license_no]);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM drivers WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM drivers WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile($id, $name, $phone) {
        $stmt = $this->db->prepare("UPDATE drivers SET name = ?, phone = ? WHERE id = ?");
        return $stmt->execute([$name, $phone, $id]);
    }

    public function updatePassword($id, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE drivers SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }

    public function updateOnlineStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE drivers SET is_online = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function updateLocation($id, $lat, $lng) {
        $stmt = $this->db->prepare("UPDATE drivers SET latitude = ?, longitude = ? WHERE id = ?");
        return $stmt->execute([$lat, $lng, $id]);
    }

    public function getAvailableDrivers($vehicle_type) {
        // Return drivers who are online, approved, and have a vehicle of the specified type
        $stmt = $this->db->prepare("
            SELECT d.*, v.type as vehicle_type, v.model, v.plate_number 
            FROM drivers d 
            JOIN vehicles v ON d.id = v.driver_id 
            WHERE d.status = 'approved' AND d.is_online = 1 AND v.type = ?
        ");
        $stmt->execute([$vehicle_type]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        // Fetch all drivers with their vehicle details if any
        $stmt = $this->db->prepare("
            SELECT d.*, v.type as vehicle_type, v.model, v.plate_number, v.color 
            FROM drivers d 
            LEFT JOIN vehicles v ON d.id = v.driver_id 
            ORDER BY d.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE drivers SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM drivers");
        return $stmt->fetch()['total'];
    }

    public function countActive() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM drivers WHERE status = 'approved' AND is_online = 1");
        return $stmt->fetch()['total'];
    }
}
