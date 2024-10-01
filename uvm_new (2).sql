  -- phpMyAdmin SQL Dump
  -- version 5.2.1
  -- https://www.phpmyadmin.net/
  --
  -- Host: 127.0.0.1
  -- Generation Time: Sep 28, 2024 at 12:48 PM
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
  -- Database: `uvm_new`
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
  (2, 'pe uniform'),
  (1, 'school uniform'),
  (3, 'washday shirt');

  -- --------------------------------------------------------

  --
  -- Table structure for table `items`
  --

  CREATE TABLE `items` (
    `item_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `items`
  --

  INSERT INTO `items` (`item_id`, `product_id`, `order_id`, `quantity`) VALUES
  (21, 57, 1010, 1),
  (22, 61, 1010, 1),
  (23, 72, 1010, 1),
  (24, 76, 1010, 1),
  (25, 79, 1010, 1),
  (26, 57, 1011, 1),
  (27, 80, 1011, 1),
  (28, 57, 1012, 1),
  (29, 72, 1012, 1),
  (30, 54, 1013, 1),
  (31, 55, 1013, 2),
  (32, 56, 1013, 1),
  (33, 57, 1013, 2);

  -- --------------------------------------------------------

  --
  -- Table structure for table `orders`
  --

  CREATE TABLE `orders` (
    `order_id` int(11) NOT NULL,
    `student_id` int(11) NOT NULL,
    `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `status` enum('pending','processed','completed') DEFAULT 'pending'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `orders`
  --

  INSERT INTO `orders` (`order_id`, `student_id`, `order_date`, `status`) VALUES
  (1010, 7, '2024-09-25 10:00:12', 'pending'),
  (1011, 4, '2024-09-25 12:59:33', 'pending'),
  (1012, 4, '2024-09-25 13:04:17', 'pending'),
  (1013, 1, '2024-09-26 05:13:26', 'pending');

  -- --------------------------------------------------------

  --
  -- Table structure for table `products`
  --

  CREATE TABLE `products` (
    `product_id` int(11) NOT NULL,
    `product_name` varchar(100) NOT NULL,
    `cell_num` tinyint(4) NOT NULL,
    `size` int(11) NOT NULL,
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

  INSERT INTO `products` (`product_id`, `product_name`, `cell_num`, `size`, `gender`, `product_quantity`, `price`, `sold_quantity`, `date_added`, `is_archived`, `date_archived`, `category_id`) VALUES
  (54, 'Male Polo', 1, 1, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (55, 'Male Polo', 2, 2, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (56, 'Male Polo', 3, 3, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (57, 'Male Polo', 4, 4, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (58, 'Male Pants', 5, 1, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (59, 'Male Pants', 6, 2, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (60, 'Male Pants', 7, 3, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (61, 'Male Pants', 8, 4, 'male', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (62, 'Female Blouse', 9, 1, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (63, 'Female Blouse', 10, 2, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (64, 'Female Blouse', 11, 3, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (65, 'Female Blouse', 12, 4, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (66, 'Female Pants', 13, 1, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (67, 'Female Pants', 14, 2, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (68, 'Female Pants', 15, 3, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (69, 'Female Pants', 16, 4, 'female', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 1),
  (70, 'PE Shirt', 17, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-09-27 20:26:38', 2),
  (71, 'PE Shirt', 18, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (72, 'PE Shirt', 19, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (73, 'PE Shirt', 20, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (74, 'Jogging Pants', 21, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 1, '2024-09-27 20:26:53', 2),
  (75, 'Jogging Pants', 22, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (76, 'Jogging Pants', 23, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (77, 'Jogging Pants', 24, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 2),
  (78, 'Washday Shirt', 25, 1, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 3),
  (79, 'Washday Shirt', 26, 2, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 3),
  (80, 'Washday Shirt', 27, 3, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 3),
  (81, 'Washday Shirt', 28, 4, 'unisex', 20, 375.00, 0, '2024-09-25 09:43:10', 0, NULL, 3);

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
    `status` enum('unclaimed','claimed') DEFAULT 'unclaimed'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

  --
  -- Dumping data for table `transactions`
  --

  INSERT INTO `transactions` (`transaction_id`, `order_id`, `user_id`, `total_quantity`, `total_amount`, `transaction_date`, `qr_code`, `status`) VALUES
  (1, 1010, 0, 0, 0.00, '2024-09-25 10:52:36', 123456, 'unclaimed'),
  (2, 1013, 1, 4, 1400.00, '2024-09-26 05:17:38', 654321, 'unclaimed');

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
  (2, '1', '1', 'test1', 'test@gmail.com', 1, '2024-09-22 13:56:15', '$2y$10$BaGJhHroXUcM2nBc1eszou6cRSqbd8G1CVMG6Dz8TWl6XhacfTx72');

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
  -- Indexes for table `orders`
  --
  ALTER TABLE `orders`
    ADD PRIMARY KEY (`order_id`),
    ADD KEY `student_id` (`student_id`);

  --
  -- Indexes for table `products`
  --
  ALTER TABLE `products`
    ADD PRIMARY KEY (`product_id`),
    ADD UNIQUE KEY `unique_cell_num` (`cell_num`),
    ADD KEY `fk_category` (`category_id`);

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
    ADD KEY `user_id` (`user_id`);

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
    MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

  --
  -- AUTO_INCREMENT for table `orders`
  --
  ALTER TABLE `orders`
    MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1014;

  --
  -- AUTO_INCREMENT for table `products`
  --
  ALTER TABLE `products`
    MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

  --
  -- AUTO_INCREMENT for table `sizes`
  --
  ALTER TABLE `sizes`
    MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

  --
  -- AUTO_INCREMENT for table `students`
  --
  ALTER TABLE `students`
    MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

  --
  -- AUTO_INCREMENT for table `transactions`
  --
  ALTER TABLE `transactions`
    MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

  --
  -- AUTO_INCREMENT for table `users`
  --
  ALTER TABLE `users`
    MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
  COMMIT;

  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
