CREATE DATABASE IF NOT EXISTS laemmainfotech;
USE laemmainfotech;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'default.png',
    role ENUM('student', 'admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount INT DEFAULT 0,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    price DECIMAL(10, 2),
    order_status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS internships (
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Default Admin
INSERT INTO users (name, email, password, role) 
VALUES ('Emmanuel HAKIZIMANA', 'laemma50@gmail.com', '$2y$10$7I1D6D7J6D7J6D7J6D7J6euVj5Z7R5z5z5z5z5z5z5z5z5z5z5z', 'admin');
-- Note: Password in hash for 'laemma@123' should be generated properly. 
-- For the sake of this prototype, I'll use a simple password_hash in PHP later.
