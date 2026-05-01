<?php
include 'includes/db.php';

try {
    echo "Partners Table Columns:\n";
    $stmt = $pdo->query("DESCRIBE partners");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($columns);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
