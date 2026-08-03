# Smart Cab Booking System

## Project Overview

This project is a web-based cab booking system developed using PHP, MySQL, HTML, CSS, JavaScript, Bootstrap, and jQuery AJAX. The system is designed to provide an easy and efficient platform for users to book cabs, drivers to manage rides, and administrators to monitor operations.

The application is suitable for academic projects, final-year submissions, and practical demonstrations because it simulates real-world cab booking workflow in a simple and effective way.

## Objective

The main objective of this project is to automate the traditional taxi booking process and reduce manual effort. It aims to:

- simplify cab booking for customers
- help drivers manage trip requests
- allow admins to monitor bookings and system activity
- provide a digital and organized booking experience

## Problem Statement

In the traditional cab booking system, the process is usually manual and inefficient. Customers often face delays, drivers have difficulty handling requests, and administrators struggle to track bookings and records. This leads to poor communication, slow service, and less customer satisfaction.

This project solves these problems by providing a web-based solution that automates booking, tracking, payment simulation, and administration.

## Key Features

- User registration and login
- Driver registration and approval system
- Cab booking request creation
- Driver acceptance and ride status updates
- Live-style ride tracking simulation
- Fare calculation based on distance and vehicle type
- Payment simulation using different methods
- Driver rating and review system
- Notifications for users and drivers
- Admin dashboard for monitoring bookings and system data

## Technology Stack

- Frontend: HTML, CSS, JavaScript, Bootstrap, jQuery
- Backend: PHP
- Database: MySQL
- Architecture: MVC-style structure with separate controllers, models, and views

## Project Structure

- controllers/ - Handles user requests and business logic
- models/ - Contains database and data handling classes
- views/ - Contains all UI pages for users, drivers, and admin
- config/ - Database configuration file
- assets/ - CSS and JavaScript assets
- schema.sql - Database schema and seed data

## Database Overview

The system uses a relational database with tables for:

- users
- drivers
- vehicles
- bookings
- booking_requests
- payments
- reviews
- notifications
- earnings
- admin

## Installation Instructions

1. Place the project folder in your local server directory.
   - XAMPP: C:\xampp\htdocs\CabBooking
   - WAMP: C:\wamp64\www\CabBooking
2. Start Apache and MySQL from your server control panel.
3. Open phpMyAdmin and create a database named smart_cab_db.
4. Import the file schema.sql into the database.
5. Open the project in your browser:
   http://localhost/CabBooking/

## Database Configuration

If needed, update the database credentials in config/database.php:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Demo Login Credentials

The project includes sample records for testing:

| Role     | Email / Username    | Password |
| -------- | ------------------- | -------- |
| Admin    | admin               | admin123 |
| User     | user@example.com    | password |
| Driver 1 | driver1@example.com | password |
| Driver 2 | driver2@example.com | password |
| Driver 3 | driver3@example.com | password |

## How the System Works

1. A user logs in and requests a ride.
2. The system searches for available drivers.
3. A driver receives the booking request and can accept it.
4. The ride status changes from pending to accepted, ongoing, and completed.
5. The user completes payment and can rate the driver.

## Future Scope

- Mobile app integration
- Live GPS tracking with real map API
- Online payment gateway integration
- SMS and email notifications
- Multi-city support
- Advanced admin analytics

## Conclusion

This project demonstrates how a web-based cab booking system can improve transportation services by making booking faster, more transparent, and more efficient. It provides a complete digital solution for users, drivers, and administrators.

## Submitted By

[Your Name]
[Your College Name]
[Course / Department]
