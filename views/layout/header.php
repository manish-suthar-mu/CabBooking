<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . " - Smart Cab Booking System" : "Smart Cab Booking System" ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Design Skins -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Injection of user states for notification pollers -->
    <script>
        window.userId = <?= isset($_SESSION['user_id']) && $_SESSION['role'] === 'user' ? intval($_SESSION['user_id']) : 'null' ?>;
        window.driverId = <?= isset($_SESSION['driver_id']) && $_SESSION['role'] === 'driver' ? intval($_SESSION['driver_id']) : 'null' ?>;
        window.csrfToken = "<?= $_SESSION['csrf_token'] ?? '' ?>";
    </script>
</head>
<body>

    <!-- Responsive Navbar -->
    <nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-taxi-front-fill text-warning fs-3"></i>
                <span class="brand-font fs-4 text-dark">Smart Cab</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    
                    <?php if (isset($_SESSION['role'])): ?>
                        
                        <?php if ($_SESSION['role'] === 'user'): ?>
                            <!-- User Navigation Menu -->
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=user&action=dashboard"><i class="bi bi-search"></i> Book Ride</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=user&action=history"><i class="bi bi-clock-history"></i> History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium position-relative" href="index.php?controller=user&action=notifications">
                                    <i class="bi bi-bell"></i> Notifications
                                    <span id="navNotificationBadge" class="notification-badge" style="display: none;">0</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=user&action=profile"><i class="bi bi-person-circle"></i> Profile</a>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'driver'): ?>
                            <!-- Driver Navigation Menu -->
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=driver&action=dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=driver&action=vehicle"><i class="bi bi-car-front"></i> Vehicle Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=driver&action=history"><i class="bi bi-journal-text"></i> Trip History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-mediumposition-relative" href="index.php?controller=driver&action=dashboard#notif-pane">
                                    <i class="bi bi-bell"></i> Alerts
                                    <span id="navNotificationBadge" class="notification-badge" style="display: none;">0</span>
                                </a>
                            </li>
                            
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            <!-- Admin Navigation Menu -->
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=dashboard"><i class="bi bi-grid-fill"></i> Stats</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=users"><i class="bi bi-people"></i> Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=drivers"><i class="bi bi-person-badge"></i> Drivers</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=bookings"><i class="bi bi-calendar3"></i> Bookings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=fares"><i class="bi bi-currency-rupee"></i> Fare Rates</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=reviews"><i class="bi bi-star"></i> Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=admin&action=earnings"><i class="bi bi-wallet2"></i> Earnings</a>
                            </li>
                            
                        <?php endif; ?>
                        
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-danger fw-semibold" href="index.php?controller=auth&action=logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                        
                    <?php else: ?>
                        <!-- Guest Navigation Menu -->
                        <li class="nav-item">
                            <a class="nav-link text-dark px-3 fw-medium" href="index.php"><i class="bi bi-house"></i> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark px-3 fw-medium" href="index.php?controller=auth&action=login"><i class="bi bi-box-arrow-in-right"></i> Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-custom" href="index.php?controller=auth&action=register">Book Now</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-accent-custom" href="index.php?controller=auth&action=driverRegister">Drive with Us</a>
                        </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </nav>

    <!-- Session Alerts -->
    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
