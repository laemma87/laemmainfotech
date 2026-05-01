CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    website_url VARCHAR(255) NOT NULL,
    logo VARCHAR(255),
    description TEXT,
    status ENUM('Online', 'Maintenance', 'Development') DEFAULT 'Online',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    interest VARCHAR(100),
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50)
);

INSERT INTO social_media (platform, url, icon) VALUES 
('facebook', '#', 'fab fa-facebook'),
('twitter', '#', 'fab fa-twitter'),
('instagram', '#', 'fab fa-instagram'),
('linkedin', '#', 'fab fa-linkedin');
