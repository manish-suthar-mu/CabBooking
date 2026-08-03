<?php
// Smart Cab Booking System - Notification Model
// Location: models/Notification.php

class Notification {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getForUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getForDriver($driver_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE driver_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$driver_id]);
        return $stmt->fetchAll();
    }

    public function getForAdmin($admin_id) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE admin_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$admin_id]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function markAllAsRead($role, $id) {
        if ($role === 'user') {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        } elseif ($role === 'driver') {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE driver_id = ?");
        } else {
            $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE admin_id = ?");
        }
        return $stmt->execute([$id]);
    }

    public function getUnreadCount($role, $id) {
        if ($role === 'user') {
            $stmt = $this->db->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0");
        } elseif ($role === 'driver') {
            $stmt = $this->db->prepare("SELECT COUNT(*) as unread FROM notifications WHERE driver_id = ? AND is_read = 0");
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as unread FROM notifications WHERE admin_id = ? AND is_read = 0");
        }
        $stmt->execute([$id]);
        return $stmt->fetch()['unread'];
    }
}
