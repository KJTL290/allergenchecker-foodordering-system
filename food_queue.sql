-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 05:11 AM
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
-- Database: `food_queue`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(10) DEFAULT '?️',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `sort_order`) VALUES
(1, 'Chicken', '🍗', 0),
(2, 'Burgers', '🍔', 1),
(3, 'Noodles', '🍝', 2),
(4, 'Drinks', '🥤', 3),
(6, 'Desserts', '🍰', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('unpaid','pending','preparing','ready','completed') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `total_price`, `status`, `created_at`) VALUES
(1, 89.00, 'completed', '2025-12-08 00:25:04'),
(3, 450.00, 'completed', '2025-12-08 07:25:28'),
(4, 40.00, 'completed', '2025-12-08 07:25:35'),
(5, 29.00, 'completed', '2025-12-08 07:25:39'),
(7, 169.00, 'completed', '2025-12-08 12:25:49'),
(8, 89.00, 'completed', '2025-12-08 13:28:00'),
(10, 358.00, 'completed', '2025-12-08 14:51:36'),
(11, 624.00, 'completed', '2025-12-09 03:42:40'),
(12, 95.00, 'completed', '2025-12-09 03:43:45'),
(13, 95.00, 'completed', '2025-12-09 03:47:11'),
(14, 95.00, 'completed', '2025-12-09 03:48:09'),
(15, 95.00, 'completed', '2025-12-09 03:54:49'),
(16, 95.00, 'completed', '2025-12-09 04:03:23'),
(17, 95.00, 'completed', '2025-12-09 04:05:21'),
(18, 450.00, 'completed', '2025-12-09 04:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `quantity`) VALUES
(1, 1, '1pc Chicken w/ Rice', 1),
(3, 3, 'Bucket of 6', 1),
(4, 4, 'Cheeseburger', 1),
(5, 5, 'Coke', 1),
(7, 7, 'Spicy Chicken', 1),
(8, 7, 'Regular Burger', 1),
(9, 7, 'Coke', 1),
(10, 8, '1 slice mango pie', 1),
(11, 8, 'Coke', 1),
(13, 10, '2pc Chicken w/ Rice', 2),
(14, 10, 'Coke', 2),
(15, 11, 'Bucket of 6', 1),
(16, 11, 'Coke', 6),
(17, 12, 'Spicy Chicken', 1),
(18, 13, 'Spicy Chicken', 1),
(19, 14, 'Spicy Chicken', 1),
(20, 15, 'Spicy Chicken', 1),
(21, 16, 'Spicy Chicken', 1),
(22, 17, 'Spicy Chicken', 1),
(23, 18, 'Bucket of 6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `is_available` tinyint(4) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 999
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `category`, `is_available`, `image`, `sort_order`) VALUES
(1, '1pc Chicken w/ Rice', 99.00, 'Chicken', 1, NULL, 2),
(2, '2pc Chicken w/ Rice', 150.00, 'Chicken', 1, NULL, 3),
(3, 'Bucket of 6', 450.00, 'Chicken', 1, NULL, 1),
(4, 'Spicy Chicken', 95.00, 'Chicken', 1, NULL, 0),
(5, 'Regular Burger', 45.00, 'Burgers', 1, NULL, 0),
(6, 'Cheeseburger', 40.00, 'Burgers', 1, NULL, 1),
(7, 'Double Patty', 95.00, 'Burgers', 1, NULL, 2),
(8, 'Spaghetti', 59.00, 'Noodles', 1, NULL, 999),
(9, 'Coke', 29.00, 'Drinks', 1, NULL, 999),
(11, '1 slice mango pie', 60.00, 'Desserts', 1, NULL, 999);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'admin', '2025-12-08 12:36:44'),
(2, 'staff', 'de9bf5643eabf80f4a56fda3bbb84483', 'staff', '2025-12-08 13:05:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
