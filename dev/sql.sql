-- Minimal schema for GRADLINK (users, posts with optional image, comments, post likes)
-- Run inside an existing database (creation handled externally).



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


-- Add special_alumni column to users table
ALTER TABLE users ADD COLUMN special_alumni BOOLEAN DEFAULT 0;



-- Pending alumni table
CREATE TABLE IF NOT EXISTS `unregisted_alumni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('alumni') NOT NULL DEFAULT 'alumni',
  `display_name` varchar(100) DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `profile_image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `bio` text DEFAULT NULL,
  `explain_yourself` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `batch_no` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


 create table notifications (
    id int auto_increment primary key,
    receiver_id int not null,
    type varchar(50) not null,
    reference_id int not null,
    content text not null,
    is_read boolean default false,
    created_at timestamp default current_timestamp,
    
    foreign key (receiver_id) references users(id) on delete cascade,
    index idx_user_id (receiver_id),
    index idx_is_read (is_read)
 );

create table followers (
    follower_id int not null,
    followed_id int not null,
    primary key (follower_id, followed_id),
    foreign key (follower_id) references users(id) on delete cascade,
    foreign key (followed_id) references users(id) on delete cascade
);

-- table to find weather user has a public profile or not
CREATE TABLE user_profiles_visibility (
    user_id INT NOT NULL,
    is_public BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



-- EVENTS & RELATED TABLES (types match your `users` table: signed INT)
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) DEFAULT NULL,
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
  `updated_at` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP,
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
  `slug` VARCHAR(255) DEFAULT NULL,
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
  `updated_at` TIMESTAMP NOT NULL  ON UPDATE CURRENT_TIMESTAMP,
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



CREATE TABLE IF NOT EXISTS `event_requests` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `club_name` VARCHAR(255) DEFAULT NULL,
  `position` VARCHAR(255) DEFAULT NULL,
  `short_tagline` VARCHAR(255) DEFAULT NULL,
  `event_type` VARCHAR(64) DEFAULT NULL,
  `post_caption` TEXT DEFAULT NULL,
  `add_to_calendar` TINYINT(1) NOT NULL DEFAULT 0,
  `president_name` VARCHAR(255) DEFAULT NULL,
  `approval_date` DATE DEFAULT NULL,
  `attachment_image` VARCHAR(512) DEFAULT NULL,
  `event_date` DATE DEFAULT NULL,
  `event_time` TIME DEFAULT NULL,
  `event_venue` VARCHAR(255) DEFAULT NULL,
  `event_id` INT DEFAULT NULL,
  `post_id` BIGINT DEFAULT NULL,
  `status` ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `views` INT NOT NULL DEFAULT 0,
  `unique_viewers` INT NOT NULL DEFAULT 0,
  `interested_count` INT NOT NULL DEFAULT 0,
  `going_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_event_id` (`event_id`),
  KEY `idx_post_id` (`post_id`),
  CONSTRAINT `event_requests_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_requests_fk_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `event_requests_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id)
);


