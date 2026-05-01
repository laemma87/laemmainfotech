<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'laemmainfotech';

try {
    // Connect to MySQL first without a database
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("<div style='padding: 20px; background: #ff4757; color: white; font-family: sans-serif; border-radius: 10px; margin: 20px;'>
            <strong>Connection Error:</strong> " . $e->getMessage() . "
            <br><br>
            Please make sure XAMPP (Apache and MySQL) is running.
         </div>");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
