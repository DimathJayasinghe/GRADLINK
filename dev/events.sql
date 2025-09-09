-- Event table definition for explore feature
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `organizer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample event data
INSERT INTO `events` (`title`, `description`, `event_date`, `location`, `image`, `organizer_id`) VALUES
('UCSC Alumni Meetup 2025', 'Join us for the annual alumni meetup where you can network with fellow graduates and explore new opportunities.', '2025-09-15 18:00:00', 'UCSC Auditorium', 'alumni-meetup.jpg', 1),
('Tech Career Fair', 'Connect with top tech companies and explore internship and job opportunities in the tech industry.', '2025-10-05 10:00:00', 'UCSC Main Hall', 'career-fair.jpg', 2),
('Research Symposium', 'Showcase your research work and connect with faculty members and industry experts.', '2025-09-25 09:00:00', 'UCSC Research Center', 'research-symposium.jpg', 1),
('Hackathon 2025', 'A 48-hour coding marathon to build innovative solutions for real-world problems.', '2025-11-10 09:00:00', 'UCSC Computer Labs', 'hackathon.jpg', 3),
('Alumni Industry Panel', 'Listen to successful alumni share their experiences and insights about the industry.', '2025-09-30 15:00:00', 'UCSC Conference Room', 'industry-panel.jpg', 2);
