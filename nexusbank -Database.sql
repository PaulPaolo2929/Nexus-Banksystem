-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 19, 2025 at 08:18 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u936666569_nexusbank`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`account_id`, `user_id`, `account_number`, `balance`, `created_at`) VALUES
(1, 1, 'SB90284168', 500500.97, '2025-04-16 14:16:34'),
(2, 2, 'SB50491031', 105706.98, '2025-04-16 16:33:52'),
(7, 3, 'SB99139149', 100412.00, '2025-04-24 23:09:40'),
(8, 4, 'SB53061920', 999400.00, '2025-04-24 23:09:40'),
(11, 5, 'SB61285649', 43510.00, '2025-05-02 17:22:20'),
(13, 7, 'SB16865613', 50540.00, '2025-05-05 19:56:20'),
(15, 35, 'SB50703654', 7278.00, '2025-05-31 10:29:50'),
(18, 38, 'SB26110498', 0.00, '2025-06-04 12:55:23'),
(19, 40, 'SB46495057', 30200.00, '2025-06-04 16:07:49'),
(20, 42, 'SB49932115', 0.00, '2025-12-08 11:11:54'),
(21, 43, 'SB61761981', 0.00, '2025-12-08 11:12:14'),
(22, 45, 'SB52868111', 0.00, '2025-12-19 07:37:32'),
(23, 44, 'SB10500751', 0.00, '2025-12-19 07:37:34');

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

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('new','read','replied') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `id_verifications` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_type` varchar(50) NOT NULL,
  `id_file_path` varchar(255) NOT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `id_verifications`
--

INSERT INTO `id_verifications` (`verification_id`, `user_id`, `id_type`, `id_file_path`, `verification_status`, `created_at`, `verified_at`) VALUES
(2, 38, 'drivers_license', 'uploads/id_verifications/id_38_1749041706.png', 'pending', '2025-06-04 12:55:06', NULL),
(4, 40, 'passport', 'uploads/id_verifications/id_40_1749053254.png', 'pending', '2025-06-04 16:07:34', NULL),
(5, 41, 'passport', 'uploads/id_verifications/id_41_1764132668.jpg', 'pending', '2025-11-26 04:51:08', NULL),
(6, 42, 'national_id', 'uploads/id_verifications/id_42_1764770008.png', 'pending', '2025-12-03 13:53:28', NULL),
(7, 43, 'national_id', 'uploads/id_verifications/id_43_1765019424.jpeg', 'pending', '2025-12-06 11:10:24', NULL),
(8, 44, 'national_id', 'uploads/id_verifications/id_44_1765229494.jpg', 'pending', '2025-12-08 21:31:34', NULL),
(9, 45, 'other', 'uploads/id_verifications/id_45_1766051805.png', 'pending', '2025-12-18 09:56:45', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`investment_id`, `user_id`, `plan_name`, `amount`, `interest_rate`, `duration_months`, `status`, `created_at`, `matured_at`, `plan_id`, `withdrawn_at`) VALUES
(1, 5, '', 1000.00, 0.00, 0, 'withdrawn', '2025-05-01 10:14:36', '2025-05-01 10:22:52', 1, NULL),
(2, 5, '', 500.00, 0.00, 0, 'matured', '2025-05-02 16:56:06', NULL, 1, '2025-05-02 17:00:42'),
(3, 5, '', 500.00, 0.00, 0, 'matured', '2025-05-02 17:02:11', '2025-11-26 04:45:49', 1, NULL),
(5, 7, '', 1000.00, 0.00, 0, 'active', '2025-05-08 22:14:30', NULL, 2, NULL),
(7, 4, NULL, 500.00, 0.00, 12, 'matured', '2025-05-18 03:13:58', '2025-11-26 04:45:49', 1, NULL),
(8, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:14:19', NULL, 2, NULL),
(9, 4, NULL, 2000.00, 0.00, 12, 'active', '2025-05-18 03:15:56', NULL, 2, NULL),
(10, 4, NULL, 600.00, 0.00, 12, 'matured', '2025-05-18 05:15:57', '2025-11-26 04:45:49', 1, NULL),
(11, 35, NULL, 12000.00, 0.00, 12, 'active', '2025-06-02 17:19:34', NULL, 4, NULL),
(12, 5, NULL, 5000.00, 0.00, 12, 'active', '2025-06-05 03:06:03', NULL, 4, NULL),
(13, 40, NULL, 25000.00, 0.00, 12, 'active', '2025-11-26 07:25:37', NULL, 3, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `is_paid` enum('yes','no') NOT NULL DEFAULT 'no',
  `total_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `penalty_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `id_selfie_file_path` varchar(255) DEFAULT NULL,
  `id_document_file_path` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `amount`, `interest_rate`, `term_months`, `status`, `created_at`, `approved_at`, `purpose`, `is_paid`, `total_due`, `penalty_amount`, `id_selfie_file_path`, `id_document_file_path`, `due_date`) VALUES
(1, 5, 100.00, 5.00, 1, 'approved', '2025-04-29 10:55:03', '2025-04-29 02:55:14', 'awdasd', 'no', 105.00, 214.20, NULL, NULL, '2025-05-29'),
(4, 4, 20000.00, 4.50, 12, 'approved', '2025-05-18 10:15:04', '2025-05-18 17:15:19', 'w', 'no', 20900.00, 0.00, NULL, NULL, '2026-05-18'),
(58, 5, 1000.00, 5.00, 12, 'approved', '2025-05-31 12:18:59', '2025-05-31 04:19:20', 'asad', 'no', 550.00, 0.00, NULL, NULL, '2026-05-31'),
(59, 35, 1000.00, 5.00, 1, 'approved', '2025-06-02 15:57:45', '2025-06-02 10:00:38', 'Hello world', 'no', 1050.00, 1785.00, NULL, NULL, '2025-07-02'),
(60, 35, 20000.00, 4.50, 12, 'approved', '2025-06-02 16:03:16', '2025-06-02 10:03:50', 'Gusto ko bumili ng bahay HAHAHA', 'no', 18678.00, 0.00, NULL, NULL, '2026-06-02'),
(61, 5, 100.00, 5.00, 12, 'approved', '2025-06-04 21:08:10', '2025-12-19 15:38:20', 'para sa akong mga anak', 'no', 105.00, 0.00, '../uploads/loan_verifications/selfie_5_1749042489.png', '../uploads/loan_verifications/document_5_1749042490.png', '2026-12-18'),
(62, 5, 1000.00, 5.00, 12, 'rejected', '2025-06-04 15:08:18', NULL, 'asdwas', 'no', 1050.00, 0.00, 'uploads/loan_verifications/selfie_5_1749049698.png', 'uploads/loan_verifications/document_5_1749049698.png', NULL),
(63, 5, 1000.00, 5.00, 1, 'approved', '2025-06-04 15:17:43', '2025-12-19 15:38:25', 'ganon e', 'no', 1050.00, 0.00, 'uploads/loan_verifications/selfie_5_1749050263.png', 'uploads/loan_verifications/document_5_1749050263.png', '2026-01-18'),
(64, 5, 1000.00, 5.00, 1, 'approved', '2025-06-04 15:32:30', '2025-06-05 00:44:38', 'asd', 'no', 1050.00, 1753.50, 'uploads/loan_verifications/selfie_5_1749051150.png', 'uploads/loan_verifications/document_5_1749051150.png', '2025-07-04'),
(65, 40, 200.00, 5.00, 13, 'approved', '2025-06-04 16:09:55', '2025-06-05 00:11:16', 'dsfds', 'no', 210.00, 0.00, 'uploads/loan_verifications/selfie_40_1749053395.png', 'uploads/loan_verifications/document_40_1749053395.png', '2026-07-04'),
(66, 5, 1000.00, 5.00, 1, 'approved', '2025-06-05 01:40:37', '2025-06-05 09:43:09', 'asd', 'no', 1050.00, 1753.50, 'uploads/loan_verifications/selfie_5_1749087637.jpg', 'uploads/loan_verifications/document_5_1749087637.jpg', '2025-07-04'),
(67, 5, 1000.00, 5.00, 1, 'approved', '2025-06-05 03:09:39', '2025-06-05 11:11:05', 'para sa pamilya', 'no', 1050.00, 1753.50, 'uploads/loan_verifications/selfie_5_1749092979.jpg', 'uploads/loan_verifications/document_5_1749092979.jpg', '2025-07-04'),
(68, 40, 40000.00, 4.50, 20, 'approved', '2025-11-26 04:45:32', '2025-11-26 15:00:23', 'Pang Jolibee', 'no', 41800.00, 0.00, 'uploads/loan_verifications/selfie_40_1764132332.jpg', 'uploads/loan_verifications/document_40_1764132332.jpg', '2027-07-25'),
(69, 40, 3000.00, 5.00, 12, 'rejected', '2025-11-26 07:03:58', NULL, 'Wala lang', 'no', 3150.00, 0.00, 'uploads/loan_verifications/selfie_40_1764140638.jpg', 'uploads/loan_verifications/document_40_1764140638.jpg', NULL);

--
-- Triggers `loans`
--
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

CREATE TABLE `loan_history` (
  `history_id` int(11) NOT NULL,
  `loan_id` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `changed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_history`
--

INSERT INTO `loan_history` (`history_id`, `loan_id`, `status`, `changed_at`) VALUES
(40, 58, 'approved', '2025-05-31 04:19:20'),
(41, 59, 'approved', '2025-06-02 10:00:38'),
(42, 60, 'approved', '2025-06-02 10:03:50'),
(43, 62, 'rejected', '2025-06-04 23:15:54'),
(44, 65, 'approved', '2025-06-05 00:11:16'),
(45, 64, 'approved', '2025-06-05 00:44:38'),
(46, 66, 'approved', '2025-06-05 09:43:09'),
(47, 67, 'approved', '2025-06-05 11:11:05'),
(48, 68, 'approved', '2025-11-26 15:00:23'),
(49, 69, 'rejected', '2025-11-26 15:06:01'),
(50, 61, 'approved', '2025-12-19 15:38:20'),
(51, 63, 'approved', '2025-12-19 15:38:25');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(59, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 13:01:07', '2025-06-04 13:01:07'),
(60, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'failed', '2025-06-04 14:15:44', '2025-06-04 14:15:44'),
(61, 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 14:15:50', '2025-06-04 14:15:50'),
(62, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'failed', '2025-06-04 14:54:50', '2025-06-04 14:54:50'),
(63, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 14:54:59', '2025-06-04 14:54:59'),
(66, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:04:04', '2025-06-04 15:04:04'),
(67, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:08:32', '2025-06-04 15:08:32'),
(68, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:16:28', '2025-06-04 15:16:28'),
(69, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'failed', '2025-06-04 15:18:03', '2025-06-04 15:18:03'),
(70, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:18:10', '2025-06-04 15:18:10'),
(71, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:31:18', '2025-06-04 15:31:18'),
(72, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:32:42', '2025-06-04 15:32:42'),
(73, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:41:06', '2025-06-04 15:41:06'),
(74, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 15:43:29', '2025-06-04 15:43:29'),
(75, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:04:57', '2025-06-04 16:04:57'),
(77, 40, '49.144.200.25', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:07:57', '2025-06-04 16:07:57'),
(78, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:41:43', '2025-06-04 16:41:43'),
(79, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:42:26', '2025-06-04 16:42:26'),
(80, 1, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:43:47', '2025-06-04 16:43:47'),
(81, 5, '136.158.65.120', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-04 16:44:56', '2025-06-04 16:44:56'),
(82, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 01:25:20', '2025-06-05 01:25:20'),
(83, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 01:26:15', '2025-06-05 01:26:15'),
(84, 5, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 01:35:27', '2025-06-05 01:35:27'),
(85, 1, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 01:41:02', '2025-06-05 01:41:02'),
(86, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 01:53:24', '2025-06-05 01:53:24'),
(87, 1, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'failed', '2025-06-05 02:48:50', '2025-06-05 02:48:50'),
(88, 1, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 02:49:06', '2025-06-05 02:49:06'),
(89, 5, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 02:55:58', '2025-06-05 02:55:58'),
(90, 1, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 03:09:58', '2025-06-05 03:09:58'),
(91, 5, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-05 03:18:24', '2025-06-05 03:18:24'),
(92, 35, '2001:4453:576:2600:9c6a:70e:4142:702c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'success', '2025-06-22 09:27:55', '2025-06-22 09:27:55'),
(93, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 04:36:42', '2025-11-26 04:36:42'),
(94, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 04:40:17', '2025-11-26 04:40:17'),
(95, 41, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'failed', '2025-11-26 04:51:47', '2025-11-26 04:51:47'),
(96, 41, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 04:52:22', '2025-11-26 04:52:22'),
(97, 41, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 04:53:38', '2025-11-26 04:53:38'),
(98, 35, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 06:55:10', '2025-11-26 06:55:10'),
(99, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'failed', '2025-11-26 07:01:18', '2025-11-26 07:01:18'),
(100, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 07:01:28', '2025-11-26 07:01:28'),
(101, 35, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 07:04:42', '2025-11-26 07:04:42'),
(102, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'failed', '2025-11-26 07:34:11', '2025-11-26 07:34:11'),
(103, 40, '203.177.99.202', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'success', '2025-11-26 07:34:22', '2025-11-26 07:34:22'),
(104, 42, '41.60.23.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'failed', '2025-12-03 13:53:47', '2025-12-03 13:53:47'),
(105, 42, '41.60.23.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'failed', '2025-12-03 13:57:51', '2025-12-03 13:57:51'),
(106, 40, '2001:4453:5ae:9f00:bcbd:1a77:f8e3:9f9c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'failed', '2025-12-08 11:09:06', '2025-12-08 11:09:06'),
(107, 40, '2001:4453:5ae:9f00:bcbd:1a77:f8e3:9f9c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'failed', '2025-12-08 11:09:21', '2025-12-08 11:09:21'),
(108, 35, '2001:4453:5ae:9f00:bcbd:1a77:f8e3:9f9c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-08 11:09:39', '2025-12-08 11:09:39'),
(109, 40, '2001:4453:5ae:9f00:bcbd:1a77:f8e3:9f9c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'failed', '2025-12-08 11:24:08', '2025-12-08 11:24:08'),
(110, 40, '2001:4453:5ae:9f00:bcbd:1a77:f8e3:9f9c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-08 11:28:14', '2025-12-08 11:28:14'),
(111, 44, '176.123.23.106', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36', 'failed', '2025-12-08 21:31:57', '2025-12-08 21:31:57'),
(112, 45, '115.241.45.226', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'failed', '2025-12-18 10:08:45', '2025-12-18 10:08:45'),
(113, 40, '2001:4453:5ae:9f00:b5e2:4447:8cd0:9ff7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-19 07:32:54', '2025-12-19 07:32:54'),
(114, 35, '2001:4453:5ae:9f00:b5e2:4447:8cd0:9ff7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-19 07:34:05', '2025-12-19 07:34:05');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_verifications`
--

INSERT INTO `login_verifications` (`id`, `user_id`, `token`, `verified`, `ip_address`, `user_agent`, `status`, `created_at`, `expires_at`) VALUES
(108, 40, '4bd9ff974e183d1fb70e704334d2c87c6e0fc66997c677ec40ae3bf1d6797d20', 1, '2001:4453:5ae:9f00:b5e2:4447:8cd0:9ff7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-19 07:33:14', '2025-12-19 07:48:14'),
(109, 35, '29dda5840c61a6f64824a9ab7388ae598cb47dd2d837669fe2b477fe6ca0afa7', 1, '2001:4453:5ae:9f00:b5e2:4447:8cd0:9ff7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'success', '2025-12-19 07:35:00', '2025-12-19 07:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verification`
--

INSERT INTO `otp_verification` (`id`, `email`, `otp`, `created_at`, `expires_at`, `is_used`) VALUES
(2, '0323-4199@lspu.edu.ph', '966304', '2025-04-23 15:25:43', '2025-04-23 22:30:43', 0),
(3, 'amiguelll0513@gmail.com', '046422', '2025-05-18 09:51:44', '2025-05-18 16:56:44', 1),
(4, '0323-4199@lspu.edu.ph', '097192', '2025-05-18 12:14:53', '2025-05-18 19:19:53', 1),
(5, 'shaison62@gmail.com', '638497', '2025-05-15 05:50:13', '2025-05-14 21:55:13', 1),
(7, 'senioritaisabel@gmail.com', '686762', '2025-05-02 09:22:47', '2025-05-02 01:27:47', 1),
(75, 'shaison61@gmail.com', '845178', '2025-06-05 03:09:58', '2025-06-05 11:14:58', 1),
(76, 'ramosrayson84@gmail.com', '526554', '2025-06-05 03:18:24', '2025-06-05 11:23:24', 1),
(84, '0323-3811@lspu.edu.ph', '437076', '2025-11-26 04:53:38', '2025-11-26 12:58:38', 1),
(91, 'paulpaolomamugay6@gmail.com', '194334', '2025-12-19 07:32:54', '2025-12-19 15:37:54', 1),
(92, '0323-3883@lspu.edu.ph', '012895', '2025-12-19 07:34:05', '2025-12-19 15:39:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `type` enum('deposit','withdrawal','transfer_in','transfer_out','investment','approved_loan','withdrawal_matured_investment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `related_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(166, 2, '', 10500.00, 'Full Loan Payment', NULL, '2025-05-27 04:07:54'),
(167, 2, '', 1050.00, 'Full Loan Payment', NULL, '2025-05-27 04:08:06'),
(168, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-27 15:06:53'),
(169, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-05-31 04:42:21'),
(170, 11, '', 500.00, 'Partial Loan Payment', NULL, '2025-05-30 20:47:43'),
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
(217, 15, '', 2222.00, 'Partial Loan Payment', NULL, '2025-06-02 02:07:13'),
(218, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-06-04 13:30:53'),
(219, 19, 'approved_loan', 200.00, 'Loan approved: ₱200.00', NULL, '2025-06-04 16:11:16'),
(220, 19, 'deposit', 10000.00, 'Deposit of $10,000.00', NULL, '2025-06-04 16:14:16'),
(221, 11, 'approved_loan', 1000.00, 'Loan approved: ₱1,000.00', NULL, '2025-06-04 16:44:38'),
(222, 11, 'approved_loan', 1000.00, 'Loan approved: ₱1,000.00', NULL, '2025-06-05 01:43:09'),
(223, 11, 'deposit', 1000.00, 'Deposit of $1,000.00', NULL, '2025-06-05 02:57:38'),
(224, 11, 'transfer_out', 50000.00, 'para sa pamilya', 13, '2025-06-05 03:04:41'),
(225, 13, 'transfer_in', 50000.00, 'para sa pamilya', 11, '2025-06-05 03:04:41'),
(226, 11, 'investment', -5000.00, 'Investment in High Yield Bond', NULL, '2025-06-05 03:06:03'),
(227, 11, 'approved_loan', 1000.00, 'Loan approved: ₱1,000.00', NULL, '2025-06-05 03:11:05'),
(228, 19, 'deposit', 20000.00, 'Deposit of $20,000.00', NULL, '2025-11-26 04:42:51'),
(229, 19, 'withdrawal', 15000.00, 'Withdrawal of $15,000.00', NULL, '2025-11-26 04:43:43'),
(230, 19, 'approved_loan', 40000.00, 'Loan approved: ₱40,000.00', NULL, '2025-11-26 07:00:23'),
(231, 19, 'investment', -25000.00, 'Investment in Aggressive Growth', NULL, '2025-11-26 07:25:37'),
(232, 11, 'approved_loan', 100.00, 'Loan approved: ₱100.00', NULL, '2025-12-19 07:38:20'),
(233, 11, 'approved_loan', 1000.00, 'Loan approved: ₱1,000.00', NULL, '2025-12-19 07:38:25');

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
  `profile_picture` varchar(255) DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `blocked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `age`, `birth_year`, `email`, `address`, `occupation`, `phone`, `password_hash`, `created_at`, `is_admin`, `status`, `is_active`, `reset_token`, `reset_expires_at`, `profile_picture`, `login_attempts`, `blocked_until`) VALUES
(1, 'Shaison', 24, 2000, 'shaison61@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$LDpOyFZkS9D.mRYfzdnOdOtvhjqhE1bk5B/85d/bXgX1/CKLXlHfe', '2025-04-16 08:41:45', 1, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(2, 'Amiguel', 24, 2000, 'amiguelll0513@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(3, 'Shaison2', 24, 2000, 'shaison62@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(4, 'Isabel', 24, 2000, 'senioritaisabel@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 1, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(5, 'Rayson', 24, 2000, 'ramosrayson84@gmail.com', 'Manila', 'Student', '09123456789', '$2y$10$FTiIs2N1IyTHLvJ7QBjFhuA..klvK9ehN2YmMozrtRxn5VVI6eUtG', '2025-04-16 08:52:35', 0, 'approved', 1, 'ffc4d96c894e785e289208178e9ace3b56d1126ad568ebcdd022a782f098cc66', '2025-06-05 10:32:11', 'profile_5_1748358975.png', 0, NULL),
(7, 'LSPU Student 1', 24, 2000, '0323-4199@lspu.edu.ph', 'Manila', 'Student', '09123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-16 08:52:35', 0, 'approved', 1, NULL, NULL, 'default.jpg', 0, NULL),
(35, 'Pamela A. Mamugay', 19, 2005, '0323-3883@lspu.edu.ph', 'Poblacion street Brgy-3D', 'Student', '09217844447', '$2y$10$.krvdQIYwvC2CB.Do9ugcuDD8pifxhNOlW.fp0BruQ9n6uHLAvEHy', '2025-05-31 10:25:04', 1, 'approved', 1, NULL, NULL, 'profile_35_1748687596.jpg', 0, NULL),
(38, 'nexus banking system', 20, 2004, 'nexusbanksystem@gmail.com', 'brgy San Isidro Calauan laguna', 'Student', '09300674760', '$2y$10$pefsBS9ZUH9uRng3MC0TDOIg5gR74SPj2DSIUgXVqi4g7iS30oTqa', '2025-06-04 12:55:06', 0, 'approved', 1, NULL, NULL, NULL, 0, NULL),
(40, 'Mamugay Aro Paul Paolo', 20, 2005, 'paulpaolomamugay6@gmail.com', 'NO.040 (Pob)Alcantara Subdivision, Brgy 3-D', 'Doctor', '09217844447', '$2y$10$bHFspPwHCVlJA8UF7UVUjOKY5iP58AZZsj/KpU3.WXaNPXG9K49JO', '2025-06-04 16:07:34', 0, 'approved', 1, NULL, NULL, 'profile_40_1764132121.png', 0, NULL),
(41, 'Nicole', 20, 2005, '0323-3811@lspu.edu.ph', 'NO.040 (Pob)Alcantara Subdivision, Brgy 3-D', 'Kupal', '09217844447', '$2y$10$ZbkSg0DRuH3NqR/IpM7YkOyhXhISZCaPGvTCWVCZJnVcxCRs1Zx.O', '2025-11-26 04:51:08', 1, 'approved', 1, NULL, NULL, NULL, 0, NULL),
(42, 'emmanuel', 20, 2005, 'musondabwalya475@gmail.com', 'zambia', 'self employed', '+260960860021', '$2y$10$62ywoRg1ilClk4HBaRkyS.4lBF8rC/eu5A2ifEEdMWYGIINA/RZV6', '2025-12-03 13:53:28', 0, 'approved', 1, NULL, NULL, NULL, 0, NULL),
(43, 'Misbahus Surur', 37, 1988, 'misanpedia@gmail.com', 'Jl Villa Puncak Tidar Ruko Elpico', 'IT Support', '085814443199', '$2y$10$sa.HSdmwquYoevlr2tNDO.GwfjIt4HTdo5H8TkLKmAvZ4t8W.bzki', '2025-12-06 11:10:24', 0, 'approved', 1, NULL, NULL, NULL, 0, NULL),
(44, 'mohammed shayaa', 30, 1995, 'mshay2024mshay@gmail.com', 'Yemen', 'forex', '773178684', '$2y$10$yFLmFIYHSx7AkW6l1bfUBOIDbUkMhL1PvW3sJmKQSAmhiqSmrdhfy', '2025-12-08 21:31:34', 0, 'approved', 1, NULL, NULL, NULL, 1, NULL),
(45, 'Hemant singh', 27, 1998, 'hemant2307sb@gmail.com', 'Room no -315/C , second floor , regal apartment', 'developer', '08419924876', '$2y$10$bpVkVdGYAQ1UO2lYDkjWU.LsVKLnxrjyLt6Xvo2.J8sNTxFO5zar2', '2025-12-18 09:56:45', 0, 'approved', 1, NULL, NULL, NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`balance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `id_verifications`
--
ALTER TABLE `id_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`investment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `related_account_id` (`related_account_id`);

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
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `balance`
--
ALTER TABLE `balance`
  MODIFY `balance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `id_verifications`
--
ALTER TABLE `id_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `investment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `investment_plans`
--
ALTER TABLE `investment_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `loan_history`
--
ALTER TABLE `loan_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `login_records`
--
ALTER TABLE `login_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `login_verifications`
--
ALTER TABLE `login_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

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
