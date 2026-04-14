-- -------------------------------------------------------------
-- -------------------------------------------------------------
-- TablePlus 1.4.0
--
-- https://tableplus.com/
--
-- Database: mysql
-- Generation Time: 2026-03-25 12:14:44.886729
-- -------------------------------------------------------------

-- Save current session settings and set optimal values for import
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0;
SET NAMES utf8mb4;

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `case_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `case_notes_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `case_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `case_type` enum('criminal','civil','family','corporate','land','other') NOT NULL,
  `status` enum('filed','under_investigation','hearing_scheduled','in_progress','closed','dismissed') NOT NULL DEFAULT 'filed',
  `description` text DEFAULT NULL,
  `lawyer_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `court_name` varchar(150) DEFAULT NULL,
  `judge_name` varchar(150) DEFAULT NULL,
  `filing_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `case_number` (`case_number`),
  KEY `lawyer_id` (`lawyer_id`),
  KEY `client_id` (`client_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`lawyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cases_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `doc_type` enum('evidence','affidavit','court_ruling','contract','petition','other') NOT NULL DEFAULT 'other',
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `hearings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hearing_date` date NOT NULL,
  `hearing_time` time DEFAULT NULL,
  `court_room` varchar(100) DEFAULT NULL,
  `court_name` varchar(150) DEFAULT NULL,
  `judge_name` varchar(150) DEFAULT NULL,
  `status` enum('scheduled','completed','postponed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `hearings_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hearings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('hearing','case_update','document','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lawyer','clerk','staff') NOT NULL DEFAULT 'clerk',
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `description`, `ip_address`, `created_at`) VALUES 
(1, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:09:29'),
(2, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:10:34'),
(3, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:18:43'),
(4, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:20:40'),
(5, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:38:49'),
(6, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:39:07'),
(7, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:39:36'),
(8, NULL, 'login_failed', 'users', NULL, 'Failed login for email: lawyer@legalcase.ug', '127.0.0.1', '2026-03-16 22:39:58'),
(9, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:46:32'),
(10, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:46:47'),
(11, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:53:56'),
(12, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@legalcase.ug', '127.0.0.1', '2026-03-16 22:55:17'),
(13, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-16 22:56:15'),
(14, 1, 'logout', 'users', 1, 'User logged out', '127.0.0.1', '2026-03-16 23:02:13'),
(15, NULL, 'login_failed', 'users', NULL, 'Failed login for email: lawyer@legalcase.ug', '127.0.0.1', '2026-03-16 23:02:37'),
(16, 2, 'login', 'users', 2, 'User logged in', '127.0.0.1', '2026-03-16 23:03:07'),
(17, 2, 'logout', 'users', 2, 'User logged out', '127.0.0.1', '2026-03-16 23:03:42'),
(18, 3, 'login', 'users', 3, 'User logged in', '127.0.0.1', '2026-03-16 23:04:06'),
(19, 3, 'client_created', 'clients', 1, '', '127.0.0.1', '2026-03-16 23:05:57'),
(20, 3, 'case_created', 'cases', 1, 'Case LC-2026-0001 created', '127.0.0.1', '2026-03-16 23:07:01'),
(21, 3, 'document_uploaded', 'documents', 1, '', '127.0.0.1', '2026-03-16 23:07:38'),
(22, 3, 'logout', 'users', 3, 'User logged out', '127.0.0.1', '2026-03-16 23:16:24'),
(23, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-16 23:16:56'),
(24, 1, 'case_updated', 'cases', 1, 'Case #1 updated', '127.0.0.1', '2026-03-16 23:20:55'),
(25, 1, 'hearing_scheduled', 'hearings', 1, '', '127.0.0.1', '2026-03-16 23:22:18'),
(26, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-16 23:40:38'),
(27, 1, 'logout', 'users', 1, 'User logged out', '127.0.0.1', '2026-03-16 23:45:41'),
(28, NULL, 'login_failed', 'users', NULL, 'Failed login for email: lawyer@legalcase.ug', '127.0.0.1', '2026-03-16 23:46:06'),
(29, 2, 'login', 'users', 2, 'User logged in', '127.0.0.1', '2026-03-16 23:46:36'),
(30, 2, 'case_updated', 'cases', 1, 'Case #1 updated', '127.0.0.1', '2026-03-16 23:47:11'),
(31, 2, 'logout', 'users', 2, 'User logged out', '127.0.0.1', '2026-03-16 23:47:35'),
(32, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-16 23:48:00'),
(33, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-17 10:42:22'),
(34, 1, 'user_created', 'users', NULL, 'User admin@oiltyrepro.com created', '127.0.0.1', '2026-03-17 10:44:51'),
(35, 1, 'logout', 'users', 1, 'User logged out', '127.0.0.1', '2026-03-17 10:45:11'),
(36, NULL, 'login_failed', 'users', NULL, 'Failed login for email: admin@oiltyrepro.com', '127.0.0.1', '2026-03-17 10:45:39'),
(37, 1, 'login', 'users', 1, 'User logged in', '127.0.0.1', '2026-03-17 10:48:32'),
(38, 1, 'user_updated', 'users', 4, '', '127.0.0.1', '2026-03-17 10:49:10'),
(39, 1, 'logout', 'users', 1, 'User logged out', '127.0.0.1', '2026-03-17 10:49:18'),
(40, 4, 'login', 'users', 4, 'User logged in', '127.0.0.1', '2026-03-17 10:49:32');

INSERT INTO `cases` (`id`, `case_number`, `title`, `case_type`, `status`, `description`, `lawyer_id`, `client_id`, `court_name`, `judge_name`, `filing_date`, `closing_date`, `created_by`, `created_at`, `updated_at`) VALUES (1, 'LC-2026-0001', 'Family abuse', 'family', 'hearing_scheduled', 'fgrgjkerhuir hriuhi', 2, 1, 'High Court', 'Mugwanya timothy', '2026-03-16', NULL, 3, '2026-03-16 23:07:01', '2026-03-16 23:47:11');

INSERT INTO `clients` (`id`, `full_name`, `email`, `phone`, `address`, `id_number`, `date_of_birth`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES (1, 'Munygwa John', 'pdfj@hn.com', '+256707492545', 'sssssssssg', '', NULL, 'hghghgh', 3, '2026-03-16 23:05:57', '2026-03-16 23:05:57');

INSERT INTO `documents` (`id`, `case_id`, `title`, `doc_type`, `file_name`, `file_path`, `file_size`, `mime_type`, `uploaded_by`, `created_at`) VALUES (1, 1, 'kdhgjhg', 'affidavit', 'DOC-20260101-WA0054 (2).docx', 'uploads/1/doc_69b8630ad82c46.01274311.docx', 870866, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 3, '2026-03-16 23:07:38');

INSERT INTO `hearings` (`id`, `case_id`, `title`, `hearing_date`, `hearing_time`, `court_room`, `court_name`, `judge_name`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES (1, 1, 'Bail application', '2026-03-24', '00:00:00', 'Room 3A', 'High Court', 'Mugwanya timothy', 'scheduled', 'vyrtyty', 1, '2026-03-16 23:22:17', '2026-03-16 23:22:17');

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `link`, `created_at`) VALUES (1, 1, 'Case Status Changed', 'Case LC-2026-0001 status changed from Under Investigation to Hearing Scheduled.', 'case_update', 0, 'http://localhost:8080/cases/1', '2026-03-16 23:47:11');

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES 
(1, 'System Administrator', 'admin@legalcase.ug', '$2y$10$JKen2E1wMqqTu/5..zsyGOHwi2WcNMRHTqIfUH5CXN2zhc.vOPHPi', 'admin', NULL, 1, '2026-03-16 21:57:04', '2026-03-16 22:53:31'),
(2, 'Jane Nakato', 'lawyer@legalcase.ug', '$2y$10$JWdk8IwHNJBEIs2YwkJjs.sMYRZDP0mDeyU/omuNPOkdEg8aWSgdG', 'lawyer', NULL, 1, '2026-03-16 21:57:04', '2026-03-16 22:53:32'),
(3, 'Paul Ssemakula', 'clerk@legalcase.ug', '$2y$10$SXk5Sb6xydoZOQnsno833uAryiRW2enEUIrK3ULF1XaDOzjcoLpim', 'clerk', NULL, 1, '2026-03-16 21:57:04', '2026-03-16 22:53:34'),
(4, 'Kalyango Elvis', 'admin@oiltyrepro.com', '$2y$10$APkyUjXjNYg/q54r/Wk.u.ymL6qhJJDPLJt8DwPJvj8UrTB3N7BmG', 'lawyer', '0707492500', 1, '2026-03-17 10:44:50', '2026-03-17 10:49:09');


-- Restore original session settings
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET SQL_MODE=@OLD_SQL_MODE;
SET SQL_NOTES=@OLD_SQL_NOTES;
