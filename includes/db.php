<?php
$host = getenv('MYSQLHOST') ?: 'localhost';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: 'laemmainfotech';
$port = getenv('MYSQLPORT') ?: 3306;

try {
    // If it's local (root), create the database if not exists
    if ($host === 'localhost') {
        $pdo_init = new PDO("mysql:host=$host;port=$port;charset=utf8", $username, $password);
        $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    }
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("<div style='padding: 20px; background: #ff4757; color: white; font-family: sans-serif; border-radius: 10px; margin: 20px;'>
            <strong>Connection Error:</strong> " . $e->getMessage() . "
            <br><br>
            Please make sure your database server is running and credentials are correct.
         </div>");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
