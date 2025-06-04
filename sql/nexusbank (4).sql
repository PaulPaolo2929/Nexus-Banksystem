-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 04, 2025 at 01:22 PM
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
-- Table structure for table `accounts`
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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `user_id`, `account_number`, `balance`, `created_at`) VALUES
(1, 1, 'SB90284168', 500500.97, '2025-04-16 14:16:34'),
(2, 2, 'SB50491031', 105706.98, '2025-04-16 16:33:52'),
(7, 3, 'SB99139149', 100412.00, '2025-04-24 23:09:40'),
(8, 4, 'SB53061920', 999400.00, '2025-04-24 23:09:40'),
(11, 5, 'SB61285649', 92410.00, '2025-05-02 17:22:20'),
(13, 7, 'SB16865613', 540.00, '2025-05-05 19:56:20'),
(15, 35, 'SB50703654', 7278.00, '2025-05-31 10:29:50'),
(18, 38, 'SB26110498', 0.00, '2025-06-04 12:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `balance`
--

DROP TABLE IF EXISTS `balance`;
CREATE TABLE IF NOT EXISTS `balance` (
  `balance_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `total_balance` decimal(12,2) NOT NULL,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`balance_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`balance_id`, `user_id`, `full_name`, `total_balance`, `last_updated`) VALUES
(3, 7, 'LSPU Student 1', 840.00, '2025-05-05 23:16:22'),
(42, 7, 'LSPU Student 1', 1040.00, '2025-05-08 22:13:12'),
(43, 7, 'LSPU Student 1', 40.00, '2025-05-08 22:14:30'),
(51, 7, 'LSPU Student 1', 540.00, '2025-05-09 14:15:14'),
(61, 2, 'Amiguel', 144206.98, '2025-05-11 21:42:12'),
(62, 2, 'Amiguel', 145206.98, '2025-05-12 20:27:56'),
(63, 2, 'Amiguel', 144206.98, '2025-05-15 13:23:28'),
(64, 2, 'Amiguel', 44206.98, '2025-05-15 13:26:26'),
(68, 2, 'Amiguel', 54206.98, '2025-05-15 13:44:30'),
(70, 2, 'Amiguel', 64206.98, '2025-05-15 13:50:31'),
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

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') COLLATE utf8mb4_general_ci DEFAULT 'new',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(1, 'Renz', 'ramosrayson84@gmail.com', 'Renz', 'hahahha', '2025-05-20 09:55:47', 'replied'),
(2, 'Renz Rayson Ramos', 'ramosrayson84@gmail.com', 'Hello po', 'napaka airolev', '2025-05-30 03:51:24', 'new'),
(3, 'Paul Paolo Aro Mamugay', 'paulpaolomamugay6@gmail.com', 'Loan', 'Please ioapproved nyo na load ko huhuhhuh', '2025-05-31 07:27:31', 'replied'),
(4, 'Paul Paolo Aro Mamugay', 'paulpaolomamugay6@gmail.com', 'Loan', 'Please ioapproved nyo na load ko huhuhhuh', '2025-05-31 07:27:35', 'replied');

-- --------------------------------------------------------

--
-- Table structure for table `id_verifications`
--

DROP TABLE IF EXISTS `id_verifications`;
CREATE TABLE IF NOT EXISTS `id_verifications` (
  `verification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `id_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `verification_status` enum('pending','verified','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `verified_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`verification_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `id_verifications`
--

INSERT INTO `id_verifications` (`verification_id`, `user_id`, `id_type`, `id_file_path`, `verification_status`, `created_at`, `verified_at`) VALUES
(2, 38, 'drivers_license', 'uploads/id_verifications/id_38_1749041706.png', 'pending', '2025-06-04 12:55:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

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
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `user_id`, `plan_name`, `amount`, `interest_rate`, `duration_months`, `status`, `created_at`, `matured_at`, `plan_id`, `withdrawn_at`) VALUES
(1, 5, '', 1000.00, 0.00, 0, 'withdrawn', '2025-05-01 10:14:36', '2025-05-01 10:22:52', 1, NULL),
(2, 5, '', 500.00, 0.00, 0, 'matured', '2025-05-02 16:56:06', NULL, 1, '2025-05-02 17:00:42'),
(3, 5, '', 500.00, 0.00, 0, 'active', '2025-05-02 17:02:11', NULL, 1, NULL),
(5, 7, '', 1000.00, 0.00, 0, 'active', '2025-05-08 22:14:30', NULL, 2, NULL),
(7, 4, NULL, 500.00, 0.00, 12, 'active', '2025-05-18 03:13:58', NULL, 1, NULL),
(8, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:14:19', NULL, 2, NULL),
(9, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:15:56', NULL, 2, NULL),
(10, 4, NULL, 600.00, 0.00, 12, 'active', '2025-05-18 05:15:57', NULL, 1, NULL),
(11, 35, NULL, 12000.00, 0.00, 12, 'active', '2025-06-02 17:19:34', NULL, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investment_plans`
--

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

--
-- Dumping data for table `investment_plans`
--

INSERT INTO `investment_plans` (`plan_id`, `plan_name`, `interest_rate`, `duration_months`, `min_amount`, `risk_level`, `max_amount`) VALUES
(1, 'Starter Plan', 3.50, 6, 500.00, 'Low', 5000.00),
(2, 'Balanced Growth', 5.00, 12, 1000.00, 'Medium', 10000.00),
(3, 'Aggressive Growth', 7.00, 24, 2500.00, 'High', 25000.00),
(4, 'High Yield Bond', 9.00, 36, 5000.00, 'High', 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

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
  `id_selfie_file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_document_file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`loan_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `amount`, `interest_rate`, `term_months`, `status`, `created_at`, `approved_at`, `purpose`, `is_paid`, `total_due`, `penalty_amount`, `id_selfie_file_path`, `id_document_file_path`) VALUES
(1, 5, 100.00, 5.00, 1, 'approved', '2025-04-29 10:55:03', '2025-04-29 02:55:14', 'awdasd', 'no', 105.00, 6.30, NULL, NULL),
(4, 4, 20000.00, 4.50, 12, 'approved', '2025-05-18 10:15:04', '2025-05-18 17:15:19', 'w', 'no', 20900.00, 0.00, NULL, NULL),
(58, 5, 1000.00, 5.00, 12, 'approved', '2025-05-31 12:18:59', '2025-05-31 04:19:20', 'asad', 'no', 550.00, 0.00, NULL, NULL),
(59, 35, 1000.00, 5.00, 1, 'approved', '2025-06-02 15:57:45', '2025-06-02 10:00:38', 'Hello world', 'no', 1050.00, 0.00, NULL, NULL),
(60, 35, 20000.00, 4.50, 12, 'approved', '2025-06-02 16:03:16', '2025-06-02 10:03:50', 'Gusto ko bumili ng bahay HAHAHA', 'no', 18678.00, 0.00, NULL, NULL),
(61, 5, 100.00, 5.00, 12, 'pending', '2025-06-04 21:08:10', NULL, 'para sa akong mga anak', 'no', 100.00, 0.00, '../uploads/loan_verifications/selfie_5_1749042489.png', '../uploads/loan_verifications/document_5_1749042490.png');

--
-- Triggers `loans`
--
DROP TRIGGER IF EXISTS `update_loan_penalty`;
DELIMITER $$
CREATE TRIGGER `update_loan_penalty` BEFORE UPDATE ON `loans` FOR EACH ROW BEGIN
    IF NEW.approved_at IS NOT NULL AND NEW.is_paid = 'no' THEN
        IF DATE_ADD(NEW.approved_at, INTERVAL NEW.term_months MONTH) < NOW() THEN
            SET NEW.penalty_amount = NEW.total_due * (0.01 * DATEDIFF(NOW(), DATE_ADD(NEW.approved_at, INTERVAL NEW.term_months MONTH)));
        ELSE
            SET NEW.penalty_amount = 0;
        END IF;
    ELSE
        SET NEW.penalty_amount = 0;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loan_history`
--

DROP TABLE IF EXISTS `loan_history`;
CREATE TABLE IF NOT EXISTS `loan_history` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `loan_id` int DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `loan_id` (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_history`
--

INSERT INTO `loan_history` (`history_id`, `loan_id`, `status`, `changed_at`) VALUES
(40, 58, 'approved', '2025-05-31 04:19:20'),
(41, 59, 'approved', '2025-06-02 10:00:38'),
(42, 60, 'approved', '2025-06-02 10:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `login_records`
--

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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_records`
--

INSERT INTO `login_records` (`id`, `user_id`, `ip_address`, `user_agent`, `status`, `login_time`, `created_at`) VALUES
(1, 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 04:15:57', '2025-05-18 04:15:57'),
(2, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 04:28:58', '2025-05-18 04:28:58'),
(3, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 05:04:21', '2025-05-18 05:04:21'),
(4, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 05:25:03', '2025-05-18 05:25:03'),
(5, 4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 08:46:28', '2025-05-18 08:46:28'),
(25, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'failed', '2025-05-27 14:49:16', '2025-05-27 14:49:16'),
(26, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'failed', '2025-05-27 14:49:22', '2025-05-27 14:49:22'),
(27, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-27 14:56:58', '2025-05-27 14:56:58'),
(28, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-27 15:00:15', '2025-05-27 15:00:15'),
(29, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'failed', '2025-05-31 04:15:18', '2025-05-31 04:15:18'),
(30, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'failed', '2025-05-31 04:15:24', '2025-05-31 04:15:24'),
(31, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'failed', '2025-05-31 04:15:50', '2025-05-31 04:15:50'),
(32, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-31 04:16:47', '2025-05-31 04:16:47'),
(33, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-05-31 04:17:52', '2025-05-31 04:17:52'),
(34, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-05-31 05:22:53', '2025-05-31 05:22:53'),
(35, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-05-31 05:24:21', '2025-05-31 05:24:21'),
(36, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-05-31 05:27:33', '2025-05-31 05:27:33'),
(48, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-31 10:30:28', '2025-05-31 10:30:28'),
(49, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-06-02 05:20:15', '2025-06-02 05:20:15'),
(50, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-06-02 05:20:29', '2025-06-02 05:20:29'),
(51, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'success', '2025-06-02 05:41:09', '2025-06-02 05:41:09'),
(52, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'success', '2025-06-02 05:57:45', '2025-06-02 05:57:45'),
(53, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'success', '2025-06-02 05:58:15', '2025-06-02 05:58:15'),
(54, 35, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-06-02 07:40:30', '2025-06-02 07:40:30'),
(56, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-06-04 10:33:54', '2025-06-04 10:33:54'),
(57, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-06-04 11:27:07', '2025-06-04 11:27:07'),
(58, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-06-04 11:42:56', '2025-06-04 11:42:56'),
(59, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 13:01:07', '2025-06-04 13:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `login_verifications`
--

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
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_verifications`
--

INSERT INTO `login_verifications` (`id`, `user_id`, `token`, `verified`, `ip_address`, `user_agent`, `status`, `created_at`, `expires_at`) VALUES
(71, 1, '1e4e6cb8a0edb9c019a5839b898bc49f4bb3c0f363a2954c8c77ef7f1cde123a', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-06-04 11:43:15', '2025-06-04 03:58:15'),
(72, 5, 'bd620602e9089ee5cfc6db124efbddc3eefc224b5bb473bdd1af61fab4372b58', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 13:01:31', '2025-06-04 05:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

DROP TABLE IF EXISTS `otp_verification`;
CREATE TABLE IF NOT EXISTS `otp_verification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verification`
--

INSERT INTO `otp_verification` (`id`, `email`, `otp`, `created_at`, `expires_at`, `is_used`) VALUES
(2, '0323-4199@lspu.edu.ph', '966304', '2025-04-23 15:25:43', '2025-04-23 22:30:43', 0),
(3, 'amiguelll0513@gmail.com', '046422', '2025-05-18 09:51:44', '2025-05-18 16:56:44', 1),
(4, '0323-4199@lspu.edu.ph', '097192', '2025-05-18 12:14:53', '2025-05-18 19:19:53', 1),
(5, 'shaison62@gmail.com', '638497', '2025-05-15 05:50:13', '2025-05-14 21:55:13', 1),
(7, 'senioritaisabel@gmail.com', '686762', '2025-05-02 09:22:47', '2025-05-02 01:27:47', 1),
(37, '0323-3883@lspu.edu.ph', '376984', '2025-06-02 07:40:30', '2025-06-02 01:45:30', 1),
(41, 'shaison61@gmail.com', '487405', '2025-06-04 11:42:56', '2025-06-04 03:47:56', 1),
(42, 'ramosrayson84@gmail.com', '482723', '2025-06-04 13:01:07', '2025-06-04 05:06:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

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
  KEY `related_account_id` (`related_account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=218 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(82, 13, 'transfer_out', 500.00, 'Pera mo', NULL, '2025-05-05 11:59:16'),
(87, 13, 'transfer_in', 300.00, 'Sukli mo', NULL, '2025-05-05 12:03:01'),
(94, 13, 'deposit', 40.00, 'Cash deposit', NULL, '2025-05-05 15:16:22'),
(128, 13, 'deposit', 200.00, 'Cash deposit', NULL, '2025-05-08 14:13:12'),
(136, 13, 'transfer_in', 500.00, 'hello how\'s your day?', NULL, '2025-05-09 06:15:14'),
(145, 2, 'deposit', 100000.00, 'Cash deposit', NULL, '2025-05-11 13:42:12'),
(146, 2, 'withdrawal', 1000.00, 'Cash withdrawal', NULL, '2025-05-15 05:23:28'),
(147, 2, 'transfer_out', 100000.00, 'ipapasa ko sayo to', NULL, '2025-05-15 05:26:26'),
(151, 2, 'transfer_in', 10000.00, 'Transfer from SB49600110', NULL, '2025-05-15 05:44:30'),
(153, 2, 'transfer_in', 10000.00, 'Transfer from SB49600110', NULL, '2025-05-15 05:50:31'),
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
(167, 2, 'loanpayment', 1050.00, 'Full Loan Payment', NULL, '2025-05-27 04:08:06'),
(168, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-27 15:06:53'),
(169, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-31 04:42:21'),
(170, 11, 'loanpayment', 500.00, 'Partial Loan Payment', NULL, '2025-05-30 20:47:43'),
(171, 15, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-31 10:45:56'),
(192, 15, 'deposit', 5000.00, 'Initial deposit', NULL, '2025-05-01 02:00:00'),
(193, 15, 'withdrawal', 1200.00, 'ATM withdrawal', NULL, '2025-05-02 06:15:00'),
(194, 15, 'deposit', 2000.00, 'Salary', NULL, '2025-05-03 01:30:00'),
(195, 15, 'transfer_out', 1000.00, 'Sent to friend', NULL, '2025-05-04 08:20:00'),
(196, 15, 'transfer_in', 1500.00, 'Received from boss', NULL, '2025-05-05 03:10:00'),
(197, 15, 'withdrawal', 800.00, 'Grocery shopping', NULL, '2025-05-06 10:45:00'),
(198, 15, 'deposit', 3500.00, 'Freelance project', NULL, '2025-05-07 00:00:00'),
(199, 15, 'transfer_out', 500.00, 'Payment to supplier', NULL, '2025-05-08 05:30:00'),
(200, 15, 'transfer_in', 250.00, 'Refund from supplier', NULL, '2025-05-09 02:05:00'),
(201, 15, 'deposit', 1000.00, 'Bonus', NULL, '2025-05-10 04:00:00'),
(202, 15, 'withdrawal', 400.00, 'Utility bills', NULL, '2025-05-11 09:45:00'),
(203, 15, 'transfer_out', 300.00, 'Money to sibling', NULL, '2025-05-12 12:15:00'),
(204, 15, 'transfer_in', 500.00, 'Sibling paid back', NULL, '2025-05-13 00:55:00'),
(205, 15, 'deposit', 1800.00, 'Project milestone', NULL, '2025-05-14 01:00:00'),
(206, 15, 'withdrawal', 250.00, 'Online shopping', NULL, '2025-05-15 14:00:00'),
(207, 15, 'transfer_out', 700.00, 'Rent payment', NULL, '2025-05-15 23:30:00'),
(208, 15, 'transfer_in', 600.00, 'Partial refund', NULL, '2025-05-17 02:00:00'),
(209, 15, 'deposit', 2200.00, 'Monthly payment', NULL, '2025-05-18 00:30:00'),
(210, 15, 'withdrawal', 500.00, 'Cash withdrawal', NULL, '2025-05-19 11:20:00'),
(211, 15, 'transfer_out', 950.00, 'Gift to parent', NULL, '2025-05-20 04:40:00'),
(212, 15, 'deposit', 150.00, 'Deposit of $150.00', NULL, '2025-05-31 11:00:27'),
(213, 15, 'transfer_out', 50.00, 'sdfsdfsd', 2, '2025-05-31 11:02:05'),
(214, 2, 'transfer_in', 50.00, 'sdfsdfsd', 15, '2025-05-31 11:02:05'),
(215, 15, 'withdrawal', 100.00, 'Withdrawal of $100.00', NULL, '2025-05-31 11:06:44'),
(216, 15, 'withdrawal', 500.00, 'Withdrawal of $500.00', NULL, '2025-06-02 05:59:58'),
(217, 15, 'loanpayment', 2222.00, 'Partial Loan Payment', NULL, '2025-06-02 02:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
  `login_attempts` int NOT NULL DEFAULT '0',
  `blocked_until` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `age`, `birth_year`, `email`, `address`, `occupation`, `phone`, `password_hash`, `created_at`, `is_admin`, `status`, `is_active`, `reset_token`, `reset_expires_at`, `profile_picture`, `login_attempts`, `blocked_until`) VALUES
(1, 'Shaison', 24, 2000, 'shaison61@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$LDpOyFZkS9D.mRYfzdnOdOtvhjqhE1bk5B/85d/bXgX1/CKLXlHfe', '2025-04-16 08:41:45', 1, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(2, 'Amiguel', 24, 2000, 'amiguelll0513@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(3, 'Shaison2', 24, 2000, 'shaison62@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(4, 'Isabel', 24, 2000, 'senioritaisabel@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 1, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(5, 'Rayson', 24, 2000, 'ramosrayson84@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$FTiIs2N1IyTHLvJ7QBjFhuA..klvK9ehN2YmMozrtRxn5VVI6eUtG', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'profile_5_1748358975.png', 0, NULL),
(7, 'LSPU Student 1', 24, 2000, '0323-4199@lspu.edu.ph', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(35, 'Pamela A. Mamugay', 19, 2005, '0323-3883@lspu.edu.ph', 'Poblacion street Brgy-3D', 'Student', '09217844447', '$2y$10$.krvdQIYwvC2CB.Do9ugcuDD8pifxhNOlW.fp0BruQ9n6uHLAvEHy', '2025-05-31 10:25:04', 0, 'approved', 1, NULL, NULL, 'profile_35_1748687596.jpg', 0, NULL),
(38, 'nexus banking system', 20, 2004, 'nexusbanksystem@gmail.com', 'brgy San Isidro Calauan laguna', 'Student', '09300674760', '$2y$10$pefsBS9ZUH9uRng3MC0TDOIg5gR74SPj2DSIUgXVqi4g7iS30oTqa', '2025-06-04 12:55:06', 0, 'approved', 1, NULL, NULL, NULL, 0, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `balance`
--
ALTER TABLE `balance`
  ADD CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `id_verifications`
--
ALTER TABLE `id_verifications`
  ADD CONSTRAINT `id_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `investments`
--
ALTER TABLE `investments`
  ADD CONSTRAINT `investments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `investments_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `investment_plans` (`plan_id`) ON DELETE SET NULL;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_history`
--
ALTER TABLE `loan_history`
  ADD CONSTRAINT `loan_history_ibfk_1` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`loan_id`) ON DELETE CASCADE;

--
-- Constraints for table `login_records`
--
ALTER TABLE `login_records`
  ADD CONSTRAINT `login_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `login_verifications`
--
ALTER TABLE `login_verifications`
  ADD CONSTRAINT `login_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD CONSTRAINT `otp_verification_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`related_account_id`) REFERENCES `accounts` (`account_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
