SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- dev/events_seed.sql
-- Seed data that matches the full DDL in dev/events.sql
-- IMPORTANT: organizer_id and user_id values reference your existing users table.
-- Please adjust user ids if necessary before importing.

-- -----------------------------
-- event_series
-- -----------------------------
INSERT INTO event_series (id, rrule, created_at) VALUES
(1, 'FREQ=WEEKLY;BYDAY=MO;INTERVAL=1', NOW()),
(2, 'FREQ=MONTHLY;BYDAY=FR;BYSETPOS=1', NOW());

-- -----------------------------
-- events (explicit ids for deterministic relations)
-- -----------------------------
INSERT INTO events (id, slug, title, description, start_datetime, end_datetime, all_day, timezone, venue, capacity, organizer_id, status, visibility, series_id, created_at)
VALUES
(1, 'ieee-agm-2025', 'IEEE CS Annual General Meeting 2025', 'Annual General Meeting for the IEEE CS Chapter: presentations, elections, and planning.', '2025-11-15 08:00:00', '2025-11-15 12:00:00', 0, 'Asia/Colombo', '@S104, Main Building', 200, 3, 'published', 'public', NULL, NOW()),
(2, 'tech-career-fair-2025', 'Tech Career Fair', 'Connect with top tech companies and explore internship and job opportunities.', '2025-10-05 10:00:00', '2025-10-05 16:00:00', 0, 'Asia/Colombo', 'UCSC Main Hall', 1000, 2, 'published', 'public', NULL, NOW()),
(3, 'research-symposium-2025', 'Research Symposium', 'Showcase your research work and connect with faculty members and industry experts.', '2025-09-25 09:00:00', '2025-09-25 17:00:00', 0, 'Asia/Colombo', 'UCSC Research Center', 300, 1, 'published', 'public', NULL, NOW()),
(4, 'hackathon-2025', 'Hackathon 2025', 'A 48-hour coding marathon to build innovative solutions for real-world problems.', '2025-11-10 09:00:00', '2025-11-12 09:00:00', 0, 'Asia/Colombo', 'UCSC Computer Labs', 500, 3, 'published', 'public', NULL, NOW()),
(5, 'alumni-industry-panel-2025', 'Alumni Industry Panel', 'Listen to successful alumni share their experiences and insights about the industry.', '2025-09-30 15:00:00', '2025-09-30 17:00:00', 0, 'Asia/Colombo', 'UCSC Conference Room', 200, 2, 'published', 'public', NULL, NOW()),
(6, 'drama-shakespeare-2025', 'Drama Club: Shakespeare Reimagined', 'Modern interpretations of classic Shakespeare plays performed by the Drama Club.', '2025-12-10 18:30:00', '2025-12-10 21:30:00', 0, 'Asia/Colombo', 'University Theater', 450, 10, 'published', 'public', NULL, NOW()),
(7, 'weekly-yoga-2025-10-06', 'Weekly Yoga — 06 Oct 2025', 'Weekly yoga session. Bring your own mat.', '2025-10-06 07:00:00', '2025-10-06 08:00:00', 0, 'Asia/Colombo', 'Sports Complex Studio', 40, 1, 'published', 'public', 1, NOW()),
(8, 'alumni-meet-oct-2025', 'Alumni Meetup — Oct 2025', 'Monthly alumni meetup with guest speaker and networking.', '2025-10-02 18:00:00', '2025-10-02 20:00:00', 0, 'Asia/Colombo', 'Alumni Lounge', 120, 2, 'published', 'public', 2, NOW());

-- -----------------------------
-- event_images
-- -----------------------------
INSERT INTO event_images (event_id, file_path, caption, is_primary, created_at) VALUES
(1, 'storage/posts/IEEE_CS_AGM_25.png', 'IEEE CS AGM 2025 poster', 1, NOW()),
(2, 'storage/posts/career-fair.jpg', 'Career Fair banner', 1, NOW()),
(3, 'storage/posts/research-symposium.jpg', 'Research Symposium image', 1, NOW()),
(4, 'storage/posts/hackathon.jpg', 'Hackathon poster', 1, NOW()),
(6, 'storage/posts/Drama_Performance.png', 'Drama performance poster', 1, NOW()),
(7, 'storage/posts/yoga_session.png', 'Weekly yoga session', 1, NOW()),
(8, 'storage/posts/alumni_oct_2025.png', 'Alumni meetup flyer', 1, NOW());

-- -----------------------------
-- tags
-- -----------------------------
INSERT INTO tags (id, name, slug) VALUES
(1, 'Workshop', 'workshop'),
(2, 'Alumni', 'alumni'),
(3, 'Performance', 'performance'),
(4, 'Career', 'career'),
(5, 'Wellness', 'wellness'),
(6, 'Community', 'community');

-- -----------------------------
-- event_tags
-- -----------------------------
INSERT INTO event_tags (event_id, tag_id) VALUES
(1, 2), -- IEEE AGM -> Alumni
(1, 6), -- IEEE AGM -> Community
(2, 4), -- Career Fair -> Career
(3, 1), -- Research Symposium -> Workshop
(4, 1), -- Hackathon -> Workshop
(6, 3), -- Drama -> Performance
(7, 5), -- Yoga -> Wellness
(8, 2); -- Alumni Meetup -> Alumni

-- -----------------------------
-- event_attendees
-- -----------------------------
INSERT INTO event_attendees (event_id, user_id, status, guests, created_at) VALUES
(1, 4, 'attending', 0, NOW()),
(1, 5, 'maybe', 0, NOW()),
(2, 6, 'attending', 2, NOW()),
(3, 7, 'attending', 0, NOW()),
(7, 8, 'attending', 0, NOW()),
(8, 9, 'maybe', 0, NOW());

-- -----------------------------
-- event_bookmarks
-- -----------------------------
INSERT INTO event_bookmarks (user_id, event_id, created_at) VALUES
(5, 1, NOW()),
(7, 2, NOW()),
(4, 3, NOW()),
(9, 7, NOW());

-- -----------------------------
-- event_exceptions (series exceptions)
-- -----------------------------
-- skip weekly yoga on 2025-10-13
INSERT INTO event_exceptions (series_id, exception_date) VALUES
(1, '2025-10-13'),
-- create a modified instance placeholder for alumni series in Dec (you may link to a new event)
(2, '2025-12-01');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

-- End of seed file.