<?php
// Smart Cab Booking System - Vehicle Model
// Location: models/Vehicle.php

class Vehicle {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findByDriverId($driver_id) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE driver_id = ? LIMIT 1");
        $stmt->execute([$driver_id]);
        return $stmt->fetch();
    }

    public function save($driver_id, $type, $model, $plate_number, $color) {
        // Check if vehicle already exists for driver
        $existing = $this->findByDriverId($driver_id);
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE vehicles 
                SET type = ?, model = ?, plate_number = ?, color = ? 
                WHERE driver_id = ?
            ");
            return $stmt->execute([$type, $model, $plate_number, $color, $driver_id]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO vehicles (driver_id, type, model, plate_number, color) 
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$driver_id, $type, $model, $plate_number, $color]);
        }
    }

    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT v.*, d.name as driver_name 
            FROM vehicles v 
            JOIN drivers d ON v.driver_id = d.id 
            ORDER BY v.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
