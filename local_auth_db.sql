-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 11:57 PM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `local_auth_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username_attempted` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `username_attempted`, `ip_address`, `user_agent`, `success`, `logged_at`) VALUES
(1, 1, 'admin', '::1', '', 1, '2026-09-23 13:02:48'),
(2, 1, 'admin', '::1', '', 1, '2026-09-23 13:03:10'),
(3, 3, 'user1', '::1', '', 1, '2026-09-23 13:03:23'),
(4, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 13:13:44'),
(5, 1, 'admin', '::1', '', 1, '2026-09-23 13:17:03'),
(6, 1, 'admin', '::1', '', 1, '2026-09-23 13:17:37'),
(7, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 13:21:54'),
(8, 1, 'admin', '::1', '', 1, '2026-09-23 13:31:35'),
(9, 1, 'admin', '::1', '', 1, '2026-09-23 13:39:30'),
(10, 1, 'admin', '::1', '', 1, '2026-09-23 13:39:46'),
(11, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 13:47:11'),
(12, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 0, '2026-09-23 13:47:43'),
(13, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 0, '2026-09-23 13:47:58'),
(14, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 13:48:10'),
(15, NULL, 'Hassan', '::1', '', 0, '2026-09-23 14:00:07'),
(16, 3, 'user1', '::1', '', 0, '2026-09-23 14:00:07'),
(17, 3, 'user1', '::1', '', 1, '2026-09-23 14:00:07'),
(18, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 14:01:32'),
(19, 1, 'admin', '::1', '', 1, '2026-09-23 14:06:13'),
(20, 3, 'user1', '::1', '', 1, '2026-09-23 14:06:45'),
(21, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 14:08:44'),
(22, 1, 'admin', '::1', '', 1, '2026-09-23 14:11:42'),
(23, 1, 'admin', '::1', '', 1, '2026-09-23 14:11:54'),
(24, 2, 'tech1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 19:00:44'),
(25, 3, 'user1', '::1', '', 1, '2026-09-23 19:07:01'),
(26, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 19:25:35'),
(27, 1, 'admin', '::1', '', 1, '2026-09-23 20:10:40'),
(28, NULL, 'stu1', '::1', '', 0, '2026-09-23 20:10:40'),
(29, 1, 'admin', '::1', '', 1, '2026-09-23 20:10:56'),
(30, NULL, 'stu1', '::1', '', 1, '2026-09-23 20:10:57'),
(31, 1, 'admin', '::1', '', 1, '2026-09-23 20:11:24'),
(32, NULL, 'stu1', '::1', '', 1, '2026-09-23 20:11:24'),
(33, 2, 'tech1', '::1', '', 1, '2026-09-23 20:11:44'),
(34, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 20:13:21'),
(35, 8, 'aisha1', '::1', '', 1, '2026-09-23 20:16:18'),
(36, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 20:31:03'),
(37, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 20:46:10'),
(38, 1, 'admin', '::1', '', 0, '2026-09-23 20:55:52'),
(39, 1, 'admin', '::1', '', 1, '2026-09-23 20:56:27'),
(40, 1, 'admin', '::1', '', 1, '2026-09-23 20:56:42'),
(101, 1, 'admin', '::1', '', 1, '2026-09-23 20:57:02'),
(162, 1, 'admin', '::1', '', 1, '2026-09-23 20:57:07'),
(163, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 21:01:47'),
(164, 3, 'user1', '10.178.3.1', 'Mozilla/5.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/151.0.7922.199 Mobile Safari/537.36 OPR/97.1.2254.80843', 1, '2026-09-23 21:13:24'),
(165, 1, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 21:23:38'),
(166, 3, 'user1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 21:29:21'),
(167, 2, 'tech1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', 1, '2026-09-23 21:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `description`) VALUES
(1, 'dashboard.view', 'Access the dashboard'),
(2, 'profile.edit', 'Edit own profile and change own password'),
(3, 'users.view', 'View the user list'),
(4, 'users.create', 'Create new user accounts'),
(5, 'users.edit', 'Edit user account details'),
(6, 'users.manage', 'Activate or deactivate users and reset passwords'),
(7, 'roles.manage', 'Create, edit and delete roles'),
(8, 'permissions.assign', 'Assign permissions to roles'),
(9, 'logs.view', 'View authentication logs');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Administrator', 'Full access to all users, roles, permissions and authentication logs', '2026-09-23 11:47:34'),
(2, 'Network Technician', 'Can view users and authentication logs; cannot modify accounts', '2026-09-23 11:47:34'),
(3, 'Student', 'Student with access to their own dashboard and profile only', '2026-09-23 11:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(4, 1, 2),
(9, 1, 3),
(6, 1, 4),
(7, 1, 5),
(8, 1, 6),
(5, 1, 7),
(3, 1, 8),
(2, 1, 9),
(16, 2, 1),
(18, 2, 2),
(19, 2, 3),
(17, 2, 9),
(23, 3, 1),
(24, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `level` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `first_name`, `last_name`, `email`, `department`, `level`, `phone`, `created_at`) VALUES
(1, 3, 'CSC/2023/0021', 'Hassan', 'Abdullahi', 'user1@local.test', 'Computer Science', '400 Level', '+234 803 555 0192', '2026-09-23 20:06:43'),
(3, 8, 'CSC/2022/0031', 'Aisha', 'Bello', 'aisha1@local.test', 'Computer Science', '300 Level', '+234 802 111 2233', '2026-09-23 20:15:43'),
(4, 9, 'SWE/2022/0017', 'Ibrahim', 'Musa', 'ibrahim1@local.test', 'Software Engineering', '300 Level', '+234 803 222 3344', '2026-09-23 20:15:43'),
(5, 10, 'IT/2023/0045', 'Chidera', 'Okafor', 'chidera1@local.test', 'Information Technology', '200 Level', '+234 814 333 4455', '2026-09-23 20:15:43'),
(6, 11, 'EEE/2021/0028', 'Emmanuel', 'Adeyemi', 'emmanuel1@local.test', 'Electrical Engineering', '400 Level', '+234 805 444 5566', '2026-09-23 20:15:43'),
(7, 12, 'CYS/2023/0009', 'Fatima', 'Bello', 'fatima1@local.test', 'Cyber Security', '200 Level', '+234 816 555 6677', '2026-09-23 20:15:43'),
(8, 13, 'MEE/2022/0036', 'Samuel', 'Eze', 'samuel1@local.test', 'Mechanical Engineering', '300 Level', '+234 806 666 7788', '2026-09-23 20:15:43'),
(9, 14, 'CSC/2021/0102', 'Blessing', 'Uche', 'blessing1@local.test', 'Computer Science', '400 Level', '+234 807 777 8899', '2026-09-23 20:15:43'),
(10, 15, 'IT/2021/0071', 'John', 'Agboola', 'john1@local.test', 'Information Technology', '400 Level', '+234 808 888 9900', '2026-09-23 20:15:43'),
(11, 16, 'SWE/2023/0053', 'Maryam', 'Lawal', 'maryam1@local.test', 'Software Engineering', '200 Level', '+234 809 999 0011', '2026-09-23 20:15:43'),
(12, 17, 'CYS/2022/0014', 'David', 'Nwosu', 'david1@local.test', 'Cyber Security', '300 Level', '+234 810 000 1122', '2026-09-23 20:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password_hash`, `role_id`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@local.test', 'System Administrator', '$2y$10$yyBBbWocsMOU7hbCNzgbjObGBFE9TcdecD1bCwyPWMhbMR4QfCpam', 1, 1, NULL, '2026-09-23 11:47:34', '2026-09-23 12:49:35'),
(2, 'tech1', 'tech1@local.test', 'Network Technician One', '$2y$10$tKE0Z8xfM60WTTlR4pZtFeqd7ZYrDqXKo5wCp9tW4Y.h6M4/Mcwlq', 2, 1, NULL, '2026-09-23 11:47:34', '2026-09-23 12:49:36'),
(3, 'user1', 'user1@local.test', 'Hassan Abdullahi', '$2y$10$66Vte/XMzslbeeLlV4f/huTfxzww4eCzsuseTfxhq5RXuYIDPvv0W', 3, 1, NULL, '2026-09-23 11:47:34', '2026-09-23 20:06:43'),
(8, 'aisha1', 'aisha1@local.test', 'Aisha Bello', '$2y$10$zDJOLv.C6XwyryHECxpj7e13fcPewKKNCg1cjU/NiiaMRoTLjoyIq', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(9, 'ibrahim1', 'ibrahim1@local.test', 'Ibrahim Musa', '$2y$10$vaRpR4a154ctwqXm6.KuHuJm1DHmg0mfVlGZCMgM9QHWW8m4PxBGS', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(10, 'chidera1', 'chidera1@local.test', 'Chidera Okafor', '$2y$10$jrpx.V7.8Q6Hh0y22OQhTeNUVYYydyngUZUhIfjUsGnp1hx3DFSZW', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(11, 'emmanuel1', 'emmanuel1@local.test', 'Emmanuel Adeyemi', '$2y$10$FvLcJtvWRTsLlI3rc59LeuuFbBdU7O7iyRBwGrzQncxZaYfxNlznu', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(12, 'fatima1', 'fatima1@local.test', 'Fatima Bello', '$2y$10$dhGMBZ5D0jX/ASIQWd6k8.TGzyap4gECSOJWjU.w4oTfItxr6ft1i', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(13, 'samuel1', 'samuel1@local.test', 'Samuel Eze', '$2y$10$lOp2Fm63Dg.BXoVWaL0FvOVOOmr8exAyZBIXbTzAYwM8djlzk2pou', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(14, 'blessing1', 'blessing1@local.test', 'Blessing Uche', '$2y$10$s0KlO3s6CTAoyROlNHguL.rLHZB0FeF5GxgsUQt/0GmofLBt9LBk.', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(15, 'john1', 'john1@local.test', 'John Agboola', '$2y$10$mpurZcZ3FegAlOURMcvXNu8NCrVIasGX5PFQ2dkGT4OlbYwlXMNQS', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(16, 'maryam1', 'maryam1@local.test', 'Maryam Lawal', '$2y$10$lczMxilOjHTesyASjRjsdOmoreHSkrncnZcQvMPnor5aBlTRTMR/q', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57'),
(17, 'david1', 'david1@local.test', 'David Nwosu', '$2y$10$ESCC52/3YgWi1hI/LZx/5uJECqGYfktoQEVFBhGwgSim2lZYbV24y', 3, 1, NULL, '2026-09-23 20:14:57', '2026-09-23 20:14:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username_attempted`),
  ADD KEY `idx_logged_at` (`logged_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
