-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 05:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `securebank`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `user_id`, `account_number`, `balance`, `created_at`) VALUES
(1, 3, 'SB90284168', 500500.97, '2025-04-16 06:16:34'),
(2, 5, 'SB50491031', 64206.98, '2025-04-16 08:33:52'),
(7, 19, 'SB99139149', 100412.00, '2025-04-24 15:09:40'),
(8, 24, 'SB53061920', 999400.00, '2025-04-24 15:09:40'),
(14, 31, 'SB49600110', 81000.00, '2025-05-12 12:01:52'),
(11, 27, 'SB61285649', 89910.00, '2025-05-02 09:22:20'),
(12, 29, 'SB30481356', 1587.49, '2025-05-05 11:52:55'),
(13, 30, 'SB16865613', 540.00, '2025-05-05 11:56:20');

--
-- Triggers `accounts`
--
DELIMITER $$
CREATE TRIGGER `after_balance_update` AFTER UPDATE ON `accounts` FOR EACH ROW BEGIN
    DECLARE user_full_name VARCHAR(100);

    -- Retrieve the full name from the users table
    SELECT full_name INTO user_full_name FROM users WHERE user_id = NEW.user_id;

    -- Insert the new balance into the balance table
    INSERT INTO `balance` (`user_id`, `full_name`, `total_balance`, `last_updated`)
    VALUES (NEW.user_id, user_full_name, NEW.balance, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `balance`
--

CREATE TABLE `balance` (
  `balance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `total_balance` decimal(12,2) NOT NULL,
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `balance`
--

INSERT INTO `balance` (`balance_id`, `user_id`, `full_name`, `total_balance`, `last_updated`) VALUES
(3, 30, 'Poleene', 840.00, '2025-05-05 23:16:22'),
(22, 29, 'Pamela', 10.00, '2025-05-06 19:58:02'),
(23, 29, 'Pamela', 60.00, '2025-05-06 19:58:36'),
(24, 29, 'Pamela', 30.00, '2025-05-06 19:58:41'),
(25, 29, 'Pamela', 100.00, '2025-05-06 19:58:48'),
(26, 29, 'Pamela', 0.00, '2025-05-06 19:58:53'),
(27, 29, 'Pamela', 90.00, '2025-05-06 19:59:00'),
(28, 29, 'Pamela', 146.00, '2025-05-06 20:02:40'),
(29, 29, 'Pamela', 24.00, '2025-05-06 20:03:31'),
(30, 29, 'Pamela', 34.00, '2025-05-06 20:06:23'),
(31, 29, 'Pamela', 54.00, '2025-05-06 20:10:37'),
(32, 29, 'Pamela', 88.00, '2025-05-06 20:12:14'),
(33, 29, 'Pamela', 118.00, '2025-05-06 23:36:11'),
(34, 29, 'Pamela', 142.00, '2025-05-07 10:54:32'),
(35, 29, 'Pamela', 112.00, '2025-05-07 12:04:27'),
(36, 29, 'Pamela', 62.00, '2025-05-07 12:04:35'),
(37, 29, 'Pamela', 1062.00, '2025-05-07 12:07:11'),
(38, 29, 'Pamela', 717.00, '2025-05-08 12:34:07'),
(39, 29, 'Pamela', 740.00, '2025-05-08 14:29:05'),
(40, 29, 'Pamela', 490.00, '2025-05-08 15:43:13'),
(41, 29, 'Pamela', 540.00, '2025-05-08 17:19:17'),
(42, 30, 'Poleene', 1040.00, '2025-05-08 22:13:12'),
(43, 30, 'Poleene', 40.00, '2025-05-08 22:14:30'),
(44, 29, 'Pamela', 563.00, '2025-05-09 13:25:36'),
(45, 29, 'Pamela', 563.48, '2025-05-09 13:26:04'),
(46, 29, 'Pamela', 1063.48, '2025-05-09 13:26:40'),
(47, 29, 'Pamela', 1063.49, '2025-05-09 13:41:57'),
(48, 29, 'Pamela', 1068.49, '2025-05-09 13:42:15'),
(49, 29, 'Pamela', 1000.49, '2025-05-09 13:51:29'),
(50, 29, 'Pamela', 500.49, '2025-05-09 14:15:14'),
(51, 30, 'Poleene', 540.00, '2025-05-09 14:15:14'),
(52, 29, 'Pamela', 1500.49, '2025-05-09 15:35:47'),
(53, 29, 'Pamela', 2500.49, '2025-05-09 15:36:11'),
(54, 29, 'Pamela', 0.49, '2025-05-09 15:36:30'),
(55, 29, 'Pamela', 500.49, '2025-05-09 15:48:35'),
(56, 29, 'Pamela', 1287.49, '2025-05-09 15:48:41'),
(57, 29, 'Pamela', 1787.49, '2025-05-10 19:28:28'),
(58, 29, 'Pamela', 1187.49, '2025-05-10 23:46:31'),
(59, 29, 'Pamela', 1287.49, '2025-05-11 17:49:33'),
(60, 29, 'Pamela', 1387.49, '2025-05-11 18:02:08'),
(61, 5, 'Renz', 144206.98, '2025-05-11 21:42:12'),
(62, 5, 'Renz', 145206.98, '2025-05-12 20:27:56'),
(63, 5, 'Renz', 144206.98, '2025-05-15 13:23:28'),
(64, 5, 'Renz', 44206.98, '2025-05-15 13:26:26'),
(65, 31, 'Renz', 100000.00, '2025-05-15 13:26:26'),
(66, 31, 'Renz', 101000.00, '2025-05-15 13:29:30'),
(67, 31, 'Renz', 91000.00, '2025-05-15 13:44:30'),
(68, 5, 'Renz', 54206.98, '2025-05-15 13:44:30'),
(69, 31, 'Renz', 81000.00, '2025-05-15 13:50:31'),
(70, 5, 'Renz', 64206.98, '2025-05-15 13:50:31'),
(71, 29, 'Pamela A. Mamugay', 1587.49, '2025-05-16 23:33:57'),
(72, 24, 'tutoy', 2050865.00, '2025-05-17 21:30:26'),
(73, 24, 'tutoy', 2052865.00, '2025-05-17 23:15:00'),
(74, 24, 'tutoy', 2000000.00, '2025-05-17 23:15:33'),
(75, 24, 'tutoy', 1900000.00, '2025-05-17 23:17:28'),
(76, 3, 'Renz', 100500.00, '2025-05-17 23:17:28'),
(101, 24, 'tutoy', 1468999.97, '2025-05-18 03:13:58'),
(102, 24, 'tutoy', 1466999.97, '2025-05-18 03:14:19'),
(103, 24, 'tutoy', 1486999.97, '2025-05-18 03:15:19'),
(104, 24, 'tutoy', 1484999.97, '2025-05-18 03:15:56'),
(105, 24, 'tutoy', 1400000.97, '2025-05-18 03:20:36'),
(106, 24, 'tutoy', 1000000.00, '2025-05-18 03:21:49'),
(107, 3, 'Renz', 500500.97, '2025-05-18 03:21:49'),
(108, 24, 'tutoy', 999400.00, '2025-05-18 05:15:57');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `investment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `duration_months` int(11) NOT NULL DEFAULT 12,
  `status` enum('active','matured','withdrawn') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `matured_at` datetime DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `withdrawn_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `user_id`, `plan_name`, `amount`, `interest_rate`, `duration_months`, `status`, `created_at`, `matured_at`, `plan_id`, `withdrawn_at`) VALUES
(1, 5, '', 1000.00, 0.00, 0, 'withdrawn', '2025-05-01 10:14:36', '2025-05-01 10:22:52', 1, NULL),
(2, 5, '', 500.00, 0.00, 0, 'matured', '2025-05-02 16:56:06', NULL, 1, '2025-05-02 17:00:42'),
(3, 5, '', 500.00, 0.00, 0, 'active', '2025-05-02 17:02:11', NULL, 1, NULL),
(4, 29, '', 1000.00, 0.00, 0, 'active', '2025-05-05 20:10:32', NULL, 2, NULL),
(5, 30, '', 1000.00, 0.00, 0, 'active', '2025-05-08 22:14:30', NULL, 2, NULL),
(6, 29, '', 2500.00, 0.00, 0, 'active', '2025-05-09 15:36:30', NULL, 3, NULL),
(7, 24, NULL, 500.00, 0.00, 12, 'active', '2025-05-18 03:13:58', NULL, 1, NULL),
(8, 24, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:14:19', NULL, 2, NULL),
(9, 24, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:15:56', NULL, 2, NULL),
(10, 24, NULL, 600.00, 0.00, 12, 'active', '2025-05-18 05:15:57', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investment_plans`
--

CREATE TABLE `investment_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `duration_months` int(11) NOT NULL,
  `min_amount` decimal(12,2) NOT NULL,
  `risk_level` varchar(50) DEFAULT NULL,
  `max_amount` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','paid') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `is_paid` enum('yes','no') DEFAULT 'no',
  `total_due` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `amount`, `interest_rate`, `term_months`, `status`, `created_at`, `approved_at`, `purpose`, `is_paid`, `total_due`) VALUES
(49, 26, 100.00, 5.00, 1, 'approved', '2025-04-29 10:55:03', '2025-04-29 02:55:14', 'awdasd', 'no', 105.00),
(50, 29, 5000.00, 5.00, 11, 'approved', '2025-05-05 12:04:06', '2025-05-05 06:04:16', 'Ulam', 'no', 1571.00),
(51, 29, 1000.00, 5.00, 1, 'pending', '2025-05-11 10:03:39', NULL, 'For The application of my college student', 'no', 0.00),
(52, 5, 1000.00, 5.00, 12, 'approved', '2025-05-12 12:27:47', '2025-05-12 04:27:56', 'for my mom', 'no', 1050.00),
(53, 24, 20000.00, 4.50, 12, 'approved', '2025-05-18 10:15:04', '2025-05-18 17:15:19', 'w', 'no', 20900.00),
(54, 24, 2000.00, 5.00, 6, 'pending', '2025-05-18 12:15:47', NULL, 'w', 'no', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `loan_history`
--

CREATE TABLE `loan_history` (
  `history_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `changed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_history`
--

INSERT INTO `loan_history` (`history_id`, `loan_id`, `status`, `changed_at`, `notes`) VALUES
(20, 35, 'accepted', '2025-04-26 08:54:56', NULL),
(19, 36, 'accepted', '2025-04-26 08:54:54', NULL),
(18, 34, 'accepted', '2025-04-26 08:54:12', NULL),
(17, 33, 'accepted', '2025-04-26 08:54:11', NULL),
(16, 32, 'accepted', '2025-04-26 08:54:10', NULL),
(15, 31, 'accepted', '2025-04-26 08:54:08', NULL),
(14, 30, 'accepted', '2025-04-26 08:54:07', NULL),
(13, 28, 'accepted', '2025-04-26 08:46:21', NULL),
(12, 29, 'accepted', '2025-04-26 08:39:41', NULL),
(11, 27, 'accepted', '2025-04-26 08:38:21', NULL),
(21, 37, 'approved', '2025-04-26 09:08:40', NULL),
(22, 38, 'approved', '2025-04-26 09:09:58', NULL),
(23, 39, 'approved', '2025-04-26 09:10:00', NULL),
(24, 40, 'approved', '2025-04-26 09:10:02', NULL),
(25, 41, 'rejected', '2025-04-29 09:56:49', NULL),
(26, 43, 'rejected', '2025-04-29 10:01:03', NULL),
(27, 42, 'rejected', '2025-04-29 10:01:05', NULL),
(28, 45, 'rejected', '2025-04-29 10:20:23', NULL),
(29, 44, 'rejected', '2025-04-29 10:20:25', NULL),
(30, 46, 'approved', '2025-04-29 10:22:38', NULL),
(31, 47, 'approved', '2025-04-29 10:23:19', NULL),
(32, 48, 'approved', '2025-04-29 10:37:03', NULL),
(33, 49, 'approved', '2025-04-29 10:55:14', NULL),
(34, 50, 'approved', '2025-05-05 14:04:16', NULL),
(35, 52, 'approved', '2025-05-12 12:27:56', NULL),
(36, 53, 'approved', '2025-05-18 10:15:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_records`
--

CREATE TABLE `login_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_records`
--

INSERT INTO `login_records` (`id`, `user_id`, `ip_address`, `user_agent`, `status`, `login_time`, `created_at`) VALUES
(1, 19, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 04:15:57', '2025-05-18 04:15:57'),
(2, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 04:28:58', '2025-05-18 04:28:58'),
(3, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 05:04:21', '2025-05-18 05:04:21'),
(4, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 05:25:03', '2025-05-18 05:25:03'),
(5, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 08:46:28', '2025-05-18 08:46:28'),
(6, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 09:13:40', '2025-05-18 09:13:40'),
(7, 19, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'success', '2025-05-18 09:51:44', '2025-05-18 09:51:44'),
(8, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 10:17:45', '2025-05-18 10:17:45'),
(9, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 11:37:16', '2025-05-18 11:37:16'),
(10, 24, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 12:14:53', '2025-05-18 12:14:53'),
(11, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-05-18 08:48:18', '2025-05-18 08:48:18'),
(12, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 08:48:27', '2025-05-18 08:48:27'),
(13, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 08:51:51', '2025-05-18 08:51:51'),
(14, 28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 11:29:50', '2025-05-18 11:29:50'),
(15, 28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 12:23:05', '2025-05-18 12:23:05'),
(16, 28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 12:27:17', '2025-05-18 12:27:17'),
(17, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 14:27:06', '2025-05-18 14:27:06'),
(18, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-05-18 14:28:22', '2025-05-18 14:28:22'),
(19, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-05-18 14:28:28', '2025-05-18 14:28:28'),
(20, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-05-18 14:28:36', '2025-05-18 14:28:36'),
(21, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 14:28:50', '2025-05-18 14:28:50'),
(22, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 14:55:45', '2025-05-18 14:55:45'),
(23, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'failed', '2025-05-18 15:05:49', '2025-05-18 15:05:49'),
(24, 29, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 15:05:57', '2025-05-18 15:05:57'),
(25, 28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 15:18:28', '2025-05-18 15:18:28'),
(26, 28, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'success', '2025-05-18 15:18:32', '2025-05-18 15:18:32');

-- --------------------------------------------------------

--
-- Table structure for table `login_verifications`
--

CREATE TABLE `login_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status` enum('success','failed') DEFAULT 'success',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_verifications`
--

INSERT INTO `login_verifications` (`id`, `user_id`, `token`, `verified`, `ip_address`, `user_agent`, `status`, `created_at`, `expires_at`) VALUES
(37, 19, 'fb55ea309a419e8c87c67fe8810c6c7bfb24f6c7dff3808652aa1958d0eda4e3', 1, NULL, NULL, 'success', '2025-05-18 09:52:10', '2025-05-18 17:07:10'),
(40, 24, '9202546bef3b6effd7e7c00e5acfc67ba13f2aa0bff743ffc5f5fabc04dfc0ee', 1, NULL, NULL, 'success', '2025-05-18 12:15:11', '2025-05-18 19:30:11'),
(47, 29, '3200cb2a30441909756efd6bb4467363beb14dc75c1af8dfca74ae9ce696490e', 1, NULL, NULL, 'success', '2025-05-18 15:06:22', '2025-05-18 07:21:22'),
(48, 28, 'adf09755146977f444080bd06b0a05ccdf491f7e9473dbfdc4f1c3cafcac7fb1', 1, NULL, NULL, 'success', '2025-05-18 15:19:45', '2025-05-18 07:34:45');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `otp_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verification`
--

INSERT INTO `otp_verification` (`otp_id`, `email`, `otp_code`, `created_at`, `expires_at`, `is_used`) VALUES
(215, 'shaison61@gmail.com', '425877', '2025-05-16 09:48:23', '2025-05-16 01:53:23', 1),
(42, '0323-4199@lspu.ed.ph', '966304', '2025-04-23 15:25:43', '2025-04-23 22:30:43', 0),
(264, 'amiguelll0513@gmail.com', '046422', '2025-05-18 09:51:44', '2025-05-18 16:56:44', 1),
(269, '0323-4199@lspu.edu.ph', '097192', '2025-05-18 12:14:53', '2025-05-18 19:19:53', 1),
(196, 'shaison62@gmail.com', '638497', '2025-05-15 05:50:13', '2025-05-14 21:55:13', 1),
(123, '', '194789', '2025-05-01 02:31:44', '2025-04-30 18:36:44', 0),
(137, 'senioritaisabel@gmail.com', '686762', '2025-05-02 09:22:47', '2025-05-02 01:27:47', 1),
(226, 'ramosrayson84@gmail.com', '887203', '2025-05-16 10:37:34', '2025-05-16 02:42:34', 1),
(280, 'paulpaolomamugay6@gmail.com', '272614', '2025-05-18 15:18:32', '2025-05-18 09:23:32', 1),
(278, '0323-3883@lspu.edu.ph', '797807', '2025-05-18 15:05:57', '2025-05-18 09:10:57', 1),
(152, 'paolomamugay5@gmail.com', '522697', '2025-05-08 14:11:54', '2025-05-08 08:16:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `type` enum('deposit','withdrawal','transfer_in','transfer_out','loanpayment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `related_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 2, '', 100.00, 'Loan payment for Loan #7', NULL, '2025-04-21 10:19:07'),
(8, 2, 'deposit', 10000.00, 'Cash deposit', NULL, '2025-04-21 10:50:36'),
(9, 2, '', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:05:57'),
(10, 2, '', 10.00, 'Partial Loan Payment', NULL, '2025-04-21 03:06:44'),
(11, 2, '', 10.00, 'Partial Loan Payment', NULL, '2025-04-21 03:06:49'),
(12, 2, '', 80.00, 'Full Loan Payment', NULL, '2025-04-21 03:07:18'),
(13, 2, '', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:07:43'),
(14, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:09:50'),
(15, 2, '', 5.00, 'Full Loan Payment', NULL, '2025-04-21 03:12:35'),
(16, 2, '', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:14:55'),
(17, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:14:57'),
(18, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:00'),
(19, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:01'),
(20, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:02'),
(21, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:02'),
(22, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:03'),
(23, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:15:04'),
(24, 2, '', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:16:37'),
(25, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:17:22'),
(26, 2, '', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:19:07'),
(27, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:19:13'),
(28, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:24:14'),
(29, 2, '', 500.00, 'Partial Loan Payment', NULL, '2025-04-21 03:24:49'),
(30, 2, '', 500.00, 'Full Loan Payment', NULL, '2025-04-21 03:24:54'),
(31, 2, '', 100.00, 'Full Loan Payment', NULL, '2025-04-21 03:29:28'),
(32, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:31:26'),
(33, 2, '', 5.00, 'Partial Loan Payment', NULL, '2025-04-21 03:34:01'),
(34, 2, '', 0.20, 'Partial Loan Payment', NULL, '2025-04-21 03:36:31'),
(35, 2, '', 0.06, 'Full Loan Payment', NULL, '2025-04-21 03:36:43'),
(36, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:44:19'),
(37, 2, '', 5.00, 'Partial Loan Payment', NULL, '2025-04-21 03:44:34'),
(38, 2, '', 0.26, 'Full Loan Payment', NULL, '2025-04-21 03:45:21'),
(39, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:46:11'),
(40, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 03:46:23'),
(41, 2, '', 100.00, 'Partial Loan Payment', NULL, '2025-04-21 04:11:49'),
(42, 2, '', 5.00, 'Full Loan Payment', NULL, '2025-04-21 04:12:19'),
(43, 2, '', 525.00, 'Full Loan Payment', NULL, '2025-04-21 04:19:13'),
(44, 2, '', 1000.00, 'Partial Loan Payment', NULL, '2025-04-21 04:20:35'),
(45, 2, '', 1000.00, 'Partial Loan Payment', NULL, '2025-04-21 04:22:43'),
(46, 2, '', 50.00, 'Full Loan Payment', NULL, '2025-04-21 04:24:58'),
(47, 6, 'deposit', 20000.00, 'Cash deposit', NULL, '2025-04-23 17:37:18'),
(48, 6, 'transfer_out', 15000.00, 'wsad', 2, '2025-04-23 17:39:29'),
(49, 2, 'transfer_in', 15000.00, 'wsad', 6, '2025-04-23 17:39:29'),
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
(63, 8, '', 209000.00, 'Full Loan Payment', NULL, '2025-04-25 20:49:44'),
(64, 8, '', 3150.00, 'Full Loan Payment', NULL, '2025-04-25 20:49:57'),
(65, 8, '', 2205.00, 'Full Loan Payment', NULL, '2025-04-25 20:50:07'),
(66, 8, '', 209000.00, 'Full Loan Payment', NULL, '2025-04-26 16:19:49'),
(67, 8, '', 25000.00, 'Partial Loan Payment', NULL, '2025-04-26 16:20:14'),
(68, 8, '', 80.00, 'Full Loan Payment', NULL, '2025-04-26 16:20:24'),
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
(88, 12, '', 200.00, 'Partial Loan Payment', NULL, '2025-05-05 06:04:46'),
(89, 12, '', 250.00, 'Partial Loan Payment', NULL, '2025-05-05 06:05:48'),
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
(146, 2, 'withdrawal', 1000.00, NULL, NULL, '2025-05-15 05:23:28'),
(147, 2, 'transfer_out', 100000.00, 'ipapasa ko sayo to', 14, '2025-05-15 05:26:26'),
(148, 14, 'transfer_in', 100000.00, 'ipapasa ko sayo to', 2, '2025-05-15 05:26:26'),
(149, 14, 'deposit', 1000.00, NULL, NULL, '2025-05-15 05:29:30'),
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
(162, 1, 'transfer_in', 400000.97, 'utang', 8, '2025-05-18 10:21:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `birth_year` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `occupation` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `age`, `birth_year`, `email`, `address`, `occupation`, `phone`, `password_hash`, `created_at`, `is_admin`, `status`, `is_active`, `reset_token`, `reset_expires_at`, `profile_picture`) VALUES
(3, 'Renz', 0, 0, 'shaison61@gmail.com', '', '', '', '$2y$10$b4SAB4j.yrr2VLBm71w6V.g7LWB4lheE3.y5JucGdzp5h/DGJ36k2', '2025-04-16 06:16:01', 1, 'approved', 1, NULL, NULL, NULL),
(5, 'Renz', 21, 2004, 'ramosrayson84@gmail.com', '', '', '', '$2y$10$68uffF/bsMZuW1v9M6aYqeqQCC.1x5n7i6T/.dzdAklxqtVQAIFFu', '2025-04-16 08:32:24', 0, 'approved', 1, 'f2c602c8e75aaf5844f33c31f6c4f4faf186efce910d46e76950fe0c5101b086', '2025-05-11 22:45:05', NULL),
(19, 'meow', 20, 2005, 'amiguelll0513@gmail.com', 'batangas', 'tralela', '13242698810', '$2y$10$oInacoL.PERFbh41f63Jz.V6eTE0cmh6fqVPSQWQEv81C2EkAijYK', '2025-04-23 16:32:58', 1, 'approved', 1, 'bbc25abed5b104df2039022e8c8b463e26d84394ddd26d932529f0fb2059d899', '2025-04-25 16:42:31', NULL),
(24, 'tutoy', 21, 2004, '0323-4199@lspu.edu.ph', 'dyaan', 'nyt2', '12345745901', '$2y$10$14jSMq0o01Ujt.JQqfZ2HuwxhAXfI2G3ryuZ2R0fcAmOZ4ZID0YH.', '2025-04-24 14:34:45', 0, 'approved', 1, NULL, NULL, 'profile_24_1747565246.png'),
(27, 'Irene Nicole', 25, 2000, 'senioritaisabel@gmail.com', 'tiaong,quezon', 'okay', '09516025282', '$2y$10$xevT9AO0JWr3wMtcBWDMdOiyvvkoMXY9hhWy76jGggpBfNOUYsAQi', '2025-05-02 09:22:00', 1, 'approved', 1, NULL, NULL, NULL),
(28, 'Paul Paolo A. Mamugay', 19, 2005, 'paulpaolomamugay6@gmail.com', 'No.040', 'Student', '09217844447', '$2y$10$NRCOZht.DPOjB2THNee9/ejPWHBAjmviLPYgJkwqBRRuqVkKwGiRG', '2025-05-05 11:49:44', 1, 'approved', 1, NULL, NULL, NULL),
(29, 'Pamela A. Mamugay', 19, 2005, '0323-3883@lspu.edu.ph', 'No.040 alcantara Subdivision', 'Student', '09217844447', '$2y$10$1GejzUcXpSD69ohd34Vpfeayn2TwPnoB6rhGVIQnoMJRPkVU843de', '2025-05-05 11:52:49', 0, 'approved', 1, '31cd2d42137125ca3b4cbeb1b87926fbc6cbbb4c15166ff6d7ad57096e8e1a53', '2025-05-18 23:56:48', 'profile_29_1746959477.jpg'),
(30, 'Poleene', 19, 2005, 'paolomamugay5@gmail.com', '76', 'ghj', '09217844447', '$2y$10$ZxCX5e6OO8OSQ4jLn1dcV.NSUky8Mk9aK3rvhjkLZDOfKhLspdn7S', '2025-05-05 11:56:06', 0, 'approved', 1, NULL, NULL, NULL),
(31, 'Renz', 20, 2004, 'shaison62@gmail.com', 'brgyawd adwdas', 'wala lang', '09300674760', '$2y$10$hZkS2tAKpeOVR2EWFXvqP.oceH93Hms0XmGbwAATfN48fPESBb6Oq', '2025-05-12 12:01:40', 0, 'approved', 1, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD UNIQUE KEY `account_number_2` (`account_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`balance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`investment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `investment_plans`
--
ALTER TABLE `investment_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `loan_history`
--
ALTER TABLE `loan_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `loan_id` (`loan_id`);

--
-- Indexes for table `login_records`
--
ALTER TABLE `login_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `login_verifications`
--
ALTER TABLE `login_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`otp_id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `balance`
--
ALTER TABLE `balance`
  MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `investment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `investment_plans`
--
ALTER TABLE `investment_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `loan_history`
--
ALTER TABLE `loan_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `login_records`
--
ALTER TABLE `login_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `login_verifications`
--
ALTER TABLE `login_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance`
--
ALTER TABLE `balance`
  ADD CONSTRAINT `balance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
