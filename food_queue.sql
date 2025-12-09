-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 04:37 PM
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
(6, 'Pastries & Bread', '🥐', 2),
(7, 'Coffee', '☕', 0),
(8, 'Tea & Milk', '🍵', 1),
(9, 'Cakes & Sweets', '🍰', 3);

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
  `sort_order` int(11) DEFAULT 999,
  `ingredients` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `category`, `is_available`, `image`, `sort_order`, `ingredients`) VALUES
(1, '1pc Chicken w/ Rice', 99.00, 'Chicken', 1, NULL, 2, NULL),
(3, 'Bucket of 6', 450.00, 'Chicken', 1, NULL, 1, NULL),
(4, 'Spicy Chicken', 95.00, 'Chicken', 1, NULL, 0, NULL),
(5, 'Regular Burger', 45.00, 'Burgers', 1, NULL, 0, NULL),
(6, 'Cheeseburger', 40.00, 'Burgers', 1, NULL, 1, NULL),
(7, 'Double Patty', 95.00, 'Burgers', 1, NULL, 2, NULL),
(8, 'Spaghetti', 59.00, 'Noodles', 1, NULL, 999, NULL),
(9, 'Coke', 29.00, 'Drinks', 1, NULL, 999, NULL),
(11, 'Classic Butter Croissant', 75.00, 'Pastries & Bread', 1, '1765291481_Croissant.jpg', 999, 'Bread Flour, Unsalted Butter, Whole Milk, Water, Sugar, Salt, Instant Yeast, Egg'),
(12, 'Café Americano', 85.00, 'Coffee', 1, '1765290656_Cafe Americano.jpg', 999, 'Espresso Beans, Water'),
(13, 'Classic Cappuccino', 110.00, 'Coffee', 1, '1765290661_Classic Cappuccino.jpg', 999, 'Espresso Beans, Milk, Cocoa powder'),
(14, 'Café Latte', 120.00, 'Coffee', 1, '1765290666_Cafe Latte.jpg', 999, 'Espresso Beans, Milk'),
(15, 'Dulcé Spanish Latte', 135.00, 'Coffee', 1, '1765290759_Spanish latte.jpg', 999, 'Espresso Beans, Milk, Sweetened Condensed Milk'),
(16, 'Caramel Macchiato', 145.00, 'Coffee', 1, '1765290715_Cafe Macchiato.jpg', 999, 'Espresso Beans, Milk, Vanilla Syrup, Caramel Sauce'),
(17, 'Signature Hot Chocolate', 110.00, 'Tea & Milk', 1, '1765290912_Hot Chocolate.jpg', 999, 'Cacao Tablea, Milk, Sugar, Whipped Cream.'),
(18, 'Matcha Green Tea Latte', 130.00, 'Tea & Milk', 1, '1765290981_matcha green tea latte.jpg', 999, 'Matcha Powder, Milk, Sugar Syrup'),
(19, 'Chamomile Tea', 80.00, 'Tea & Milk', 1, '1765291137_Chamomile Tea.jpg', 999, 'Tea Leaves , Hot Water'),
(20, 'Iced Hibiscus Berry', 100.00, 'Tea & Milk', 1, '1765291301_Iced_Hibiscus_Tea.jpg', 999, 'Hibiscus Tea Leaves, Water, Ice, Sugar Syrup, Dried Berries'),
(21, 'Pain au Chocolat', 85.00, 'Pastries & Bread', 1, '1765291659_Pain au Chocolat.jpg', 999, 'Bread Flour, Unsalted Butter, Whole Milk, Water, Sugar, Salt, Instant Yeast, Dark Chocolate Batons, Egg'),
(22, 'Cream Cheese Danish', 80.00, 'Pastries & Bread', 1, '1765291903_Cream-Cheese-Danish.jpg', 999, 'Flour, Butter, Sugar, Milk, Yeast, Salt, Cream cheese, Egg Yolk, Vanilla Extract'),
(23, 'Special Ensaymada', 70.00, 'Pastries & Bread', 1, '1765291974_Special Ensaymada.jpg', 999, 'All-Purpose Flour, Butter, Sugar, Eggs, Milk, Yeast, Cheddar Cheese'),
(24, 'Garlic Cream Cheese Bun', 90.00, 'Pastries & Bread', 1, '1765292020_Garlic Cream Cheese Bun.jpg', 999, 'Bread Flour, Milk, Sugar, Yeast, Salt, Butter, Eggs, Garlic, Cream Cheese, Parsley'),
(25, 'Tres Leches Slice', 140.00, 'Cakes & Sweets', 1, '1765292094_Tres Leches Slice.jpg', 999, 'All-Purpose Flour, Sugar, Eggs, Baking Powder, Whole Milk, Condensed Milk, Evaporated Milk, Heavy Whipping Cream, Vanilla Extract.'),
(26, 'Basque Burnt Cheesecake', 150.00, 'Cakes & Sweets', 1, '1765292140_Basque Burnt Cheesecake.jpg', 999, 'Cream Cheese, White Sugar, Eggs, Heavy Cream, Cake Flour, Vanilla Extract'),
(27, 'Dark Chocolate Moist Cake', 130.00, 'Cakes & Sweets', 1, '1765292223_Dark chocolate moist cake.jpg', 999, 'All-Purpose Flour, Cocoa Powder, Sugar, Baking Soda, Baking Powder, Eggs, Buttermilk, Vegetable Oil, Vanilla Extract, Dark Chocolate, Heavy Cream'),
(28, 'Dulcé De Leche Cheesecake', 155.00, 'Cakes & Sweets', 1, '1765292292_Dulcé De Leche Cheesecake.jpg', 999, 'Cream Cheese, Sugar, Eggs, Sour Cream, Graham Cracker Crumbs, Melted Butter, Dulce de leche (caramel)');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
