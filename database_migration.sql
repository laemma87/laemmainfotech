-- Database Migration for Application Status Tracking & Mobile Payment Integration
-- Run this script to update the database schema
-- Date: 2026-02-11

USE laemmainfotech;

-- ============================================
-- INTERNSHIPS TABLE UPDATES
-- ============================================

-- Add status tracking fields
ALTER TABLE internships 
ADD COLUMN IF NOT EXISTS status_updated_at TIMESTAMP NULL DEFAULT NULL AFTER status,
ADD COLUMN IF NOT EXISTS status_notes TEXT NULL AFTER status_updated_at;

-- Add mobile payment fields
ALTER TABLE internships
ADD COLUMN IF NOT EXISTS payment_provider ENUM('MTN', 'Tigo', 'Airtel') NULL AFTER payment_status,
ADD COLUMN IF NOT EXISTS payment_phone VARCHAR(20) NULL AFTER payment_provider,
ADD COLUMN IF NOT EXISTS transaction_reference VARCHAR(100) NULL AFTER payment_phone,
ADD COLUMN IF NOT EXISTS payment_attempted_at TIMESTAMP NULL DEFAULT NULL AFTER transaction_reference;

-- Update existing status enum to include 'under_review'
ALTER TABLE internships 
MODIFY COLUMN status ENUM('pending', 'under_review', 'accepted', 'rejected') DEFAULT 'under_review';

-- ============================================
-- ORDERS TABLE UPDATES
-- ============================================

-- Add mobile payment fields
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS payment_provider ENUM('MTN', 'Tigo', 'Airtel', 'Card') NULL AFTER payment_method,
ADD COLUMN IF NOT EXISTS payment_phone VARCHAR(20) NULL AFTER payment_provider,
ADD COLUMN IF NOT EXISTS transaction_reference VARCHAR(100) NULL AFTER payment_phone,
ADD COLUMN IF NOT EXISTS payment_attempted_at TIMESTAMP NULL DEFAULT NULL AFTER transaction_reference;

-- ============================================
-- UPDATE EXISTING RECORDS
-- ============================================

-- Set status_updated_at for existing records
UPDATE internships 
SET status_updated_at = created_at 
WHERE status_updated_at IS NULL;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Verify internships table structure
SELECT 
    'Internships Table Structure' as Info;
DESCRIBE internships;

-- Verify orders table structure
SELECT 
    'Orders Table Structure' as Info;
DESCRIBE orders;

-- Show sample data
SELECT 
    'Sample Internship Records' as Info;
SELECT id, full_names, status, payment_status, status_updated_at, payment_provider 
FROM internships 
LIMIT 5;

SELECT 
    'Sample Order Records' as Info;
SELECT id, name, payment_status, payment_provider, transaction_reference 
FROM orders 
LIMIT 5;
