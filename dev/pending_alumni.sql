-- NOTE: If this table already exists in your DB, run the following to add the new columns:
-- ALTER TABLE `unregisted_alumni` ADD COLUMN `explain_yourself` text DEFAULT NULL AFTER `bio`;
-- ALTER TABLE `unregisted_alumni` ADD COLUMN `gender` ENUM('male','female') NULL AFTER `display_name`;

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
