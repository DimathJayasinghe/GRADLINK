CREATE DATABASE IF NOT EXISTS GL_db;
USE GL_db;

-- Updated Users table with additional fields
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'alumni', 'undergrad') NOT NULL DEFAULT 'undergrad',
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    interests TEXT,
    nic VARCHAR(20),
    batch_no VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);

-- Sample data for Users table with new fields
INSERT INTO Users (name, email, password, role, interests, nic, batch_no) VALUES
('aimath', 'aimathjaya@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni', 'Machine Learning, Data Science', '123456789V', '2019'),
('bimath', 'bimathjaya@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'undergrad', 'Web Development, Algorithms', '987654321V', '2022'),
('cimath', 'cimathjaya@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administration', '654321789V', '2018'),
('Dimath', 'dimathjaya@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'undergrad', 'Mobile Development, UI/UX', '789123456V', '2023');



-- DEVELOP STATE: SIMPLE LOGIN
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'alumni', 'undergrad') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data with proper password hashing
INSERT INTO users (name, email, password, role) VALUES
('Alumni User', 'alumni@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alumni'),
('Student User', 'student@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'undergrad'),
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


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

