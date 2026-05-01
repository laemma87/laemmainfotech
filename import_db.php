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
        $sql = file_get_contents($file);
        
        // Strip out CREATE DATABASE and USE statements (Railway handles database creation)
        $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
        $sql = preg_replace('/USE [A-Za-z0-9_]+;/i', '', $sql);

        // Execute all queries in the file
        try {
            $pdo->exec($sql);
            echo "&nbsp;&nbsp;✅ Finished running <i>{$file}</i> successfully!<br/><br/>";
        } catch (PDOException $e) {
            echo "&nbsp;&nbsp;⚠️ Notice in <i>{$file}</i>: " . $e->getMessage() . "<br/><br/>";
        }
    } else {
        echo "⚠️ Could not find $file <br/><br/>";
    }
}

echo "<hr/><h3>Database Migration Complete! 🚀</h3>";
echo "Now go back to your <a href='index.php'>Homepage</a> to see the content.";
?>
