-- Minimal schema for GRADLINK (users, posts with optional image, comments, post likes)
-- Run inside an existing database (creation handled externally).

DROP TABLE IF EXISTS post_likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users;

-- NOTE: If this table already exists, run:
-- ALTER TABLE users ADD COLUMN gender ENUM('male','female') NULL AFTER display_name;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','alumni','undergrad') NOT NULL DEFAULT 'undergrad',
    display_name VARCHAR(100) NULL,
    gender ENUM('male','female') NULL,
    profile_image VARCHAR(255) NOT NULL DEFAULT 'default.jpg',
    bio TEXT NULL,
    skills TEXT NULL,
    nic VARCHAR(20) NULL,
    batch_no INT NULL,
    student_id VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id, created_at),
    INDEX (created_at)
);

CREATE TABLE comments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (post_id, created_at)
);

CREATE TABLE post_likes (
    post_id BIGINT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (post_id,user_id),
    CONSTRAINT fk_post_likes_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_post_likes_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE certificates (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NOT NULL,
    issued_date DATE NOT NULL,
    certificate_file VARCHAR(255) NOT NULL, -- path to uploaded PDF
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificates_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (user_id, issued_date)
);



-- Sample user (password is 'password' hashed using bcrypt 60-char standard example)
INSERT INTO users (name,email,password,role) VALUES
 ('Sample User','user@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','undergrad');

-- Add special_alumni column to users table
ALTER TABLE users ADD COLUMN special_alumni BOOLEAN DEFAULT 0;

-- Messaging System Tables
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversations;

-- Conversations table to track conversations between users
CREATE TABLE conversations (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    CONSTRAINT fk_conversations_user1 FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_conversations_user2 FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_users (user1_id, user2_id),
    INDEX idx_last_activity (last_activity)
);

-- Messages table with the exact structure you requested
CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    conversation_id INT NOT NULL,
    message_text TEXT NOT NULL,
    message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT 0,
    read_at TIMESTAMP NULL,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_conversation FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
    INDEX idx_conversation_time (conversation_id, message_time),
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_unread (receiver_id, is_read)
);

-- Optional: Conversation reports table for moderation
CREATE TABLE conversation_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reason TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    CONSTRAINT fk_reports_conversation FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
    CONSTRAINT fk_reports_reporter FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_report_status (status, created_at)
);

-- Sample data for testing (optional)
-- You can uncomment these lines if you want test data
/*
INSERT INTO conversations (user1_id, user2_id) VALUES 
(1, 2), 
(1, 3);

INSERT INTO messages (sender_id, receiver_id, conversation_id, message_text) VALUES
(1, 2, 1, 'Hello! How are you doing?'),
(2, 1, 1, 'Hi there! I am doing great, thanks for asking!'),
(1, 2, 1, 'That is awesome to hear!');
*/


 CREATE TABLE certificates (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    issuer VARCHAR(255) NOT NULL,
    issued_date DATE NOT NULL,
    certificate_file VARCHAR(255) NOT NULL,  -- path to uploaded PDF
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_certificates_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_issued (user_id, issued_date)
);

-- Users table
ALTER TABLE users ADD COLUMN gender ENUM('male','female') NULL AFTER display_name;

-- Pending alumni table
ALTER TABLE unregisted_alumni ADD COLUMN gender ENUM('male','female') NULL AFTER display_name;



