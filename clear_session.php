<?php
// clear_session.php
// Visit this page to clear all session cookies and invalidate OPcache memory.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

// Reset PHP OPcache if enabled on Laragon/XAMPP
if (function_exists('opcache_reset')) {
    opcache_reset();
}

session_start();
$_SESSION['success'] = "Session and OPcache cleared successfully! You are logged out.";
header("Location: index.php?controller=auth&action=login");
exit;
