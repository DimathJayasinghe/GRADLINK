CREATE DATABASE IF NOT EXISTS GL_db;
USE GL_db;

-- Updated Users table with additional fields


-- DEVELOP STATE: SIMPLE LOGIN
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'alumni', 'undergrad') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Add the missing columns to users table for profile data
ALTER TABLE users
    -- Profile info
    ADD display_name VARCHAR(100) NULL AFTER name,
    ADD profile_image VARCHAR(255) DEFAULT 'default.jpg' AFTER role,
    
    -- Bio and skills
    ADD bio TEXT NULL,
    ADD skills TEXT NULL,
    
    -- Contact/ID information
    ADD nic VARCHAR(20) NULL,
    ADD student_id VARCHAR(50) NULL,
    
    -- Academic info
    ADD batch_no INT NULL,
    ADD graduation_year INT NULL;


-- Update the profile_image field to reference the correct storage location
ALTER TABLE users
    MODIFY profile_image VARCHAR(255) DEFAULT 'default.jpg' COMMENT 'Stored in /storage/profile_pic/';

