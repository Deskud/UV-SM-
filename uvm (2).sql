-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2024 at 12:03 PM
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
-- Database: `uvm`
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
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `product_id`, `order_id`, `quantity`) VALUES
(1, 14, 1, 1),
(2, 15, 1, 2),
(3, 14, 2, 2),
(4, 15, 2, 1),
(5, 14, 3, 2),
(6, 15, 3, 1),
(7, 16, 4, 2),
(8, 17, 4, 3),
(9, 18, 4, 1),
(10, 18, 5, 4),
(11, 14, 7, 2),
(12, 15, 7, 2),
(13, 18, 7, 1),
(14, 14, 8, 2),
(15, 15, 8, 2),
(16, 18, 8, 1),
(17, 14, 9, 2),
(18, 15, 9, 2),
(19, 18, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `student_id`, `order_date`, `status`) VALUES
(1, 1, '2024-09-15 09:24:33', 'completed'),
(2, 1, '2024-09-15 09:25:39', 'completed'),
(3, 1, '2024-09-15 09:25:45', 'completed'),
(4, 2, '2024-09-15 09:29:00', 'completed'),
(5, 3, '2024-09-15 09:32:14', 'completed'),
(6, 4, '2024-09-15 09:33:14', 'pending'),
(7, 5, '2024-09-15 10:04:56', 'completed'),
(8, 5, '2024-09-15 10:05:05', 'completed'),
(9, 5, '2024-09-15 10:06:06', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `size` enum('S','M','L','XL') DEFAULT NULL,
  `gender` enum('male','female','unisex') DEFAULT NULL,
  `product_quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `sold_quantity` int(11) DEFAULT 0,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0,
  `date_archived` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `size`, `gender`, `product_quantity`, `price`, `sold_quantity`, `date_added`, `is_archived`, `date_archived`) VALUES
(14, 'School Uniform', '', 'unisex', 50, 1200.00, 0, '2024-09-15 07:52:15', 0, NULL),
(15, 'Pants', '', 'male', 30, 900.00, 0, '2024-09-15 07:52:15', 0, NULL),
(16, 'PE Shirt', '', 'female', 20, 800.00, 0, '2024-09-15 07:52:15', 1, '2024-09-15 15:52:15'),
(17, 'Jogging Pants', '', 'unisex', 15, 1000.00, 0, '2024-09-15 07:52:15', 1, '2024-09-15 15:52:15'),
(18, 'College Shirt', '', 'male', 40, 1100.00, 0, '2024-09-15 07:52:15', 0, NULL),
(19, 'PE Shirt', 'XL', 'unisex', 20, 375.00, 0, '2024-09-15 10:30:28', 0, NULL),
(20, 'School Uniform', 'S', 'unisex', 123, 123.00, 0, '2024-09-16 10:01:57', 0, NULL),
(21, 'Jogging Pants', 'S', 'female', 123, 123.00, 0, '2024-09-16 10:02:08', 0, NULL);

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
(4, NULL),
(5, '123456'),
(2, '20190412'),
(3, '20210049'),
(1, '20231123');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_quantity` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','canceled') DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `order_id`, `user_id`, `total_quantity`, `total_amount`, `status`, `transaction_date`) VALUES
(1, 2, NULL, 3, 3300.00, 'completed', '2024-09-16 08:02:13'),
(2, 7, NULL, 5, 5300.00, 'completed', '2024-09-16 08:05:21'),
(3, 8, NULL, 5, 5300.00, 'completed', '2024-09-16 08:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `access_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `last_name`, `first_name`, `username`, `email`, `access_id`, `date_created`, `password`) VALUES
(2, '01', 'admin', 'admin01', 'admin01@gmail.com', 1, '2024-09-15 04:22:25', '$2y$10$KjNjQithLjLvpNTuIFw4YeSrIIHv6ur8ssTZcesXnQjrLMEce6Y5C'),
(3, '01', 'cashier', 'cashier01', 'cashier01@gmail.com', 2, '2024-09-15 04:26:06', '$2y$10$sZU3VnnwatfmCNxFt3cJfOx5g5JUDCJqNxnh.DAE67oJ8/z3Cm6.m'),
(4, '02', 'admin', 'admin02', 'admin02@gmail.com', 1, '2024-09-15 04:42:21', '$2y$10$3wBlUh1eZp/3PsAY02Il..qM2RNx72c2NBPoxgbn/cS4k2y2LZwZa'),
(5, '02', 'cashier', 'cashier02', 'cashier02@gmail.com', 2, '2024-09-15 05:51:41', '$2y$10$P/sv5RYlg4iOw4hrXZdIa.KbOX7bQEndHkHk/5YuTwPgS7sOkxD2.'),
(6, '03', 'admin', 'admin03', 'admin03@gmail.com', 1, '2024-09-15 05:54:13', '$2y$10$axdsB3Q0m819eN7xX1FHUOL3ewljwkzbD1YqAqfenhfQKyJT0kpv6'),
(7, '04', 'admin', 'admin04', 'admin04@gmail.com', 1, '2024-09-15 10:29:00', '$2y$10$ykwEp3jr60ku/MXGyI4iWelMK5FjRAG9RNsye0DolHAPN86lIWfFm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslevels`
--
ALTER TABLE `accesslevels`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

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
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
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
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `access_id` FOREIGN KEY (`access_id`) REFERENCES `accesslevels` (`access_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
