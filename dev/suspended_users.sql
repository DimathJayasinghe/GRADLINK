-- Suspended users audit table
-- Run this script once if you prefer manual DB setup.

CREATE TABLE IF NOT EXISTS suspended_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    suspended_by INT NOT NULL,
    reason TEXT NULL,
    status ENUM('active','lifted','removed') NOT NULL DEFAULT 'active',
    suspended_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    lifted_at DATETIME NULL,
    lifted_by INT NULL,
    removed_at DATETIME NULL,
    removed_by INT NULL,
    snapshot_name VARCHAR(255) NULL,
    snapshot_email VARCHAR(255) NULL,
    snapshot_role VARCHAR(50) NULL,
    INDEX idx_suspended_users_user (user_id),
    INDEX idx_suspended_users_status (status),
    INDEX idx_suspended_users_suspended_at (suspended_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
