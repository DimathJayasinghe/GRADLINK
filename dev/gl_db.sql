SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `certificates` (
  `id` bigint NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `issuer` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `issued_date` date NOT NULL,
  `certificate_file` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `certificates` (`id`, `user_id`, `name`, `issuer`, `issued_date`, `certificate_file`, `created_at`) VALUES
(16, 15, 'PHP leanrning', 'LinkedIn', '2025-03-29', '1761191854_7f891c0d.pdf', '2025-10-23 03:57:34'),
(17, 2, 'Give4Good Donation Project', 'LinkedIn', '2025-03-29', '1761192770_ff155e69.pdf', '2025-10-23 04:07:21'),
(18, 2, 'Ordinary level certificate', 'Government', '2024-10-17', '1761192656_c7434777.pdf', '2025-10-23 04:09:06');

CREATE TABLE `comments` (
  `id` bigint NOT NULL,
  `post_id` bigint NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(23, 55, 14, 'Exciting!!', '2025-10-23 03:18:15'),
(24, 56, 2, 'test', '2025-10-24 05:39:23');

CREATE TABLE events (
id int NOT NULL,
slug varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
title varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
description text COLLATE utf8mb4_general_ci,
start_datetime datetime NOT NULL,
end_datetime datetime DEFAULT NULL,
all_day tinyint(1) NOT NULL DEFAULT '0',
timezone varchar(64) COLLATE utf8mb4_general_ci DEFAULT 'UTC',
venue varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
capacity int DEFAULT NULL,
organizer_id int NOT NULL,
status enum('draft','published','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'published',
visibility enum('public','private') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'public',
series_id int DEFAULT NULL,
 created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `events` (`id`, `slug`, `title`, `description`, `start_datetime`, `end_datetime`, `all_day`, `timezone`, `venue`, `capacity`, `organizer_id`, `status`, `visibility`, `series_id`, `created_at`, `updated_at`) VALUES
(44, NULL, 'IEEE Introductory session', 'Calling first yr volunteers ', '2025-10-31 16:00:00', NULL, 0, 'UTC', 'S104', NULL, 4, 'published', 'public', NULL, '2025-10-22 16:39:23', '2025-10-22 16:39:23'),
(45, NULL, 'Freshers Announce', '', '2025-10-20 11:00:00', NULL, 0, 'UTC', 'Ground', NULL, 16, 'published', 'public', NULL, '2025-10-23 03:31:01', '2025-10-23 03:31:01'),
(46, NULL, '𝗗𝗲𝘀𝗶𝗴𝗻𝗶𝗻𝗴 𝘁𝗵𝗲 𝗔𝗜 𝗔𝗴𝗲𝗻𝘁: 𝗙𝗿𝗼𝗺 𝗣𝗿𝗼𝗯𝗹𝗲𝗺 𝘁𝗼 𝗔𝗿𝗰𝗵𝗶𝘁𝗲𝗰𝘁𝘂𝗿𝗲', 'Distribute AI Knowledge', '2025-10-30 09:00:00', NULL, 0, 'UTC', 'Trace Expert City', NULL, 16, 'published', 'public', NULL, '2025-10-23 06:52:47', '2025-10-23 06:52:47');

CREATE TABLE `event_attendees` (
  `id` int NOT NULL,
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('attending','maybe','not_attending') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'attending',
  `guests` smallint DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event_bookmarks` (
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_bookmarks` (`user_id`, `event_id`, `created_at`) VALUES
(14, 44, '2025-10-23 06:46:54');

CREATE TABLE `event_exceptions` (
  `id` int NOT NULL,
  `series_id` int NOT NULL,
  `exception_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event_images` (
  `id` int NOT NULL,
  `event_id` int NOT NULL,
  `file_path` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_images` (`id`, `event_id`, `file_path`, `caption`, `is_primary`, `created_at`) VALUES
(43, 44, '1761150903_post_1__1_.png', NULL, 1, '2025-10-22 16:39:23'),
(44, 45, '1761190256_WhatsApp_Image_2025-10-22_at_14.38.45_acb2b93f.jpg', NULL, 1, '2025-10-23 03:31:01'),
(45, 46, '1761189493_WhatsApp_Image_2025-10-18_at_18.06.40_edc84203.jpg', NULL, 1, '2025-10-23 06:52:47');

CREATE TABLE `event_requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `club_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attachment_image` varchar(512) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `event_venue` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `views` int NOT NULL DEFAULT '0',
  `unique_viewers` int NOT NULL DEFAULT '0',
  `interested_count` int NOT NULL DEFAULT '0',
  `going_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `short_tagline` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event_type` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `post_caption` text COLLATE utf8mb4_general_ci,
  `add_to_calendar` tinyint(1) NOT NULL DEFAULT '0',
  `president_name` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `approval_date` date DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `post_id` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `event_requests` (`id`, `user_id`, `title`, `description`, `club_name`, `position`, `attachment_image`, `event_date`, `event_time`, `event_venue`, `status`, `views`, `unique_viewers`, `interested_count`, `going_count`, `created_at`, `updated_at`, `short_tagline`, `event_type`, `post_caption`, `add_to_calendar`, `president_name`, `approval_date`, `event_id`, `post_id`) VALUES
(7, 4, 'IEEE Introductory session', 'Calling first yr volunteers ', 'IEEE CS Chapter', 'Marketing Lead', '1761150903_post_1__1_.png', '2025-10-31', '16:00:00', 'S104', 'Approved', 0, 0, 0, 0, '2025-10-22 16:35:03', '2025-10-22 16:39:23', 'AGM\' 25', 'Other', '💙 Join the IEEE Fam & Kickstart Your Journey of Tech Adventures!\r\n\r\nEver wondered what all the hype about IEEE is? Here’s your chance to find out!\r\n\r\n📅 Date: 21st of August\r\n⏰ Time: 4:00 PM\r\n📍 Venue: S104 Lecture Hall\r\n\r\n✨ Meet the team, make friends, explore opportunities, and be part of something bigger.\r\n\r\n🔥Your adventure with IEEE begins NOW!🔥\r\n\r\n#IEEESB # IEEECSChapter # IEEEWIE #UCSC', 1, 'Kaviru Hapuarachchi', '2025-10-22', 44, 53),
(8, 16, '𝗗𝗲𝘀𝗶𝗴𝗻𝗶𝗻𝗴 𝘁𝗵𝗲 𝗔𝗜 𝗔𝗴𝗲𝗻𝘁: 𝗙𝗿𝗼𝗺 𝗣𝗿𝗼𝗯𝗹𝗲𝗺 𝘁𝗼 𝗔𝗿𝗰𝗵𝗶𝘁𝗲𝗰𝘁𝘂𝗿𝗲', 'Distribute AI Knowledge', 'AIDSL', 'Program Lead', '1761189493_WhatsApp_Image_2025-10-18_at_18.06.40_edc84203.jpg', '2025-10-30', '09:00:00', 'Trace Expert City', 'Approved', 0, 0, 0, 0, '2025-10-23 03:18:13', '2025-10-23 06:52:47', 'AI Agent Masterty', 'Workshop', 'The wait is finally over! \r\nAI Agent Mastery kicks off TODAY! 🚀\r\n\r\nJoin us at 7:00 PM via Zoom for Session 01\r\n𝗗𝗲𝘀𝗶𝗴𝗻𝗶𝗻𝗴 𝘁𝗵𝗲 𝗔𝗜 𝗔𝗴𝗲𝗻𝘁: 𝗙𝗿𝗼𝗺 𝗣𝗿𝗼𝗯𝗹𝗲𝗺 𝘁𝗼 𝗔𝗿𝗰𝗵𝗶𝘁𝗲𝗰𝘁𝘂𝗿𝗲, led by Mr. Pasan Jayawardene, Software Engineer (AI/R&D) at Insighture. \r\n\r\nGet ready to explore how real-world problems transform into intelligent AI solutions. \r\n\r\nSee you there! 🤖\r\n\r\n#AIAgentMastery #AI #IEEE #YPSL #AIDrivenSL #KDU #UOP #UWU #Insighture', 1, 'SS', '2025-10-22', 46, 60),
(9, 16, 'ReidXtreme 4.0 - Final Round', 'Promote CP', 'IEEE SB UCSC', 'Program Lead', '1761189791_WhatsApp_Image_2025-10-17_at_21.33.15_17e19e71.jpg', '2025-11-02', '10:00:00', '@E401 - UCSC', 'Pending', 0, 0, 0, 0, '2025-10-23 03:23:11', '2025-10-23 03:23:11', 'UCSC Premier CP Hack', 'Competition', '🔥 Final Showdown Tomorrow! \r\n \r\nThe wait is almost over ! The Grand Finale of ReidXtreme 4.0 is happening TOMORROW!  \r\n\r\nGet ready to battle it out for glory and claim your spot at the top! 🏆  \r\n\r\n#ReidXtreme4.0 #FinalRound #CodeToVictory #IEEEXTREME19.0 #UCSC #IEEESB #ACMUCSC', 0, 'Likitha', '2025-10-21', NULL, NULL),
(10, 16, 'Freshers Announce', '', 'UOC', 'VP', '1761190256_WhatsApp_Image_2025-10-22_at_14.38.45_acb2b93f.jpg', '2025-10-20', '11:00:00', 'Ground', 'Approved', 0, 0, 0, 0, '2025-10-23 03:30:56', '2025-10-23 03:31:01', '', 'Cultural Event', 'reshers’ Championship 2025🏆\r\n\r\n2022 – FMF 👑\r\n2023 – FMF 👑\r\n2024 – UCSC 👑\r\n2025 – Who’s next?\r\n\r\nThe crown is on the line… 🔥\r\n\r\n#FreshersChampionship #WhoWillReign #BattleForGlory #uocsports #AmalgamatedClub', 1, 'sashika', '2025-10-10', 45, 59),
(11, 15, 'Volunteering', 'Inform about the volunteering camp.', 'AIESEC', 'Secretary', '1761190395_WhatsApp_Image_2025-09-17_at_18.18.24_4e112ec5.jpg', '2025-11-18', '08:00:00', 's104', 'Pending', 0, 0, 0, 0, '2025-10-23 03:33:15', '2025-10-23 03:33:15', 'Volunteering campaign ', 'Seminar', 'Turn your volunteering dream reality', 1, 'Kaveesha', '2025-10-24', NULL, NULL);

CREATE TABLE `event_series` (
  `id` int NOT NULL,
  `rrule` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `event_tags` (
  `event_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `followers` (
  `follower_id` int NOT NULL,
  `followed_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `followers` (`follower_id`, `followed_id`) VALUES
(2, 3),
(2, 14);

CREATE TABLE `messages` (
  `message_id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `message_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_text`, `message_time`) VALUES
(48, 14, 16, 'Hello', '2025-10-23 03:12:40'),
(49, 17, 14, 'hii dimath', '2025-10-23 04:00:15'),
(50, 17, 14, 'i\'m interested on your project', '2025-10-23 04:00:30'),
(51, 17, 14, 'can we discuss about that', '2025-10-23 04:00:45'),
(58, 2, 14, 'hello pky', '2025-10-24 05:42:52'),
(59, 14, 2, 'hello pko', '2025-10-24 05:42:55'),
(61, 3, 2, 'hi', '2025-11-07 10:35:43');

CREATE TABLE `posts` (
  `id` bigint NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `posts` (`id`, `user_id`, `content`, `image`, `created_at`) VALUES
(53, 3, '💙 Join the IEEE Fam & Kickstart Your Journey of Tech Adventures!\r\n\r\nEver wondered what all the hype about IEEE is? Here’s your chance to find out!\r\n\r\n📅 Date: 21st of August\r\n⏰ Time: 4:00 PM\r\n📍 Venue: S104 Lecture Hall\r\n\r\n✨ Meet the team, make friends, explore opportunities, and be part of something bigger.\r\n\r\n🔥Your adventure with IEEE begins NOW!🔥\r\n\r\n#IEEESB # IEEECSChapter # IEEEWIE #UCSC', '1761150903_post_1__1_.png', '2025-10-22 16:39:23'),
(55, 15, '🏆𝗨𝗻𝘃𝗲𝗶𝗹𝗶𝗻𝗴 𝘁𝗵𝗲 𝗥𝗲𝘄𝗮𝗿𝗱𝘀                                         \"𝗔𝗟𝗚𝗢𝗔𝗥𝗘𝗡𝗔 ’𝟮𝟱!\"  Get ready to shine for your creativity, innovation, and hard work! 🌟  🥇 Winner – LKR 50,000 🥈 1st Runner-Up – LKR 30,000 🥉 2nd Runner-Up – LKR 20,000  Your ideas deserve the spotlight — and the prizes! 💡  Step into Sri Lanka’s ultimate designathon and make your mark in  the digital arena!  𝐃𝐨𝐧\'𝐭 𝐦𝐢𝐬𝐬 𝐨𝐮𝐭 𝐨𝐧 𝐰𝐡𝐚𝐭\'𝐬 𝐜𝐨𝐦𝐢𝐧𝐠 𝐧𝐞𝐱𝐭 - 𝐞𝐱𝐜𝐢𝐭𝐢𝐧𝐠 𝐭𝐡𝐢𝐧', '1761189163_29146f7c.jpg', '2025-10-23 03:12:43'),
(56, 14, 'Quantum Horizons Expanded!🚀  The Qiskit Fall Fest 2025 schedule has been refreshed due to the high participants requests to adjust the schedule. 💫 A Q&A session will be held every Saturday throughout the program to answer all your quantum queries and serve as guidance for your self-study note books. 🌟', '1761189350_554096c8.jpg', '2025-10-23 03:15:50'),
(58, 2, 'We’re on the lookout for a Software Engineer Intern to join our team and dive into real-world software development 📍 Apply here: https://lnkd.in/g-XVaA6E', '1761190211_790d8a9c.jpg', '2025-10-23 03:30:11'),
(59, 3, 'reshers’ Championship 2025🏆\r\n\r\n2022 – FMF 👑\r\n2023 – FMF 👑\r\n2024 – UCSC 👑\r\n2025 – Who’s next?\r\n\r\nThe crown is on the line… 🔥\r\n\r\n#FreshersChampionship #WhoWillReign #BattleForGlory #uocsports #AmalgamatedClub', '1761190256_WhatsApp_Image_2025-10-22_at_14.38.45_acb2b93f.jpg', '2025-10-23 03:31:01'),
(60, 3, 'The wait is finally over! \r\nAI Agent Mastery kicks off TODAY! 🚀\r\n\r\nJoin us at 7:00 PM via Zoom for Session 01\r\n𝗗𝗲𝘀𝗶𝗴𝗻𝗶𝗻𝗴 𝘁𝗵𝗲 𝗔𝗜 𝗔𝗴𝗲𝗻𝘁: 𝗙𝗿𝗼𝗺 𝗣𝗿𝗼𝗯𝗹𝗲𝗺 𝘁𝗼 𝗔𝗿𝗰𝗵𝗶𝘁𝗲𝗰𝘁𝘂𝗿𝗲, led by Mr. Pasan Jayawardene, Software Engineer (AI/R&D) at Insighture. \r\n\r\nGet ready to explore how real-world problems transform into intelligent AI solutions. \r\n\r\nSee you there! 🤖\r\n\r\n#AIAgentMastery #AI #IEEE #YPSL #AIDrivenSL #KDU #UOP #UWU #Insighture', '1761189493_WhatsApp_Image_2025-10-18_at_18.06.40_edc84203.jpg', '2025-10-23 06:52:47'),
(82, 2, 'DEsign X 2025, Produdly announce to you by the IEEE CS chapter UCSC', '1762361911_7736f9f6.png', '2025-11-05 16:58:31'),
(83, 2, 'DEsign X 2025, Produdly announce to you by the IEEE CS chapter UCSC', '1762361960_49a71ad9.png', '2025-11-05 16:59:20');

CREATE TABLE `post_likes` (
  `post_id` bigint NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `post_likes` (`post_id`, `user_id`, `created_at`) VALUES
(53, 14, '2025-10-22 17:03:19'),
(55, 14, '2025-10-23 03:17:55'),
(56, 2, '2025-10-24 05:39:16'),
(58, 2, '2025-11-05 08:31:15'),
(58, 3, '2025-10-23 14:05:12'),
(58, 15, '2025-10-23 03:33:53');

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `unregisted_alumni` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('alumni') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'alumni',
  `display_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'default.jpg',
  `bio` text COLLATE utf8mb4_general_ci,
  `explain_yourself` text COLLATE utf8mb4_general_ci,
  `skills` text COLLATE utf8mb4_general_ci,
  `nic` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `batch_no` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `unregisted_alumni` (`id`, `name`, `email`, `password`, `role`, `display_name`, `gender`, `profile_image`, `bio`, `explain_yourself`, `skills`, `nic`, `batch_no`, `created_at`, `status`) VALUES
(2, 'Alumni Dimath', 'dima@gmail.com', '$2y$10$brwWevWldjhHhcVyHYy19.VeZ2j5iKYwCEgYl8HDD3VVFYchHdvWO', 'alumni', 'Debug J', NULL, '2_1760933665.png', '', NULL, NULL, '23456789', 2, '2025-10-20 04:14:25', 'pending'),
(5, 'Hasrshath', 'harshath@gmail.com', '$2y$10$eetfFE3Yhj1RRpbteidzRO5sebouwEq9NBnoQrlMcYxRGWLHRjzG2', 'alumni', 'harshath', 'male', '5_1761188798.jpg', 'cyber security enthusian \r\nquantumn computing reseacher', 'i\' m graduate  Bsc.Hons in computer science and from batch 13 2016', '[\"CLOUD\",\"CYBER\"]', '908765432101', 13, '2025-10-23 03:06:38', 'pending'),
(6, 'ramarajan perera', 'perera@gmail.com', '$2y$10$LMRDvxlxza29AXipNA/LXe1RKvIUcHupP1kTdZwbPtWrI0.TquOWq', 'alumni', 'perera', 'male', '6_1761189679.jpg', 'researcher in Artificial intelligence and Machine Learning', 'I am an alumnus of the University of Colombo School of Computing (UCSC), belonging to Batch 3 of 2005, where I obtained my Bachelor of Science in Computer Science', '[\"ML\",\"AI\"]', '123456789099', 3, '2025-10-23 03:21:19', 'pending');

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','alumni','undergrad') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'undergrad',
  `display_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'default.jpg',
  `bio` text COLLATE utf8mb4_general_ci,
  `skills` text COLLATE utf8mb4_general_ci,
  `nic` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `batch_no` int DEFAULT NULL,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `special_alumni` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `display_name`, `gender`, `profile_image`, `bio`, `skills`, `nic`, `batch_no`, `student_id`, `created_at`, `special_alumni`) VALUES
(1, 'Sunali Perera', '2023cs001@stu.ucsc.cmb.ac.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'undergrad', 'Sunali', 'female', 'real2.png', 'Hi! I\'m Sunali P', '', '2003012212204', 21, '2023cs078', '2025-08-28 06:11:54', 0),
(2, 'Isum', 'isum@gmail.com', '$2y$10$sjd7/FcQvfwDDNsZKGnK6OkJTnf/Zni8f/ZCRqtZrIyYWsQUDiN0G', 'alumni', 'Isum', NULL, 'isumreal.png', '--Sample Bio--', '[\"MOBILE\",\"DATA\",\"CLOUD\"]', '23456789', 12, NULL, '2025-08-28 08:55:11', 1),
(3, 'System Administrator', 'admin@gradlink.com', 'admin@123', 'admin', NULL, NULL, 'default.jpg', NULL, NULL, NULL, NULL, NULL, '2025-09-04 06:14:49', 0),
(4, 'Sandaru Ruwaneka', '2023cs135@stu.ucsc.cmb.ac.lk', '$2y$10$ToXnEz6H7H7iCbdmhBbGSeram39PXMgjSdeWCQrlgs.jWWaQcVXcq', 'undergrad', 'Ruwaneka', 'male', 'chickenreal.png', 'Computer science UG at UCSC', '[\"DB\"]', NULL, 22, '2023/cs/134', '2025-10-22 02:59:38', 0),
(14, 'Dimath Jayasinghe', '2023cs071@stu.ucsc.cmb.ac.lk', '$2y$10$.Ww6bvsB9fkMVzZjwm8P8.c2x.ic8CDAzULuo91jJwqQI12HU8W9m', 'undergrad', 'Dimath', 'male', '14_1761152578.jpg', '2dn yr undergrad', '[\"AI\"]', NULL, 21, '2023/cs/071', '2025-10-22 17:02:58', 0),
(15, 'Ashera', '2023cs138@stu.ucsc.cmb.ac.lk', '$2y$10$5.MSVXLO6N/ghEk50NQ7R.EEBfFHzbuGJpriYvolHKI2lYkHlgLIm', 'undergrad', 'Ashera Perera', 'female', '15_1761188753.jpg', 'CS undergraduate', '[\"WEB\"]', NULL, 21, '2023/cs/138', '2025-10-23 03:05:53', 0),
(16, 'Kaveen Amarasekara', '2023cs006@stu.ucsc.cmb.ac.lk', '$2y$10$hwMMZP1LlMt8kzUNFYQP/em7WIkeyTVGJchOr3L20OPGGlTpD1KKe', 'undergrad', 'Kaveen2003', 'male', '16_1761188847.jpg', '', '[\"ML\",\"AI\",\"WEB\"]', NULL, 21, '2023/CS/006', '2025-10-23 03:07:27', 0),
(17, 'mohomed', '2023cs112@stu.ucsc.cmb.ac.lk', '$2y$10$xI8mQYCrXC6qmdcVKIYioOBqYA1QY8KQ05NQBMNMihc6/8H5Lm4u2', 'undergrad', 'mohomed', 'male', '17_1761191982.jpg', '#cyber security \r\n#networking\r\n#quantum computing', '[\"CYBER\"]', NULL, 21, '2023/cs/112', '2025-10-23 03:59:42', 0);

CREATE TABLE `user_profiles_visibility` (
  `user_id` int NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_profiles_visibility` (`user_id`, `is_public`) VALUES
(14, 0),
(15, 0);


ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_issued` (`user_id`,`issued_date`);

ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_user` (`user_id`),
  ADD KEY `post_id` (`post_id`,`created_at`);

ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_slug` (`slug`),
  ADD KEY `idx_start` (`start_datetime`),
  ADD KEY `idx_end` (`end_datetime`),
  ADD KEY `idx_organizer` (`organizer_id`);

ALTER TABLE `event_attendees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_event_user` (`event_id`,`user_id`),
  ADD KEY `idx_event` (`event_id`),
  ADD KEY `idx_user` (`user_id`);

ALTER TABLE `event_bookmarks`
  ADD PRIMARY KEY (`user_id`,`event_id`),
  ADD KEY `event_bookmarks_fk_event` (`event_id`);

ALTER TABLE `event_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_series` (`series_id`);

ALTER TABLE `event_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`);

ALTER TABLE `event_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_post_id` (`post_id`);

ALTER TABLE `event_series`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `event_tags`
  ADD PRIMARY KEY (`event_id`,`tag_id`),
  ADD KEY `idx_tag` (`tag_id`);

ALTER TABLE `followers`
  ADD PRIMARY KEY (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`);

ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`),
  ADD KEY `created_at` (`created_at`);

ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`post_id`,`user_id`),
  ADD KEY `fk_post_likes_user` (`user_id`);

ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_tag_name` (`name`);

ALTER TABLE `unregisted_alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user_profiles_visibility`
  ADD PRIMARY KEY (`user_id`);


ALTER TABLE `certificates`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `comments`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

ALTER TABLE `event_attendees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `event_exceptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `event_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

ALTER TABLE `event_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `event_series`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `messages`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

ALTER TABLE `posts`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `unregisted_alumni`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;


ALTER TABLE `certificates`
  ADD CONSTRAINT `fk_certificates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `events`
  ADD CONSTRAINT `events_fk_organizer` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_attendees`
  ADD CONSTRAINT `event_attendees_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_attendees_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_bookmarks`
  ADD CONSTRAINT `event_bookmarks_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_bookmarks_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_exceptions`
  ADD CONSTRAINT `event_exceptions_fk_series` FOREIGN KEY (`series_id`) REFERENCES `event_series` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_images`
  ADD CONSTRAINT `event_images_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_requests`
  ADD CONSTRAINT `event_requests_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_requests_fk_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_requests_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `event_tags`
  ADD CONSTRAINT `event_tags_fk_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_tags_fk_tag` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `post_likes`
  ADD CONSTRAINT `fk_post_likes_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_likes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_profiles_visibility`
  ADD CONSTRAINT `user_profiles_visibility_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
