-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 27, 2025 at 12:44 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nexusbank`
--

-- --------------------------------------------------------

--
-- First create the independent tables
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `age` int NOT NULL,
  `birth_year` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `occupation` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `investment_plans`;
CREATE TABLE IF NOT EXISTS `investment_plans` (
  `plan_id` int NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration_months` int NOT NULL,
  `min_amount` decimal(12,2) NOT NULL,
  `risk_level` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `max_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') DEFAULT 'new',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `otp_verification`;
CREATE TABLE IF NOT EXISTS `otp_verification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  CONSTRAINT `otp_verification_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Then create tables that depend on users
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `account_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `balance` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `balance`;
CREATE TABLE IF NOT EXISTS `balance` (
  `balance_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `total_balance` decimal(12,2) NOT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `investments`;
CREATE TABLE IF NOT EXISTS `investments` (
  `investment_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `plan_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `duration_months` int NOT NULL DEFAULT '12',
  `status` enum('active','matured','withdrawn') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `matured_at` datetime DEFAULT NULL,
  `plan_id` int DEFAULT NULL,
  `withdrawn_at` datetime DEFAULT NULL,
  PRIMARY KEY (`investment_id`),
  KEY `user_id` (`user_id`),
  KEY `plan_id` (`plan_id`),
  CONSTRAINT `investments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `investments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `investment_plans` (`plan_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `loans`;
CREATE TABLE IF NOT EXISTS `loans` (
  `loan_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int NOT NULL,
  `status` enum('pending','approved','rejected','paid') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `approved_at` datetime DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_paid` enum('yes','no') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no',
  `total_due` decimal(12,2) NOT NULL DEFAULT '0.00',
  `penalty_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`loan_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `login_records`;
CREATE TABLE IF NOT EXISTS `login_records` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('success','failed') COLLATE utf8mb4_general_ci NOT NULL,
  `login_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `login_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `login_verifications`;
CREATE TABLE IF NOT EXISTS `login_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `status` enum('success','failed') COLLATE utf8mb4_general_ci DEFAULT 'success',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `login_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Then create tables that depend on other tables
--

DROP TABLE IF EXISTS `loan_history`;
CREATE TABLE IF NOT EXISTS `loan_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`history_id`),
  KEY `loan_id` (`loan_id`),
  CONSTRAINT `loan_history_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `type` enum('deposit','withdrawal','transfer_in','transfer_out','loanpayment') COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `related_account_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `account_id` (`account_id`),
  KEY `related_account_id` (`related_account_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`related_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `age`, `birth_year`, `email`, `address`, `occupation`, `phone`, `password_hash`, `created_at`, `is_admin`, `status`, `is_active`, `reset_token`, `reset_expires_at`, `profile_picture`) VALUES
(1, 'Shaison', 24, 2000, 'shaison61@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:41:45', 1, 'approved', 1, NULL, NULL, 'default.jpg'),
(2, 'Amiguel', 24, 2000, 'amiguelll0513@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg'),
(3, 'Shaison2', 24, 2000, 'shaison62@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg'),
(4, 'Isabel', 24, 2000, 'senioritaisabel@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 1, 'approved', 1, NULL, NULL, 'default.jpg'),
(5, 'Rayson', 24, 2000, 'ramosrayson84@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg'),
(6, 'Paulo', 24, 2000, 'paulpaolomamugay6@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 1, 'approved', 1, NULL, NULL, 'default.jpg'),
(7, 'LSPU Student 1', 24, 2000, '0323-4199@lspu.edu.ph', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg'),
(8, 'LSPU Student 2', 24, 2000, '0323-3883@lspu.edu.ph', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg'),
(32, 'Renz', 0, 0, 'renz.shaison@gmail.com', '', '', '', '$2y$10$b4SAB4j.yrr2VLBm71w6V.g7LWB4lheE3.y5JucGdzp5h/DGJ36k2', '2025-04-16 06:16:01', 1, 'approved', 1, NULL, NULL, NULL),
(33, 'Renz', 21, 2004, 'renz.ramos@gmail.com', '', '', '', '$2y$10$68uffF/bsMZuW1v9M6aYqeqQCC.1x5n7i6T/.dzdAklxqtVQAIFFu', '2025-04-16 08:32:24', 0, 'approved', 1, 'f2c602c8e75aaf5844f33c31f6c4f4faf186efce910d46e76950fe0c5101b086', '2025-05-11 22:45:05', NULL),
(19, 'meow', 20, 2005, 'meow.amiguel@gmail.com', 'batangas', 'tralela', '13242698810', '$2y$10$oInacoL.PERFbh41f63Jz.V6eTE0cmh6fqVPSQWQEv81C2EkAijYK', '2025-04-23 16:32:58', 1, 'approved', 1, 'bbc25abed5b104df2039022e8c8b463e26d84394ddd26d932529f0fb2059d899', '2025-04-25 16:42:31', NULL),
(24, 'tutoy', 21, 2004, 'tutoy.lspu@gmail.com', 'dyaan', 'nyt2', '12345745901', '$2y$10$14jSMq0o01Ujt.JQqfZ2HuwxhAXfI2G3ryuZ2R0fcAmOZ4ZID0YH.', '2025-04-24 14:34:45', 0, 'approved', 1, NULL, NULL, 'profile_24_1747565246.png'),
(26, 'User 26', 0, 0, 'user26@example.com', '', '', '', '$2y$10$default_hash', '2025-04-29 10:55:03', 0, 'approved', 1, NULL, NULL, NULL),
(27, 'Irene Nicole', 25, 2000, 'irene.seniorita@gmail.com', 'tiaong,quezon', 'okay', '09516025282', '$2y$10$xevT9AO0JWr3wMtcBWDMdOiyvvkoMXY9hhWy76jGggpBfNOUYsAQi', '2025-05-02 09:22:00', 1, 'approved', 1, NULL, NULL, NULL),
(28, 'Paul Paolo A. Mamugay', 19, 2005, 'paul.mamugay@gmail.com', 'No.040', 'Student', '09217844447', '$2y$10$NRCOZht.DPOjB2THNee9/ejPWHBAjmviLPYgJkwqBRRuqVkKwGiRG', '2025-05-05 11:49:44', 1, 'approved', 1, NULL, NULL, NULL),
(29, 'Pamela A. Mamugay', 19, 2005, 'pamela.lspu@gmail.com', 'No.040', 'Student', '09217844447', '$2y$10$1GejzUcXpSD69ohd34Vpfeayn2TwPnoB6rhGVIQnoMJRPkVU843de', '2025-05-05 11:52:49', 0, 'approved', 1, NULL, NULL, 'profile_29_1746959477.jpg'),
(30, 'Poleene', 19, 2005, 'paolomamugay5@gmail.com', '76', 'ghj', '09217844447', '$2y$10$ZxCX5e6OO8OSQ4jLn1dcV.NSUky8Mk9aK3rvhjkLZDOfKhLspdn7S', '2025-05-05 11:56:06', 0, 'approved', 1, NULL, NULL, NULL),
(31, 'Renz', 20, 2004, 'renz.shaison2@gmail.com', 'brgyawd adwdas', 'wala lang', '09300674760', '$2y$10$hZkS2tAKpeOVR2EWFXvqP.oceH93Hms0XmGbwAATfN48fPESBb6Oq', '2025-05-12 12:01:40', 0, 'approved', 1, NULL, NULL, NULL);

--
-- Dumping data for table `investment_plans`
--

INSERT INTO `investment_plans` (`plan_id`, `plan_name`, `interest_rate`, `duration_months`, `min_amount`, `risk_level`, `max_amount`) VALUES
(1, 'Starter Plan', 3.50, 6, 500.00, 'Low', 5000.00),
(2, 'Balanced Growth', 5.00, 12, 1000.00, 'Medium', 10000.00),
(3, 'Aggressive Growth', 7.00, 24, 2500.00, 'High', 25000.00),
(4, 'High Yield Bond', 9.00, 36, 5000.00, 'High', 100000.00);

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(1, 'Renz', 'ramosrayson84@gmail.com', 'Renz', 'hahahha', '2025-05-20 09:55:47', 'replied');

--
-- Dumping data for table `otp_verification`
--

INSERT INTO `otp_verification` (`id`, `email`, `otp`, `created_at`, `expires_at`, `is_used`) VALUES
(1, 'shaison61@gmail.com', '808857', '2025-05-27 11:51:42', '2025-05-27 03:56:42', 1),
(2, '0323-4199@lspu.edu.ph', '966304', '2025-04-23 15:25:43', '2025-04-23 22:30:43', 0),
(3, 'amiguelll0513@gmail.com', '046422', '2025-05-18 09:51:44', '2025-05-18 16:56:44', 1),
(4, '0323-4199@lspu.edu.ph', '097192', '2025-05-18 12:14:53', '2025-05-18 19:19:53', 1),
(5, 'shaison62@gmail.com', '638497', '2025-05-15 05:50:13', '2025-05-14 21:55:13', 1),
(6, 'shaison61@gmail.com', '194789', '2025-05-01 02:31:44', '2025-04-30 18:36:44', 0),
(7, 'senioritaisabel@gmail.com', '686762', '2025-05-02 09:22:47', '2025-05-02 01:27:47', 1),
(8, 'ramosrayson84@gmail.com', '785043', '2025-05-27 12:33:32', '2025-05-27 04:38:32', 1),
(9, 'paulpaolomamugay6@gmail.com', '597313', '2025-05-16 16:49:09', '2025-05-16 10:54:09', 1),
(10, '0323-3883@lspu.edu.ph', '919734', '2025-05-17 02:30:16', '2025-05-16 20:35:16', 1),
(11, 'paulpaolomamugay6@gmail.com', '522697', '2025-05-08 14:11:54', '2025-05-08 08:16:54', 1);

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `user_id`, `account_number`, `balance`, `created_at`) VALUES
(1, 1, 'SB90284168', 500500.97, '2025-04-16 14:16:34'),
(2, 2, 'SB50491031', 105656.98, '2025-04-16 16:33:52'),
(7, 3, 'SB99139149', 100412.00, '2025-04-24 23:09:40'),
(8, 4, 'SB53061920', 999400.00, '2025-04-24 23:09:40'),
(11, 5, 'SB61285649', 89910.00, '2025-05-02 17:22:20'),
(12, 6, 'SB30481356', 1587.49, '2025-05-05 19:52:55'),
(13, 7, 'SB16865613', 540.00, '2025-05-05 19:56:20'),
(14, 8, 'SB49600110', 81000.00, '2025-05-12 20:01:52');

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`balance_id`, `user_id`, `full_name`, `total_balance`, `last_updated`) VALUES
(3, 7, 'LSPU Student 1', 840.00, '2025-05-05 23:16:22'),
(22, 6, 'Paulo', 10.00, '2025-05-06 19:58:02'),
(23, 6, 'Paulo', 60.00, '2025-05-06 19:58:36'),
(24, 6, 'Paulo', 30.00, '2025-05-06 19:58:41'),
(25, 6, 'Paulo', 100.00, '2025-05-06 19:58:48'),
(26, 6, 'Paulo', 0.00, '2025-05-06 19:58:53'),
(27, 6, 'Paulo', 90.00, '2025-05-06 19:59:00'),
(28, 6, 'Paulo', 146.00, '2025-05-06 20:02:40'),
(29, 6, 'Paulo', 24.00, '2025-05-06 20:03:31'),
(30, 6, 'Paulo', 34.00, '2025-05-06 20:06:23'),
(31, 6, 'Paulo', 54.00, '2025-05-06 20:10:37'),
(32, 6, 'Paulo', 88.00, '2025-05-06 20:12:14'),
(33, 6, 'Paulo', 118.00, '2025-05-06 23:36:11'),
(34, 6, 'Paulo', 142.00, '2025-05-07 10:54:32'),
(35, 6, 'Paulo', 112.00, '2025-05-07 12:04:27'),
(36, 6, 'Paulo', 62.00, '2025-05-07 12:04:35'),
(37, 6, 'Paulo', 1062.00, '2025-05-07 12:07:11'),
(38, 6, 'Paulo', 717.00, '2025-05-08 12:34:07'),
(39, 6, 'Paulo', 740.00, '2025-05-08 14:29:05'),
(40, 6, 'Paulo', 490.00, '2025-05-08 15:43:13'),
(41, 6, 'Paulo', 540.00, '2025-05-08 17:19:17'),
(42, 7, 'LSPU Student 1', 1040.00, '2025-05-08 22:13:12'),
(43, 7, 'LSPU Student 1', 40.00, '2025-05-08 22:14:30'),
(44, 6, 'Paulo', 563.00, '2025-05-09 13:25:36'),
(45, 6, 'Paulo', 563.48, '2025-05-09 13:26:04'),
(46, 6, 'Paulo', 1063.48, '2025-05-09 13:26:40'),
(47, 6, 'Paulo', 1063.49, '2025-05-09 13:41:57'),
(48, 6, 'Paulo', 1068.49, '2025-05-09 13:42:15'),
(49, 6, 'Paulo', 1000.49, '2025-05-09 13:51:29'),
(50, 6, 'Paulo', 500.49, '2025-05-09 14:15:14'),
(51, 7, 'LSPU Student 1', 540.00, '2025-05-09 14:15:14'),
(52, 6, 'Paulo', 1500.49, '2025-05-09 15:35:47'),
(53, 6, 'Paulo', 2500.49, '2025-05-09 15:36:11'),
(54, 6, 'Paulo', 0.49, '2025-05-09 15:36:30'),
(55, 6, 'Paulo', 500.49, '2025-05-09 15:48:35'),
(56, 6, 'Paulo', 1287.49, '2025-05-09 15:48:41'),
(57, 6, 'Paulo', 1787.49, '2025-05-10 19:28:28'),
(58, 6, 'Paulo', 1187.49, '2025-05-10 23:46:31'),
(59, 6, 'Paulo', 1287.49, '2025-05-11 17:49:33'),
(60, 6, 'Paulo', 1387.49, '2025-05-11 18:02:08'),
(61, 2, 'Amiguel', 144206.98, '2025-05-11 21:42:12'),
(62, 2, 'Amiguel', 145206.98, '2025-05-12 20:27:56'),
(63, 2, 'Amiguel', 144206.98, '2025-05-15 13:23:28'),
(64, 2, 'Amiguel', 44206.98, '2025-05-15 13:26:26'),
(65, 8, 'LSPU Student 2', 100000.00, '2025-05-15 13:26:26'),
(66, 8, 'LSPU Student 2', 101000.00, '2025-05-15 13:29:30'),
(67, 8, 'LSPU Student 2', 91000.00, '2025-05-15 13:44:30'),
(68, 2, 'Amiguel', 54206.98, '2025-05-15 13:44:30'),
(69, 8, 'LSPU Student 2', 81000.00, '2025-05-15 13:50:31'),
(70, 2, 'Amiguel', 64206.98, '2025-05-15 13:50:31'),
(71, 6, 'Paulo', 1587.49, '2025-05-16 23:33:57'),
(72, 4, 'Isabel', 2050865.00, '2025-05-17 21:30:26'),
(73, 4, 'Isabel', 2052865.00, '2025-05-17 23:15:00'),
(74, 4, 'Isabel', 2000000.00, '2025-05-17 23:15:33'),
(75, 4, 'Isabel', 1900000.00, '2025-05-17 23:17:28'),
(76, 1, 'Shaison', 100500.00, '2025-05-17 23:17:28'),
(101, 4, 'Isabel', 1468999.97, '2025-05-18 03:13:58'),
(102, 4, 'Isabel', 1466999.97, '2025-05-18 03:14:19'),
(103, 4, 'Isabel', 1486999.97, '2025-05-18 03:15:19'),
(104, 4, 'Isabel', 1484999.97, '2025-05-18 03:15:56'),
(105, 4, 'Isabel', 1400000.97, '2025-05-18 03:20:36'),
(106, 4, 'Isabel', 1000000.00, '2025-05-18 03:21:49'),
(107, 1, 'Shaison', 500500.97, '2025-05-18 03:21:49'),
(108, 4, 'Isabel', 999400.00, '2025-05-18 05:15:57'),
(109, 2, 'Amiguel', 65206.98, '2025-05-20 16:10:51'),
(110, 2, 'Amiguel', 66206.98, '2025-05-20 16:17:15'),
(111, 2, 'Amiguel', 56206.98, '2025-05-20 16:32:34'),
(112, 2, 'Amiguel', 106206.98, '2025-05-27 19:56:22'),
(113, 2, 'Amiguel', 107206.98, '2025-05-27 19:57:28'),
(114, 2, 'Amiguel', 117206.98, '2025-05-27 20:02:52'),
(115, 2, 'Amiguel', 106706.98, '2025-05-27 20:07:54'),
(116, 2, 'Amiguel', 105656.98, '2025-05-27 20:08:06');

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `user_id`, `plan_name`, `amount`, `interest_rate`, `duration_months`, `status`, `created_at`, `matured_at`, `plan_id`, `withdrawn_at`) VALUES
(1, 5, '', 1000.00, 0.00, 0, 'withdrawn', '2025-05-01 10:14:36', '2025-05-01 10:22:52', 1, NULL),
(2, 5, '', 500.00, 0.00, 0, 'matured', '2025-05-02 16:56:06', NULL, 1, '2025-05-02 17:00:42'),
(3, 5, '', 500.00, 0.00, 0, 'active', '2025-05-02 17:02:11', NULL, 1, NULL),
(4, 6, '', 1000.00, 0.00, 0, 'active', '2025-05-05 20:10:32', NULL, 2, NULL),
(5, 7, '', 1000.00, 0.00, 0, 'active', '2025-05-08 22:14:30', NULL, 2, NULL),
(6, 6, '', 2500.00, 0.00, 0, 'active', '2025-05-09 15:36:30', NULL, 3, NULL),
(7, 4, NULL, 500.00, 0.00, 12, 'active', '2025-05-18 03:13:58', NULL, 1, NULL),
(8, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:14:19', NULL, 2, NULL),
(9, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:15:56', NULL, 2, NULL),
(10, 4, NULL, 600.00, 0.00, 12, 'active', '2025-05-18 05:15:57', NULL, 1, NULL);

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `amount`, `interest_rate`, `term_months`, `status`, `created_at`, `approved_at`, `purpose`, `is_paid`, `total_due`, `penalty_amount`) VALUES
(1, 5, 100.00, 5.00, 1, 'approved', '2025-04-29 10:55:03', '2025-04-29 02:55:14', 'awdasd', 'no', 105.00, 0.00),
(2, 6, 5000.00, 5.00, 11, 'approved', '2025-05-05 12:04:06', '2025-05-05 06:04:16', 'Ulam', 'no', 1571.00, 0.00),
(3, 6, 1000.00, 5.00, 1, 'pending', '2025-05-11 10:03:39', NULL, 'For The application of my college student', 'no', 0.00, 0.00),
(4, 4, 20000.00, 4.50, 12, 'approved', '2025-05-18 10:15:04', '2025-05-18 17:15:19', 'w', 'no', 20900.00, 0.00);

--
-- Dumping data for table `login_records`
--

INSERT INTO `login_records` (`id`, `user_id`, `ip_address`, `user_agent`, `status`, `login_time`, `created_at`) VALUES
(1, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 04:15:57', '2025-05-18 04:15:57'),
(2, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 04:28:58', '2025-05-18 04:28:58'),
(3, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 05:04:21', '2025-05-18 05:04:21'),
(4, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 05:25:03', '2025-05-18 05:25:03'),
(5, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 08:46:28', '2025-05-18 08:46:28');

--
-- Dumping data for table `login_verifications`
--

INSERT INTO `login_verifications` (`id`, `user_id`, `token`, `verified`, `ip_address`, `user_agent`, `status`, `created_at`, `expires_at`) VALUES
(53, 5, '49f13a47347cfcf16ef00afe5b34d955b0200574016abc77963b15b7b92d0da6', 1, NULL, NULL, 'success', '2025-05-27 12:33:51', '2025-05-27 04:48:51'),
(49, 3, 'f24e7f03aa361ee06a0e9a1032a8f5c6fa7d48c1d1f76f9dc6a20d21c09b0446', 1, NULL, NULL, 'success', '2025-05-27 11:51:55', '2025-05-27 04:06:55');

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `account_id`, `type`, `amount`, `description`, `related_account_id`, `created_at`) VALUES
(1, 1, 'deposit', 2000.00, 'Cash deposit', NULL, '2025-04-16 08:41:45'),
(2, 1, 'withdrawal', 1000.00, 'Cash withdrawal', NULL, '2025-04-16 08:44:38'),
(3, 1, 'transfer_out', 500.00, 'Hello', 2, '2025-04-16 08:52:35'),
(4, 2, 'transfer_in', 500.00, 'Hello', 1, '2025-04-16 08:52:35'),
(5, 2, 'deposit', 100.00, 'Cash deposit', NULL, '2025-04-20 13:34:01'),
(6, 2, 'withdrawal', 100.00, 'Cash withdrawal', NULL, '2025-04-20 13:34:18'),
(7, 2, 'loanpayment', 100.00, 'Loan payment for Loan #7', NULL, '2025-04-21 10:19:07'),
(8, 2, 'deposit', 10000.00, 'Cash deposit', NULL, '2025-04-21 10:50:36'),
(9, 2, 'loanpayment', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:05:57'),
(10, 2, 'loanpayment', 10.00, 'Partial Loan Payment', NULL, '2025-04-21 03:06:44'),
(11, 2, 'loanpayment', 10.00, 'Partial Loan Payment', NULL, '2025-04-21 03:06:49'),
(12, 2, 'loanpayment', 80.00, 'Full Loan Payment', NULL, '2025-04-21 03:07:18'),
(13, 2, 'loanpayment', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:07:43'),
(14, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:09:50'),
(15, 2, 'loanpayment', 5.00, 'Full Loan Payment', NULL, '2025-04-21 03:12:35'),
(16, 2, 'loanpayment', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:14:55'),
(17, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:14:57'),
(18, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:00'),
(19, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:01'),
(20, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:02'),
(21, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:02'),
(22, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:03'),
(23, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:04'),
(24, 2, 'loanpayment', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:16:37'),
(25, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:17:22'),
(26, 2, 'loanpayment', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:19:07'),
(27, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:19:13'),
(28, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:24:14'),
(29, 2, 'loanpayment', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:24:49'),
(30, 2, 'loanpayment', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:24:54'),
(31, 2, 'loanpayment', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:29:28'),
(32, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:31:26'),
(33, 2, 'loanpayment', 5.00, 'Partial Loan Payment', NULL, '2025-04-21 03:34:01'),
(34, 2, 'loanpayment', 0.20, 'Partial Loan Payment', NULL, '2025-04-21 03:36:31'),
(35, 2, 'loanpayment', 0.06, 'Full Loan Payment', NULL, '2025-04-21 03:36:43'),
(36, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:44:19'),
(37, 2, 'loanpayment', 5.00, 'Partial Loan Payment', NULL, '2025-04-21 03:44:34'),
(38, 2, 'loanpayment', 0.26, 'Full Loan Payment', NULL, '2025-04-21 03:45:21'),
(39, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:46:11'),
(40, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:46:23'),
(41, 2, 'loanpayment', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 04:11:49'),
(42, 2, 'loanpayment', 5.00, 'Full Loan Payment', NULL, '2025-04-21 04:12:19'),
(43, 2, 'loanpayment', 525.00, 'Full Loan Payment', NULL, '2025-04-21 04:19:13'),
(44, 2, 'loanpayment', 1000.00, 'Partial Loan Payment', NULL, '2025-04-21 04:20:35'),
(45, 2, 'loanpayment', 1000.00, 'Partial Loan Payment', NULL, '2025-04-21 04:22:43'),
(46, 2, 'loanpayment', 50.00, 'Full Loan Payment', NULL, '2025-04-21 04:24:58'),
(50, 8, 'deposit', 99139149.00, 'Cash deposit', NULL, '2025-04-24 15:14:18'),
(51, 8, 'withdrawal', 99139.00, 'Cash withdrawal', NULL, '2025-04-24 15:15:02'),
(52, 8, 'withdrawal', 99040010.00, 'Cash withdrawal', NULL, '2025-04-24 15:15:21'),
(53, 8, 'deposit', 20000.00, 'Cash deposit', NULL, '2025-04-24 15:15:34'),
(54, 8, 'transfer_out', 15000.00, 'wow yaman', 7, '2025-04-24 15:16:01'),
(55, 7, 'transfer_in', 15000.00, 'wow yaman', 8, '2025-04-24 15:16:01'),
(56, 7, 'deposit', 20000.00, 'Cash deposit', NULL, '2025-04-24 15:47:10'),
(57, 7, 'deposit', 2322.00, 'Cash deposit', NULL, '2025-04-25 10:00:49'),
(58, 7, 'withdrawal', 331000.00, 'Cash withdrawal', NULL, '2025-04-25 10:00:58'),
(59, 8, 'withdrawal', 800000.00, 'Cash withdrawal', NULL, '2025-04-25 13:33:27'),
(60, 8, 'transfer_out', 5000.00, 'sayo na yan ah', 2, '2025-04-25 13:34:06'),
(61, 2, 'transfer_in', 5000.00, 'sayo na yan ah', 8, '2025-04-25 13:34:06'),
(62, 8, 'deposit', 300000.00, 'Cash deposit', NULL, '2025-04-25 13:49:26'),
(63, 8, 'loanpayment', 209000.00, 'Full Loan Payment', NULL, '2025-04-25 20:49:44'),
(64, 8, 'loanpayment', 3150.00, 'Full Loan Payment', NULL, '2025-04-25 20:49:57'),
(65, 8, 'loanpayment', 2205.00, 'Full Loan Payment', NULL, '2025-04-25 20:50:07'),
(66, 8, 'loanpayment', 209000.00, 'Full Loan Payment', NULL, '2025-04-26 16:19:49'),
(67, 8, 'loanpayment', 25000.00, 'Partial Loan Payment', NULL, '2025-04-26 16:20:14'),
(68, 8, 'loanpayment', 80.00, 'Full Loan Payment', NULL, '2025-04-26 16:20:24'),
(69, 8, 'withdrawal', 740000.00, 'Cash withdrawal', NULL, '2025-04-26 09:20:35'),
(70, 2, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-02 08:39:56'),
(71, 2, 'withdrawal', 1400.00, 'Cash withdrawal', NULL, '2025-05-02 08:40:25'),
(72, 2, 'transfer_out', 100000.00, 'awdasd', 7, '2025-05-02 08:41:04'),
(73, 7, 'transfer_in', 100000.00, 'awdasd', 2, '2025-05-02 08:41:04'),
(74, 11, 'deposit', 90000.00, 'Cash deposit', NULL, '2025-05-02 09:23:58'),
(75, 11, 'deposit', 70.00, 'Cash deposit', NULL, '2025-05-02 09:24:09'),
(76, 11, 'withdrawal', 70.00, 'Cash withdrawal', NULL, '2025-05-02 09:24:41'),
(77, 11, 'transfer_out', 90.00, 'awdasd', 7, '2025-05-02 09:25:04'),
(78, 7, 'transfer_in', 90.00, 'awdasd', 11, '2025-05-02 09:25:04'),
(79, 13, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-05 11:57:57'),
(80, 13, 'deposit', 500.00, 'Cash deposit', NULL, '2025-05-05 11:58:00'),
(81, 13, 'withdrawal', 500.00, 'Cash withdrawal', NULL, '2025-05-05 11:58:18'),
(82, 13, 'transfer_out', 500.00, 'Pera mo', 12, '2025-05-05 11:59:16'),
(83, 12, 'transfer_in', 500.00, 'Pera mo', 13, '2025-05-05 11:59:16'),
(84, 12, 'deposit', 569.00, 'Cash deposit', NULL, '2025-05-05 12:01:30'),
(85, 12, 'withdrawal', 69.00, 'Cash withdrawal', NULL, '2025-05-05 12:01:41'),
(86, 12, 'transfer_out', 300.00, 'Sukli mo', 13, '2025-05-05 12:03:01'),
(87, 13, 'transfer_in', 300.00, 'Sukli mo', 12, '2025-05-05 12:03:01'),
(88, 12, 'loanpayment', 200.00, 'Partial Loan Payment', NULL, '2025-05-05 06:04:46'),
(89, 12, 'loanpayment', 250.00, 'Partial Loan Payment', NULL, '2025-05-05 06:05:48'),
(90, 12, 'loanpayment', 257.00, 'Partial Loan Payment', NULL, '2025-05-05 06:08:12'),
(91, 12, 'deposit', 100.00, 'Cash deposit', NULL, '2025-05-05 12:08:46'),
(92, 12, 'deposit', 50.00, 'Cash deposit', NULL, '2025-05-05 15:13:30'),
(93, 12, 'deposit', 34.00, 'Cash deposit', NULL, '2025-05-05 15:13:48'),
(94, 13, 'deposit', 40.00, 'Cash deposit', NULL, '2025-05-05 15:16:22'),
(95, 12, 'deposit', 34.00, 'Cash deposit', NULL, '2025-05-05 15:28:23'),
(96, 12, 'deposit', 50.00, 'Cash deposit', NULL, '2025-05-05 15:47:31'),
(97, 12, 'deposit', 67.00, 'Cash deposit', NULL, '2025-05-05 15:47:33'),
(98, 12, 'deposit', 45.00, 'Cash deposit', NULL, '2025-05-05 15:47:39'),
(99, 12, 'withdrawal', 45.00, 'Cash withdrawal', NULL, '2025-05-05 15:47:48'),
(100, 12, 'withdrawal', 234.00, 'Cash withdrawal', NULL, '2025-05-05 15:47:50'),
(101, 12, 'withdrawal', 76.00, 'Cash withdrawal', NULL, '2025-05-05 15:47:52'),
(102, 12, 'withdrawal', 76.00, 'Cash withdrawal', NULL, '2025-05-05 15:56:56'),
(103, 12, 'deposit', 100.00, 'Cash deposit', NULL, '2025-05-06 09:01:55'),
(104, 12, 'withdrawal', 212.00, 'Cash withdrawal', NULL, '2025-05-06 09:02:13'),
(105, 12, 'withdrawal', 200.00, 'Cash withdrawal', NULL, '2025-05-06 09:02:29'),
(106, 12, 'loanpayment', 2122.00, 'Partial Loan Payment', NULL, '2025-05-06 03:04:48'),
(107, 12, 'withdrawal', 1508.00, 'Cash withdrawal', NULL, '2025-05-06 11:53:13'),
(108, 12, 'deposit', 10.00, 'Cash deposit', NULL, '2025-05-06 11:58:02'),
(109, 12, 'deposit', 50.00, 'Cash deposit', NULL, '2025-05-06 11:58:36'),
(110, 12, 'withdrawal', 30.00, 'Cash withdrawal', NULL, '2025-05-06 11:58:41'),
(111, 12, 'deposit', 70.00, 'Cash deposit', NULL, '2025-05-06 11:58:48'),
(112, 12, 'withdrawal', 100.00, 'Cash withdrawal', NULL, '2025-05-06 11:58:53'),
(113, 12, 'deposit', 90.00, 'Cash deposit', NULL, '2025-05-06 11:59:00'),
(114, 12, 'deposit', 56.00, 'Cash deposit', NULL, '2025-05-06 12:02:40'),
(115, 12, 'withdrawal', 122.00, 'Cash withdrawal', NULL, '2025-05-06 12:03:31'),
(116, 12, 'deposit', 10.00, 'Cash deposit', NULL, '2025-05-06 12:06:23'),
(117, 12, 'deposit', 20.00, 'Cash deposit', NULL, '2025-05-06 12:10:37'),
(118, 12, 'deposit', 34.00, 'Cash deposit', NULL, '2025-05-06 12:12:14'),
(119, 12, 'deposit', 30.00, 'Cash deposit', NULL, '2025-05-06 15:36:11'),
(120, 12, 'deposit', 24.00, 'Cash deposit', NULL, '2025-05-07 02:54:32'),
(121, 12, 'withdrawal', 30.00, 'Cash withdrawal', NULL, '2025-05-07 04:04:27'),
(122, 12, 'withdrawal', 50.00, 'Cash withdrawal', NULL, '2025-05-07 04:04:36'),
(123, 12, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-07 04:07:11'),
(124, 12, 'withdrawal', 345.00, 'Cash withdrawal', NULL, '2025-05-08 04:34:07'),
(125, 12, 'deposit', 23.00, 'Cash deposit', NULL, '2025-05-08 06:29:05'),
(126, 12, 'loanpayment', 250.00, 'Partial Loan Payment', NULL, '2025-05-08 01:43:13'),
(127, 12, 'deposit', 50.00, 'Cash deposit', NULL, '2025-05-08 09:19:17'),
(128, 13, 'deposit', 200.00, 'Cash deposit', NULL, '2025-05-08 14:13:12'),
(129, 12, 'deposit', 23.00, 'Cash deposit', NULL, '2025-05-09 05:25:36'),
(130, 12, 'deposit', 0.48, 'Cash deposit', NULL, '2025-05-09 05:26:04'),
(131, 12, 'deposit', 500.00, 'Cash deposit', NULL, '2025-05-09 05:26:40'),
(132, 12, 'deposit', 0.01, 'Cash deposit', NULL, '2025-05-09 05:41:57'),
(133, 12, 'deposit', 5.00, 'Cash deposit', NULL, '2025-05-09 05:42:15'),
(134, 12, 'withdrawal', 68.00, 'Cash withdrawal', NULL, '2025-05-09 05:51:29'),
(135, 12, 'transfer_out', 500.00, 'hello how\'s your day?', 13, '2025-05-09 06:15:14'),
(136, 13, 'transfer_in', 500.00, 'hello how\'s your day?', 12, '2025-05-09 06:15:14'),
(137, 12, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-09 07:35:47'),
(138, 12, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-09 07:36:11'),
(139, 12, 'deposit', 500.00, 'Cash deposit', NULL, '2025-05-09 07:48:35'),
(140, 12, 'deposit', 787.00, 'Cash deposit', NULL, '2025-05-09 07:48:41'),
(141, 12, 'deposit', 500.00, 'Cash deposit', NULL, '2025-05-10 11:28:28'),
(142, 12, 'loanpayment', 600.00, 'Partial Loan Payment', NULL, '2025-05-10 09:46:31'),
(143, 12, 'deposit', 100.00, 'Cash deposit', NULL, '2025-05-11 09:49:33'),
(144, 12, 'deposit', 100.00, 'Cash deposit', NULL, '2025-05-11 10:02:08'),
(145, 2, 'deposit', 100000.00, 'Cash deposit', NULL, '2025-05-11 13:42:12'),
(146, 2, 'withdrawal', 1000.00, 'Cash withdrawal', NULL, '2025-05-15 05:23:28'),
(147, 2, 'transfer_out', 100000.00, 'ipapasa ko sayo to', 14, '2025-05-15 05:26:26'),
(148, 14, 'transfer_in', 100000.00, 'ipapasa ko sayo to', 2, '2025-05-15 05:26:26'),
(149, 14, 'deposit', 1000.00, 'Cash deposit', NULL, '2025-05-15 05:29:30'),
(150, 14, 'transfer_out', 10000.00, 'Transfer to SB50491031', 2, '2025-05-15 05:44:30'),
(151, 2, 'transfer_in', 10000.00, 'Transfer from SB49600110', 14, '2025-05-15 05:44:30'),
(152, 14, 'transfer_out', 10000.00, 'Transfer to SB50491031', 2, '2025-05-15 05:50:31'),
(153, 2, 'transfer_in', 10000.00, 'Transfer from SB49600110', 14, '2025-05-15 05:50:31'),
(154, 12, 'deposit', 200.00, 'Deposit of $200.00', NULL, '2025-05-16 15:33:57'),
(155, 8, 'deposit', 50000.00, 'Deposit of $50,000.00', NULL, '2025-05-18 04:30:26'),
(156, 8, 'deposit', 2000.00, 'Deposit of $2,000.00', NULL, '2025-05-18 06:15:00'),
(157, 8, 'withdrawal', 52865.00, 'Withdrawal of $52,865.00', NULL, '2025-05-18 06:15:33'),
(158, 8, 'transfer_out', 100000.00, 'gift q', 1, '2025-05-18 06:17:28'),
(159, 1, 'transfer_in', 100000.00, 'gift q', 8, '2025-05-18 06:17:28'),
(160, 8, 'withdrawal', 84999.00, 'Withdrawal of $84,999.00', NULL, '2025-05-18 10:20:36'),
(161, 8, 'transfer_out', 400000.97, 'utang', 1, '2025-05-18 10:21:49'),
(162, 1, 'transfer_in', 400000.97, 'utang', 8, '2025-05-18 10:21:49'),
(163, 2, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-20 08:10:51'),
(164, 2, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-20 08:17:15'),
(165, 2, 'withdrawal', 10000.00, 'Withdrawal of $10,000.00', NULL, '2025-05-20 08:32:34'),
(166, 2, 'loanpayment', 10500.00, 'Full Loan Payment', NULL, '2025-05-27 04:07:54'),
(167, 2, 'loanpayment', 1050.00, 'Full Loan Payment', NULL, '2025-05-27 04:08:06');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance`
--
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
