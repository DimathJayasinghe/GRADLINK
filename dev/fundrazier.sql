-- Fundraiser Application Database Schema

-- 1. Fundraising Requests Table
-- Stores the main application details for a fundraiser
CREATE TABLE `fundraising_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL, -- The applicant/creator
  `club_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `requester_position` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `requester_phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `headline` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `project_poster` varchar(512) COLLATE utf8mb4_general_ci DEFAULT NULL, -- File path
  
  -- Financials & Timeline
  `goal_amount` decimal(15,2) NOT NULL,
  `collected_amount` decimal(15,2) DEFAULT '0.00', -- Normalized for quick access
  `objective` text COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  
  -- Fund Manager Info
  `fund_manager_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fund_manager_contact` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  
  -- Approval & Status
  `advisor_id` int DEFAULT NULL, -- Lecturer in charge/Advisor
  `status` enum('Pending','Approved','Rejected','Active','Completed','Cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `admin_contact_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL, -- If an admin reviews it
  `rejection_reason` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_fundraising_user` (`user_id`),
  KEY `idx_fundraising_advisor` (`advisor_id`),
  KEY `idx_fundraising_status` (`status`),
  CONSTRAINT `fk_fundraising_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fundraising_advisor` FOREIGN KEY (`advisor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Fundraising Team Members Table
-- Stores tagged team members for a specific request
CREATE TABLE `fundraising_team_members` (
  `request_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`request_id`, `user_id`),
  KEY `idx_ftm_user` (`user_id`),
  CONSTRAINT `fk_ftm_request` FOREIGN KEY (`request_id`) REFERENCES `fundraising_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ftm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Fundraising Bank Details Table
-- Stores bank account info separately for security/privacy access control
CREATE TABLE `fundraising_bank_details` (
  `request_id` int NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `account_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `account_holder` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`request_id`),
  CONSTRAINT `fk_fbd_request` FOREIGN KEY (`request_id`) REFERENCES `fundraising_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Fundraising Donations Table
-- Tracks actual donations/transactions linked to a fundraising request
CREATE TABLE `fundraising_donations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `donor_user_id` int DEFAULT NULL, -- Null if anonymous or external
  `amount` decimal(15,2) NOT NULL,
  `transaction_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL, -- Payment gateway ID or receipt number
  `donor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL, -- For display if user_id is null or custom name
  `donor_email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci, -- Optional message from donor
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0', -- If true, hide name from public list
  `status` enum('Pending','Successful','Failed','Refunded') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_donation_request` (`request_id`),
  KEY `idx_donation_donor` (`donor_user_id`),
  CONSTRAINT `fk_donation_request` FOREIGN KEY (`request_id`) REFERENCES `fundraising_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_donation_user` FOREIGN KEY (`donor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
