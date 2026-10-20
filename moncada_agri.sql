-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 09:22 AM
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
(41, 1, 'Logout', NULL, NULL, NULL, '::1', '2026-10-20 06:19:31');

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
(1, 'APP-2026-0001', 1, 1, 1, '2026-10-18', 'Submitted', NULL, 1, '2026-10-18 11:14:52', '2026-10-18 11:14:52');

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
(1, 1, 'Rice', 'Demo variety', 'Wet', '2026-06-15', NULL, 1.50, 'Planted', '2026-10-18 09:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int(10) UNSIGNED NOT NULL,
  `farmer_id` varchar(30) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `date_registered` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `farmer_id`, `full_name`, `address`, `contact_number`, `gender`, `date_registered`, `created_at`, `updated_at`) VALUES
(1, 'MON-2026-0001', 'Juan Dela Cruz', 'Brgy. Poblacion 1, Moncada, Tarlac', '09171234567', 'Male', '2026-01-12', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(2, 'MON-2026-0002', 'Maria Santos', 'Brgy. Burgos, Moncada, Tarlac', '09182345678', 'Female', '2026-01-20', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(3, 'MON-2026-0003', 'Pedro Ramos', 'Brgy. Calamay, Moncada, Tarlac', '09193456789', 'Male', '2026-02-03', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(4, 'MON-2026-0004', 'Rosario Mendoza', 'Brgy. Ablang-Sapang, Moncada, Tarlac', '09204567890', 'Female', '2026-02-15', '2026-10-18 09:45:59', '2026-10-18 09:45:59'),
(5, 'MON-2026-0005', 'Antonio Villanueva', 'Brgy. Lapsing, Moncada, Tarlac', '09215678901', 'Male', '2026-03-01', '2026-10-18 09:45:59', '2026-10-18 09:45:59');

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
  `ownership` enum('Owner','Tenant','Lessee','Caretaker') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farms`
--

INSERT INTO `farms` (`id`, `farmer_id`, `farm_name`, `barangay`, `location`, `area_ha`, `ownership`, `created_at`, `updated_at`) VALUES
(1, 1, 'Demo rice field', 'Poblacion 1', 'Near barangay hall', 1.50, 'Owner', '2026-10-18 09:59:12', '2026-10-18 09:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `role` varchar(30) NOT NULL DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `role`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$SRhygsyZhNDxpitUKm9YBeF5W4mQjH8Yoh2798JiVRhkLms7YBIxG', 'System Administrator', 'admin', '2026-10-20 14:19:23', '2026-10-18 09:45:59'),
(3, 'staff', '$2y$10$vhKXCESDmfgc6qRh77/7kOiW10YOIFIJ/FWPHxxhFEwrU7itJGV1e', 'MAO Staff', 'staff', '2026-10-18 18:53:22', '2026-10-18 09:59:12');

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
(1, 1, NULL, 'Submitted', 'Application recorded', 1, '2026-10-18 11:14:52');

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
  ADD KEY `idx_name` (`full_name`);

--
-- Indexes for table `farms`
--
ALTER TABLE `farms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_farm_farmer` (`farmer_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `farms`
--
ALTER TABLE `farms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `verification_records`
--
ALTER TABLE `verification_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `farms`
--
ALTER TABLE `farms`
  ADD CONSTRAINT `fk_farm_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`);

--
-- Constraints for table `verification_records`
--
ALTER TABLE `verification_records`
  ADD CONSTRAINT `fk_ver_app` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
