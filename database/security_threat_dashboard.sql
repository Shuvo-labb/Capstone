-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 10:10 AM
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
-- Database: `security_threat_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `attackevents`
--

CREATE TABLE `attackevents` (
  `attack_id` int(11) NOT NULL,
  `source_ip` varchar(45) NOT NULL,
  `attack_type` enum('SQL Injection','XSS','Directory Traversal','Brute Force','Other') NOT NULL,
  `payload` text NOT NULL,
  `target_endpoint` varchar(255) NOT NULL,
  `attempted_username` varchar(100) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attackevents`
--

INSERT INTO `attackevents` (`attack_id`, `source_ip`, `attack_type`, `payload`, `target_endpoint`, `attempted_username`, `user_agent`, `created_at`) VALUES
(1, '::1', 'SQL Injection', '\' OR \'1\'=\'1', 'login.php', '\' OR \'1\'=\'1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:30:47'),
(2, '::1', 'SQL Injection', '\' OR \'1\'=\'1', 'login.php', '\' OR \'1\'=\'1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:30:52'),
(3, '::1', 'SQL Injection', 'admin\' UNION SELECT 1,2,3--', 'login.php', 'admin\' UNION SELECT 1,2,3--', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:31:39'),
(4, '::1', 'XSS', '<script>alert(\'xss\')</script>', 'login.php', '<script>alert(\'xss\')</script>', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:32:02'),
(5, '::1', 'SQL Injection', '1\' OR \'1\'=\'1', '/Security%20Threat%20Dashboardd/frontend/html/dashboard/index.php?id=1%27%20OR%20%271%27=%271 (GET: id)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:51:36'),
(6, '::1', 'SQL Injection', '1\' OR \'1\'=\'1', '/Security%20Threat%20Dashboardd/frontend/html/dashboard/index.php?id=1%27%20OR%20%271%27=%271 (GET: id)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:52:42'),
(7, '::1', 'SQL Injection', '1\' OR \'1\'=\'1', '/Security%20Threat%20Dashboardd/frontend/html/dashboard/index.php?id=1%27%20OR%20%271%27=%271 (GET: id)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 10:57:28'),
(8, '::1', 'SQL Injection', '1\' OR \'1\'=\'1', '/Security%20Threat%20Dashboardd/frontend/html/index.php?id=1%27%20OR%20%271%27=%271 (GET: id)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 11:03:46'),
(9, '::1', 'XSS', '<script>alert(1)</script>', '/Security%20Threat%20Dashboardd/frontend/html/index.php?q=%3Cscript%3Ealert(1)%3C/script%3E (GET: q)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 11:04:13'),
(10, '::1', 'XSS', '<script>alert(1)</script>', '/Security%20Threat%20Dashboardd/frontend/html/index.php?q=%3Cscript%3Ealert(1)%3C/script%3E (GET: q)', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0', '2026-06-19 11:05:20');

-- --------------------------------------------------------

--
-- Table structure for table `audittrail`
--

CREATE TABLE `audittrail` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audittrail`
--

INSERT INTO `audittrail` (`audit_id`, `user_id`, `username`, `action`, `ip_address`, `created_at`) VALUES
(1, 1, 'admin1', 'Login', '192.0.2.10', '2026-06-18 11:10:00'),
(2, 2, 'securityadmin', 'Viewed Report', '198.51.100.42', '2026-06-18 11:12:00'),
(3, 3, 'testadmin', 'Upload Log', '203.0.113.5', '2026-06-17 09:20:00'),
(4, 1, 'admin1', 'Resolved Threat', '192.0.2.10', '2026-06-16 14:50:00'),
(5, 2, 'securityadmin', 'Changed Settings', '198.51.100.42', '2026-06-15 08:30:00'),
(6, 4, 'ggg', 'Logout', '::1', '2026-06-19 09:43:36'),
(7, 4, 'ggg', 'Login', '::1', '2026-06-19 09:43:42'),
(8, 4, 'ggg', 'Generated Export (PDF - All)', '::1', '2026-06-19 09:49:46'),
(9, 4, 'ggg', 'Generated Export (CSV - All)', '::1', '2026-06-19 09:51:36'),
(10, 4, 'ggg', 'Blocked IP: 203.0.113.5', '::1', '2026-06-19 09:57:09'),
(11, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:00:16'),
(12, 4, 'ggg', 'Login', '::1', '2026-06-19 10:00:37'),
(13, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:24:19'),
(14, 4, 'ggg', 'Login', '::1', '2026-06-19 10:28:48'),
(15, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:30:44'),
(16, 4, 'ggg', 'Login', '::1', '2026-06-19 10:33:27'),
(17, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:43:15'),
(18, 4, 'ggg', 'Login', '::1', '2026-06-19 10:45:04'),
(19, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:53:15'),
(20, 4, 'ggg', 'Login', '::1', '2026-06-19 10:57:17'),
(21, 4, 'ggg', 'Logout', '::1', '2026-06-19 10:58:14'),
(22, 4, 'ggg', 'Login', '::1', '2026-06-19 10:58:41'),
(23, 4, 'ggg', 'Logout', '::1', '2026-06-19 11:01:57'),
(24, 4, 'ggg', 'Login', '::1', '2026-06-19 11:07:24'),
(25, 4, 'ggg', 'Login', '::1', '2026-06-21 21:55:11'),
(26, 4, 'ggg', 'Logout', '::1', '2026-06-21 21:55:20'),
(27, 4, 'ggg', 'Login', '::1', '2026-06-21 21:55:30'),
(28, 4, 'ggg', 'Logout', '::1', '2026-06-21 21:57:15'),
(29, 4, 'ggg', 'Login', '::1', '2026-06-22 07:17:17'),
(30, 4, 'ggg', 'Changed Password', '::1', '2026-06-22 07:18:32'),
(31, 4, 'ggg', 'Logout', '::1', '2026-06-22 07:18:40'),
(32, 4, 'ggg', 'Login', '::1', '2026-06-22 07:18:45'),
(33, 4, 'ggg', 'Resolved Threat ID: 49', '::1', '2026-06-22 07:32:23'),
(34, 4, 'ggg', 'Resolved Threat ID: 39', '::1', '2026-06-22 07:32:24'),
(35, 4, 'ggg', 'Resolved Threat ID: 34', '::1', '2026-06-22 07:32:25'),
(36, 4, 'ggg', 'Resolved Threat ID: 29', '::1', '2026-06-22 07:32:30'),
(37, 4, 'ggg', 'Resolved Threat ID: 24', '::1', '2026-06-22 07:32:30'),
(38, 4, 'ggg', 'Resolved Threat ID: 19', '::1', '2026-06-22 07:32:30'),
(39, 4, 'ggg', 'Resolved Threat ID: 14', '::1', '2026-06-22 07:32:30'),
(40, 4, 'ggg', 'Resolved Threat ID: 44', '::1', '2026-06-22 07:32:30'),
(41, 4, 'ggg', 'Resolved Threat ID: 9', '::1', '2026-06-22 07:32:30'),
(42, 4, 'ggg', 'Resolved Threat ID: 18', '::1', '2026-06-22 07:32:30'),
(43, 4, 'ggg', 'Resolved Threat ID: 48', '::1', '2026-06-22 07:32:31'),
(44, 4, 'ggg', 'Resolved Threat ID: 43', '::1', '2026-06-22 07:32:31'),
(45, 4, 'ggg', 'Resolved Threat ID: 38', '::1', '2026-06-22 07:32:31'),
(46, 4, 'ggg', 'Resolved Threat ID: 33', '::1', '2026-06-22 07:32:31'),
(47, 4, 'ggg', 'Resolved Threat ID: 28', '::1', '2026-06-22 07:32:31'),
(48, 4, 'ggg', 'Resolved Threat ID: 23', '::1', '2026-06-22 07:32:31'),
(49, 4, 'ggg', 'Resolved Threat ID: 47', '::1', '2026-06-22 07:32:31'),
(50, 4, 'ggg', 'Resolved Threat ID: 17', '::1', '2026-06-22 07:32:31'),
(51, 4, 'ggg', 'Resolved Threat ID: 42', '::1', '2026-06-22 07:32:31'),
(52, 4, 'ggg', 'Resolved Threat ID: 37', '::1', '2026-06-22 07:32:31'),
(53, 4, 'ggg', 'Resolved Threat ID: 32', '::1', '2026-06-22 07:32:31'),
(54, 4, 'ggg', 'Resolved Threat ID: 12', '::1', '2026-06-22 07:32:31'),
(55, 4, 'ggg', 'Resolved Threat ID: 22', '::1', '2026-06-22 07:32:31'),
(56, 4, 'ggg', 'Resolved Threat ID: 27', '::1', '2026-06-22 07:32:31'),
(57, 4, 'ggg', 'Resolved Threat ID: 7', '::1', '2026-06-22 07:32:31'),
(58, 4, 'ggg', 'Resolved Threat ID: 36', '::1', '2026-06-22 07:32:31'),
(59, 4, 'ggg', 'Resolved Threat ID: 41', '::1', '2026-06-22 07:32:31'),
(60, 4, 'ggg', 'Resolved Threat ID: 46', '::1', '2026-06-22 07:32:31'),
(61, 4, 'ggg', 'Resolved Threat ID: 31', '::1', '2026-06-22 07:32:31'),
(62, 4, 'ggg', 'Resolved Threat ID: 26', '::1', '2026-06-22 07:32:31'),
(63, 4, 'ggg', 'Resolved Threat ID: 10', '::1', '2026-06-22 07:32:31'),
(64, 4, 'ggg', 'Resolved Threat ID: 45', '::1', '2026-06-22 07:32:31'),
(65, 4, 'ggg', 'Resolved Threat ID: 25', '::1', '2026-06-22 07:32:31'),
(66, 4, 'ggg', 'Resolved Threat ID: 15', '::1', '2026-06-22 07:32:31'),
(67, 4, 'ggg', 'Resolved Threat ID: 40', '::1', '2026-06-22 07:32:31'),
(68, 4, 'ggg', 'Resolved Threat ID: 35', '::1', '2026-06-22 07:32:31'),
(69, 4, 'ggg', 'Resolved Threat ID: 5', '::1', '2026-06-22 07:32:31'),
(70, 4, 'ggg', 'Resolved Threat ID: 20', '::1', '2026-06-22 07:32:31'),
(71, 4, 'ggg', 'Resolved Threat ID: 30', '::1', '2026-06-22 07:32:31'),
(72, 4, 'ggg', 'Resolved Threat ID: 3', '::1', '2026-06-22 07:32:31'),
(73, 4, 'ggg', 'Resolved Threat ID: 2', '::1', '2026-06-22 07:32:31'),
(74, 4, 'ggg', 'Logout', '::1', '2026-06-22 07:50:58'),
(75, 5, 'gggg', 'Login', '::1', '2026-06-22 15:48:49'),
(76, 5, 'gggg', 'Resolved Threat ID: 54', '::1', '2026-06-22 15:55:41'),
(77, 5, 'gggg', 'Resolved Threat ID: 53', '::1', '2026-06-22 15:55:41'),
(78, 5, 'gggg', 'Resolved Threat ID: 52', '::1', '2026-06-22 15:55:41'),
(79, 5, 'gggg', 'Resolved Threat ID: 51', '::1', '2026-06-22 15:55:41'),
(80, 5, 'gggg', 'Resolved Threat ID: 50', '::1', '2026-06-22 15:55:41'),
(81, 5, 'gggg', 'Blocked IP: 198.51.100.42', '::1', '2026-06-22 15:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `failedlogins`
--

CREATE TABLE `failedlogins` (
  `login_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(50) NOT NULL,
  `attempted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failedlogins`
--

INSERT INTO `failedlogins` (`login_id`, `ip_address`, `username`, `attempted_at`) VALUES
(1, '192.0.2.10', 'unknown', '2026-06-18 11:00:00'),
(2, '192.0.2.10', 'unknown', '2026-06-18 10:59:00'),
(3, '192.0.2.10', 'unknown', '2026-06-18 10:58:00'),
(4, '192.0.2.10', 'unknown', '2026-06-18 10:57:00'),
(5, '192.0.2.10', 'unknown', '2026-06-18 10:56:00'),
(6, '192.0.2.10', 'unknown', '2026-06-18 10:55:00'),
(7, '192.0.2.10', 'unknown', '2026-06-18 10:54:00'),
(8, '192.0.2.10', 'unknown', '2026-06-18 10:53:00'),
(9, '192.0.2.10', 'unknown', '2026-06-18 10:52:00'),
(10, '192.0.2.10', 'unknown', '2026-06-18 10:51:00'),
(11, '192.0.2.10', 'unknown', '2026-06-18 10:50:00'),
(12, '192.0.2.10', 'unknown', '2026-06-18 10:49:00'),
(13, '203.0.113.5', 'testuser', '2026-06-17 09:10:00'),
(14, '203.0.113.5', 'testuser', '2026-06-17 09:09:00'),
(15, '203.0.113.5', 'testuser', '2026-06-17 09:08:00'),
(16, '203.0.113.5', 'testuser', '2026-06-17 09:07:00'),
(17, '198.51.100.42', 'admin1', '2026-06-18 10:40:00'),
(18, '198.51.100.42', 'admin1', '2026-06-18 10:39:00'),
(19, '198.51.100.42', 'admin1', '2026-06-18 10:38:00'),
(20, '198.51.100.42', 'admin1', '2026-06-18 10:37:00'),
(21, '198.51.100.42', 'admin1', '2026-06-18 10:36:00'),
(22, '198.51.100.42', 'admin1', '2026-06-18 10:35:00'),
(23, '198.51.100.42', 'admin1', '2026-06-18 10:34:00'),
(24, '::1', 'gg@gg.com', '2026-06-22 15:48:03');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `log_file_name` varchar(255) NOT NULL,
  `file_format` enum('TXT','CSV','JSON') NOT NULL,
  `file_size` int(11) NOT NULL,
  `upload_timestamp` datetime NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `parse_status` enum('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `log_file_name`, `file_format`, `file_size`, `upload_timestamp`, `uploaded_by`, `parse_status`) VALUES
(1, 'apache_log_01.txt', 'TXT', 4200, '2026-04-20 11:00:00', 1, 'Completed'),
(2, 'firewall_data.csv', 'CSV', 8500, '2026-04-20 13:10:00', 2, 'Completed'),
(3, 'web_access.json', 'JSON', 7200, '2026-04-21 09:45:00', 1, 'Pending'),
(4, 'sample_access.log', 'TXT', 854, '2026-06-19 01:41:19', 4, 'Completed'),
(5, 'sample_access.log', 'TXT', 854, '2026-06-19 01:42:19', 4, 'Completed'),
(6, 'sample_access.log', 'TXT', 854, '2026-06-19 01:43:28', 4, 'Completed'),
(7, 'sample_access.log', 'TXT', 854, '2026-06-19 01:52:13', 4, 'Completed'),
(8, 'sample_access.log', 'TXT', 854, '2026-06-19 02:15:13', 4, 'Completed'),
(9, 'sample_access.log', 'TXT', 854, '2026-06-19 02:17:00', 4, 'Completed'),
(10, 'sample_access.log', 'TXT', 854, '2026-06-19 09:44:02', 4, 'Completed'),
(11, 'sample_access.log', 'TXT', 854, '2026-06-19 09:49:28', 4, 'Completed'),
(12, 'sample_access.log', 'TXT', 854, '2026-06-19 10:39:40', 4, 'Completed'),
(13, '1781826079_sample_access.log', 'TXT', 854, '2026-06-22 15:49:22', 5, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `passwordresettokens`
--

CREATE TABLE `passwordresettokens` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `report_date` date NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `file_format` enum('PDF','CSV') NOT NULL,
  `generated_by` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `report_type`, `report_date`, `date_from`, `date_to`, `file_format`, `generated_by`, `file_path`) VALUES
(1, 'all', '2026-06-19', '2026-06-07', '2026-06-19', 'PDF', 4, '/exports/export_1781833786_014888e4.pdf'),
(2, 'all', '2026-06-19', '2026-06-07', '2026-06-19', 'CSV', 4, '/exports/export_1781833896_f5259d78.csv');

-- --------------------------------------------------------

--
-- Table structure for table `suspiciousips`
--

CREATE TABLE `suspiciousips` (
  `ip_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `first_seen` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suspiciousips`
--

INSERT INTO `suspiciousips` (`ip_id`, `ip_address`, `reason`, `first_seen`, `last_seen`, `is_blocked`) VALUES
(1, '192.0.2.10', 'Multiple SQLi attempts', '2026-06-10 09:00:00', '2026-06-18 11:05:00', 1),
(2, '198.51.100.42', 'Status updated via watchlist tools', '2026-06-12 10:15:00', '2026-06-22 15:55:58', 1),
(3, '203.0.113.5', 'Brute force spike on login attempts', '2026-06-11 11:20:00', '2026-06-19 09:57:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `threats`
--

CREATE TABLE `threats` (
  `threat_id` int(11) NOT NULL,
  `log_id` int(11) NOT NULL,
  `threat_type` varchar(100) NOT NULL,
  `severity` enum('Low','Medium','High','Critical') NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `action_taken` varchar(255) DEFAULT NULL,
  `detected_at` datetime NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `threats`
--

INSERT INTO `threats` (`threat_id`, `log_id`, `threat_type`, `severity`, `ip_address`, `action_taken`, `detected_at`, `is_resolved`) VALUES
(50, 13, 'SQL Injection', 'Medium', '198.51.100.10', 'Flagged', '2026-06-18 10:01:05', 1),
(51, 13, 'SQL Injection', 'High', '198.51.100.11', 'Flagged', '2026-06-18 10:02:10', 1),
(52, 13, 'XSS', 'High', '198.51.100.12', 'Flagged', '2026-06-18 10:03:15', 1),
(53, 13, 'XSS', 'High', '198.51.100.13', 'Flagged', '2026-06-18 10:04:20', 1),
(54, 13, 'Brute Force', 'Medium', '203.0.113.5', 'Flagged', '2026-06-18 10:10:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `created_at`, `last_login`, `is_active`) VALUES
(1, 'admin1', '$2y$10$abc123hash', 'admin1@security.com', '2026-04-01 09:00:00', '2026-04-20 10:15:00', 1),
(2, 'securityadmin', '$2y$10$xyz456hash', 'security@dashboard.com', '2026-04-03 14:30:00', '2026-04-21 08:40:00', 1),
(3, 'testadmin', '$2y$10$sample789hash', 'test@security.com', '2026-04-05 11:20:00', NULL, 1),
(4, 'ggg', '$2y$10$mHF3BvF.SwNlJfaeUx22p.rvQhCO.hZ76zHy2LjYtINV7Az6U05t.', 'gg@gg.com', '2026-06-18 19:55:40', '2026-06-22 07:18:45', 1),
(5, 'gggg', '$2y$10$NzdeFMOxpIqa7D6xoyWpUuWqmMs9GJy9iBpJeIg.aDv4M4gpqMQWi', 'gg@ggg.com', '2026-06-22 15:48:38', '2026-06-22 15:48:49', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attackevents`
--
ALTER TABLE `attackevents`
  ADD PRIMARY KEY (`attack_id`),
  ADD KEY `idx_attack_type` (`attack_type`),
  ADD KEY `idx_source_ip` (`source_ip`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `audittrail`
--
ALTER TABLE `audittrail`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `failedlogins`
--
ALTER TABLE `failedlogins`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `passwordresettokens`
--
ALTER TABLE `passwordresettokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `suspiciousips`
--
ALTER TABLE `suspiciousips`
  ADD PRIMARY KEY (`ip_id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Indexes for table `threats`
--
ALTER TABLE `threats`
  ADD PRIMARY KEY (`threat_id`),
  ADD KEY `log_id` (`log_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attackevents`
--
ALTER TABLE `attackevents`
  MODIFY `attack_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `audittrail`
--
ALTER TABLE `audittrail`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `failedlogins`
--
ALTER TABLE `failedlogins`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `passwordresettokens`
--
ALTER TABLE `passwordresettokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suspiciousips`
--
ALTER TABLE `suspiciousips`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `threats`
--
ALTER TABLE `threats`
  MODIFY `threat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `passwordresettokens`
--
ALTER TABLE `passwordresettokens`
  ADD CONSTRAINT `passwordresettokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `threats`
--
ALTER TABLE `threats`
  ADD CONSTRAINT `threats_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `logs` (`log_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
