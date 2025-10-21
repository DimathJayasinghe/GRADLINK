-- Simple Messages Tables (No foreign keys, basic structure)

-- Drop tables if they exist (for clean install)
DROP TABLE IF EXISTS conversation_reports;
DROP TABLE IF EXISTS messages; 
DROP TABLE IF EXISTS conversations;

-- Conversations table
CREATE TABLE conversations (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL
);

-- Messages table  
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL
);

-- Reports table
CREATE TABLE conversation_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    notes TEXT NULL
);

-- Insert sample data (using user IDs that should exist)
INSERT INTO conversations (user1_id, user2_id, created_at, last_activity) VALUES
(1, 2, NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 30 MINUTE),
(1, 3, NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 1 HOUR),
(1, 4, NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 5 HOUR);

INSERT INTO messages (conversation_id, sender_id, receiver_id, message_text, created_at) VALUES
(1, 2, 1, 'Hey! Are you coming to the meeting?', NOW() - INTERVAL 30 MINUTE),
(2, 3, 1, 'Thanks for the help yesterday', NOW() - INTERVAL 1 HOUR),
(3, 4, 1, 'Can you share the project files?', NOW() - INTERVAL 5 HOUR);