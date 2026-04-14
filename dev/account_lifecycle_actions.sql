-- Account lifecycle table for deactivation / delayed deletion workflow
-- This is a new table and does not modify existing tables.

CREATE TABLE IF NOT EXISTS account_lifecycle_actions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action_type ENUM('deactivate_only', 'deactivate_and_delete') NOT NULL,
    status ENUM('pending', 'reactivated', 'deleted', 'cancelled') NOT NULL DEFAULT 'pending',
    reason VARCHAR(100) NULL,
    other_reason VARCHAR(500) NULL,
    deactivated_at DATETIME NOT NULL,
    reactivate_at DATETIME NOT NULL,
    delete_at DATETIME NULL,
    processed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_account_lifecycle_user (user_id),
    KEY idx_account_lifecycle_status_delete_at (status, delete_at),
    KEY idx_account_lifecycle_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
