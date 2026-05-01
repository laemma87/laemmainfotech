<?php
include 'includes/db.php';
try {
    $pdo->query("SELECT 1 FROM partners LIMIT 1");
    echo "Partners table ok\n";
    $pdo->query("SELECT 1 FROM messages LIMIT 1");
    echo "Messages table ok\n";
    $pdo->query("SELECT 1 FROM social_media LIMIT 1");
    echo "Social Media table ok\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
