-- =====================================================================
-- Custom Type Master Pro - Database Schema & Initial Data
-- Database Name: custom_type_master_pro
-- Compatible with MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- Character Set: utf8mb4 / utf8mb4_unicode_ci
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `custom_type_master_pro` 
  DEFAULT CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `custom_type_master_pro`;

-- ---------------------------------------------------------------------
-- 1. Table: users
-- Stores registered accounts, authentication hashes, and roles
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `typing_results`;
DROP TABLE IF EXISTS `user_settings`;
DROP TABLE IF EXISTS `typing_passages`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. Table: user_settings
-- Stores customizable themes, fonts, colors, sound, and typing preferences
-- ---------------------------------------------------------------------
CREATE TABLE `user_settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL UNIQUE,
  `theme` VARCHAR(50) NOT NULL DEFAULT 'orange',
  `font_family` VARCHAR(60) NOT NULL DEFAULT 'JetBrains Mono',
  `font_size` INT UNSIGNED NOT NULL DEFAULT 22,
  `line_height` DECIMAL(3,1) NOT NULL DEFAULT 1.8,
  `caret_style` ENUM('line', 'block', 'underline') NOT NULL DEFAULT 'line',
  `caret_color` VARCHAR(25) NOT NULL DEFAULT '#ff6a00',
  `correct_color` VARCHAR(25) NOT NULL DEFAULT '#4ade80',
  `incorrect_color` VARCHAR(25) NOT NULL DEFAULT '#f87171',
  `typing_area_width` VARCHAR(20) NOT NULL DEFAULT '920px',
  `sound_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `sound_theme` ENUM('mechanical', 'click', 'beep', 'silent') NOT NULL DEFAULT 'mechanical',
  `default_duration` INT UNSIGNED NOT NULL DEFAULT 60,
  `default_difficulty` ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'intermediate',
  `show_live_wpm` TINYINT(1) NOT NULL DEFAULT 1,
  `show_live_accuracy` TINYINT(1) NOT NULL DEFAULT 1,
  `show_live_word_count` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_settings_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. Table: typing_passages
-- Stores typing texts categorized by difficulty with pre-calculated metrics
-- ---------------------------------------------------------------------
CREATE TABLE `typing_passages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `content` TEXT NOT NULL,
  `difficulty` ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'intermediate',
  `category` VARCHAR(60) NOT NULL DEFAULT 'general',
  `word_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `char_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_difficulty` (`difficulty`),
  INDEX `idx_status` (`status`),
  INDEX `idx_category` (`category`),
  CONSTRAINT `fk_passages_author` FOREIGN KEY (`created_by`) 
    REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. Table: typing_results
-- Comprehensive typing session logs including real-time word count & accuracy metrics
-- ---------------------------------------------------------------------
CREATE TABLE `typing_results` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `passage_id` INT UNSIGNED DEFAULT NULL,
  `gross_wpm` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `net_wpm` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `accuracy` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `duration_seconds` INT UNSIGNED NOT NULL DEFAULT 60,
  `elapsed_seconds` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
  `words_typed` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_keystrokes` INT UNSIGNED NOT NULL DEFAULT 0,
  `correct_keystrokes` INT UNSIGNED NOT NULL DEFAULT 0,
  `error_keystrokes` INT UNSIGNED NOT NULL DEFAULT 0,
  `backspace_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `difficulty` ENUM('beginner', 'intermediate', 'advanced', 'custom') NOT NULL DEFAULT 'intermediate',
  `test_mode` ENUM('timed', 'passage', 'custom') NOT NULL DEFAULT 'timed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_results_user` (`user_id`),
  INDEX `idx_results_user_date` (`user_id`, `created_at`),
  INDEX `idx_results_net_wpm` (`net_wpm`),
  INDEX `idx_results_accuracy` (`accuracy`),
  CONSTRAINT `fk_results_user` FOREIGN KEY (`user_id`) 
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_results_passage` FOREIGN KEY (`passage_id`) 
    REFERENCES `typing_passages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SEED DATA
-- Default administrator account and rich practice passages
-- =====================================================================

-- Default Admin User:
-- Username: admin
-- Email: admin@typemaster.pro
-- Password: Admin@123456
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`) VALUES
(1, 'admin', 'admin@typemaster.pro', '$2y$10$K7UcCDyvlBrG3mBGWXBXQuEw/KWL/SGfwUv7bdJMRkWZrUOzminr6', 'Master Administrator', 'admin'),
(2, 'demouser', 'demo@typemaster.pro', '$2y$10$K7UcCDyvlBrG3mBGWXBXQuEw/KWL/SGfwUv7bdJMRkWZrUOzminr6', 'Demo Typist', 'user');

-- Default User Settings for Admin & Demo User
INSERT INTO `user_settings` (`user_id`, `theme`, `font_family`, `font_size`, `line_height`, `caret_style`, `caret_color`, `correct_color`, `incorrect_color`, `typing_area_width`, `sound_enabled`, `sound_theme`, `default_duration`, `default_difficulty`, `show_live_wpm`, `show_live_accuracy`, `show_live_word_count`) VALUES
(1, 'dark', 'JetBrains Mono', 22, 1.8, 'line', '#38bdf8', '#4ade80', '#f87171', '920px', 1, 'mechanical', 60, 'intermediate', 1, 1, 1),
(2, 'dark', 'JetBrains Mono', 22, 1.8, 'line', '#38bdf8', '#4ade80', '#f87171', '920px', 1, 'mechanical', 60, 'beginner', 1, 1, 1);

-- ---------------------------------------------------------------------
-- Seed Passages: Beginner, Intermediate, and Advanced
-- ---------------------------------------------------------------------

-- Beginner Passages (Simple vocabulary, smooth rhythm, low punctuation)
INSERT INTO `typing_passages` (`id`, `title`, `content`, `difficulty`, `category`, `word_count`, `char_count`, `created_by`, `status`) VALUES
(1, 'The Joy of Morning Walks', 
'A morning walk is a great way to start your day with energy. The fresh air clears your mind and gives you a calm feeling. Walking each day keeps your body strong and active. You can listen to the birds sing and watch the sun rise over the trees. It is simple, free, and brings peace to your busy life.',
'beginner', 'Lifestyle', 64, 305, 1, 'active'),

(2, 'The Little Coffee Shop', 
'Around the corner sits a cozy little coffee shop with warm lights and quiet music. People visit every day to read their favorite books or talk with good friends. The sweet smell of roasted beans fills the room. Sitting by the window while sipping warm tea brings a feeling of comfort and joy.',
'beginner', 'Stories', 57, 280, 1, 'active'),

(3, 'Learning Something New', 
'Every day gives us a fresh chance to learn something helpful. Whether you pick up a book, try a new hobby, or practice typing on a keyboard, your brain grows stronger. Small efforts made every day lead to big changes over time. Never be afraid to make mistakes because they help you grow.',
'beginner', 'Inspiration', 56, 281, 1, 'active'),

(4, 'The River by the Mountain', 
'Clean blue water runs down from the green hills into a calm river. Fish swim gently under the clear surface while bright flowers bloom along the banks. Taking time to sit by the river helps you relax and forget about your worries. Nature always has a quiet way of making us feel better.',
'beginner', 'Nature', 58, 282, 1, 'active');

-- Intermediate Passages (Natural prose, varied punctuation, technology, science)
INSERT INTO `typing_passages` (`id`, `title`, `content`, `difficulty`, `category`, `word_count`, `char_count`, `created_by`, `status`) VALUES
(5, 'The Evolution of Software Engineering', 
'Modern software development has transformed dramatically over the last few decades. What began as simple procedural routines executed on punch cards has evolved into globally distributed microservices and intelligent cloud architectures. Developers today must balance algorithmic efficiency, data security, user experience, and scalable design patterns. Consistent practice and deep understanding of foundational concepts remain the hallmarks of an exceptional programmer.',
'intermediate', 'Technology', 63, 467, 1, 'active'),

(6, 'The Architecture of the Modern Web', 
'The World Wide Web relies on a foundational set of open protocols and standards. When a user requests a URL, the browser initiates a DNS lookup, establishes a secure TLS connection, and issues an HTTP request. The server interprets the request, queries relational databases using optimized SQL statements, and returns dynamic HTML, CSS, and JavaScript. Understanding this full-stack lifecycle empowers developers to build responsive and resilient web applications.',
'intermediate', 'Technology', 68, 477, 1, 'active'),

(7, 'The Art of Focus in a Digital Era', 
'In an age of relentless digital notifications, the ability to cultivate deep focus is a rare superpower. When you deliberately eliminate distractions and commit your full cognitive energy to a single demanding task, your brain enters a state of flow. During flow, time seems to bend, complex challenges become manageable, and the quality of your output reaches unprecedented heights. Daily typing drills are an ideal way to train this disciplined concentration.',
'intermediate', 'Productivity', 72, 482, 1, 'active'),

(8, 'Scientific Discovery and Curious Minds', 
'Throughout human history, major breakthroughs in science and medicine were rarely accidents; they were the fruits of persistent curiosity and rigorous experimentation. From observing celestial orbits through early telescopes to sequencing the human genome with advanced computational algorithms, humanity has pushed the frontiers of what is possible. Cultivating curiosity within ourselves allows us to solve the most pressing challenges of tomorrow.',
'intermediate', 'Science', 64, 466, 1, 'active');

-- Advanced Passages (Technical vocabulary, punctuation, symbols, numbers, complex syntax)
INSERT INTO `typing_passages` (`id`, `title`, `content`, `difficulty`, `category`, `word_count`, `char_count`, `created_by`, `status`) VALUES
(9, 'Algorithmic Complexity & Big-O Notation', 
'When analyzing algorithmic performance, computer scientists utilize asymptotic Big-O notation, such as O(1), O(log n), O(n log n), and O(n^2). For instance, an in-place QuickSort algorithm achieves an average time complexity of O(n log n) with O(log n) auxiliary stack space; however, adversarial input sequences can degrade performance to O(n^2). Balancing time-space trade-offs, cache locality, and branch prediction is crucial when optimizing high-throughput production systems.',
'advanced', 'Computer Science', 67, 498, 1, 'active'),

(10, 'Database Concurrency & ACID Guarantees', 
'Relational database management systems (RDBMS) enforce the ACID paradigm: Atomicity, Consistency, Isolation, and Durability. Implementing rigorous multi-version concurrency control (MVCC) enables high-concurrency read-write transactions without blocking read operations. Database administrators must carefully configure isolation levels—such as READ COMMITTED vs. SERIALIZABLE—to prevent dirty reads, non-repeatable reads, and phantom row anomalies under heavy concurrent load (e.g., >10,000 queries/sec).',
'advanced', 'Databases', 63, 513, 1, 'active'),

(11, 'Asynchronous Event Loops & Non-Blocking I/O', 
'Under the hood, event-driven architectures rely on kernel-level multiplexing primitives like epoll() on Linux, kqueue() on BSD/macOS, and I/O Completion Ports (IOCP) on Windows. Rather than allocating a heavy thread per socket connection (1MB+ stack overhead), a single thread can monitor thousands of active file descriptors simultaneously. When network packets arrive, non-blocking callback queues trigger asynchronous resolution with sub-millisecond latency: latency <= 0.85ms!',
'advanced', 'System Architecture', 65, 499, 1, 'active'),

(12, 'Cryptographic Security & Modern Protocols', 
'Transport Layer Security (TLS 1.3) minimizes handshake latency by mandating a 1-RTT (or 0-RTT with PSK) exchange utilizing ephemeral Diffie-Hellman (ECDHE) keys over Curve25519. In conjunction with SHA-256/SHA-384 hashing and AES-256-GCM authenticated encryption, modern cipher suites ensure forward secrecy. Cryptographic integrity requires deterministic verification against timing side-channel vectors: never use variable-time memory comparisons for secure HMAC tokens!',
'advanced', 'Cybersecurity', 61, 494, 1, 'active'),

(13, 'CSS Variables & Design Tokens',
':root {\n    --header-height: 3.5rem;\n    --first-color: #ff5532;\n    --second-color: #575757;\n    --text-color: #111;\n    --body-color: #fff;\n    --shadow: rgba(0, 0, 0, 0.1) 0px 10px 50px;\n    --normal-font-size: .938rem;\n    --small-font-size: .813rem;\n    --smaller-font-size: .75rem;\n    --z-tooltip: 10;\n    --z-fixed: 100;\n}',
'advanced', 'CSS / Web Design', 32, 290, 1, 'active');


-- Sample Initial Results for Demo User
INSERT INTO `typing_results` 
(`user_id`, `passage_id`, `gross_wpm`, `net_wpm`, `accuracy`, `duration_seconds`, `elapsed_seconds`, `words_typed`, `total_keystrokes`, `correct_keystrokes`, `error_keystrokes`, `backspace_count`, `difficulty`, `test_mode`) VALUES
(1, 6, 76.50, 74.20, 97.45, 60, 60.00, 74, 382, 372, 10, 8, 'intermediate', 'timed'),
(1, 5, 82.40, 80.10, 98.20, 60, 60.00, 80, 412, 405, 7, 5, 'intermediate', 'timed'),
(1, 9, 68.30, 65.50, 96.10, 60, 60.00, 66, 341, 328, 13, 11, 'advanced', 'timed'),
(1, 1, 92.00, 90.50, 98.80, 60, 60.00, 91, 460, 455, 5, 3, 'beginner', 'timed'),
(2, 1, 52.00, 48.50, 93.80, 60, 60.00, 50, 260, 244, 16, 12, 'beginner', 'timed'),
(2, 2, 58.40, 55.20, 95.10, 60, 60.00, 56, 292, 278, 14, 9, 'beginner', 'timed');
