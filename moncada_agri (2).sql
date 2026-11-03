-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2026 at 06:27 AM
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
-- Database: `moncada_agri`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(80) NOT NULL,
  `entity` varchar(40) DEFAULT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'Generated report', 'report', NULL, 'applications, 0 rows', '::1', '2026-10-18 09:59:44'),
(2, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 10:07:30'),
(3, NULL, 'Failed login', NULL, NULL, 'username: staff', '::1', '2026-10-18 10:14:20'),
(4, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 10:14:29'),
(5, 3, 'Generated report', 'report', NULL, 'applications, 0 rows', '::1', '2026-10-18 10:14:48'),
(6, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 10:15:07'),
(7, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 10:17:56'),
(8, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 10:20:14'),
(9, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 10:46:14'),
(10, 1, 'Generated report', 'report', NULL, 'applications, 0 rows', '::1', '2026-10-18 10:47:08'),
(11, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 10:47:45'),
(12, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 10:49:28'),
(13, 1, 'Generated report', 'report', NULL, 'applications, 0 rows', '::1', '2026-10-18 10:51:57'),
(14, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 10:52:47'),
(15, NULL, 'Failed login', NULL, NULL, 'username: staff', '::1', '2026-10-18 10:53:14'),
(16, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 10:53:22'),
(17, 3, 'Generated report', 'report', NULL, 'applications, 0 rows', '::1', '2026-10-18 11:08:00'),
(18, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 11:12:15'),
(19, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 11:12:29'),
(20, 1, 'Created application', 'application', 1, 'APP-2026-0001', '::1', '2026-10-18 11:14:52'),
(21, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 11:16:42'),
(22, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 11:19:23'),
(23, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 11:22:34'),
(24, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 11:24:50'),
(25, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 11:25:01'),
(26, 1, 'Updated user', 'user', 3, 'staff', '::1', '2026-10-18 11:26:32'),
(27, 1, 'Updated user', 'user', 1, 'admin', '::1', '2026-10-18 11:26:54'),
(28, 1, 'Updated user', 'user', 3, 'staff', '::1', '2026-10-18 11:27:09'),
(29, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-18 11:32:12'),
(30, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 11:34:35'),
(31, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-18 11:35:53'),
(32, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-18 11:36:24'),
(33, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-19 01:21:35'),
(34, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-19 01:22:31'),
(35, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-20 02:16:57'),
(36, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-20 02:17:51'),
(37, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-20 06:15:31'),
(38, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-20 06:15:42'),
(39, NULL, 'Failed login', NULL, NULL, 'username: admin', '::1', '2026-10-20 06:19:06'),
(40, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-20 06:19:23'),
(41, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-20 06:19:31'),
(42, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:04:06'),
(43, 1, 'Updated user', 'user', 1, 'admin', '::1', '2026-10-23 11:09:40'),
(44, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:09:53'),
(45, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:10:05'),
(46, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:10:28'),
(47, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:23:01'),
(48, 1, 'Updated user', 'user', 1, 'admin', '::1', '2026-10-23 11:23:44'),
(49, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:23:54'),
(50, NULL, 'Password reset requested', 'users', 1, NULL, '::1', '2026-10-23 11:24:28'),
(51, NULL, 'Password reset requested', 'users', 1, NULL, '::1', '2026-10-23 11:24:37'),
(52, NULL, 'Password reset requested', 'users', 1, NULL, '::1', '2026-10-23 11:24:43'),
(53, NULL, 'Password reset OTP sent', 'users', 1, NULL, '::1', '2026-10-23 11:30:21'),
(54, NULL, 'Password reset OTP sent', 'users', 1, NULL, '::1', '2026-10-23 11:31:33'),
(55, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:39:41'),
(56, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:39:58'),
(57, NULL, 'Password reset OTP sent', 'users', 1, NULL, '::1', '2026-10-23 11:50:38'),
(58, NULL, 'Password reset completed via OTP', 'users', 1, NULL, '::1', '2026-10-23 11:51:25'),
(59, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:51:46'),
(60, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:52:29'),
(61, NULL, 'Password reset OTP sent', 'users', 1, NULL, '::1', '2026-10-23 11:52:58'),
(62, NULL, 'Password reset completed via OTP', 'users', 1, NULL, '::1', '2026-10-23 11:53:30'),
(63, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:56:21'),
(64, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 11:57:23'),
(65, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 11:59:03'),
(66, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:00:38'),
(67, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:04:59'),
(68, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:11:44'),
(69, 1, 'Generated report', 'report', NULL, 'farms, 1 rows', '::1', '2026-10-23 12:11:50'),
(70, 1, 'Generated report', 'report', NULL, 'farmers, 5 rows', '::1', '2026-10-23 12:12:00'),
(71, 1, 'Generated report', 'report', NULL, 'crops, 1 rows', '::1', '2026-10-23 12:12:05'),
(72, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:12:11'),
(73, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:12:21'),
(74, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 12:12:36'),
(75, 1, 'Created application', 'application', 2, 'APP-2026-0002', '::1', '2026-10-23 12:28:21'),
(76, 1, 'Status changed', 'application', 2, 'Submitted to Approved', '::1', '2026-10-23 12:28:33'),
(77, 1, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 12:40:21'),
(78, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 12:40:32'),
(79, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 12:40:46'),
(80, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 12:41:04'),
(81, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 12:41:19'),
(82, 3, 'Created farmer', 'farmer', 7, 'MON-2026-0006', '::1', '2026-10-23 12:42:27'),
(83, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 12:44:25'),
(84, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 12:44:40'),
(85, 1, 'Approved farmer', 'farmer', 7, NULL, '::1', '2026-10-23 12:44:58'),
(86, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 12:45:36'),
(87, NULL, 'Failed login', NULL, NULL, 'username: admin', '::1', '2026-10-23 12:46:08'),
(88, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 12:46:18'),
(89, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 12:50:38'),
(90, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 12:51:02'),
(91, 1, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 12:57:33'),
(92, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:18:24'),
(93, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:30:38'),
(94, 1, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 13:31:58'),
(95, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 13:32:07'),
(96, 1, 'Generated report', 'report', NULL, 'farms, 11 rows', '::1', '2026-10-23 13:32:14'),
(97, 1, 'Generated report', 'report', NULL, 'applications, 1 rows', '::1', '2026-10-23 13:32:31'),
(98, 1, 'Generated report', 'report', NULL, 'crops, 11 rows', '::1', '2026-10-23 13:32:33'),
(99, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:33:17'),
(100, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:33:34'),
(101, 3, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 13:34:29'),
(102, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:34:40'),
(103, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:35:03'),
(104, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:42:37'),
(105, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:42:48'),
(106, 1, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 13:43:06'),
(107, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:43:33'),
(108, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:43:42'),
(109, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:44:09'),
(110, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-23 13:44:20'),
(111, 1, 'Generated report', 'report', NULL, 'applications, 2 rows', '::1', '2026-10-23 13:44:51'),
(112, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-23 13:45:06'),
(113, NULL, 'Failed login', NULL, NULL, 'username: admin', '::1', '2026-10-29 11:22:17'),
(114, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:22:33'),
(115, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:22:56'),
(116, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:23:05'),
(117, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:23:13'),
(118, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:27:59'),
(119, 3, 'Submitted farmer', 'farmer', 18, 'MON-2026-0017', '::1', '2026-10-29 11:30:24'),
(120, 1, 'Rejected farmer', 'farmer', 18, NULL, '::1', '2026-10-29 11:30:31'),
(121, 3, 'Resubmitted farmer', 'farmer', 18, NULL, '::1', '2026-10-29 11:30:33'),
(122, 1, 'Approved farmer', 'farmer', 18, NULL, '::1', '2026-10-29 11:30:35'),
(123, 1, 'Generated report', 'report', NULL, 'farmers, 17 rows', '::1', '2026-10-29 11:30:55'),
(124, 3, 'Generated report', 'report', NULL, 'farmers, 17 rows', '::1', '2026-10-29 11:30:57'),
(125, 3, 'Submitted farmer', 'farmer', 19, 'MON-2026-0018', '::1', '2026-10-29 11:31:11'),
(126, 3, 'Submitted farmer', 'farmer', 20, 'MON-2026-0017', '::1', '2026-10-29 11:33:48'),
(127, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:34:03'),
(128, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:34:15'),
(129, 1, 'Approved farmer', 'farmer', 20, NULL, '::1', '2026-10-29 11:34:51'),
(130, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:35:09'),
(131, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:35:41'),
(132, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:35:54'),
(133, NULL, 'Failed login', NULL, NULL, 'username: staff', '::1', '2026-10-29 11:36:12'),
(134, 3, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:36:18'),
(135, 3, 'Submitted farmer', 'farmer', 21, 'MON-2026-0018', '::1', '2026-10-29 11:39:48'),
(136, 3, 'Logout', NULL, NULL, NULL, '::1', '2026-10-29 11:39:55'),
(137, 1, 'Login', NULL, NULL, NULL, '::1', '2026-10-29 11:40:04'),
(138, 1, 'Rejected farmer', 'farmer', 21, NULL, '::1', '2026-10-29 11:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `app_no` varchar(30) NOT NULL,
  `farmer_id` int(10) UNSIGNED NOT NULL,
  `farm_id` int(10) UNSIGNED NOT NULL,
  `crop_id` int(10) UNSIGNED NOT NULL,
  `date_applied` date NOT NULL,
  `status` enum('Submitted','Under Review','Verified','Approved','Returned') NOT NULL DEFAULT 'Submitted',
  `remarks` varchar(500) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `app_no`, `farmer_id`, `farm_id`, `crop_id`, `date_applied`, `status`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'APP-2026-0001', 1, 1, 1, '2026-10-18', 'Submitted', NULL, 1, '2026-10-18 11:14:52', '2026-10-18 11:14:52'),
(2, 'APP-2026-0002', 1, 1, 1, '2026-10-23', 'Approved', NULL, 1, '2026-10-23 12:28:21', '2026-10-23 12:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `application_documents`
--

CREATE TABLE `application_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` int(10) UNSIGNED NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `submitted_date` date NOT NULL,
  `status` enum('Submitted','Verified','Rejected') NOT NULL DEFAULT 'Submitted',
  `remarks` varchar(255) DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(10) UNSIGNED NOT NULL,
  `farm_id` int(10) UNSIGNED NOT NULL,
  `crop_type` varchar(80) NOT NULL,
  `variety` varchar(80) DEFAULT NULL,
  `season` enum('Wet','Dry') NOT NULL,
  `planting_date` date NOT NULL,
  `expected_harvest` date DEFAULT NULL,
  `area_planted_ha` decimal(8,2) NOT NULL,
  `status` enum('Planted','Harvested','Failed') NOT NULL DEFAULT 'Planted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `farm_id`, `crop_type`, `variety`, `season`, `planting_date`, `expected_harvest`, `area_planted_ha`, `status`, `created_at`) VALUES
(1, 1, 'Rice', 'Demo variety', 'Wet', '2026-06-15', NULL, 1.50, 'Planted', '2026-10-18 09:59:12'),
(2, 2, 'Rice', 'NSIC Rc 222', 'Wet', '2026-06-20', '2026-10-15', 1.50, 'Planted', '2026-10-23 12:58:04'),
(3, 3, 'Corn', 'Pioneer 3561', 'Dry', '2026-01-10', '2026-04-25', 2.00, 'Harvested', '2026-10-23 12:58:04'),
(4, 4, 'Vegetables', 'Eggplant (Talong)', 'Wet', '2026-07-05', '2026-11-20', 1.00, 'Planted', '2026-10-23 12:58:04'),
(5, 5, 'Rice', 'Tubig 14 (PSB Rc 68)', 'Wet', '2026-06-18', '2026-10-10', 2.50, 'Planted', '2026-10-23 12:58:04'),
(6, 6, 'Rice', 'NSIC Rc 160', 'Wet', '2026-07-01', '2026-10-28', 1.75, 'Planted', '2026-10-23 12:58:04'),
(7, 7, 'Corn', 'NK6410 Syngenta', 'Dry', '2026-02-01', '2026-05-15', 2.00, 'Harvested', '2026-10-23 12:58:04'),
(8, 8, 'Rice', 'Mestiso 20', 'Wet', '2026-06-25', '2026-10-20', 1.20, 'Planted', '2026-10-23 12:58:04'),
(9, 9, 'Vegetables', 'Tomato (Kamatis)', 'Dry', '2026-01-15', '2026-04-30', 1.80, 'Harvested', '2026-10-23 12:58:04'),
(10, 10, 'Sugarcane', 'Phil 8013', 'Wet', '2026-05-10', '2026-12-15', 2.50, 'Planted', '2026-10-23 12:58:04'),
(11, 11, 'Rice', 'NSIC Rc 480', 'Wet', '2026-06-30', '2026-10-25', 1.50, 'Planted', '2026-10-23 12:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmer_id` varchar(30) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `birthday` date DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `total_farm_area_ha` decimal(8,2) DEFAULT NULL,
  `insured_area_ha` decimal(8,2) DEFAULT NULL,
  `crop_type` varchar(80) DEFAULT NULL,
  `farm_location` varchar(255) DEFAULT NULL,
  `barangay` varchar(80) DEFAULT NULL,
  `farm_ownership` enum('Owner','Tenant','Lessee','Caretaker') DEFAULT NULL,
  `status` enum('pending','active','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` varchar(500) DEFAULT NULL,
  `encoded_by` int(10) UNSIGNED DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `date_registered` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `farmer_id`, `full_name`, `birthday`, `id_number`, `address`, `contact_number`, `gender`, `total_farm_area_ha`, `insured_area_ha`, `crop_type`, `farm_location`, `barangay`, `farm_ownership`, `status`, `rejection_reason`, `encoded_by`, `submitted_at`, `reviewed_by`, `reviewed_at`, `date_registered`, `created_at`, `updated_at`) VALUES
(1, 'MON-2026-0001', 'Juan Dela Cruz', NULL, NULL, 'Brgy. Poblacion 1, Moncada, Tarlac', '09171234567', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-01-12', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(2, 'MON-2026-0002', 'Maria Santos', NULL, NULL, 'Brgy. Burgos, Moncada, Tarlac', '09182345678', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-01-20', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(3, 'MON-2026-0003', 'Pedro Ramos', NULL, NULL, 'Brgy. Calamay, Moncada, Tarlac', '09193456789', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-02-03', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(4, 'MON-2026-0004', 'Rosario Mendoza', NULL, NULL, 'Brgy. Ablang-Sapang, Moncada, Tarlac', '09204567890', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-02-15', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(5, 'MON-2026-0005', 'Antonio Villanueva', NULL, NULL, 'Brgy. Lapsing, Moncada, Tarlac', '09215678901', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-03-01', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(7, 'MON-2026-0006', 'mark joseph tamsi', NULL, NULL, 'moncada tarlac', '09121212121', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-10-23', '2026-10-23 12:42:27', '2026-10-23 12:44:58'),
(8, 'MON-2026-0007', 'Eduardo R. Ramos', NULL, NULL, 'Brgy. San Julian, Moncada, Tarlac', '09175550101', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-03-10', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(9, 'MON-2026-0008', 'Corazon M. Aquino', NULL, NULL, 'Brgy. Baquero Norte, Moncada, Tarlac', '09185550202', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-03-15', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(10, 'MON-2026-0009', 'Danilo S. Bautista', NULL, NULL, 'Brgy. Rizal, Moncada, Tarlac', '09195550303', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-02', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(11, 'MON-2026-0010', 'Elena T. Gomez', NULL, NULL, 'Brgy. Banaoang West, Moncada, Tarlac', '09205550404', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-04-18', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(12, 'MON-2026-0011', 'Roberto V. Pascual', NULL, NULL, 'Brgy. Santa Lucia East, Moncada, Tarlac', '09215550505', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-04', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(13, 'MON-2026-0012', 'Teresa L. Magdayo', NULL, NULL, 'Brgy. Capaoayan, Moncada, Tarlac', '09225550606', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-05-19', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(14, 'MON-2026-0013', 'Fernando K. Castro', NULL, NULL, 'Brgy. San Roque, Moncada, Tarlac', '09235550707', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-06-01', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(15, 'MON-2026-0014', 'Gloria P. Dizon', NULL, NULL, 'Brgy. Maligaya, Moncada, Tarlac', '09245550808', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-06-15', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(16, 'MON-2026-0015', 'Rogelio D. Morales', NULL, NULL, 'Brgy. Aringin, Moncada, Tarlac', '09255550909', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-07-02', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(17, 'MON-2026-0016', 'Josefina B. Tolentino', NULL, NULL, 'Brgy. Tubectubang, Moncada, Tarlac', '09265551010', 'Female', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, '2026-07-20', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(20, 'MON-2026-0017', 'mark joseph tamsi', '2026-10-29', '3123123', NULL, '09990006122', 'Male', 1.00, 1.00, 'Corn', 'khjjh', 'Maligaya', 'Owner', 'active', NULL, 3, '2026-10-29 19:33:48', 1, '2026-10-29 19:34:51', '2026-10-29', '2026-10-29 11:33:48', '2026-10-29 11:34:51'),
(21, 'MON-2026-0018', 'mark joseph tamsi', '2026-10-29', '1212', NULL, '09990006121', 'Female', 1.00, 1.00, 'Vegetables', 'asas', 'Baquero Norte', 'Tenant', 'rejected', 'HAhaha', 3, '2026-10-29 19:39:48', 1, '2026-10-29 19:40:46', '2026-10-29', '2026-10-29 11:39:48', '2026-10-29 11:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_approvals`
--

CREATE TABLE `farmer_approvals` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmer_id` int(10) UNSIGNED NOT NULL,
  `action` enum('Submitted','Resubmitted','Approved','Rejected') NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `acted_by` int(10) UNSIGNED DEFAULT NULL,
  `acted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_approvals`
--

INSERT INTO `farmer_approvals` (`id`, `farmer_id`, `action`, `remarks`, `acted_by`, `acted_at`) VALUES
(6, 20, 'Submitted', NULL, 3, '2026-10-29 11:33:48'),
(7, 20, 'Approved', NULL, 1, '2026-10-29 11:34:51'),
(8, 21, 'Submitted', NULL, 3, '2026-10-29 11:39:48'),
(9, 21, 'Rejected', 'HAhaha', 1, '2026-10-29 11:40:46');

-- --------------------------------------------------------

--
-- Table structure for table `farms`
--

CREATE TABLE `farms` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmer_id` int(10) UNSIGNED NOT NULL,
  `farm_name` varchar(120) NOT NULL,
  `barangay` varchar(80) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `area_ha` decimal(8,2) NOT NULL,
  `insured_area_ha` decimal(8,2) DEFAULT NULL,
  `ownership` enum('Owner','Tenant','Lessee','Caretaker') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `farmer_id`, `farm_name`, `barangay`, `location`, `area_ha`, `insured_area_ha`, `ownership`, `created_at`, `updated_at`) VALUES
(1, 1, 'Demo rice field', 'Poblacion 1', 'Near barangay hall', 1.50, NULL, 'Owner', '2026-10-18 09:59:12', '2026-10-18 09:59:12'),
(2, 8, 'San Julian Lowland Rice Field', 'San Julian', 'Sitio Centro, Near Irrigation Canal', 1.80, 1.50, 'Owner', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(3, 9, 'Baquero Hybrid Corn Plantation', 'Baquero Norte', 'Purok 3, Adjacent to Feeder Road', 2.50, 2.00, 'Tenant', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(4, 10, 'Rizal Riverside Vegetable Plot', 'Rizal', 'Sitio Riverside, Lot 142-B', 1.20, 1.00, 'Lessee', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(5, 11, 'Banaoang Certified Seed Farm', 'Banaoang West', 'Purok Silangan, Near Solar Dryer', 3.00, 2.50, 'Owner', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(6, 12, 'Santa Lucia Premium Palay', 'Santa Lucia East', 'Sitio Curva, Cadastral Lot 305', 1.75, 1.75, 'Owner', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(7, 13, 'Capaoayan Commercial Cornfield', 'Capaoayan', 'Purok 2, Behind Elementary School', 2.20, 2.00, 'Tenant', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(8, 14, 'San Roque Inbred Rice Land', 'San Roque', 'Sitio Ilang-Ilang, Lot 88', 1.40, 1.20, 'Lessee', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(9, 15, 'Maligaya High-Value Crop Land', 'Maligaya', 'Purok 4, Near Barangay Outpost', 2.00, 1.80, 'Owner', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(10, 16, 'Aringin Sugarcane & Crop Estate', 'Aringin', 'Sitio Kanluran, National Highway side', 2.80, 2.50, 'Tenant', '2026-10-23 12:58:04', '2026-10-23 12:58:04'),
(11, 17, 'Tubectubang Agro Parcel', 'Tubectubang', 'Purok 1, Boundary with Panampunan', 1.50, 1.50, 'Owner', '2026-10-23 12:58:04', '2026-10-23 12:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, 'f352a939056d70e5ab8f0097525af30815f130a4ac6c34d3d9e01a319b037336', '2026-10-23 14:24:23', 1, '2026-10-23 11:24:23'),
(2, 1, '3de6faa08c73bbbc9c1e334e07660f546542f55f7fcdc267ea4181b31e199373', '2026-10-23 14:24:32', 1, '2026-10-23 11:24:32'),
(3, 1, '7a2c94caec5c90cddd4a583987e6d1cc768bf31bccc5d8a53f667679c6a22236', '2026-10-23 14:24:39', 1, '2026-10-23 11:24:39'),
(4, 1, 'cc3d9ab276def996df7613c14c27093a4d38796e388c343b5b3978685f0766bd', '2026-10-23 13:45:16', 1, '2026-10-23 11:30:16'),
(5, 1, '6685f9b8f06b377144ece12b472e317cd95b12365e7dcf85589014d946b95caf', '2026-10-23 13:46:28', 1, '2026-10-23 11:31:28'),
(6, 1, '8491eab549e7547856fdd6e1e74f7e9fb8c591a0603397fc18be02b161d30f1b', '2026-10-23 20:05:33', 1, '2026-10-23 11:50:33'),
(7, 1, 'ecc8c62fac7bf3329fe1aa7f90ea1c1ec538168d0729d0c7c94912129f5d38e6', '2026-10-23 20:07:52', 1, '2026-10-23 11:52:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(180) DEFAULT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `email`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$ut8dtnEVWm4fFDvE8dhKCOfIVEP9ZnlzmpmS1kjfqpkcc2uIWmnXu', 'System Administrator', 'genjutsukaido25@gmail.com', 'admin', '2026-10-29 19:40:04', '2026-10-18 09:45:59'),
(3, 'staff', '$2y$10$vhKXCESDmfgc6qRh77/7kOiW10YOIFIJ/FWPHxxhFEwrU7itJGV1e', 'MAO Staff', NULL, 'staff', '2026-10-29 19:36:18', '2026-10-18 09:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `verification_records`
--

CREATE TABLE `verification_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `application_id` int(10) UNSIGNED NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_records`
--

INSERT INTO `verification_records` (`id`, `application_id`, `old_status`, `new_status`, `remarks`, `changed_by`, `changed_at`) VALUES
(1, 1, NULL, 'Submitted', 'Application recorded', 1, '2026-10-18 11:14:52'),
(2, 2, NULL, 'Submitted', 'Application recorded', 1, '2026-10-23 12:28:21'),
(3, 2, 'Submitted', 'Approved', NULL, 1, '2026-10-23 12:28:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_time` (`created_at`),
  ADD KEY `idx_log_user` (`user_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_no` (`app_no`),
  ADD KEY `idx_app_status` (`status`),
  ADD KEY `idx_app_date` (`date_applied`),
  ADD KEY `fk_app_farmer` (`farmer_id`),
  ADD KEY `fk_app_farm` (`farm_id`),
  ADD KEY `fk_app_crop` (`crop_id`);

--
-- Indexes for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doc_app` (`application_id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crop_farm` (`farm_id`),
  ADD KEY `idx_crop_type` (`crop_type`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `farmer_id` (`farmer_id`),
  ADD UNIQUE KEY `uq_farmer_id_number` (`id_number`),
  ADD KEY `idx_name` (`full_name`),
  ADD KEY `idx_farmer_status` (`status`);

--
-- Indexes for table `farmer_approvals`
--
ALTER TABLE `farmer_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fa_farmer` (`farmer_id`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_farm_farmer` (`farmer_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_pr_token` (`token`),
  ADD KEY `idx_pr_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `verification_records`
--
ALTER TABLE `verification_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ver_app` (`application_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `farmer_approvals`
--
ALTER TABLE `farmer_approvals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `verification_records`
--
ALTER TABLE `verification_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_app_crop` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`),
  ADD CONSTRAINT `fk_app_farm` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`),
  ADD CONSTRAINT `fk_app_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`);

--
-- Constraints for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD CONSTRAINT `fk_doc_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crops`
--
ALTER TABLE `crops`
  ADD CONSTRAINT `fk_crop_farm` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`);

--
-- Constraints for table `farmer_approvals`
--
ALTER TABLE `farmer_approvals`
  ADD CONSTRAINT `fk_fa_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `fk_farm_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_records`
--
ALTER TABLE `verification_records`
  ADD CONSTRAINT `fk_ver_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
