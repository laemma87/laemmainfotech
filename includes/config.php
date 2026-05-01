<?php
/**
 * Configuration file for LAEMMA INFO TECH
 */

// Deployment Mode: 'DEVELOPMENT' or 'PRODUCTION'
// Automatically switch to 'PRODUCTION' when hosted on Railway
$is_production = getenv('MYSQLHOST') ? 'PRODUCTION' : 'DEVELOPMENT';
define('APP_MODE', $is_production);

// Base URL configuration
$protocol = "http://";
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    $protocol = "https://";
} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = "https://";
}

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
