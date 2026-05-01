<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Restoring LAEMMA INFO TECH Database...</h1>";
echo "<pre>";

try {
    // 1. Connect and create database 'laemmainfotech' if it doesn't exist
    include 'includes/db.php';
    echo "[OK] Connected to MySQL and created database.\n";
    
    // 2. Read and run laemma.sql (base schema)
    if (file_exists('laemma.sql')) {
        $sql = file_get_contents('laemma.sql');
        $pdo->exec($sql);
        echo "[OK] Imported base schema from laemma.sql.\n";
    }

    // 3. Read and run database_update.sql (new features: blogs, partners, etc.)
    if (file_exists('database_update.sql')) {
        $sql = file_get_contents('database_update.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    echo "[Warning] " . $e->getMessage() . "\n";
                }
            }
        }
        echo "[OK] Applied database_update.sql updates.\n";
    }

    // 4. Create missing tables properly (the additional missing ones)
    if (file_exists('create_missing_tables.sql')) {
        $sql = file_get_contents('create_missing_tables.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore already exists
                }
            }
        }
        echo "[OK] Applied create_missing_tables.sql updates.\n";
    }

    // 5. Fix partners table
    if (file_exists('fix_partners_table.sql')) {
        $sql = file_get_contents('fix_partners_table.sql');
        try {
            $pdo->exec($sql);
            echo "[OK] Applied fix_partners_table.sql updates.\n";
        } catch (PDOException $e) {
            // Ignore if column already exists
        }
    }

    // 6. Ensure default admin exists
    $admin_email = 'laemma50@gmail.com';
    $admin_pass = password_hash('laemma@123', PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkAdmin->execute([$admin_email]);
    if (!$checkAdmin->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Emmanuel HAKIZIMANA', $admin_email, $admin_pass]);
        echo "[OK] Default Admin account (laemma50@gmail.com) created.\n";
    } else {
        echo "[OK] Admin account already exists.\n";
    }
    
    // 7. sample products
    $checkProd = $pdo->query("SELECT id FROM products LIMIT 1");
    if (!$checkProd->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['MacBook Pro 2022', 'Computers', 'Apple M2 chip, 8GB RAM, 256GB SSD.', 1450000, 5, 'macbook.jpg']);
        $stmt->execute(['HP EliteBook', 'Computers', 'Intel Core i7, 16GB RAM, 512GB SSD.', 850000, 10, 'hp.jpg']);
        echo "[OK] Sample products added.\n";
    }

    echo "\n\n🎉 SUCCESS! Your entire database has been fully restored.";
    echo "\n<a href='index.php'>Go to Homepage</a>";
    
} catch (PDOException $e) {
    echo "\n[ERROR] Database error: " . $e->getMessage();
}

echo "</pre>";
?>
