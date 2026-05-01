-- Database Update for New Features
-- Run this script to add tables and columns for Users, Blogs, Chat, etc.

-- ============================================
-- 1. USERS TABLE UPDATES
-- ============================================

-- Add status and verification fields
ALTER TABLE users
ADD COLUMN IF NOT EXISTS status ENUM('active', 'banned') DEFAULT 'active' AFTER role,
ADD COLUMN IF NOT EXISTS email_verified TINYINT(1) DEFAULT 0 AFTER status,
ADD COLUMN IF NOT EXISTS verification_token VARCHAR(255) NULL AFTER email_verified,
ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL AFTER verification_token,
ADD COLUMN IF NOT EXISTS reset_token_expiry DATETIME NULL AFTER reset_token,
ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) NULL AFTER reset_token_expiry;

-- ============================================
-- 2. BLOG SYSTEM TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    category_id INT NULL,
    image VARCHAR(255) NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

-- Insert default categories
INSERT IGNORE INTO blog_categories (name, slug) VALUES 
('Partner Updates', 'partner-updates'),
('System Status', 'system-status'),
('News & Articles', 'news-articles'),
('Tutorials', 'tutorials');

-- ============================================
-- 3. PARTNERS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255) NULL,
    website_url VARCHAR(255) NULL,
    status ENUM('Online', 'Maintenance', 'Under Development') DEFAULT 'Online',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 4. CHAT SYSTEM TABLES
-- ============================================

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL, -- 0 or 1 for admin? Better to use actual user IDs. Admin usually has specific ID.
    message TEXT NOT NULL,
    attachment VARCHAR(255) NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- 5. SOCIAL MEDIA SETTINGS
-- ============================================

CREATE TABLE IF NOT EXISTS social_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    username VARCHAR(100) NULL,
    status ENUM('Visible', 'Hidden') DEFAULT 'Visible',
    icon VARCHAR(50) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default social media links
INSERT IGNORE INTO social_media (platform, url, username, status) VALUES 
('YouTube', 'https://youtube.com/@laemmainfo', '@laemmainfo', 'Visible'),
('Facebook', 'https://facebook.com/laemma250', 'laemma250', 'Visible'),
('Instagram', 'https://instagram.com/laemma87', '@laemma87', 'Visible'),
('TikTok', 'https://tiktok.com/@laemmainfo', '@laemmainfo', 'Visible'),
('Twitter', 'https://x.com/laemma87', '@laemma87', 'Visible');

