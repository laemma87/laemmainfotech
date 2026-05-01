<?php
// Automatic Railway Database Importer
include 'includes/db.php';

echo "<h2>Starting Railway Database Immigration...</h2><hr/>";

// Sequential list of SQL files representing the whole database state
$sql_files = [
    'laemma.sql',
    'create_missing_tables.sql',
    'database_update.sql',
    'database_migration.sql',
    'fix_partners_table.sql'
];

foreach ($sql_files as $file) {
    if (file_exists($file)) {
        echo "<b>Processing {$file}...</b><br/>";
        $sql_content = file_get_contents($file);
        
        // Strip out CREATE DATABASE and USE statements
        $sql_content = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql_content);
        $sql_content = preg_replace('/USE [A-Za-z0-9_]+;/i', '', $sql_content);

        // Split by semicolon, but try to avoid splitting inside quotes or functions if possible
        // For simplicity in this context, we take the semicolon approach and filter empty
        $queries = explode(';', $sql_content);

        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;

            // Remove MariaDB-specific 'IF NOT EXISTS' from ALTER TABLE which breaks standard MySQL
            if (stripos($query, 'ALTER TABLE') !== false) {
                $query = str_ireplace('IF NOT EXISTS', '', $query);
            }

            try {
                $pdo->exec($query);
                // We don't echo success for every single tiny query to keep it clean
            } catch (PDOException $e) {
                // Ignore "Column already exists" (1060) or "Duplicate key" (1061/1062)
                $errCode = $e->errorInfo[1] ?? 0;
                if (!in_array($errCode, [1060, 1061, 1062])) {
                    echo "&nbsp;&nbsp;⚠️ Notice in query: " . $e->getMessage() . "<br/>";
                }
            }
        }
        echo "&nbsp;&nbsp;✅ Finished processing <i>{$file}</i>!<br/><br/>";
    } else {
        echo "⚠️ Could not find $file <br/><br/>";
    }
}

echo "<hr/><h3>Database Migration Complete! 🚀</h3>";
echo "Now go back to your <a href='index.php'>Homepage</a> to see the content.";
?>
