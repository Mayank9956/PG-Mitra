-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 01, 2026 at 03:00 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql_pgmitra_in`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` int(11) NOT NULL,
  `staff_user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'referral_amount', '100', '2026-04-01 03:37:02'),
(2, 'commission_percent', '10', '2026-03-29 16:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `bank_cards`
--

CREATE TABLE `bank_cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `booking_number` varchar(255) NOT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_days` int(11) NOT NULL DEFAULT 0,
  `completed_days` int(11) NOT NULL DEFAULT 0,
  `guests` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `wallet_used` decimal(10,2) NOT NULL,
  `total_rent` decimal(10,2) NOT NULL,
  `final_amount` decimal(10,2) NOT NULL,
  `months` int(11) NOT NULL,
  `special_requests` mediumtext DEFAULT NULL,
  `security_deposit` decimal(10,2) NOT NULL,
  `booking_date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_gateway` varchar(255) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_discount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `booking_number`, `room_name`, `user_id`, `check_in`, `check_out`, `total_days`, `completed_days`, `guests`, `total_price`, `original_amount`, `wallet_used`, `total_rent`, `final_amount`, `months`, `special_requests`, `security_deposit`, `booking_date`, `status`, `payment_status`, `payment_method`, `payment_gateway`, `coupon_id`, `coupon_discount`, `created_at`, `updated_at`, `payment_id`, `razorpay_order_id`) VALUES
(5, 15, 'BKG136F602C20260331', 'Girls rooms A', 2, '2026-03-31', '2026-05-01', 31, 0, 1, 5500.00, 5500.00, 0.00, 5500.00, 5500.00, 1, '', 0.00, '2026-03-31 05:28:21', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-03-31 05:28:21', NULL, NULL, 'order_SXiXW8EkVspNJi'),
(6, 15, 'BKG7B7D3D3620260331', 'Girls rooms A', 2, '2026-03-31', '2026-05-01', 31, 0, 1, 5000.00, 5500.00, 0.00, 5500.00, 5000.00, 1, '', 0.00, '2026-03-31 05:32:11', 'pending', 'pending', NULL, NULL, NULL, 500.00, '2026-03-31 05:32:11', NULL, NULL, 'order_SXibZRXAwTDlmT'),
(7, 15, 'BKGAC69E85920260331', 'Girls rooms A', 2, '2026-03-31', '2026-05-01', 31, 0, 1, 5500.00, 5500.00, 0.00, 5500.00, 5500.00, 1, '', 0.00, '2026-03-31 08:13:48', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-03-31 08:13:48', NULL, NULL, 'order_SXlMHiYvaNwFxQ'),
(9, 16, 'BKGAB4195E220260401', 'Girls Flat A', 2, '2026-03-31', '2026-05-01', 31, 0, 1, 8800.00, 8800.00, 0.00, 8800.00, 8800.00, 1, '', 0.00, '2026-03-31 16:50:52', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-03-31 16:50:52', NULL, NULL, 'order_SXuAUbvcA6dTYZ'),
(10, 17, 'BKG39C9DE9E20260401', 'ROOm NO 9', 2, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 02:44:24', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 02:44:24', NULL, NULL, 'order_SY4HSXtMou9Pe8'),
(11, 21, 'BKGCC150FE620260401', 'SHARAD KUMAR ROOMS', 2, '2026-04-01', '2026-05-01', 30, 0, 1, 3850.00, 3850.00, 0.00, 3850.00, 3850.00, 1, '', 0.00, '2026-04-01 03:23:04', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 03:23:04', NULL, NULL, 'order_SY4wJ5oIaSnyIO'),
(12, 17, 'BKG93FF942720260401', 'ROOm NO 9', 2, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 04:41:19', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 04:41:19', NULL, NULL, 'order_SY6Gy0Ns46R9XK'),
(13, 17, 'BKG0FE76AB220260401', 'Room NO 9', 2, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 05:05:30', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 05:05:30', NULL, NULL, 'order_SY6gViNOtDyFf5'),
(14, 17, 'BKG31B9D33720260401', 'Room NO 9', 2, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 05:14:20', 'pending', 'pending', 'later', '', NULL, 0.00, '2026-04-01 05:14:20', NULL, NULL, NULL),
(15, 17, 'BKGAB293CC820260401', 'Room NO 9', 11, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 06:16:38', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 06:16:38', NULL, NULL, 'order_SY7teGuztBtWsv'),
(16, 17, 'BKGFEB70FCC20260401', 'Room NO 9', 11, '2026-04-01', '2026-05-01', 30, 0, 1, 4950.00, 4950.00, 0.00, 4950.00, 4950.00, 1, '', 0.00, '2026-04-01 06:49:15', 'pending', 'pending', NULL, NULL, NULL, 0.00, '2026-04-01 06:49:15', NULL, NULL, 'order_SY8S5iywajyWmY');

-- --------------------------------------------------------

--
-- Table structure for table `booking_assignments`
--

CREATE TABLE `booking_assignments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_extensions`
--

CREATE TABLE `booking_extensions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_check_out` date NOT NULL,
  `new_check_out` date NOT NULL,
  `extra_days` int(11) NOT NULL DEFAULT 0,
  `extra_months` int(11) NOT NULL DEFAULT 0,
  `extra_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_conversations`
--

CREATE TABLE `chat_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `support_agent_id` int(11) DEFAULT NULL,
  `status` enum('active','closed','resolved') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_conversations`
--

INSERT INTO `chat_conversations` (`id`, `user_id`, `support_agent_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, NULL, 'active', '2026-03-28 15:02:01', '2026-03-28 15:02:01'),
(3, 9, NULL, 'active', '2026-04-01 01:44:16', '2026-04-01 01:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_type` enum('user','support') NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `conversation_id`, `sender_type`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(2, 2, 'support', NULL, '👋 Hi there! Welcome to StayEase Support. How can we help you today?', 1, '2026-03-28 15:02:01'),
(3, 2, 'user', 2, 'hi', 1, '2026-03-28 15:02:06'),
(4, 2, 'support', NULL, 'Hello! 👋 How can I assist you today?', 1, '2026-03-28 15:02:06'),
(5, 2, 'user', 2, 'hello', 1, '2026-03-28 15:02:12'),
(6, 2, 'support', NULL, 'Hello! 👋 How can I assist you today?', 1, '2026-03-28 15:02:12'),
(7, 2, 'user', 2, 'room book hoga', 1, '2026-03-28 15:02:27'),
(8, 2, 'support', NULL, 'I can help you with room information. Which room are you interested in?', 1, '2026-03-28 15:02:27'),
(9, 2, 'support', 10, 'aajao', 1, '2026-03-28 15:03:09'),
(10, 2, 'user', 2, 'Payment Issues', 1, '2026-03-31 19:20:28'),
(11, 2, 'support', NULL, 'For payment-related issues, please check your payment method. If you need assistance, our team will help you resolve it.', 1, '2026-03-31 19:20:28'),
(12, 3, 'support', NULL, '👋 Hi there! Welcome to StayEase Support. How can we help you today?', 1, '2026-04-01 01:44:16'),
(13, 3, 'user', 9, 'Cancellation Policy', 1, '2026-04-01 01:44:37'),
(14, 3, 'support', NULL, 'Our cancellation policy varies by property. Please check your booking details for specific terms. Would you like me to help you find this information?', 1, '2026-04-01 01:44:37'),
(15, 3, 'support', 12, 'hey\r\nkya khana khaya', 1, '2026-04-01 01:45:50'),
(16, 3, 'support', 12, 'number milega mam', 1, '2026-04-01 01:47:07'),
(17, 3, 'user', 9, 'alredyy hii bola haii', 1, '2026-04-01 01:47:14'),
(18, 3, 'support', NULL, 'Hello! 👋 How can I assist you today?', 1, '2026-04-01 01:47:14'),
(19, 3, 'support', 12, 'khana chaye apko kya', 1, '2026-04-01 01:47:45'),
(20, 3, 'user', 9, 'yess for 50 students\'', 1, '2026-04-01 01:47:55'),
(21, 3, 'user', 9, 'only for me', 1, '2026-04-01 01:48:03'),
(22, 3, 'support', 12, '6394296931@ybl 50000 rs bhej do', 1, '2026-04-01 01:48:11'),
(23, 3, 'user', 9, 'bhikari hu me', 1, '2026-04-01 01:48:32'),
(24, 3, 'support', NULL, 'Hello! 👋 How can I assist you today?', 1, '2026-04-01 01:48:32'),
(25, 3, 'user', 9, 'tum paga;l', 1, '2026-04-01 01:48:41'),
(26, 3, 'support', 12, 'mam dont use wrong word', 0, '2026-04-01 06:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(100) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `city`, `latitude`, `longitude`, `created_at`, `status`) VALUES
(5, 'Lucknow University', 'lucknow', NULL, NULL, '2026-03-27 14:55:02', 'Active'),
(6, 'Lucknow University', '3', NULL, NULL, '2026-03-27 17:17:05', 'Active'),
(7, 'Lucknow University', 'Lucknow', NULL, NULL, '2026-03-27 17:26:05', 'Active'),
(8, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 16:57:48', 'Active'),
(9, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 17:01:59', 'Active'),
(10, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 17:02:10', 'Active'),
(11, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 17:02:50', 'Active'),
(12, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 17:03:25', 'Active'),
(13, 'MUMBAI University', 'MUMBAI', NULL, NULL, '2026-03-29 17:05:08', 'Active'),
(14, 'sanskriti university', 'chata', NULL, NULL, '2026-03-30 14:37:59', 'Active'),
(15, 'sanskriti university', 'chata', NULL, NULL, '2026-03-30 14:48:46', 'Active'),
(16, 'sanskriti university', 'chata', NULL, NULL, '2026-03-30 16:02:53', NULL),
(17, '4', 'MUMBAI', NULL, NULL, '2026-03-30 16:59:24', NULL),
(18, 'Lucknow University', 'r', NULL, NULL, '2026-03-31 13:19:42', NULL),
(19, '22', '2', NULL, NULL, '2026-03-31 13:25:00', 'Active'),
(20, 'GLA UNIVERSITY', 'MATHURA', NULL, NULL, '2026-04-01 02:48:04', 'Active'),
(21, 'GLA UNIVERSITY', 'MATHURA', NULL, NULL, '2026-04-01 02:57:03', 'Active'),
(22, 'SANSKRITI UNIVERSITY', 'MATHURA', NULL, NULL, '2026-04-01 03:12:19', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `contact_unlocks`
--

CREATE TABLE `contact_unlocks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_to` date NOT NULL,
  `usage_limit` int(11) DEFAULT 1,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `coupon_code`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `valid_from`, `valid_to`, `usage_limit`, `used_count`, `is_active`, `created_at`) VALUES
(1, 'STAY10', 'Get 10% off on your first booking', 'percentage', 10.00, 1000.00, 500.00, '2026-03-19', '2026-04-18', 10, 2, 1, '2026-03-28 11:01:43'),
(2, 'STAY500', 'Flat ₹500 off on bookings above ₹5000', 'fixed', 500.00, 5000.00, NULL, '2026-03-19', '2026-04-18', 1, 0, 1, '2026-03-28 11:01:43'),
(3, 'STAY20', '20% off for students', 'percentage', 20.00, 2000.00, 1000.00, '2026-03-19', '2026-04-18', 1, 0, 1, '2026-03-28 11:01:43'),
(4, 'WELCOME100', 'Welcome bonus ₹100 off', 'fixed', 100.00, 1000.00, NULL, '2026-03-19', '2026-04-18', 1, 0, 1, '2026-03-28 11:01:43'),
(5, 'STAY200', 'Flat ₹200 off', 'fixed', 200.00, 3000.00, NULL, '2026-03-19', '2026-04-18', 1, 0, 1, '2026-03-28 11:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `f_name`, `icon`) VALUES
(1, 'WiFi', 'fa-wifi'),
(2, 'Bed', 'fa-bed'),
(3, 'Cupboard', 'fa-box'),
(4, 'Fan', 'fa-fan'),
(5, 'AC', 'fa-snowflake'),
(6, 'Heater', 'fa-fire'),
(7, 'Attached Bathroom', 'fa-toilet'),
(8, 'Geyser', 'fa-hot-tub'),
(9, 'Shower', 'fa-shower'),
(10, 'Bathtub', 'fa-bath'),
(11, 'Kitchen', 'fa-utensils'),
(12, 'Gas Stove', 'fa-fire-burner'),
(13, 'Fridge', 'fa-snowflake'),
(14, 'Microwave', 'fa-microchip'),
(15, 'Water Purifier', 'fa-filter'),
(16, 'CCTV', 'fa-video'),
(17, 'Security Guard', 'fa-shield-halved'),
(18, 'Gated Society', 'fa-door-closed'),
(19, 'Fire Safety', 'fa-fire-extinguisher'),
(20, 'Laundry', 'fa-shirt'),
(21, 'Housekeeping', 'fa-broom'),
(22, 'Room Service', 'fa-concierge-bell'),
(23, 'Power Backup', 'fa-bolt'),
(24, 'Lift', 'fa-elevator'),
(25, 'Parking', 'fa-car'),
(26, 'Bike Parking', 'fa-motorcycle'),
(27, 'TV', 'fa-tv'),
(28, 'Gaming', 'fa-gamepad'),
(29, 'High Speed WiFi', 'fa-wifi'),
(30, 'Gym', 'fa-dumbbell'),
(31, 'Swimming Pool', 'fa-person-swimming'),
(32, 'Garden', 'fa-tree'),
(33, 'Balcony', 'fa-building'),
(34, 'Study Table', 'fa-table'),
(35, 'Chair', 'fa-chair'),
(36, 'Workspace', 'fa-laptop'),
(37, 'Mess', 'fa-utensils'),
(38, 'Food Included', 'fa-utensils'),
(39, 'Tiffin Service', 'fa-box'),
(40, 'Pet Friendly', 'fa-paw'),
(41, 'Smoking Allowed', 'fa-smoking'),
(42, 'Non Smoking', 'fa-ban-smoking'),
(43, '24x7 Water', 'fa-faucet'),
(44, '24x7 Electricity', 'fa-lightbulb');

-- --------------------------------------------------------

--
-- Table structure for table `owner_profiles`
--

CREATE TABLE `owner_profiles` (
  `id` int(11) NOT NULL,
  `staff_user_id` int(11) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `changed_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `order_id` varchar(100) NOT NULL,
  `razorpay_order_id` varchar(100) NOT NULL,
  `payment_id` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'razorpay',
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `user_id`, `transaction_id`, `order_id`, `razorpay_order_id`, `payment_id`, `amount`, `payment_method`, `payment_status`, `created_at`, `paid_at`) VALUES
(1, 2, 'TXN69C819E26B3E720260329', 'ORD69C819E26B3DB20260329', 'order_SWjwZwSyiyJjz7', 'pay_SWjy8va8FCsfCk', 100.00, 'razorpay', 'failed', '2026-03-28 18:11:46', NULL),
(2, 2, 'TXN69C81A7A2959E20260329', 'ORD69C81A7A2959320260329', 'order_SWjzFZIOqxdmWU', 'pay_SWjzKEmIebaev9', 100.00, 'razorpay', 'failed', '2026-03-28 18:14:18', '2026-03-28 18:14:38'),
(3, 2, 'TXN69C81AC84BAD120260329', 'ORD69C81AC84BAC320260329', 'order_SWk0cqxnNmt35k', 'pay_SWk0hVOPhBmxYe', 5000.00, 'razorpay', 'success', '2026-03-28 18:15:36', '2026-03-28 18:15:57'),
(4, 2, 'TXN69C81BD08DE5520260329', 'ORD69C81BD08DE4720260329', 'order_SWk5HKp0rnSnD9', 'pay_SWk5M0hBgwednM', 2000.00, 'razorpay', 'success', '2026-03-28 18:20:00', '2026-03-28 18:20:21'),
(5, 2, NULL, 'ORD_69c9672d85194', 'order_SX8Ajb8o0eVZ3H', NULL, 1980.00, 'razorpay', 'pending', '2026-03-29 17:53:49', NULL),
(6, 2, NULL, 'ORD_69c9683aa2bfb', 'order_SX8FTNh9UYtlV7', NULL, 1980.00, 'razorpay', 'pending', '2026-03-29 17:58:18', NULL),
(7, 2, NULL, 'ORD_69c9688584827', 'order_SX8Gn6MqXlAhYX', 'pay_SX8Grep0HaDrI4', 1980.00, 'razorpay', 'success', '2026-03-29 17:59:33', '2026-03-29 18:00:03'),
(8, 3, 'TXN69CB844A354DE20260331', 'ORD69CB844A354CE20260331', 'order_SXlVXrSH1vaMwH', NULL, 10000.00, 'razorpay', 'pending', '2026-03-31 08:22:34', NULL),
(9, 2, 'TXN69CBC47A76DAF20260331', 'ORD69CBC47A76DA420260331', 'order_SXqAqecurvoLnR', NULL, 500.00, 'razorpay', 'pending', '2026-03-31 12:56:27', NULL),
(10, 2, 'TXN69CCABFF1A4D620260401', 'ORD69CCABFF1A4C720260401', 'order_SY70IvsRtCRhnS', NULL, 100.00, 'razorpay', 'pending', '2026-04-01 05:24:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quick_replies`
--

CREATE TABLE `quick_replies` (
  `id` int(11) NOT NULL,
  `trigger_word` varchar(100) NOT NULL,
  `display_text` varchar(255) NOT NULL,
  `response` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quick_replies`
--

INSERT INTO `quick_replies` (`id`, `trigger_word`, `display_text`, `response`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'booking', 'Booking Help', 'I can help you with booking inquiries. What specific information do you need about bookings?', 1, NULL, '2026-03-27 17:22:49'),
(2, 'payment', 'Payment Issues', 'For payment-related issues, please check your payment method. If you need assistance, our team will help you resolve it.', 1, NULL, '2026-03-27 17:22:49'),
(3, 'cancellation', 'Cancellation Policy', 'Our cancellation policy varies by property. Please check your booking details for specific terms. Would you like me to help you find this information?', 1, NULL, '2026-03-27 17:22:49'),
(4, 'refund', 'Refund Status', 'Refunds typically take 5-10 business days to process. Would you like me to check the status of your refund?', 1, NULL, '2026-03-27 17:22:49'),
(5, 'room', 'Room Details', 'I can help you with room information. Which room are you interested in?', 1, NULL, '2026-03-27 17:22:49'),
(6, 'check-in', 'Check-in Time', 'Standard check-in is from 2 PM onwards. Early check-in may be available upon request.', 1, NULL, '2026-03-27 17:22:49'),
(7, 'check-out', 'Check-out Time', 'Standard check-out is at 11 AM. Late check-out may be available upon request.', 1, NULL, '2026-03-27 17:22:49'),
(8, 'amenities', 'Amenities', 'Our properties offer various amenities like WiFi, AC, parking, and more. Which property are you interested in?', 1, NULL, '2026-03-27 17:22:49'),
(9, 'location', 'Location Info', 'I can help you with property locations. Which city or area are you looking for?', 1, NULL, '2026-03-27 17:22:49'),
(10, 'price', 'Pricing', 'Prices vary by property and season. Would you like me to help you find the best deals?', 1, NULL, '2026-03-27 17:22:49');

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `referred_id` int(11) NOT NULL,
  `referral_code` varchar(20) NOT NULL,
  `status` enum('pending','active','expired') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `referrer_id`, `referred_id`, `referral_code`, `status`, `created_at`, `completed_at`) VALUES
(1, 2, 13, '2EFF440A', 'pending', '2026-04-01 04:05:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `referral_emails`
--

CREATE TABLE `referral_emails` (
  `id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `referral_code` varchar(50) NOT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `referral_emails`
--

INSERT INTO `referral_emails` (`id`, `referrer_id`, `email`, `referral_code`, `status`, `created_at`) VALUES
(1, 2, 'test@g.h', '2EFF440A', 'sent', '2026-03-31 19:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `referral_rewards`
--

CREATE TABLE `referral_rewards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `referral_id` int(11) NOT NULL,
  `reward_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','credited','expired') DEFAULT 'pending',
  `credited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `payment_id` varchar(255) DEFAULT NULL,
  `refund_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` mediumtext DEFAULT NULL,
  `review_tags` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `review_images` mediumtext DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `room_id`, `rating`, `comment`, `review_tags`, `created_at`, `review_images`, `updated_at`, `is_approved`) VALUES
(1, 2, 16, 5, 'ret', 'Friendly Staff', '2026-03-31 17:34:43', '', '2026-03-31 17:35:17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `status`, `created_at`) VALUES
(1, 'Admin', 'main_admin', 'Full system access', 1, '2026-03-27 14:45:04'),
(2, 'Owner', 'room_owner', 'Manage own rooms and bookings', 1, '2026-03-27 14:45:04'),
(3, 'Support', 'customer_service', 'Handle support and limited bookings', 1, '2026-03-27 14:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT 0.00,
  `commission_price` decimal(10,2) DEFAULT 0.00,
  `commission_rate` int(11) DEFAULT NULL,
  `security_deposit` decimal(10,2) NOT NULL,
  `non_refundable` decimal(10,2) DEFAULT 0.00,
  `location` varchar(255) NOT NULL,
  `address` mediumtext DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `area` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `distance` int(11) DEFAULT NULL,
  `distance_text` varchar(255) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT 1,
  `bathrooms` int(11) DEFAULT 1,
  `bathroom_type` varchar(255) DEFAULT NULL,
  `max_guests` int(11) DEFAULT 2,
  `room_type` varchar(255) DEFAULT NULL,
  `pg_type` varchar(255) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `amenities` mediumtext DEFAULT NULL,
  `host_name` varchar(100) DEFAULT NULL,
  `host_phone` varchar(25) NOT NULL,
  `host_email` varchar(255) NOT NULL,
  `host_image` varchar(500) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `staff_owner_id` int(11) DEFAULT NULL,
  `facilities` mediumtext DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `description`, `price`, `original_price`, `commission_price`, `commission_rate`, `security_deposit`, `non_refundable`, `location`, `address`, `city`, `area`, `country`, `distance`, `distance_text`, `bedrooms`, `bathrooms`, `bathroom_type`, `max_guests`, `room_type`, `pg_type`, `image_url`, `rating`, `reviews_count`, `amenities`, `host_name`, `host_phone`, `host_email`, `host_image`, `is_available`, `created_at`, `latitude`, `longitude`, `owner_id`, `staff_owner_id`, `facilities`, `status`) VALUES
(15, 'Girls rooms A', 'Safe & Peaceful Room Available for Girls 👩‍🎓\n\n📍 Location: Govardhan Chauraha se sirf 200m distance, market area ke paas – daily needs ke liye perfect location\n\n🛏️ Room Details:\n✔ Bed & Almari (wardrobe) available  \n✔ Clean & well-maintained room  \n✔ Peaceful & study-friendly environment  \n\n💡 Facilities:\n✔ Bijli bill alag se (as per usage)  \n✔ Drinking water available (separate supply)  \n\n🍽️ Food:\n❌ Food included nahi hai  \n✔ Self cooking allowed  \n✔ Tiffin service ka contact provide kiya jayega  \n\n👩‍🦳 Rules & Environment:\n✔ Only vegetarian girls allowed  \n✔ Boys strictly NOT allowed 🚫  \n✔ Owner (Aunty ji) same building me rehti hain  \n✔ Strict, safe & disciplined environment  \n✔ Quiet & peaceful living preferred  \n\n🔐 Safety:\n✔ Fully safe for girls  \n✔ Proper supervision available  \n\n📞 Contact now for booking & visit!\n Tiffin Service Contact: +91 8384886388  \n💧 Drinking Water Supplier: +91 9568887994\n\n', 5500.00, 5000.00, 500.00, 10, 0.00, 0.00, 'chata, uttar pradesh,mathura', NULL, 'chata', 'chata', NULL, 4, '4.0 KM', 1, 1, '0', 2, 'pg', 'women', NULL, 3.00, 0, NULL, 'babita varshney', '6395658116', 'babita@gmail.com', NULL, 1, '2026-03-30 14:37:59', NULL, NULL, 13, NULL, NULL, 'approved'),
(16, 'Girls Flat A', 'Exclusive Full Floor Flat Available for Girls 👩‍🎓\r\n\r\n📍 Location: Govardhan Chauraha se sirf 200m distance, market area ke paas – daily needs ke liye perfect location\r\n\r\n🏢 Property Details:\r\n✔ Pura separate floor (top floor)  \r\n✔ Us floor par sirf ek hi flat – complete privacy  \r\n✔ Safe & peaceful environment for girls  \r\n\r\n🛏️ Flat सुविधाएं:\r\n✔ Bed & Almari (wardrobe) available  \r\n✔ Spacious & well-ventilated rooms  \r\n✔ Clean & maintained space  \r\n\r\n💡 Facilities:\r\n✔ Bijli bill alag se (as per usage)  \r\n✔ Drinking water available (separate supply)  \r\n\r\n🍽️ Food:\r\n❌ Food included nahi hai  \r\n✔ Self cooking allowed  \r\n✔ Tiffin service ka contact available  \r\n\r\n👩‍🦳 Rules & Environment:\r\n✔ Only vegetarian girls allowed  \r\n✔ Boys strictly NOT allowed 🚫  \r\n✔ Owner (Aunty ji) same building me rehti hain  \r\n✔ Strict rules for safety & discipline  \r\n✔ Quiet & peaceful environment preferred  \r\n\r\n🔐 Safety:\r\n✔ Fully safe & secure for girls  \r\n✔ Independent floor with privacy  \r\n\r\n📞 Contact now for visit & booking!\r\n\r\n📦 Tiffin Service Contact: +91 XXXXX XXXXX  \r\n💧 Drinking Water Supplier: +91 XXXXX XXXXX  \r\n', 8800.00, 8000.00, 800.00, 10, 0.00, 0.00, 'govardhan chohraha market ki or, ', NULL, 'chata', 'chata', NULL, 4, '4.0 KM', 1, 1, '0', 3, 'apartment', 'women', NULL, 5.00, 1, NULL, 'babita varshney', '6395658116', 'babita@gmail.com', NULL, 1, '2026-03-30 14:48:46', NULL, NULL, 13, NULL, NULL, 'approved'),
(17, 'Room NO 9', 'Comfortable PG Available (Boys & Girls) 👨‍🎓👩‍🎓\r\n\r\n📍 Location: Govardhan Chauraha se approx. 350m distance, highway par – Police Chowki ke samne (prime & safe location)\r\n\r\n🛏️ Room Details:\r\n✔ Clean & comfortable rooms  \r\n✔ Suitable for students & working individuals  \r\n\r\n💡 Facilities:\r\n✔ Drinking water available 💧  \r\n✔ High-speed WiFi available 📶  \r\n✔ Good ventilation & open area  \r\n\r\n🍽️ Food:\r\n✔ Food arrangement flexible (self/tiffin possible)  \r\n\r\n🧾 Rules:\r\n✔ Unisex PG (boys & girls both allowed)  \r\n✔ Friendly & flexible environment  \r\n✔ All basic activities allowed (within discipline)  \r\n\r\n🔐 Safety:\r\n✔ Police Chowki ke samne – safe location  \r\n✔ Secure & easily accessible  \r\n\r\n📞 Contact now for booking & visit!\r\n\r\n📦 Tiffin Service Contact: +91 8384886388  \r\n💧 Drinking Water Supplier: +91 9568887994', 4950.00, 4500.00, 450.00, 10, 0.00, 0.00, 'bijli ghar ke bagal me ,chhata', NULL, 'chata', 'chata', NULL, 1, '3.4 KM', 1, 1, '0', 2, 'pg', 'unisex', NULL, 4.50, 0, NULL, 'brijesh varshney', '7017753565', 'brijesh@gmail.com', NULL, 1, '2026-03-30 16:02:53', NULL, NULL, 14, NULL, NULL, 'approved'),
(21, 'SHARAD KUMAR ROOMS', 'ONLY FOR MEN \r\n', 3850.00, 3500.00, 350.00, 10, 0.00, 0.00, 'Chaumuhan', NULL, 'MATHURA', 'Chaumuhan', NULL, 1, '1 KM', 2, 1, 'sharing', 2, 'hostel', 'men', NULL, 0.00, 0, NULL, 'Sharad kumar rooms', '6397424216', 'sharadkumarrooms@gmail.com', NULL, 1, '2026-04-01 02:48:04', NULL, NULL, 17, NULL, NULL, 'approved'),
(22, 'MEHTAB PG', 'Comfortable PG ONLY BOYS👩‍🎓\r\n\r\n📍 Location: NEAR CANARA BANK ATM Chaumuhan\r\n🛏️ Room Details:\r\n✔ Clean & comfortable rooms  \r\n✔ Suitable for students & working individuals  \r\n\r\n💡 Facilities:\r\n✔ Drinking water available 💧  \r\n✔ High-speed WiFi available 📶  \r\n✔ Good ventilation & open area  \r\n\r\n🍽️ Food:\r\n✔ Food arrangement flexible (self/tiffin possible)  \r\n\r\n🧾 Rules:\r\n✔ ONLY FOR MEN \r\n✔ Friendly & flexible environment  \r\n✔ All basic activities allowed (within discipline)  \r\n\r\n🔐 Safety:\r\n✔ Secure & easily accessible  \r\n\r\n📦 Tiffin Service Contact: +91 8384886388  \r\n💧 Drinking Water Supplier: +91 9568887994', 3850.00, 3500.00, 350.00, 10, 0.00, 0.00, 'NEAR CANARA BANK ATM Chaumuhan', NULL, 'MATHURA', 'Chaumuhan', NULL, 1, '1 KM', 2, 1, 'sharing', 2, 'hostel', 'men', NULL, 0.00, 0, NULL, 'Mehtab Pg Wale', '9520270062', 'mehtabpgwale95@gmail.com', NULL, 1, '2026-04-01 02:57:03', NULL, NULL, 18, NULL, NULL, 'approved'),
(23, 'VIRANDA GIRLS PG', 'Comfortable PG Available ONLY WOMEN 👨‍🎓👩‍🎓\r\n\r\n📍 Location: Govardhan Chauraha se approx. 350m distance, highway par – Police Chowki ke samne (prime & safe location)\r\n\r\n🛏️ Room Details:\r\n✔ Clean & comfortable rooms  \r\n✔ Suitable for students & working individuals  \r\n\r\n💡 Facilities:\r\n✔ Drinking water available 💧  \r\n✔ High-speed WiFi available 📶  \r\n✔ Good ventilation & open area  \r\n\r\n🍽️ Food:\r\n✔ Food arrangement flexible (self/tiffin possible)  \r\n\r\n🧾 Rules:\r\n✔ ONLY FOR GIRLS\r\n✔ Friendly & flexible environment  \r\n✔ All basic activities allowed (within discipline)  \r\n\r\n🔐 Safety:\r\n✔ Police Chowki ke samne – safe location  \r\n✔ Secure & easily accessible  \r\n\r\n📦 Tiffin Service Contact: +91 8384886388  \r\n💧 Drinking Water Supplier: +91 9568887994', 5500.00, 5000.00, 500.00, 10, 0.00, 0.00, 'Shiv colony , Chhata', NULL, 'MATHURA', 'CHHATA', NULL, 3, '3 KM ', 2, 1, 'seperate', 2, 'hostel', 'women', NULL, 0.00, 0, NULL, 'Vrinda Girls Pg And hostel', '09719706247', 'viradagirls97@gmail.com', NULL, 1, '2026-04-01 03:12:19', NULL, NULL, 19, NULL, NULL, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `room_colleges`
--

CREATE TABLE `room_colleges` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `distance` decimal(5,2) DEFAULT NULL,
  `distance_text` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_colleges`
--

INSERT INTO `room_colleges` (`id`, `room_id`, `college_id`, `distance`, `distance_text`, `created_at`) VALUES
(14, 15, 14, 4.00, '4.0 KM', '2026-03-30 14:37:59'),
(15, 16, 15, 4.00, '4.0 KM', '2026-03-30 14:48:46'),
(16, 17, 16, 1.00, '3.4 KM', '2026-03-30 16:02:53'),
(20, 21, 20, 1.00, '1 KM', '2026-04-01 02:48:04'),
(21, 22, 21, 1.00, '1 KM', '2026-04-01 02:57:03'),
(22, 23, 22, 3.00, '3 KM ', '2026-04-01 03:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `room_facilities`
--

CREATE TABLE `room_facilities` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facility_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_facilities`
--

INSERT INTO `room_facilities` (`id`, `room_id`, `facility_id`) VALUES
(35, 15, 2),
(36, 15, 4),
(37, 15, 11),
(40, 15, 23),
(39, 15, 25),
(38, 15, 42),
(34, 15, 43),
(44, 16, 2),
(46, 16, 4),
(42, 16, 7),
(48, 16, 23),
(45, 16, 26),
(43, 16, 33),
(47, 16, 42),
(41, 16, 43),
(62, 17, 1),
(53, 17, 2),
(51, 17, 7),
(55, 17, 16),
(59, 17, 23),
(58, 17, 25),
(54, 17, 26),
(57, 17, 29),
(52, 17, 33),
(61, 17, 34),
(56, 17, 35),
(60, 17, 41),
(50, 17, 43),
(49, 17, 44),
(68, 21, 2),
(70, 21, 4),
(69, 21, 35),
(71, 21, 39),
(67, 21, 43),
(66, 21, 44),
(74, 22, 2),
(77, 22, 4),
(79, 22, 15),
(78, 22, 23),
(75, 22, 26),
(76, 22, 35),
(73, 22, 43),
(72, 22, 44),
(87, 23, 1),
(82, 23, 2),
(86, 23, 15),
(83, 23, 16),
(84, 23, 35),
(85, 23, 39),
(81, 23, 43),
(80, 23, 44);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_url`, `is_primary`, `created_at`) VALUES
(25, 15, 'uploads/rooms/room_69ca8ac75685f6.07302032.jpeg', 1, '2026-03-30 14:37:59'),
(26, 15, 'uploads/rooms/room_69ca8ac756b3d3.00071256.jpeg', 0, '2026-03-30 14:37:59'),
(27, 15, 'uploads/rooms/room_69ca8ac756bc09.63528641.jpeg', 0, '2026-03-30 14:37:59'),
(28, 16, 'uploads/rooms/room_69ca8d4ee18c48.38106984.jpeg', 1, '2026-03-30 14:48:46'),
(29, 16, 'uploads/rooms/room_69ca8d4ee20db8.90955413.jpeg', 0, '2026-03-30 14:48:46'),
(30, 16, 'uploads/rooms/room_69ca8d4ee25398.23632145.jpeg', 0, '2026-03-30 14:48:46'),
(31, 16, 'uploads/rooms/room_69ca8d4ee2ac49.96246590.jpeg', 0, '2026-03-30 14:48:46'),
(32, 16, 'uploads/rooms/room_69ca8d4ee2b6d4.72848182.jpeg', 0, '2026-03-30 14:48:46'),
(33, 16, 'uploads/rooms/room_69ca8d4ee2bf22.41479114.jpeg', 0, '2026-03-30 14:48:46'),
(34, 17, 'uploads/rooms/room_69ca9eadb492b3.90379761.jpeg', 0, '2026-03-30 16:02:53'),
(35, 17, 'uploads/rooms/room_69ca9eadb4a492.67561560.jpeg', 0, '2026-03-30 16:02:53'),
(36, 17, 'uploads/rooms/room_69ca9eadb4aea7.28303831.jpeg', 1, '2026-03-30 16:02:53'),
(37, 17, 'uploads/rooms/room_69ca9eadb4b9b4.23526007.jpeg', 0, '2026-03-30 16:02:53'),
(38, 17, 'uploads/rooms/room_69ca9eadb505e0.38894127.jpeg', 0, '2026-03-30 16:02:53'),
(42, 21, 'uploads/rooms/room_69cc87645080b4.41451649.jpeg', 1, '2026-04-01 02:48:04'),
(43, 21, 'uploads/rooms/room_69cc8764510e62.00524456.jpeg', 0, '2026-04-01 02:48:04'),
(44, 21, 'uploads/rooms/room_69cc8764511c28.83648828.jpeg', 0, '2026-04-01 02:48:04'),
(45, 22, 'uploads/rooms/room_69cc897fe8e4a0.78413389.jpeg', 1, '2026-04-01 02:57:03'),
(46, 22, 'uploads/rooms/room_69cc897fe8f482.13816065.jpeg', 0, '2026-04-01 02:57:03'),
(47, 22, 'uploads/rooms/room_69cc897fe95650.03471420.jpeg', 0, '2026-04-01 02:57:03'),
(48, 23, 'uploads/rooms/room_69cc8d133cb4e6.62507027.png', 1, '2026-04-01 03:12:19'),
(49, 23, 'uploads/rooms/room_69cc8d133d4535.64908934.png', 0, '2026-04-01 03:12:19'),
(50, 23, 'uploads/rooms/room_69cc8d133d54e5.01073589.png', 0, '2026-04-01 03:12:19');

-- --------------------------------------------------------

--
-- Table structure for table `room_instructions`
--

CREATE TABLE `room_instructions` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `instruction_text` text NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-info-circle',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_rules`
--

CREATE TABLE `room_rules` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `rule_type` enum('allowed','not_allowed') DEFAULT 'allowed',
  `rule_text` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-check-circle',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_users`
--

CREATE TABLE `staff_users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_users`
--

INSERT INTO `staff_users` (`id`, `role_id`, `full_name`, `email`, `phone`, `password`, `profile_image`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(3, 1, 'Mayank', 'mayank@gmail.com', '8800880088', '$2y$10$iYHH.d8WhfLmraS0ndsGqOAXfCrwhHWgq9w7vPd1JmMSDqMaxAnw2', NULL, 1, '2026-03-27 14:45:55', '2026-03-27 14:45:10', '2026-03-27 17:26:27'),
(9, 2, 'Aaa', 'aaa@gmail.com', '9090909091', '$2y$10$gg0H4Y5J9sr95R4FN2MgdOoOxphmhp2hSN/SzgRpeF0JwtHiyUcwi', NULL, 1, NULL, '2026-03-27 18:11:03', '2026-03-28 10:34:45'),
(10, 3, 'sus', 'support@gmail.com', '8800880088', '$2y$10$v2P7zRAefHm53jjv7HNu3ufhr/bVbQtZem2K.YK8QdYo2/aAvLJPC', NULL, 1, NULL, '2026-03-27 18:12:02', '2026-03-28 10:34:58'),
(11, 2, 'ram', 'rajpootsyani@gmail.com', '6394296931', '$2y$10$eqkpD2/HcBrO76f60zg/cOn9j3rpFNkm2Emt6WHm2rturx2Hg54za', NULL, 1, '2026-03-30 07:27:33', '2026-03-28 11:35:57', '2026-03-30 07:27:33'),
(12, 3, 'Priyansh verma', 'anshurajverma072@gmail.com', '7217477585', '$2y$10$G5MBWSWBVF9ndmZeShPI7OSSLOK1qQ7h3Nvy0R2yYLYh/HPvidoMq', NULL, 1, NULL, '2026-03-28 15:56:57', '2026-03-28 15:57:32'),
(13, 2, 'babita varshney', 'babita@gmail.com', '6395658116', '$2y$10$NL4c4Szy7x6oi1gotUSnxOwRXRyqmUsmrjOVMKLfHRlZevcAI2QKG', NULL, 1, '2026-03-30 14:25:07', '2026-03-30 14:23:58', '2026-03-30 14:25:07'),
(14, 2, 'brijesh varshney', 'brijesh@gmail.com', '7017753565', '$2y$10$9qdNz2tDhqRYuP6FW6N0ye9HQ4i5byBmgkYxj3RGrTEj6hTLFgbJi', NULL, 1, '2026-03-31 08:22:11', '2026-03-30 15:48:57', '2026-03-31 08:22:11'),
(15, 2, 'Mehtab Pg Wale', 'mehtabpg95@gmail.com', '95202 70062', '$2y$10$IdyrUaIE1pD1a93lIgoT.OIeJHSj6mDObTApw7871zYnGFIwWyyBy', NULL, 1, '2026-03-31 07:48:19', '2026-03-31 07:47:11', '2026-03-31 07:48:19'),
(16, 2, 'Vrinda Girls Pg And hostel', 'viradagirlspg97@gmail.com', '9719706247', '$2y$10$VD/wekRT1y5Uo17jyIVg/uTY2FrWCbLcthtL9LKecoV0dzg2KzoVu', NULL, 1, '2026-03-31 11:20:32', '2026-03-31 11:18:40', '2026-03-31 11:20:32'),
(17, 2, 'Sharad kumar rooms', 'sharadkumarrooms@gmail.com', '6397424216', '$2y$10$q6Yw19X4PMEsH8I4FvW1oegQho9Tr60xk08q641JpGJUGms0ZS3tS', NULL, 1, '2026-04-01 02:42:31', '2026-04-01 02:41:50', '2026-04-01 02:42:31'),
(18, 2, 'Mehtab Pg Wale', 'mehtabpgwale95@gmail.com', '9520270062', '$2y$10$wLB2JEFK/hQaTYJe0qaqQ.Gxi3VEUpaQfGHLN5g03Pgs7tNCQQypq', NULL, 1, '2026-04-01 02:52:30', '2026-04-01 02:52:03', '2026-04-01 02:52:30'),
(19, 2, 'Vrinda Girls Pg And hostel', 'viradagirls97@gmail.com', '09719706247', '$2y$10$gm.fT3U/ekMeWLrUi2vsHeWxPX.u1vweDoInm4yy1JdU6rHzMtmFS', NULL, 1, '2026-04-01 04:55:37', '2026-04-01 03:04:45', '2026-04-01 04:55:37');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `plan_slug` varchar(100) NOT NULL,
  `gateway` varchar(50) NOT NULL DEFAULT 'razorpay',
  `order_id` varchar(191) DEFAULT NULL,
  `payment_id` varchar(191) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'INR',
  `status` enum('created','pending','paid','failed','cancelled') NOT NULL DEFAULT 'created',
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_payments`
--

INSERT INTO `subscription_payments` (`id`, `user_id`, `plan_id`, `plan_slug`, `gateway`, `order_id`, `payment_id`, `signature`, `amount`, `currency`, `status`, `paid_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'staylite-monthly', 'razorpay', 'order_SWv1pyWYmVyYEi', NULL, NULL, 199.00, 'INR', 'created', NULL, '{\"user_id\":\"2\",\"plan_id\":\"1\",\"plan_slug\":\"staylite-monthly\"}', '2026-03-29 05:02:23', '2026-03-29 05:02:23');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `billing_type` enum('monthly','yearly') NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration_days` int(11) NOT NULL,
  `discount_percent` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `billing_type`, `price`, `duration_days`, `discount_percent`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'StayLite Monthly', 'staylite-monthly', 'monthly', 199.00, 30, 0, 1, '2026-03-28 16:26:52', '2026-03-28 17:33:15'),
(2, 'StayLite Yearly', 'staylite-yearly', 'yearly', 1999.00, 365, 5, 1, '2026-03-28 16:26:52', '2026-03-29 05:01:36'),
(3, 'StayPlus Monthly', 'stayplus-monthly', 'monthly', 399.00, 30, 12, 0, '2026-03-28 16:26:52', '2026-03-28 17:33:31'),
(4, 'StayPlus Yearly', 'stayplus-yearly', 'yearly', 3999.00, 365, 12, 0, '2026-03-28 16:26:52', '2026-03-28 16:26:52'),
(5, 'StayElite Monthly', 'stayelite-monthly', 'monthly', 699.00, 30, 20, 0, '2026-03-28 16:26:52', '2026-03-28 16:26:52'),
(6, 'StayElite Yearly', 'stayelite-yearly', 'yearly', 6999.00, 365, 20, 0, '2026-03-28 16:26:52', '2026-03-28 16:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `support_agents`
--

CREATE TABLE `support_agents` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 1,
  `is_active` int(11) DEFAULT NULL,
  `last_active` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_replies`
--

CREATE TABLE `support_replies` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` mediumtext NOT NULL,
  `attachments` mediumtext DEFAULT NULL,
  `is_staff` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_number` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `description` mediumtext NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `attachments` mediumtext DEFAULT NULL,
  `status` enum('open','in-progress','resolved','closed') DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temp_bookings`
--

CREATE TABLE `temp_bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `months` int(11) DEFAULT NULL,
  `guests` int(11) DEFAULT NULL,
  `total_amount` int(11) DEFAULT NULL,
  `wallet_used` int(11) DEFAULT 0,
  `coupon_id` int(11) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `temp_bookings`
--

INSERT INTO `temp_bookings` (`id`, `user_id`, `room_id`, `months`, `guests`, `total_amount`, `wallet_used`, `coupon_id`, `razorpay_order_id`, `created_at`) VALUES
(1, 2, 14, 1, 1, 1980, 0, NULL, 'order_SX8eTK2ypKVuzt', '2026-03-29 18:21:58'),
(2, 2, 14, 1, 1, 1980, 0, NULL, 'order_SX8fdyGPTcldeX', '2026-03-29 18:23:05'),
(3, 2, 14, 1, 1, 1980, 0, NULL, 'order_SX8h7zs8UmSqEU', '2026-03-29 18:24:29'),
(4, 2, 14, 1, 1, 1980, 0, NULL, 'order_SXISQHaO8tWz08', '2026-03-30 03:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `balance_before` decimal(10,2) DEFAULT NULL,
  `balance_after` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'completed',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `transaction_id`, `amount`, `type`, `category`, `description`, `reference_id`, `balance_before`, `balance_after`, `status`, `payment_method`, `created_at`) VALUES
(1, 2, 'TXN69C812FE2EC4720260329', 100.00, 'credit', 'wallet_add', 'Added ₹100 to wallet via UPI', NULL, 5200.00, 5300.00, 'completed', 'upi', '2026-03-28 17:42:22'),
(2, 2, 'PAY87B5784D1774801211', 2000.00, 'debit', 'booking', 'Online payment for booking #BKG9B4061E420260329', '3', 0.00, 0.00, 'completed', 'online', '2026-03-29 16:20:11'),
(3, 2, 'PAY235787301774805491', 1980.00, 'debit', 'booking', 'Online payment for booking #BKGC63BC7D420260329', '4', 0.00, 0.00, 'completed', 'online', '2026-03-29 17:31:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `wallet_balance` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT 'user',
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `wallet_balance`, `full_name`, `phone`, `referral_code`, `referred_by`, `profile_image`, `role`, `status`, `created_at`, `reset_token`, `reset_token_expiry`, `updated_at`) VALUES
(2, 'mayank219', 'rajpootsyani@gmail.com', '$2y$10$ZXxBGdcaFPVTF8Z7NDNWY.uk7WTvlgx/.gPfCQnKqCHH0EJIC9rVO', 12400.00, 'Mayank', NULL, '2EFF440A', '', 'uploads/profile/21d8e159b451a12869428aaf4214b9ec.png', 'user', 1, '2026-03-27 14:08:00', '0981396257ff7e27534799714eb8509acfb17d4770d03eefe49bb1826705aca2', '2026-03-31 01:21:41', '2026-03-31 18:57:17'),
(3, 'gagan_verma917', 'insane31498@gmail.com', '$2y$10$SmVbBDx.oZt6a2JriK5dkOBZ1yrHLhty7kVMqoC5fWObLtwzY4Ypy', 0.00, 'Gagan Verma', '7452884778', '2F043163', '', NULL, 'user', 1, '2026-03-28 06:58:07', NULL, NULL, '2026-03-31 10:13:18'),
(4, 'mayank373', 'rajpootmayank409@gmail.com', '$2y$10$WTrQL4yR4RAOJfAg.haVeOMJ3oeEal8BIFZvDYKHtwef.lfH4jwiG', 0.00, 'mayank', NULL, '420D542C', '', NULL, 'user', 1, '2026-03-29 16:50:59', '08a2b4fa48dbb657e6e3527a172396c0678788ba5d9100d7faecd45af55474e1', '2026-03-30 01:51:11', '2026-03-29 16:51:11'),
(5, 'aayushi_garg879', 'aayushigarg053@gmail.com', '$2y$10$8iB3hE6UXM.h3h7r3HkjUuyLYHMelCFSZWMrqMRqS8tWdqaKC.5P6', 0.00, 'Aayushi Garg', NULL, '03F0327B', '', NULL, 'user', 1, '2026-03-30 04:25:41', NULL, NULL, '2026-03-30 04:25:41'),
(6, 'ayushi_dhawan245', 'dhawanayushi087@gmail.com', '$2y$10$JY/nOM88p0EO5754r9brA.ZIf8jEt8YQT5q709.99dMvAa37y6O2a', 0.00, 'Ayushi Dhawan', NULL, 'EEEE2ED8', '', NULL, 'user', 1, '2026-03-30 05:21:25', NULL, NULL, '2026-03-30 05:21:25'),
(7, 'ekta_sharma839', 'ektas0638@gmail.com', '$2y$10$YagaD1hIo6tlYiYObYlEwej9.yWR0KeJnzlQYztzLIbDJAM66.I82', 0.00, 'Ekta Sharma', NULL, '124CBBC2', '', NULL, 'user', 1, '2026-03-30 05:22:02', NULL, NULL, '2026-03-30 05:22:02'),
(8, 'mayank240', 'rajpootsyan@gmail.com', '$2y$10$ycwMURkLtHNdqAjFGZOoKeRlsRAER4pwp5L/G45SFimbU7SSGC3He', 0.00, 'mayank', NULL, '81C065A9', '', NULL, 'user', 1, '2026-03-30 14:39:22', NULL, NULL, '2026-03-30 14:39:22'),
(9, 'terror554', 'official6804@gmail.com', '$2y$10$gozEzAat7hdPmm01RTv7BuO5iVsxylZCz4Br0bgQtA5X20qJYGA4e', 0.00, 'terror', NULL, '8B16C986', '', NULL, 'user', 1, '2026-03-31 08:17:58', NULL, NULL, '2026-03-31 08:17:58'),
(10, 'mrtyunjay246', 'priencechauhan9027@gmail.com', '$2y$10$CRtwTdKRcBmAq6zNg1qhMOG9GP18d0.kD/YlEHVfhdfca5Q7cuLkC', 0.00, 'Mrtyunjay', NULL, '22A80F04', '', NULL, 'user', 1, '2026-03-31 15:41:02', NULL, NULL, '2026-03-31 15:41:02'),
(11, 'ansh_raj853', 'anshurajverma072@gmail.com', '$2y$10$8o4RXOOV8Rp1lkX0TREEpuETaVj7gzCrIuJmI7QI0FqxlHtx/ksja', 0.00, 'ANSH RAJ', NULL, '9D94DC92', '', NULL, 'user', 1, '2026-03-31 17:28:03', NULL, NULL, '2026-03-31 17:28:03'),
(12, 'sharad_kumar_rooms133', 'sharadkumarrooms@gmail.com', '$2y$10$8Qm0SL9Z3ak7XN56mpU/h.8H9a2GeAWhTv/vWrld0p9uwUBVzvmVC', 0.00, 'Sharad kumar rooms', NULL, 'C24A754C', '', NULL, 'user', 1, '2026-04-01 02:39:59', NULL, NULL, '2026-04-01 02:39:59'),
(13, 'mayank353', 'mayank@gmail.com', '$2y$10$yrrGLYyKEFgCt5EPlt0nDOqRK091rfPpvshhlS3QfvMSB6gIgH2uW', 0.00, 'Mayank', NULL, 'B15B4F1E', '2EFF440A', NULL, 'user', 1, '2026-04-01 04:05:00', NULL, NULL, '2026-04-01 04:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_aadhar`
--

CREATE TABLE `user_aadhar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `aadhar_number` varchar(12) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `aadhar_image_front` mediumtext DEFAULT NULL,
  `aadhar_image_back` mediumtext DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_aadhar`
--

INSERT INTO `user_aadhar` (`id`, `user_id`, `aadhar_number`, `full_name`, `aadhar_image_front`, `aadhar_image_back`, `address`, `verified`, `remarks`, `created_at`, `submitted_at`, `verified_at`, `updated_at`) VALUES
(2, 2, '874817627206', 'Mayank', 'uploads/aadhar/bdeb5b0ab51ca258a5be5790091e9e22.png', 'uploads/aadhar/39e22536ffd4bcbb61697419cea3437c.png', 'belma kalan', 1, 'ytuty', '2026-03-28 15:35:11', '2026-03-28 15:35:11', '2026-03-28 16:07:00', '2026-03-28 16:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_coupons`
--

CREATE TABLE `user_coupons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT current_timestamp(),
  `discount_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `room_id`, `created_at`) VALUES
(1, 23, 14, '2026-03-30 10:21:46'),
(6, 2, 16, '2026-03-31 18:42:54'),
(7, 2, 20, '2026-03-31 18:42:55'),
(8, 2, 15, '2026-03-31 18:42:57'),
(9, 2, 17, '2026-04-01 05:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_type` enum('monthly','yearly') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `user_id`, `plan_id`, `status`, `start_date`, `end_date`, `amount_paid`, `billing_type`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'active', '2026-03-31 00:50:31', '2026-04-08 00:50:31', 0.00, 'yearly', '2026-03-31 16:50:47', '2026-03-31 17:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_wallet`
--

CREATE TABLE `user_wallet` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank_card_id` int(11) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `withdrawal_method` enum('bank','upi') NOT NULL,
  `account_holder` varchar(100) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','cancelled') DEFAULT 'pending',
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `remarks` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `bank_cards`
--
ALTER TABLE `bank_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bank_user` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_booking_number` (`booking_number`),
  ADD KEY `idx_booking_user` (`user_id`),
  ADD KEY `idx_booking_room` (`room_id`),
  ADD KEY `idx_booking_status` (`status`),
  ADD KEY `idx_booking_dates` (`check_in`,`check_out`),
  ADD KEY `idx_booking_user_status` (`user_id`,`status`);

--
-- Indexes for table `booking_assignments`
--
ALTER TABLE `booking_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ba_booking` (`booking_id`),
  ADD KEY `idx_ba_staff` (`staff_id`);

--
-- Indexes for table `booking_extensions`
--
ALTER TABLE `booking_extensions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_be_booking` (`booking_id`);

--
-- Indexes for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_user` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_msg_conv` (`conversation_id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_unlocks`
--
ALTER TABLE `contact_unlocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_coupon_code` (`coupon_code`),
  ADD KEY `idx_coupon_active` (`is_active`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owner_profiles`
--
ALTER TABLE `owner_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_razorpay_order` (`razorpay_order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`payment_status`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_permission_slug` (`slug`);

--
-- Indexes for table `quick_replies`
--
ALTER TABLE `quick_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_emails`
--
ALTER TABLE `referral_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_rewards`
--
ALTER TABLE `referral_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_review_user` (`user_id`),
  ADD KEY `idx_review_room` (`room_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_slug` (`slug`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_permission` (`role_id`,`permission_id`),
  ADD KEY `idx_rp_role` (`role_id`),
  ADD KEY `idx_rp_permission` (`permission_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rooms_city` (`city`),
  ADD KEY `idx_rooms_owner` (`owner_id`),
  ADD KEY `idx_rooms_status` (`status`),
  ADD KEY `idx_rooms_location` (`latitude`,`longitude`),
  ADD KEY `idx_room_city_price` (`city`,`price`);

--
-- Indexes for table `room_colleges`
--
ALTER TABLE `room_colleges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rc_room` (`room_id`),
  ADD KEY `idx_rc_college` (`college_id`);

--
-- Indexes for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_room_facility` (`room_id`,`facility_id`),
  ADD KEY `fk_rf_fac` (`facility_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_room_images` (`room_id`);

--
-- Indexes for table `room_instructions`
--
ALTER TABLE `room_instructions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_id` (`room_id`);

--
-- Indexes for table `room_rules`
--
ALTER TABLE `room_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_id` (`room_id`);

--
-- Indexes for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_staff_role` (`role_id`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_agents`
--
ALTER TABLE `support_agents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `temp_bookings`
--
ALTER TABLE `temp_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD KEY `idx_users_referral_code` (`referral_code`);

--
-- Indexes for table `user_aadhar`
--
ALTER TABLE `user_aadhar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user` (`user_id`),
  ADD KEY `idx_verified` (`verified`);

--
-- Indexes for table `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallet`
--
ALTER TABLE `user_wallet`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wallet_user` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wallet_tx_user` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_withdraw_user` (`user_id`),
  ADD KEY `idx_withdraw_status` (`status`),
  ADD KEY `fk_withdraw_bank` (`bank_card_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bank_cards`
--
ALTER TABLE `bank_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `booking_assignments`
--
ALTER TABLE `booking_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_extensions`
--
ALTER TABLE `booking_extensions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `contact_unlocks`
--
ALTER TABLE `contact_unlocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `owner_profiles`
--
ALTER TABLE `owner_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quick_replies`
--
ALTER TABLE `quick_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `referral_emails`
--
ALTER TABLE `referral_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `referral_rewards`
--
ALTER TABLE `referral_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `room_colleges`
--
ALTER TABLE `room_colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `room_instructions`
--
ALTER TABLE `room_instructions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room_rules`
--
ALTER TABLE `room_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `staff_users`
--
ALTER TABLE `staff_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `support_agents`
--
ALTER TABLE `support_agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temp_bookings`
--
ALTER TABLE `temp_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_aadhar`
--
ALTER TABLE `user_aadhar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_coupons`
--
ALTER TABLE `user_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_wallet`
--
ALTER TABLE `user_wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_cards`
--
ALTER TABLE `bank_cards`
  ADD CONSTRAINT `fk_bank_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_assignments`
--
ALTER TABLE `booking_assignments`
  ADD CONSTRAINT `fk_ba_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ba_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_extensions`
--
ALTER TABLE `booking_extensions`
  ADD CONSTRAINT `fk_be_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  ADD CONSTRAINT `fk_chat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `fk_chat_msg_conv` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reviews` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_owner` FOREIGN KEY (`owner_id`) REFERENCES `staff_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `room_colleges`
--
ALTER TABLE `room_colleges`
  ADD CONSTRAINT `fk_rc_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rc_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_room_colleges` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `fk_rf_fac` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rf_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_room_facilities` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `fk_room_images` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_instructions`
--
ALTER TABLE `room_instructions`
  ADD CONSTRAINT `room_instructions_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_rules`
--
ALTER TABLE `room_rules`
  ADD CONSTRAINT `room_rules_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD CONSTRAINT `fk_staff_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_aadhar`
--
ALTER TABLE `user_aadhar`
  ADD CONSTRAINT `fk_user_aadhar_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_wallet`
--
ALTER TABLE `user_wallet`
  ADD CONSTRAINT `fk_wallet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `fk_wallet_tx_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `fk_withdraw_bank` FOREIGN KEY (`bank_card_id`) REFERENCES `bank_cards` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_withdraw_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
