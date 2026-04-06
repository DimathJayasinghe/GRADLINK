-- Follow Requests Table
-- Stores pending follow requests until owner accepts/rejects them
CREATE TABLE IF NOT EXISTS follow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    target_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_request (requester_id, target_id),
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_target_pending (target_id, status),
    INDEX idx_requester (requester_id)
);

-- Updated followers table (for reference, should already exist)
-- This table now only contains accepted follows
CREATE TABLE IF NOT EXISTS followers (
    follower_id INT NOT NULL,
    followed_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (follower_id, followed_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for profile visibility (should already exist)
CREATE TABLE IF NOT EXISTS user_profiles_visibility (
    user_id INT NOT NULL,
    is_public BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample data for testing
-- INSERT INTO follow_requests (requester_id, target_id, status) VALUES (2, 1, 'pending');
-- INSERT INTO follow_requests (requester_id, target_id, status) VALUES (3, 1, 'pending');
