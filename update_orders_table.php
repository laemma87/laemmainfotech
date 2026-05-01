<?php
include 'includes/db.php';

try {
    // Add price column if it doesn't exist
    $pdo->exec("ALTER TABLE orders ADD COLUMN IF NOT EXISTS price DECIMAL(10, 2) AFTER address");
    echo "Successfully updated orders table schema.\n";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
?>
