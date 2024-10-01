-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2024 at 06:51 AM
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
(1, 1, '2024-09-15 01:24:33', 'completed'),
(2, 1, '2024-09-15 01:25:39', 'completed'),
(3, 1, '2024-09-15 01:25:45', 'completed'),
(4, 2, '2024-09-15 01:29:00', 'completed'),
(5, 3, '2024-09-15 01:32:14', 'completed'),
(6, 4, '2024-09-15 01:33:14', 'completed'),
(7, 5, '2024-09-15 02:04:56', 'completed'),
(8, 5, '2024-09-15 02:05:05', 'completed'),
(9, 5, '2024-09-15 02:06:06', 'completed'),
(177, 10, '2024-09-17 02:53:31', 'pending'),
(178, 10, '2024-09-17 02:53:42', 'pending'),
(179, 10, '2024-09-17 02:53:52', 'pending'),
(180, 10, '2024-09-17 02:54:03', 'pending'),
(181, 10, '2024-09-17 02:54:14', 'pending'),
(182, 10, '2024-09-17 02:54:25', 'pending'),
(183, 10, '2024-09-17 02:54:35', 'pending'),
(184, 10, '2024-09-17 02:54:46', 'pending'),
(185, 10, '2024-09-17 02:54:57', 'pending'),
(186, 10, '2024-09-17 02:55:08', 'completed'),
(187, 10, '2024-09-17 02:55:18', 'pending');

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
(286, 'Pants', 'M', 'female', 345, 345.00, 0, '2024-09-16 15:27:21', 1, NULL),
(288, 'Pants', 'S', 'female', 2, 2.00, 0, '2024-09-16 22:52:11', 1, NULL),
(289, 'School Uniform', 'S', 'male', 123, 123.00, 0, '2024-09-17 02:03:27', 1, NULL),
(290, 'School Uniform', 'M', 'male', 123, 123.00, 0, '2024-09-17 02:05:49', 1, '2024-09-17 10:05:52'),
(291, 'Pants', 'M', 'female', 123, 123.00, 0, '2024-09-17 02:06:23', 1, '2024-09-17 10:06:29'),
(292, 'School Uniform', 'S', 'female', 123, 123.00, 0, '2024-09-17 02:11:40', 1, '2024-09-17 10:11:43'),
(293, 'School Uniform', 'M', 'unisex', 123, 123.00, 0, '2024-09-17 02:15:00', 1, '2024-09-17 10:15:04'),
(294, 'College Shirt', 'L', 'female', 100, 100.00, 0, '2024-09-17 02:18:22', 0, NULL),
(295, 'Jogging Pants', 'M', 'unisex', 123, 123.00, 0, '2024-09-17 02:18:32', 0, NULL),
(296, 'School Uniform', 'M', 'female', 100, 123.00, 0, '2024-09-17 02:21:17', 0, NULL),
(297, 'Pants', 'M', 'male', 123, 123.00, 0, '2024-09-17 02:26:57', 0, NULL),
(298, 'Pants', 'M', 'female', 123, 123.00, 0, '2024-09-17 02:28:01', 0, NULL),
(299, 'Pants', 'XL', 'unisex', 123, 123.00, 0, '2024-09-17 02:28:29', 0, NULL),
(300, 'Pants', 'L', 'male', 123, 123.00, 0, '2024-09-17 04:38:33', 0, NULL);

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
(1, NULL),
(2, NULL),
(3, NULL),
(4, NULL),
(5, NULL),
(6, NULL),
(7, NULL),
(8, NULL),
(9, NULL),
(10, '2021001');

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
(9, 6, NULL, 0, 0.00, 'completed', '2024-09-17 01:48:21'),
(10, 9, NULL, 0, 0.00, 'completed', '2024-09-17 01:48:26'),
(11, 1, NULL, 0, 0.00, 'completed', '2024-09-17 01:48:41'),
(12, 187, NULL, 0, 0.00, 'completed', '2024-09-17 03:22:10'),
(13, 187, NULL, 0, 0.00, 'completed', '2024-09-17 03:32:35'),
(14, 186, NULL, 0, 0.00, 'completed', '2024-09-17 03:32:40');

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
(3, 'shreksy', 'shreksy', 'shrek', 'test@gmail.com', 1, '2024-09-15 05:44:52', '$2y$10$c5EDvtNmsZ6kShxfcaTx1e0NiBppsYpeIvVIZYvzbCvsSGEC4pXj2'),
(4, '01', '01', 'beegy', '01@gmail.com', 1, '2024-09-15 12:39:14', '$2y$10$TsAhgfcoFuAkLwStbiHZwO3eMFjT86ixOeGf6IrWl7UnIGUi3gwLq');

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
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
