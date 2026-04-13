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

-- make sure to on event schedular on phpmyadmin
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS process_due_account_lifecycle_actions()
BEGIN
    -- 1) Attempt to delete due users (relies on FK cascades if present).
    DELETE FROM users
    WHERE id IN (
        SELECT a.user_id
        FROM account_lifecycle_actions a
        WHERE a.status = 'pending'
          AND a.action_type = 'deactivate_and_delete'
          AND a.delete_at IS NOT NULL
          AND a.delete_at <= NOW()
    );

    -- 2) Mark actions as deleted only if the user row is gone.
    UPDATE account_lifecycle_actions a
    LEFT JOIN users u ON u.id = a.user_id
    SET a.status = 'deleted',
        a.processed_at = NOW(),
        a.updated_at = NOW()
    WHERE a.status = 'pending'
      AND a.action_type = 'deactivate_and_delete'
      AND a.delete_at IS NOT NULL
      AND a.delete_at <= NOW()
      AND u.id IS NULL;
END$$

CREATE EVENT IF NOT EXISTS ev_process_account_lifecycle_actions
ON SCHEDULE EVERY 1 HOUR
DO
    CALL process_due_account_lifecycle_actions()$$

DELIMITER ;
CREATE TABLE user_notification_settings (
  user_id INT PRIMARY KEY,
  email_enabled TINYINT(1) DEFAULT 1,
  mentions_enabled TINYINT(1) DEFAULT 1,
  followers_enabled TINYINT(1) DEFAULT 1,
  engagement_enabled TINYINT(1) DEFAULT 1,
  dnd_enabled TINYINT(1) DEFAULT 0,
  dnd_start TIME NULL,
  dnd_end TIME NULL,
  dnd_days ENUM('weekdays','weekends','everyday') NULL,
  in_app_disabled_types TEXT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE support_tickets (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  email VARCHAR(255) NOT NULL,
  topic ENUM('account','technical','billing','other') DEFAULT 'technical',
  message TEXT NOT NULL,
  status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE support_problem_reports (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  report_type ENUM('bug','abuse','policy') DEFAULT 'bug',
  details TEXT NOT NULL,
  status ENUM('pending','triaged','resolved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE support_feedback (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  feedback_type ENUM('feature','ux','other') DEFAULT 'other',
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- Add admin reply columns to support_tickets
ALTER TABLE support_tickets 
  ADD COLUMN admin_reply TEXT NULL AFTER status,
  ADD COLUMN admin_replied_at TIMESTAMP NULL AFTER admin_reply;

-- Add admin reply columns to support_problem_reports
ALTER TABLE support_problem_reports 
  ADD COLUMN admin_reply TEXT NULL AFTER status,
  ADD COLUMN admin_replied_at TIMESTAMP NULL AFTER admin_reply;