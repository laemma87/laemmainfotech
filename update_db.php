<?php
// Script to execute the database update SQL
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting database update...\n";

try {
    include 'includes/db.php';
    
    // Read the SQL file
    $sqlFile = 'database_update.sql';
    if (!file_exists($sqlFile)) {
        die("Error: SQL file '$sqlFile' not found.\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split into individual statements (simple split by ;)
    // This is a basic split and might fail if ; is inside quotes, but fine for this script
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Ignore "Column already exists" or "Table already exists" if handled effectively by IF NOT EXISTS
                // But PDO throws error even with IF NOT EXISTS sometimes depending on driver or syntax issues
                echo "Warning executing: " . substr($statement, 0, 50) . "...\n";
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nDatabase update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>
