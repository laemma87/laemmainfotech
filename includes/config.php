<?php
/**
 * Configuration file for LAEMMA INFO TECH
 */

// Deployment Mode: 'DEVELOPMENT' or 'PRODUCTION'
// WARNING: Keep this as 'DEVELOPMENT' when testing on your own computer (localhost).
// Change it to 'PRODUCTION' ONLY on the copy you upload to InfinityFree.
define('APP_MODE', 'DEVELOPMENT');

// Base URL configuration
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/laemmainfotech');
} else {
    define('BASE_URL', $protocol . $_SERVER['HTTP_HOST']);
}

// Database Configuration (User should edit this for InfinityFree)
if (APP_MODE === 'DEVELOPMENT') {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'laemmainfotech');
} else {
    // InfinityFree Credentials
    define('DB_HOST', 'sql101.infinityfree.com'); // Example
    define('DB_USER', 'if0_xxxxxxxx');
    define('DB_PASS', 'your_password');
    define('DB_NAME', 'if0_xxxxxxxx_laemma');
}
?>
