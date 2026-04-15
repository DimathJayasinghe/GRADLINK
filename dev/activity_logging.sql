-- User Activity Logging System
-- This creates the tables needed for tracking online users and URL access logs

-- 1. User Activity Table
-- Tracks the last time an authenticated user touched the backend.
-- One row per user keeps the write path extremely cheap.
CREATE TABLE IF NOT EXISTS `user_activity` (
    `user_id` INT NOT NULL,
    `last_activity` DATETIME NOT NULL,
    PRIMARY KEY (`user_id`),
    CONSTRAINT `fk_user_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. URL Access Logs Table
-- Logs all URL access with user, URL, time, request type
CREATE TABLE IF NOT EXISTS `access_logs` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `user_id` INT DEFAULT NULL,               -- NULL if not logged in (guest)
    `user_name` VARCHAR(255) DEFAULT NULL,    -- Username from session (for display without join)
    `user_role` VARCHAR(50) DEFAULT NULL,     -- User role from session (admin, alumni, undergrad, etc.)
    `session_id` VARCHAR(128) DEFAULT NULL,
    `url` VARCHAR(512) NOT NULL,
    `method` VARCHAR(10) NOT NULL,            -- GET, POST, PUT, DELETE, etc.
    `controller` VARCHAR(100) DEFAULT NULL,
    `action` VARCHAR(100) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(512) DEFAULT NULL,
    `referer` VARCHAR(512) DEFAULT NULL,
    `response_code` SMALLINT DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_url` (`url`(255)),
    KEY `idx_method` (`method`),
    CONSTRAINT `fk_access_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Utility queries for admin dashboard:

-- Get count of currently online users
-- SELECT COUNT(*) as online_count FROM user_activity WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- Get list of online users with details
-- SELECT u.id, COALESCE(NULLIF(u.display_name, ''), u.name) AS display_name, u.profile_image, a.last_activity
-- FROM user_activity a
-- JOIN users u ON a.user_id = u.id
-- WHERE a.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
-- ORDER BY a.last_activity DESC;

-- Get access logs for today
-- SELECT * FROM access_logs WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 100;

-- Get most visited URLs
-- SELECT url, COUNT(*) as visits FROM access_logs 
-- WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) 
-- GROUP BY url ORDER BY visits DESC LIMIT 20;

-- Get user activity summary
-- SELECT user_id, COUNT(*) as page_views, MIN(created_at) as first_visit, MAX(created_at) as last_visit 
-- FROM access_logs 
-- WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) AND user_id IS NOT NULL
-- GROUP BY user_id ORDER BY page_views DESC;
