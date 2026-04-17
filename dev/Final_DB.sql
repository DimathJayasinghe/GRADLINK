CREATE DATABASE IF NOT EXISTS gl_db;
USE gl_db;

-- =========================
-- CORE USER TABLES
-- =========================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','alumni','undergrad') DEFAULT 'undergrad',
  display_name VARCHAR(100),
  gender ENUM('male','female'),
  profile_image VARCHAR(255) DEFAULT 'default.jpg',
  bio TEXT,
  skills TEXT,
  nic VARCHAR(20),
  batch_no INT,
  student_id VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  special_alumni TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE unregisted_alumni (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('alumni') DEFAULT 'alumni',
  display_name VARCHAR(100),
  gender ENUM('male','female'),
  profile_image VARCHAR(255) DEFAULT 'default.jpg',
  bio TEXT,
  explain_yourself TEXT,
  skills TEXT,
  nic VARCHAR(20),
  batch_no INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','approved','rejected') DEFAULT 'pending'
);

CREATE TABLE user_profiles_visibility (
  user_id INT PRIMARY KEY,
  is_public TINYINT(1) DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- SOCIAL FEATURES
-- =========================
CREATE TABLE followers (
  follower_id INT,
  followed_id INT,
  PRIMARY KEY (follower_id, followed_id),
  FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (followed_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE follow_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requester_id INT,
  target_id INT,
  status ENUM('pending','accepted','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY (requester_id, target_id),
  FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (target_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE posts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(512),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  post_id BIGINT,
  user_id INT,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_likes (
  post_id BIGINT,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (post_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE post_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id BIGINT NOT NULL,
  post_owner_id INT NOT NULL,
  reporter_id INT NOT NULL,
  category VARCHAR(120) NOT NULL,
  details TEXT,
  reference_link VARCHAR(255),
  status ENUM('pending','resolved','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (post_owner_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_post_reports_post_id (post_id),
  INDEX idx_post_reports_status (status),
  INDEX idx_post_reports_reporter_id (reporter_id)
);

CREATE TABLE messages (
  message_id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT,
  receiver_id INT,
  message_text TEXT,
  message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE message_unread_tracker (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_id INT NOT NULL,
  receiver_id INT NOT NULL,
  unread_count INT DEFAULT 0,
  last_message_id INT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_conversation (sender_id, receiver_id),
  FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (last_message_id) REFERENCES messages(message_id) ON DELETE SET NULL,
  INDEX idx_receiver_unread (receiver_id, unread_count)
);

CREATE TABLE notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  receiver_id INT,
  type VARCHAR(50),
  reference_id INT,
  content TEXT,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_notification_settings (
  user_id INT PRIMARY KEY,
  email_enabled TINYINT(1) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE email_jobs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  to_email VARCHAR(255) NOT NULL,
  to_name VARCHAR(255) NULL,
  subject VARCHAR(255) NOT NULL,
  html_body MEDIUMTEXT NOT NULL,
  plain_body TEXT NULL,
  status ENUM('pending','processing','failed') NOT NULL DEFAULT 'pending',
  attempts INT NOT NULL DEFAULT 0,
  reserved_at DATETIME NULL,
  last_error TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_email_jobs_status_id (status, id)
);

CREATE TABLE user_blocks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  blocker_id INT NOT NULL,
  blocked_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (blocker_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (blocked_id) REFERENCES users(id) ON DELETE CASCADE,

  UNIQUE (blocker_id, blocked_id)
);

CREATE TABLE user_security_settings (
  user_id INT PRIMARY KEY,
  is_public TINYINT(1) DEFAULT 1,
  two_factor_enabled TINYINT(1) DEFAULT 0,
  two_factor_method ENUM('app','sms') NULL,
  two_factor_phone VARCHAR(30) NULL,
  login_alerts_enabled TINYINT(1) DEFAULT 1,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- PROFILE DATA
-- =========================
CREATE TABLE certificates (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  name VARCHAR(255),
  issuer VARCHAR(255),
  issued_date DATE,
  certificate_file VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE projects (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  description TEXT,
  skills_used TEXT,
  start_date DATE,
  end_date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE work_experiences (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  position VARCHAR(255),
  company VARCHAR(255),
  period VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- EVENTS
-- =========================
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(191) UNIQUE,
  title VARCHAR(255),
  description TEXT,
  start_datetime DATETIME,
  end_datetime DATETIME,
  all_day TINYINT(1) DEFAULT 0,
  timezone VARCHAR(64) DEFAULT 'UTC',
  venue VARCHAR(255),
  capacity INT,
  organizer_id INT,
  status ENUM('draft','published','cancelled') DEFAULT 'published',
  visibility ENUM('public','private') DEFAULT 'public',
  series_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE event_attendees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  user_id INT,
  status ENUM('attending','maybe','not_attending') DEFAULT 'attending',
  guests SMALLINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY (event_id, user_id),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE event_bookmarks (
  user_id INT,
  event_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, event_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE event_series (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rrule TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_exceptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  series_id INT,
  exception_date DATE,
  FOREIGN KEY (series_id) REFERENCES event_series(id) ON DELETE CASCADE
);

CREATE TABLE event_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT,
  file_path VARCHAR(512),
  caption VARCHAR(255),
  is_primary TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE event_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  title VARCHAR(255),
  description TEXT,
  club_name VARCHAR(255),
  position VARCHAR(191),
  attachment_image VARCHAR(512),
  event_date DATE,
  event_time TIME,
  event_venue VARCHAR(255),
  status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  event_id INT,
  post_id BIGINT,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
);

CREATE TABLE tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) UNIQUE,
  slug VARCHAR(191)
);

CREATE TABLE event_tags (
  event_id INT,
  tag_id INT,
  PRIMARY KEY (event_id, tag_id),
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- =========================
-- SYSTEM / SUPPORT TABLES
-- =========================
CREATE TABLE access_logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  user_name VARCHAR(255),
  user_role VARCHAR(50),
  session_id VARCHAR(128),
  url VARCHAR(512),
  method VARCHAR(10),
  controller VARCHAR(100),
  action VARCHAR(100),
  ip_address VARCHAR(45),
  user_agent VARCHAR(512),
  referer VARCHAR(512),
  response_code SMALLINT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE email_otps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255),
  otp VARCHAR(6),
  purpose ENUM('signup','login'),
  expires_at DATETIME,
  is_used TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_activity (
  user_id INT NOT NULL,
  last_activity DATETIME NOT NULL,
  PRIMARY KEY (user_id),
  CONSTRAINT fk_user_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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

-- =========================
-- FUNDRAISING
-- =========================
CREATE TABLE fundraising_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  club_name VARCHAR(255),
  requester_position VARCHAR(255),
  requester_phone VARCHAR(20),
  title VARCHAR(255),
  headline VARCHAR(255),
  description TEXT,
  project_poster VARCHAR(512),
  goal_amount DECIMAL(15,2),
  collected_amount DECIMAL(15,2) DEFAULT 0,
  objective TEXT,
  start_date DATE,
  end_date DATE,
  fund_manager_name VARCHAR(255),
  fund_manager_contact VARCHAR(20),
  advisor_id INT,
  status ENUM('Pending','Approved','Rejected','Active','Completed','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE fundraising_bank_details (
  request_id INT PRIMARY KEY,
  bank_name VARCHAR(255),
  account_number VARCHAR(100),
  branch VARCHAR(255),
  account_holder VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES fundraising_requests(id) ON DELETE CASCADE
);

CREATE TABLE fundraising_donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT,
  donor_user_id INT,
  amount DECIMAL(15,2),
  transaction_reference VARCHAR(255),
  donor_name VARCHAR(255),
  donor_email VARCHAR(255),
  message TEXT,
  is_anonymous TINYINT(1) DEFAULT 0,
  status ENUM('Pending','Successful','Failed','Refunded') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES fundraising_requests(id) ON DELETE CASCADE,
  FOREIGN KEY (donor_user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE fundraising_team_members (
  request_id INT,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (request_id, user_id),
  FOREIGN KEY (request_id) REFERENCES fundraising_requests(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
