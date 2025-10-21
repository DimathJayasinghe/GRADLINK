-- Messages System Database Schema
-- First, let's check what users table exists and create tables without foreign keys initially

-- Conversations table (without foreign keys first)
CREATE TABLE IF NOT EXISTS conversations (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    INDEX idx_users (user1_id, user2_id),
    INDEX idx_last_activity (last_activity)
);

-- Messages table (without foreign keys first)
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_created_at (created_at),
    INDEX idx_unread (receiver_id, is_read)
);

-- Conversation reports table (without foreign keys first)
CREATE TABLE IF NOT EXISTS conversation_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    notes TEXT NULL,
    INDEX idx_conversation (conversation_id),
    INDEX idx_reporter (reporter_id),
    INDEX idx_status (status)
);

-- Insert sample data for testing
INSERT INTO conversations (user1_id, user2_id, created_at, last_activity) VALUES
(1, 2, '2024-01-15 14:30:00', '2024-01-15 15:30:00'),
(1, 3, '2024-01-15 13:15:00', '2024-01-15 13:45:00'),
(1, 4, '2024-01-15 12:45:00', '2024-01-15 12:45:00');

INSERT INTO messages (conversation_id, sender_id, receiver_id, message_text, created_at) VALUES
(1, 2, 1, 'Hey! Are you coming to the meeting?', '2024-01-15 14:30:00'),
(1, 1, 2, 'Yes, I will be there in 10 minutes', '2024-01-15 14:32:00'),
(1, 2, 1, 'Great! See you there', '2024-01-15 15:30:00'),
(2, 3, 1, 'Thanks for the help yesterday', '2024-01-15 13:15:00'),
(2, 1, 3, 'No problem! Happy to help', '2024-01-15 13:17:00'),
(2, 3, 1, 'Let me know if you need anything else', '2024-01-15 13:45:00'),
(3, 4, 1, 'Can you share the project files?', '2024-01-15 12:45:00');