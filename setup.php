<?php
include 'includes/db.php';

try {
    echo "<h1>LAEMMA INFO TECH - System Setup</h1>";
    
    // Create Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        profile_pic VARCHAR(255) DEFAULT 'default.png',
        role ENUM('student', 'admin', 'user') DEFAULT 'user',
        is_banned TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Users table ready.<br>";

    // Create Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        discount INT DEFAULT 0,
        image VARCHAR(255),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Products table ready.<br>";

    // Create Orders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        name VARCHAR(255),
        phone VARCHAR(20),
        email VARCHAR(255),
        address TEXT,
        payment_method VARCHAR(50),
        payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
        order_status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Orders table ready.<br>";

    // Create Internships Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS internships (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        full_names VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(20),
        level VARCHAR(100),
        school VARCHAR(255),
        equipment VARCHAR(255),
        field VARCHAR(100),
        payment_status ENUM('pending', 'paid') DEFAULT 'pending',
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Internships table ready.<br>";

    // Create Messages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        interest VARCHAR(100),
        message TEXT,
        status ENUM('new', 'read', 'replied') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Messages table ready.<br>";

    // Create Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL
    )");
    $pdo->exec("INSERT IGNORE INTO categories (name) VALUES ('Computers'), ('Electronics'), ('Accessories'), ('Cables')");
    echo "✅ Categories table ready.<br>";

    // Setup Default Admin
    $admin_email = 'laemma50@gmail.com';
    $admin_pass = password_hash('laemma@123', PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkAdmin->execute([$admin_email]);
    if (!$checkAdmin->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Emmanuel HAKIZIMANA', $admin_email, $admin_pass]);
        echo "✅ Default Admin account created.<br>";
    } else {
        echo "ℹ️ Admin account already exists.<br>";
    }

    // Add some sample products if catalog is empty
    $checkProd = $pdo->query("SELECT id FROM products LIMIT 1");
    if (!$checkProd->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['MacBook Pro 2022', 'Computers', 'Apple M2 chip, 8GB RAM, 256GB SSD.', 1450000, 5, 'macbook.jpg']);
        $stmt->execute(['HP EliteBook', 'Computers', 'Intel Core i7, 16GB RAM, 512GB SSD.', 850000, 10, 'hp.jpg']);
        echo "✅ Sample products added.<br>";
    }

    echo "<h3>System setup complete! <a href='index.php'>Go to Website</a></h3>";

} catch (PDOException $e) {
    echo "❌ Setup failed: " . $e->getMessage();
}
?>
