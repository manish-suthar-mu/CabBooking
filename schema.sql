-- Smart Cab Booking System Database Schema
-- Project Name: Smart Cab Booking System

CREATE DATABASE IF NOT EXISTS `smart_cab_db`;
USE `smart_cab_db`;

-- 1. Admin Table
CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `status` ENUM('active', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Drivers Table
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `license_no` VARCHAR(50) NOT NULL UNIQUE,
  `status` ENUM('pending_approval', 'approved', 'suspended') DEFAULT 'pending_approval',
  `is_online` TINYINT(1) DEFAULT 0,
  `latitude` DECIMAL(10, 8) DEFAULT 28.613939,
  `longitude` DECIMAL(11, 8) DEFAULT 77.209021,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Vehicles Table
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `driver_id` INT NOT NULL,
  `type` ENUM('bike', 'auto', 'car') NOT NULL,
  `model` VARCHAR(100) NOT NULL,
  `plate_number` VARCHAR(50) NOT NULL UNIQUE,
  `color` VARCHAR(30) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Fare Rates Table
CREATE TABLE IF NOT EXISTS `fare_rates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vehicle_type` ENUM('bike', 'auto', 'car') NOT NULL UNIQUE,
  `base_fare` DECIMAL(10, 2) NOT NULL,
  `per_km_rate` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `driver_id` INT DEFAULT NULL,
  `pickup_location` VARCHAR(255) NOT NULL,
  `drop_location` VARCHAR(255) NOT NULL,
  `pickup_lat` DECIMAL(10, 8) NOT NULL,
  `pickup_lng` DECIMAL(11, 8) NOT NULL,
  `drop_lat` DECIMAL(10, 8) NOT NULL,
  `drop_lng` DECIMAL(11, 8) NOT NULL,
  `vehicle_type` ENUM('bike', 'auto', 'car') NOT NULL,
  `distance` DECIMAL(8, 2) NOT NULL,
  `estimated_fare` DECIMAL(10, 2) NOT NULL,
  `actual_fare` DECIMAL(10, 2) DEFAULT NULL,
  `status` ENUM('pending', 'accepted', 'ongoing', 'completed', 'cancelled') DEFAULT 'pending',
  `payment_status` ENUM('pending', 'paid') DEFAULT 'pending',
  `payment_method` ENUM('cash', 'upi', 'card') DEFAULT 'cash',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add OTP column to bookings table (MySQL compatible)
SET @dbname = DATABASE();
SET @tablename = 'bookings';
SET @columnname = 'otp';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_schema = @dbname)
      AND (table_name = @tablename)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` VARCHAR(4) DEFAULT NULL AFTER `actual_fare`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 7. Booking Requests Table (Broadcast to drivers)
CREATE TABLE IF NOT EXISTS `booking_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `driver_id` INT NOT NULL,
  `status` ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Payments Table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` ENUM('cash', 'upi', 'card') NOT NULL,
  `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
  `status` ENUM('successful', 'failed') DEFAULT 'successful',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `driver_id` INT NOT NULL,
  `rating` INT NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `review_text` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Notifications Table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `driver_id` INT DEFAULT NULL,
  `admin_id` INT DEFAULT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `type` ENUM('user', 'driver', 'admin') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Earnings Table
CREATE TABLE IF NOT EXISTS `earnings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `driver_id` INT NOT NULL,
  `booking_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `commission_deducted` DECIMAL(10, 2) NOT NULL,
  `net_amount` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- SEED DATA FOR DEMONSTRATION
-- ==========================================

-- Insert Default Admin (Password: admin123)
INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'admin@smartcab.com', '$2y$10$fiQfufcrekZrWGbEb4BjoOijMV6s/DhuAaOLD.P/mmrmb5UaJVWTe');

-- Insert Default User (Password: password)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `status`) VALUES
(1, 'John Doe', 'user@example.com', '$2y$10$PdMzwt.mZtIAHt1SEnJrCezXep8CNDinHs.4XIta0/GZHqAZw5peK', '9876543210', 'active');

-- Insert Sample Drivers (Password: password)
INSERT INTO `drivers` (`id`, `name`, `email`, `password`, `phone`, `license_no`, `status`, `is_online`, `latitude`, `longitude`) VALUES
(1, 'Robert Smith (Car)', 'driver1@example.com', '$2y$10$PdMzwt.mZtIAHt1SEnJrCezXep8CNDinHs.4XIta0/GZHqAZw5peK', '8765432109', 'DL-1420230009876', 'approved', 1, 28.613939, 77.209021),
(2, 'Sarah Jenkins (Auto)', 'driver2@example.com', '$2y$10$PdMzwt.mZtIAHt1SEnJrCezXep8CNDinHs.4XIta0/GZHqAZw5peK', '7654321098', 'DL-1420230009877', 'approved', 0, 28.625000, 77.210000),
(3, 'Michael Johnson (Bike)', 'driver3@example.com', '$2y$10$PdMzwt.mZtIAHt1SEnJrCezXep8CNDinHs.4XIta0/GZHqAZw5peK', '6543210987', 'DL-1420230009878', 'pending_approval', 0, 28.630000, 77.220000);

-- Insert Sample Vehicles for Drivers
INSERT INTO `vehicles` (`id`, `driver_id`, `type`, `model`, `plate_number`, `color`) VALUES
(1, 1, 'car', 'Hyundai i20', 'DL-3C-CA-1111', 'White'),
(2, 2, 'auto', 'Bajaj RE', 'DL-1R-AA-2222', 'Yellow-Green'),
(3, 3, 'bike', 'Honda Unicorn', 'DL-5S-BB-3333', 'Black');

-- Insert Fare Rates Configuration
INSERT INTO `fare_rates` (`vehicle_type`, `base_fare`, `per_km_rate`) VALUES
('bike', 15.00, 5.00),
('auto', 25.00, 8.00),
('car', 50.00, 12.00);
