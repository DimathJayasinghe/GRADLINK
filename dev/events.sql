-- Use your database first, e.g.
-- USE gradlink_db;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- EVENTS & RELATED TABLES (types match your `users` table: signed INT)
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(191) DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME DEFAULT NULL,
  `all_day` TINYINT(1) NOT NULL DEFAULT 0,
  `timezone` VARCHAR(64) DEFAULT 'UTC',
  `venue` VARCHAR(255) DEFAULT NULL,
  `capacity` INT DEFAULT NULL,
  `organizer_id` INT NOT NULL,
  `status` ENUM('draft','published','cancelled') NOT NULL DEFAULT 'published',
  `visibility` ENUM('public','private') NOT NULL DEFAULT 'public',
  `series_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_slug` (`slug`),
  KEY `idx_start` (`start_datetime`),
  KEY `idx_end` (`end_datetime`),
  KEY `idx_organizer` (`organizer_id`),
  CONSTRAINT `events_fk_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `event_id` INT NOT NULL,
  `file_path` VARCHAR(512) NOT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event` (`event_id`),
  CONSTRAINT `event_images_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `slug` VARCHAR(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_tag_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_tags` (
  `event_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  PRIMARY KEY (`event_id`,`tag_id`),
  KEY `idx_tag` (`tag_id`),
  CONSTRAINT `event_tags_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_tags_fk_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_attendees` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `event_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `status` ENUM('attending','maybe','not_attending') NOT NULL DEFAULT 'attending',
  `guests` SMALLINT DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_event_user` (`event_id`,`user_id`),
  KEY `idx_event` (`event_id`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `event_attendees_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_attendees_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_bookmarks` (
  `user_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`event_id`),
  CONSTRAINT `event_bookmarks_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_bookmarks_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_series` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rrule` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `event_exceptions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `series_id` INT NOT NULL,
  `exception_date` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_series` (`series_id`),
  CONSTRAINT `event_exceptions_fk_series` FOREIGN KEY (`series_id`) REFERENCES `event_series` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Separate table for event requests (used by the eventrequest dashboard)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `event_requests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `club_name` VARCHAR(255) DEFAULT NULL,
  `position` VARCHAR(191) DEFAULT NULL,
  `short_tagline` VARCHAR(255) DEFAULT NULL,
  `event_type` VARCHAR(64) DEFAULT NULL,
  `post_caption` TEXT DEFAULT NULL,
  `add_to_calendar` TINYINT(1) NOT NULL DEFAULT 0,
  `president_name` VARCHAR(191) DEFAULT NULL,
  `approval_date` DATE DEFAULT NULL,
  `attachment_image` VARCHAR(512) DEFAULT NULL,
  `event_date` DATE DEFAULT NULL,
  `event_time` TIME DEFAULT NULL,
  `event_venue` VARCHAR(255) DEFAULT NULL,
  `event_id` INT DEFAULT NULL,
  `status` ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `views` INT NOT NULL DEFAULT 0,
  `unique_viewers` INT NOT NULL DEFAULT 0,
  `interested_count` INT NOT NULL DEFAULT 0,
  `going_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_event_id` (`event_id`),
  CONSTRAINT `event_requests_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_requests_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;


SET FOREIGN_KEY_CHECKS = 1;

