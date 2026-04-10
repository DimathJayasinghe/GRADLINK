-- User Activity Logging System
-- This creates the tables needed for tracking online users and URL access logs

-- 1. Online Users Table
-- Tracks currently online users (cleaned every 5 minutes)
-- We use INSERT ... ON DUPLICATE KEY UPDATE to avoid duplicates
CREATE TABLE IF NOT EXISTS `online_users` (
    `user_id` INT NOT NULL,
    `session_id` VARCHAR(128) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(512) DEFAULT NULL,
    `last_activity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `current_url` VARCHAR(512) DEFAULT NULL,
    
    PRIMARY KEY (`user_id`),
    KEY `idx_last_activity` (`last_activity`),
    CONSTRAINT `fk_online_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
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

-- 3. Cleanup Event (runs every 5 minutes to clean online_users)
-- Note: MySQL Event Scheduler must be enabled: SET GLOBAL event_scheduler = ON;
DELIMITER //
CREATE EVENT IF NOT EXISTS `cleanup_online_users`
ON SCHEDULE EVERY 5 MINUTE
DO
BEGIN
    DELETE FROM `online_users` WHERE `last_activity` < DATE_SUB(NOW(), INTERVAL 5 MINUTE);
END//
DELIMITER ;

-- Alternative: If you can't use MySQL events, use this stored procedure and call it via cron
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_cleanup_online_users`()
BEGIN
    DELETE FROM `online_users` WHERE `last_activity` < DATE_SUB(NOW(), INTERVAL 5 MINUTE);
END//
DELIMITER ;

-- Utility queries for admin dashboard:

-- Get count of currently online users
-- SELECT COUNT(*) as online_count FROM online_users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE);

-- Get list of online users with details
-- SELECT ou.*, u.name, u.display_name, u.email 
-- FROM online_users ou 
-- JOIN users u ON ou.user_id = u.id 
-- WHERE ou.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
-- ORDER BY ou.last_activity DESC;

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
