-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2024 at 06:33 AM
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
-- Database: `uvm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accesslevels`
--

CREATE TABLE `accesslevels` (
  `access_id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accesslevels`
--

INSERT INTO `accesslevels` (`access_id`, `role_name`, `description`) VALUES
(1, 'admin', 'has full access.'),
(2, 'cashier', 'has limited access.');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(3, 'Others'),
(2, 'P.E. Uniform'),
(1, 'Regular Uniform');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `quantity_dispensed` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('unclaimed','partially claimed','fully claimed') DEFAULT 'unclaimed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `product_id`, `order_id`, `quantity`, `quantity_dispensed`, `created_at`, `updated_at`, `status`) VALUES
(23, 72, 1010, 4, 0, '2024-09-29 18:33:40', '2024-10-20 10:38:11', 'unclaimed'),
(24, 76, 1010, 2, 0, '2024-09-29 18:33:40', '2024-10-19 13:01:32', 'unclaimed'),
(25, 79, 1010, 1, 0, '2024-09-29 18:33:40', '2024-10-02 20:08:24', 'unclaimed'),
(28, 57, 1012, 7, 0, '2024-09-29 18:33:40', '2024-10-20 10:50:15', 'unclaimed'),
(32, 56, 1013, 2, 0, '2024-09-29 18:33:40', '2024-10-20 07:44:19', 'unclaimed'),
(33, 57, 1013, 2, 2, '2024-09-29 18:33:40', '2024-10-06 10:21:34', 'unclaimed'),
(34, 68, 1014, 4, 0, '2024-10-01 11:30:33', '2024-10-20 09:33:46', 'unclaimed'),
(35, 68, 1015, 6, 0, '2024-10-01 11:56:19', '2024-10-20 09:37:27', 'unclaimed'),
(39, 75, 1017, 5, 0, '2024-10-01 12:08:13', '2024-10-20 09:45:03', 'unclaimed'),
(41, 81, 1019, 7, 0, '2024-10-01 14:11:48', '2024-10-20 09:46:11', 'unclaimed');

-- --------------------------------------------------------

--
-- Table structure for table `item_modifications`
--

CREATE TABLE `item_modifications` (
  `modification_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `prev_quantity` int(11) DEFAULT NULL,
  `new_quantity` int(11) DEFAULT NULL,
  `prev_price` decimal(10,2) DEFAULT NULL,
  `new_price` decimal(10,2) DEFAULT NULL,
  `modification_reason` text DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `modification_timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_modifications`
--

INSERT INTO `item_modifications` (`modification_id`, `item_id`, `order_id`, `prev_quantity`, `new_quantity`, `prev_price`, `new_price`, `modification_reason`, `modified_by`, `modification_timestamp`) VALUES
(26, 35, 1015, 2, 1, 750.00, 375.00, 'Updated item quantities', '1', '2024-10-07 06:07:43'),
(27, 35, 1015, 1, 2, 375.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:07:59'),
(28, 35, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(29, 35, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(30, 35, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(31, 35, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:01'),
(32, 28, 1012, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-09 05:37:02'),
(33, 35, 1015, 2, 5, 750.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 13:09:35'),
(42, 41, 1019, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-12 13:19:15'),
(44, 39, 1017, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-12 13:27:31'),
(48, 28, 1012, 2, 3, 750.00, 1125.00, 'Updated item quantities', '3', '2024-10-12 14:03:38'),
(49, 28, 1012, 3, 5, 1125.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 14:04:12'),
(50, 28, 1012, 5, 2, 1875.00, 750.00, 'Updated item quantities', '3', '2024-10-12 14:04:31'),
(51, 28, 1012, 2, 5, 750.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 14:10:07'),
(52, 28, 1012, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '3', '2024-10-12 14:12:03'),
(58, 32, 1013, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-12 14:29:39'),
(59, 32, 1013, 2, 3, 750.00, 1125.00, 'Updated item quantities', '2', '2024-10-12 14:39:35'),
(60, 32, 1013, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-12 14:40:14'),
(61, 32, 1013, 4, 3, 1500.00, 1125.00, 'Updated item quantities', '2', '2024-10-12 14:40:38'),
(62, 32, 1013, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-13 13:23:38'),
(63, 32, 1013, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '2', '2024-10-14 03:25:47'),
(64, 41, 1019, 2, 3, 750.00, 1125.00, 'Updated item quantities', '2', '2024-10-14 10:41:09'),
(65, 41, 1019, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-14 10:44:24'),
(66, 41, 1019, 4, 45, 1500.00, 16875.00, 'Updated item quantities', '2', '2024-10-14 10:45:40'),
(67, 41, 1019, 45, 44, 16875.00, 16500.00, 'Updated item quantities', '3', '2024-10-14 10:47:33'),
(68, 41, 1019, 44, 43, 16500.00, 16125.00, 'Updated item quantities', '3', '2024-10-14 12:23:32'),
(69, 41, 1019, 43, 44, 16125.00, 16500.00, 'Updated item quantities', '3', '2024-10-14 13:00:01'),
(70, 41, 1019, 44, 45, 16500.00, 16875.00, 'Updated item quantities', '3', '2024-10-14 13:00:20'),
(71, 41, 1019, 45, 46, 16875.00, 17250.00, 'Updated item quantities', '3', '2024-10-14 13:09:30'),
(72, 41, 1019, 46, 47, 17250.00, 17625.00, 'Updated item quantities', '3', '2024-10-14 13:32:17'),
(75, 23, 1010, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-14 13:51:32'),
(81, 41, 1019, 47, 48, 17625.00, 18000.00, 'Updated item quantities', '3', '2024-10-14 14:45:27'),
(82, 23, 1010, 2, 3, 750.00, 1125.00, 'Updated item quantities', '2', '2024-10-15 01:29:12'),
(83, 23, 1010, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-15 02:08:14'),
(84, 23, 1010, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '2', '2024-10-16 02:36:34'),
(86, 41, 1019, 48, 49, 18000.00, 18375.00, 'Updated item quantities', '3', '2024-10-17 02:20:41'),
(87, 23, 1010, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-19 05:41:58'),
(88, 28, 1012, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-19 05:42:43'),
(89, 41, 1019, 49, 40, 18375.00, 15000.00, 'Updated item quantities', '2', '2024-10-19 06:14:28'),
(90, 41, 1019, 40, 20, 15000.00, 7500.00, 'Updated item quantities', '2', '2024-10-19 06:24:53'),
(91, 24, 1010, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-19 07:01:32'),
(92, 28, 1012, 7, 8, 2625.00, 3000.00, 'Updated item quantities', '2', '2024-10-19 07:02:21'),
(93, 28, 1012, 8, 9, 3000.00, 3375.00, 'Updated item quantities', '2', '2024-10-19 07:02:48'),
(94, 41, 1019, 20, 19, 7500.00, 7125.00, 'Updated item quantities', '2', '2024-10-19 07:05:30'),
(95, 41, 1019, 19, 15, 7125.00, 5625.00, 'Updated item quantities', '2', '2024-10-20 00:55:30'),
(96, 23, 1010, 6, 5, 2250.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 01:20:39'),
(97, 23, 1010, 5, 4, 1875.00, 1500.00, 'Updated item quantities', '2', '2024-10-20 01:21:52'),
(98, 28, 1012, 9, 7, 3375.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 01:27:50'),
(99, 32, 1013, 5, 2, 1875.00, 750.00, 'Updated item quantities', '2', '2024-10-20 01:44:19'),
(100, 34, 1014, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-20 01:54:53'),
(101, 23, 1010, 4, 3, 1500.00, 1125.00, 'Updated item quantities', '2', '2024-10-20 01:58:17'),
(102, 34, 1014, 2, 1, 750.00, 375.00, 'Updated item quantities', '2', '2024-10-20 02:07:57'),
(103, 34, 1014, 1, 3, 375.00, 1125.00, 'Updated item quantities', '2', '2024-10-20 02:10:38'),
(104, 35, 1015, 5, 2, 1875.00, 750.00, 'Updated item quantities', '2', '2024-10-20 02:49:07'),
(107, 39, 1017, 2, 1, 750.00, 375.00, 'Updated item quantities', '2', '2024-10-20 02:56:42'),
(108, 39, 1017, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-20 02:56:52'),
(109, 41, 1019, 15, 12, 5625.00, 4500.00, 'Updated item quantities', '2', '2024-10-20 03:12:06'),
(110, 34, 1014, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-20 03:33:46'),
(111, 35, 1015, 2, 5, 750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:36:36'),
(112, 35, 1015, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-20 03:37:27'),
(114, 41, 1019, 12, 10, 4500.00, 3750.00, 'Updated item quantities', '2', '2024-10-20 03:42:07'),
(115, 41, 1019, 10, 5, 3750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:43:30'),
(116, 41, 1019, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-20 03:44:41'),
(117, 39, 1017, 2, 5, 750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:45:03'),
(118, 41, 1019, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 03:46:11'),
(121, 23, 1010, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-20 04:38:11'),
(122, 28, 1012, 7, 8, 2625.00, 3000.00, 'Updated item quantities', '2', '2024-10-20 04:47:09'),
(123, 28, 1012, 8, 9, 3000.00, 3375.00, 'Updated item quantities', '2', '2024-10-20 04:48:07'),
(124, 28, 1012, 9, 8, 3375.00, 3000.00, 'Updated item quantities', '2', '2024-10-20 04:49:10'),
(125, 28, 1012, 8, 7, 3000.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 04:50:15');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','completed') DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `status`, `updated_at`) VALUES
(1010, '2024-09-25 10:00:12', 'completed', '2024-10-20 10:38:30'),
(1011, '2024-09-25 12:59:33', 'pending', '2024-10-20 10:54:42'),
(1012, '2024-09-25 13:04:17', 'pending', '2024-10-20 10:57:01'),
(1013, '2024-09-26 05:13:26', 'completed', '2024-10-20 11:04:07'),
(1014, '2024-10-01 03:30:33', 'completed', '2024-10-20 11:06:38'),
(1015, '2024-10-01 03:56:19', 'pending', '2024-10-20 10:24:52'),
(1016, '2024-10-01 04:05:34', 'pending', '2024-10-20 10:24:54'),
(1017, '2024-10-01 04:08:13', 'completed', '2024-10-20 11:04:16'),
(1018, '2024-10-01 04:09:58', 'completed', '2024-10-20 10:59:06'),
(1019, '2024-10-01 06:11:48', 'pending', '2024-10-20 10:34:12'),
(1020, '2024-10-01 06:13:38', 'pending', '2024-10-20 10:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_modifications`
--

CREATE TABLE `order_modifications` (
  `modification_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `prev_total_quantity` int(11) DEFAULT NULL,
  `new_total_quantity` int(11) DEFAULT NULL,
  `prev_total_amount` decimal(10,2) DEFAULT NULL,
  `new_total_amount` decimal(10,2) DEFAULT NULL,
  `modification_reason` text DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `modification_timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_modifications`
--

INSERT INTO `order_modifications` (`modification_id`, `order_id`, `prev_total_quantity`, `new_total_quantity`, `prev_total_amount`, `new_total_amount`, `modification_reason`, `modified_by`, `modification_timestamp`) VALUES
(1, 1010, 5, 4, 1875.00, 1500.00, 'Item removed.', '1', '2024-10-07 05:41:45'),
(2, 1011, 2, 1, 750.00, 375.00, 'Item removed.', '1', '2024-10-07 06:05:07'),
(3, 1012, 2, 1, 750.00, 375.00, 'Item removed.', '1', '2024-10-07 06:06:02'),
(4, 1015, 2, 1, 750.00, 375.00, 'Updated item quantities', '1', '2024-10-07 06:07:43'),
(5, 1015, 1, 2, 375.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:07:59'),
(6, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(7, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(8, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:00'),
(9, 1015, 2, 2, 750.00, 750.00, 'Updated item quantities', '1', '2024-10-07 06:08:01'),
(10, 1012, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-09 05:37:02'),
(11, 1015, 2, 5, 750.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 13:09:35'),
(12, 1019, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-12 13:19:15'),
(13, 1016, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-12 13:23:22'),
(14, 1017, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-12 13:27:31'),
(15, 1011, 1, 2, 375.00, 750.00, 'Updated item quantities', '3', '2024-10-12 13:55:02'),
(16, 1012, 2, 3, 750.00, 1125.00, 'Updated item quantities', '3', '2024-10-12 14:03:38'),
(17, 1012, 3, 5, 1125.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 14:04:12'),
(18, 1012, 5, 2, 1875.00, 750.00, 'Updated item quantities', '3', '2024-10-12 14:04:31'),
(19, 1012, 2, 5, 750.00, 1875.00, 'Updated item quantities', '3', '2024-10-12 14:10:07'),
(20, 1012, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '3', '2024-10-12 14:12:03'),
(21, 1013, 6, 10, 2250.00, 3750.00, 'Updated item quantities', '3', '2024-10-12 14:16:47'),
(22, 1013, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '3', '2024-10-12 14:29:39'),
(23, 1013, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '2', '2024-10-12 14:39:35'),
(24, 1013, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-12 14:40:14'),
(25, 1013, 6, 5, 2250.00, 1875.00, 'Updated item quantities', '2', '2024-10-12 14:40:38'),
(26, 1013, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-13 13:23:38'),
(27, 1013, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-14 03:25:47'),
(28, 1019, 2, 3, 750.00, 1125.00, 'Updated item quantities', '2', '2024-10-14 10:41:09'),
(29, 1019, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-14 10:44:24'),
(30, 1019, 4, 45, 1500.00, 16875.00, 'Updated item quantities', '2', '2024-10-14 10:45:40'),
(31, 1019, 45, 44, 16875.00, 16500.00, 'Updated item quantities', '3', '2024-10-14 10:47:33'),
(32, 1019, 44, 43, 16500.00, 16125.00, 'Updated item quantities', '3', '2024-10-14 12:23:32'),
(33, 1019, 43, 44, 16125.00, 16500.00, 'Updated item quantities', '3', '2024-10-14 13:00:01'),
(34, 1019, 44, 45, 16500.00, 16875.00, 'Updated item quantities', '3', '2024-10-14 13:00:20'),
(35, 1019, 45, 46, 16875.00, 17250.00, 'Updated item quantities', '3', '2024-10-14 13:09:30'),
(36, 1019, 46, 47, 17250.00, 17625.00, 'Updated item quantities', '3', '2024-10-14 13:32:17'),
(37, 1010, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '2', '2024-10-14 13:50:43'),
(38, 1010, 5, 7, 1875.00, 2625.00, 'Updated item quantities', '2', '2024-10-14 13:51:32'),
(39, 1016, 2, 3, 750.00, 1125.00, 'Updated item quantities', '3', '2024-10-14 14:22:54'),
(40, 1016, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '3', '2024-10-14 14:31:49'),
(41, 1016, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '3', '2024-10-14 14:32:23'),
(42, 1019, 47, 48, 17625.00, 18000.00, 'Updated item quantities', '3', '2024-10-14 14:45:27'),
(43, 1010, 4, 5, 1500.00, 1875.00, 'Updated item quantities', '2', '2024-10-15 01:29:12'),
(44, 1010, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-15 02:08:14'),
(45, 1010, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-16 02:36:34'),
(46, 1016, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-16 21:48:16'),
(47, 1019, 48, 49, 18000.00, 18375.00, 'Updated item quantities', '3', '2024-10-17 02:20:41'),
(48, 1010, 7, 8, 2625.00, 3000.00, 'Updated item quantities', '2', '2024-10-19 05:41:58'),
(49, 1012, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-19 05:42:43'),
(50, 1019, 49, 40, 18375.00, 15000.00, 'Updated item quantities', '2', '2024-10-19 06:14:28'),
(51, 1019, 40, 20, 15000.00, 7500.00, 'Updated item quantities', '2', '2024-10-19 06:24:53'),
(52, 1010, 8, 9, 3000.00, 3375.00, 'Updated item quantities', '2', '2024-10-19 07:01:32'),
(53, 1012, 7, 8, 2625.00, 3000.00, 'Updated item quantities', '2', '2024-10-19 07:02:21'),
(54, 1012, 8, 9, 3000.00, 3375.00, 'Updated item quantities', '2', '2024-10-19 07:02:48'),
(55, 1019, 20, 19, 7500.00, 7125.00, 'Updated item quantities', '2', '2024-10-19 07:05:30'),
(56, 1019, 19, 15, 7125.00, 5625.00, 'Updated item quantities', '2', '2024-10-20 00:55:30'),
(57, 1010, 9, 8, 3375.00, 3000.00, 'Updated item quantities', '2', '2024-10-20 01:20:39'),
(58, 1010, 8, 7, 3000.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 01:21:52'),
(59, 1012, 9, 7, 3375.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 01:27:50'),
(60, 1013, 7, 4, 2625.00, 1500.00, 'Updated item quantities', '2', '2024-10-20 01:44:19'),
(61, 1014, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-20 01:54:53'),
(62, 1010, 7, 6, 2625.00, 2250.00, 'Updated item quantities', '2', '2024-10-20 01:58:17'),
(63, 1014, 2, 1, 750.00, 375.00, 'Updated item quantities', '2', '2024-10-20 02:07:57'),
(64, 1014, 1, 3, 375.00, 1125.00, 'Updated item quantities', '2', '2024-10-20 02:10:38'),
(65, 1015, 5, 2, 1875.00, 750.00, 'Updated item quantities', '2', '2024-10-20 02:49:07'),
(66, 1016, 6, 2, 2250.00, 750.00, 'Updated item quantities', '2', '2024-10-20 02:51:12'),
(67, 1016, 2, 1, 750.00, 375.00, 'Updated item quantities', '2', '2024-10-20 02:54:55'),
(68, 1017, 2, 1, 750.00, 375.00, 'Updated item quantities', '2', '2024-10-20 02:56:42'),
(69, 1017, 1, 2, 375.00, 750.00, 'Updated item quantities', '2', '2024-10-20 02:56:52'),
(70, 1019, 15, 12, 5625.00, 4500.00, 'Updated item quantities', '2', '2024-10-20 03:12:06'),
(71, 1014, 3, 4, 1125.00, 1500.00, 'Updated item quantities', '2', '2024-10-20 03:33:46'),
(72, 1015, 2, 5, 750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:36:36'),
(73, 1015, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-20 03:37:27'),
(74, 1016, 1, 5, 375.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:40:47'),
(75, 1019, 12, 10, 4500.00, 3750.00, 'Updated item quantities', '2', '2024-10-20 03:42:07'),
(76, 1019, 10, 5, 3750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:43:30'),
(77, 1019, 5, 6, 1875.00, 2250.00, 'Updated item quantities', '2', '2024-10-20 03:44:41'),
(78, 1017, 2, 5, 750.00, 1875.00, 'Updated item quantities', '2', '2024-10-20 03:45:03'),
(79, 1019, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 03:46:11'),
(80, 1010, 6, 7, 2250.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 04:38:11'),
(81, 1012, 7, 8, 2625.00, 3000.00, 'Updated item quantities', '2', '2024-10-20 04:47:09'),
(82, 1012, 8, 9, 3000.00, 3375.00, 'Updated item quantities', '2', '2024-10-20 04:48:07'),
(83, 1012, 9, 8, 3375.00, 3000.00, 'Updated item quantities', '2', '2024-10-20 04:49:10'),
(84, 1012, 8, 7, 3000.00, 2625.00, 'Updated item quantities', '2', '2024-10-20 04:50:15');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `unit_num` tinyint(4) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `gender` enum('male','female','unisex') NOT NULL,
  `product_quantity` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `sold_quantity` int(11) DEFAULT 0,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `date_archived` datetime DEFAULT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `unit_num`, `size_id`, `gender`, `product_quantity`, `price`, `sold_quantity`, `date_added`, `is_archived`, `date_archived`, `category_id`) VALUES
(54, 'MadMilk', 1, 1, 'male', 17, 375.00, 0, '2024-09-25 09:43:10', 0, '2024-10-07 11:59:38', 1),
(55, 'Male Polo', 2, 2, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(56, 'Male Polo', 3, 3, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(57, 'Male Polo', 4, 4, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(58, 'Sandvich', 5, 1, 'male', 19, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(59, 'Male Pants', 6, 2, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(60, 'Male Pants', 7, 3, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(61, 'Male Pants', 8, 4, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(62, 'The Vaccinator', 9, 1, 'female', 19, 370.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(63, 'Female Blouse', 10, 2, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(64, 'Female Blouse', 11, 3, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(65, 'Female Blouse', 12, 4, 'female', 15, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(66, 'Female Pants', NULL, 1, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(67, 'Female Pants', NULL, 2, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(68, 'Female Pants', NULL, 3, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(69, 'Female Pants', NULL, 4, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
(70, 'PE Shirt', 17, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-09-27 20:26:38', 2),
(71, 'PE Shirt', NULL, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(72, 'PE Shirt', NULL, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(73, 'PE Shirt', NULL, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(74, 'Jogging Pants', 21, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-09-27 20:26:53', 2),
(75, 'Jogging Pants', NULL, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(76, 'Jogging Pants', NULL, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(77, 'Jogging Pants', NULL, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
(78, 'Washday Shirt', 0, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-10-02 20:33:46', 3),
(79, 'Washday Shirt', NULL, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-10-17 06:00:51', 3),
(80, 'Washday Shirt', 27, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 3),
(81, 'Washday Shirt', NULL, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-10-17 06:01:15', 3),
(82, 'test', NULL, 1, 'female', 123, 123.00, 0, '2024-10-16 20:56:36', 1, '2024-10-17 06:00:40', 3),
(83, 'test', NULL, 2, 'female', 123, 123.00, 0, '2024-10-16 20:56:36', 0, NULL, 3),
(84, 'test', NULL, 3, 'female', 123, 123.00, 0, '2024-10-16 20:56:36', 0, NULL, 3),
(85, 'test', NULL, 4, 'female', 123, 123.00, 0, '2024-10-16 20:56:36', 1, '2024-10-17 06:01:12', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `size_name` enum('S','M','L','XL') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_name`) VALUES
(1, 'S'),
(2, 'M'),
(3, 'L'),
(4, 'XL');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_no`) VALUES
(1, '111'),
(2, '123'),
(4, '1234567890'),
(8, '20190412'),
(3, '3'),
(7, '369'),
(5, '6'),
(6, '9');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `qr_code` int(11) NOT NULL,
  `status` enum('unclaimed','partially claimed','fully claimed') DEFAULT NULL,
  `quantity_dispensed` int(11) DEFAULT 0,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `order_id`, `user_id`, `total_quantity`, `total_amount`, `transaction_date`, `qr_code`, `status`, `quantity_dispensed`, `updated_at`, `student_id`) VALUES
(160, 1019, 2, 12, 4500.00, '2024-10-19 19:12:47', 569882, 'unclaimed', 0, NULL, NULL),
(161, 1018, 2, 0, 0.00, '2024-10-19 19:12:47', 892575, 'unclaimed', 0, NULL, NULL),
(162, 1019, 2, 12, 4500.00, '2024-10-19 19:12:47', 523039, 'unclaimed', 0, NULL, NULL),
(163, 1019, 2, 12, 4500.00, '2024-10-19 19:12:47', 547591, 'unclaimed', 0, NULL, NULL),
(164, 1019, 2, 12, 4500.00, '2024-10-19 19:13:44', 902113, 'unclaimed', 0, NULL, NULL),
(165, 1019, 2, 12, 4500.00, '2024-10-19 19:13:44', 117723, 'unclaimed', 0, NULL, NULL),
(166, 1019, 2, 12, 4500.00, '2024-10-19 19:13:44', 655711, 'unclaimed', 0, NULL, NULL),
(167, 1018, 2, 0, 0.00, '2024-10-19 19:13:44', 694357, 'unclaimed', 0, NULL, NULL),
(168, 1010, 2, 6, 2250.00, '2024-10-19 19:13:44', 475559, 'unclaimed', 0, NULL, NULL),
(169, 1018, 2, 0, 0.00, '2024-10-19 19:13:52', 376931, 'unclaimed', 0, NULL, NULL),
(170, 1019, 2, 12, 4500.00, '2024-10-19 19:13:52', 528957, 'unclaimed', 0, NULL, NULL),
(171, 1019, 2, 12, 4500.00, '2024-10-19 19:13:52', 396515, 'unclaimed', 0, NULL, NULL),
(172, 1011, 2, 0, 0.00, '2024-10-19 19:13:52', 686763, 'unclaimed', 0, NULL, NULL),
(173, 1019, 2, 12, 4500.00, '2024-10-19 19:13:52', 862861, 'unclaimed', 0, NULL, NULL),
(174, 1010, 2, 6, 2250.00, '2024-10-19 19:13:52', 609065, 'unclaimed', 0, NULL, NULL),
(175, 1014, 2, 4, 1500.00, '2024-10-19 19:33:57', 936475, 'unclaimed', 0, NULL, NULL),
(176, 1015, 2, 6, 2250.00, '2024-10-19 19:37:33', 156656, 'unclaimed', 0, NULL, NULL),
(177, 1015, 2, 6, 2250.00, '2024-10-19 19:40:53', 726684, 'unclaimed', 0, NULL, NULL),
(178, 1016, 2, 5, 1875.00, '2024-10-19 19:40:53', 400779, 'unclaimed', 0, NULL, NULL),
(179, 1017, 2, 5, 1875.00, '2024-10-19 19:45:10', 451993, 'unclaimed', 0, NULL, NULL),
(180, 1018, 2, 0, 0.00, '2024-10-19 19:46:00', 326965, 'unclaimed', 0, NULL, NULL),
(181, 1017, 2, 5, 1875.00, '2024-10-19 19:46:00', 716798, 'unclaimed', 0, NULL, NULL),
(182, 1017, 2, 5, 1875.00, '2024-10-19 19:46:12', 452930, 'unclaimed', 0, NULL, NULL),
(183, 1018, 2, 0, 0.00, '2024-10-19 19:46:12', 153666, 'unclaimed', 0, NULL, NULL),
(184, 1019, 2, 7, 2625.00, '2024-10-19 19:46:12', 939970, 'unclaimed', 0, NULL, NULL),
(185, 1019, 2, 7, 2625.00, '2024-10-19 19:46:12', 765563, 'unclaimed', 0, NULL, NULL),
(186, 1017, 2, 5, 1875.00, '2024-10-19 19:49:19', 482885, 'unclaimed', 0, NULL, NULL),
(187, 1016, 2, 0, 0.00, '2024-10-19 19:51:42', 264215, 'unclaimed', 0, NULL, NULL),
(188, 1018, 2, 0, 0.00, '2024-10-19 19:51:54', 364930, 'unclaimed', 0, NULL, NULL),
(189, 1016, 2, 0, 0.00, '2024-10-19 19:51:54', 518833, 'unclaimed', 0, NULL, NULL),
(190, 1020, 2, 0, 0.00, '2024-10-19 20:16:54', 645037, 'unclaimed', 0, NULL, NULL),
(191, 1010, 2, 6, 2250.00, '2024-10-19 20:19:38', 576079, 'unclaimed', 0, NULL, NULL),
(192, 1016, 2, 0, 0.00, '2024-10-19 20:20:16', 335643, 'unclaimed', 0, NULL, NULL),
(193, 1017, 2, 5, 1875.00, '2024-10-19 20:20:56', 948959, 'unclaimed', 0, NULL, NULL),
(194, 1020, 2, 0, 0.00, '2024-10-19 20:21:57', 212829, 'unclaimed', 0, NULL, NULL),
(195, 1019, 2, 7, 2625.00, '2024-10-19 20:22:55', 191248, 'unclaimed', 0, NULL, NULL),
(196, 1010, 2, 6, 2250.00, '2024-10-19 20:23:59', 806787, 'unclaimed', 0, NULL, NULL),
(197, 1010, 2, 7, 2625.00, '2024-10-19 20:38:30', 380920, 'unclaimed', 0, NULL, NULL),
(198, 1011, 2, 0, 0.00, '2024-10-19 20:46:06', 649182, 'unclaimed', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_modifications`
--

CREATE TABLE `transaction_modifications` (
  `modification_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `prev_total_quantity` int(11) DEFAULT NULL,
  `new_total_quantity` int(11) DEFAULT NULL,
  `prev_total_amount` decimal(10,2) DEFAULT NULL,
  `new_total_amount` decimal(10,2) DEFAULT NULL,
  `prev_quantity_dispensed` int(11) DEFAULT NULL,
  `new_quantity_dispensed` int(11) DEFAULT NULL,
  `modification_reason` text DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  `modification_timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `access_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `last_name`, `first_name`, `username`, `email`, `access_id`, `date_created`, `password`) VALUES
(1, 'admin01', 'admin', 'admin01', 'admin01@gmail.com', 1, '2024-09-22 02:17:35', '$2y$10$QDo6TZsMxqQR9ZYtWOBc0uWNyRWlm0MlwFPICMNYzyP1yzgwAlWfG'),
(2, '1', '1', 'test1', 'test@gmail.com', 1, '2024-09-22 13:56:15', '$2y$10$BaGJhHroXUcM2nBc1eszou6cRSqbd8G1CVMG6Dz8TWl6XhacfTx72'),
(3, '01', 'Cashier', 'Cashier01', 'cashier01@gmail.com', 2, '2024-10-09 03:36:24', '$2y$10$0UIKyb7Kj3lJga.U8oAsHOR7ziS78zTowRjoG9qpLPNKhvXtvw/6a');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevels`
--
ALTER TABLE `accesslevels`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `item_modifications`
--
ALTER TABLE `item_modifications`
  ADD PRIMARY KEY (`modification_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_modifications`
--
ALTER TABLE `order_modifications`
  ADD PRIMARY KEY (`modification_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `unique_cell_num` (`unit_num`),
  ADD KEY `fk_category` (`category_id`),
  ADD KEY `fk_size_id` (`size_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_no` (`student_no`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_student` (`student_id`);

--
-- Indexes for table `transaction_modifications`
--
ALTER TABLE `transaction_modifications`
  ADD PRIMARY KEY (`modification_id`),
  ADD KEY `transaction_modifications_ibfk_1` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `access_id` (`access_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accesslevels`
--
ALTER TABLE `accesslevels`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `item_modifications`
--
ALTER TABLE `item_modifications`
  MODIFY `modification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1022;

--
-- AUTO_INCREMENT for table `order_modifications`
--
ALTER TABLE `order_modifications`
  MODIFY `modification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `transaction_modifications`
--
ALTER TABLE `transaction_modifications`
  MODIFY `modification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `item_modifications`
--
ALTER TABLE `item_modifications`
  ADD CONSTRAINT `item_modifications_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_modifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_modifications`
--
ALTER TABLE `order_modifications`
  ADD CONSTRAINT `order_modifications_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `fk_size_id` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transaction_modifications`
--
ALTER TABLE `transaction_modifications`
  ADD CONSTRAINT `transaction_modifications_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
