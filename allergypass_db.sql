-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 05:45 PM
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
-- Database: `allergypass_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `common_allergens`
--

CREATE TABLE `common_allergens` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(10) DEFAULT '⚠️'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `common_allergens`
--

INSERT INTO `common_allergens` (`id`, `name`, `icon`) VALUES
(1, 'Peanuts', '🥜'),
(2, 'Dairy', '🥛'),
(3, 'Shellfish', '🦐'),
(4, 'Gluten', '🍞'),
(5, 'Eggs', '🥚'),
(6, 'Soy', '🌱'),
(7, 'Tree Nuts', '🌰'),
(8, 'Fish', '🐟'),
(10, 'Nightshades', '🥀');

-- --------------------------------------------------------

--
-- Table structure for table `common_keywords`
--

CREATE TABLE `common_keywords` (
  `id` int(11) NOT NULL,
  `common_allergen_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `common_keywords`
--

INSERT INTO `common_keywords` (`id`, `common_allergen_id`, `word`) VALUES
(17, 5, 'Eggs'),
(18, 5, 'Whole Egg'),
(19, 5, 'Egg Whites'),
(20, 5, 'Egg Yolks'),
(21, 5, 'Egg Powder'),
(22, 5, 'Dried Eggs'),
(23, 5, 'Egg Solids'),
(36, 8, 'Salmon'),
(37, 8, 'Tuna'),
(38, 8, 'Cod'),
(39, 8, 'Haddock'),
(40, 8, 'Tiliapia'),
(41, 8, 'Mackerel'),
(42, 8, 'Sardines'),
(43, 8, 'Anchovies'),
(44, 8, 'Trout'),
(45, 8, 'Snapper'),
(46, 8, 'Halibut'),
(47, 8, 'Pollock'),
(48, 8, 'Fish Oil'),
(49, 8, 'Fish Sauce'),
(50, 8, 'Worcestershire Sauce'),
(51, 8, 'Caesar Dressing'),
(66, 1, 'Peanuts'),
(67, 1, 'Peanut Butter'),
(68, 1, 'Peanut flour'),
(69, 1, 'Peanut Oil'),
(70, 1, 'Arachis Oil'),
(71, 7, 'Almonds'),
(72, 7, 'Walnuts'),
(73, 7, 'Pecans'),
(74, 7, 'Cashews'),
(75, 7, 'Pistachios'),
(76, 7, 'Hazelnuts'),
(77, 7, 'Brazil Nuts'),
(78, 7, 'Macadamia Nuts'),
(79, 7, 'Pine Nuts'),
(80, 7, 'Chestnuts'),
(81, 7, 'Hickory Nuts'),
(82, 7, 'Shea Nuts'),
(83, 3, 'Shrimp'),
(84, 3, 'Crab'),
(85, 3, 'Lobster'),
(86, 3, 'Prawns'),
(87, 3, 'Crayfish'),
(88, 3, 'Krill'),
(89, 3, 'Clams'),
(90, 3, 'Mussels'),
(91, 3, 'Oysters'),
(92, 3, 'Scallops'),
(93, 3, 'Squid'),
(94, 3, 'Octopus'),
(95, 3, 'Snails'),
(96, 3, 'Abalone'),
(97, 3, 'Cockles'),
(98, 3, 'Fish Sauce'),
(99, 3, 'Shrimp Paste'),
(100, 3, 'Crab Extract'),
(101, 3, 'Lobster Extract'),
(102, 3, 'Seafood Seasoning'),
(208, 4, 'Wheat'),
(209, 4, 'Barley'),
(210, 4, 'Rye'),
(211, 4, 'Triticale'),
(212, 4, 'Wheat Flour'),
(213, 4, 'Whole Wheat'),
(214, 4, 'Wheat Starch'),
(215, 4, 'Barley Malt'),
(216, 4, 'Rye Flour'),
(217, 4, 'Brewer\'s Yeast'),
(218, 4, 'Modified Food Starch'),
(219, 4, 'Soy Sauce'),
(220, 4, 'Gravies'),
(221, 4, 'Beer'),
(222, 4, 'All-Purpose Flour'),
(223, 4, 'Bread Flour'),
(224, 4, 'Cake Flour'),
(225, 4, 'Graham cracker Crumbs'),
(226, 6, 'Soybeans'),
(227, 6, 'Soy Flour'),
(228, 6, 'Soy Milk'),
(229, 6, 'Soy Protein'),
(230, 6, 'Soy Protein Concentrate'),
(231, 6, 'Soy Sauce'),
(232, 6, 'Tamari'),
(233, 6, 'Miso'),
(234, 6, 'Tofu'),
(235, 6, 'Tempeh'),
(236, 6, 'Edamame'),
(237, 6, 'Dark Chocolate'),
(238, 6, 'Chocolate Batons'),
(239, 2, 'Milk'),
(240, 2, 'Whole Milk Powder'),
(241, 2, 'Skim Milk Powder'),
(242, 2, 'Cream'),
(243, 2, 'Butter'),
(244, 2, 'Ghee'),
(245, 2, 'Cheese'),
(246, 2, 'Yogurt'),
(247, 2, 'Whey'),
(248, 2, 'Casein'),
(249, 2, 'Lactose'),
(250, 2, 'Buttermilk'),
(251, 2, 'Milkfat'),
(252, 2, 'Curds'),
(253, 2, 'Cheddar Cheese'),
(254, 2, 'Cream Cheese'),
(255, 2, 'Heavy Cream'),
(256, 2, 'Heavy Whipping Cream'),
(257, 2, 'Sour Cream'),
(258, 2, 'Whole Milk'),
(259, 2, 'Fresh Milk'),
(260, 2, 'Condensed Milk'),
(261, 2, 'Evaporated Milk'),
(264, 10, 'Belladonna');

-- --------------------------------------------------------

--
-- Table structure for table `custom_allergens`
--

CREATE TABLE `custom_allergens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_allergens`
--

INSERT INTO `custom_allergens` (`id`, `user_id`, `name`, `created_at`) VALUES
(18, 6, 'Coffee', '2025-12-09 16:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `custom_keywords`
--

CREATE TABLE `custom_keywords` (
  `id` int(11) NOT NULL,
  `custom_allergen_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_keywords`
--

INSERT INTO `custom_keywords` (`id`, `custom_allergen_id`, `word`) VALUES
(26, 18, 'Caffene');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `created_at`) VALUES
(6, 'test', '202cb962ac59075b964b07152d234b70', 'John Doe', '2025-12-09 12:14:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_allergies`
--

CREATE TABLE `user_allergies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `allergy_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_allergies`
--

INSERT INTO `user_allergies` (`id`, `user_id`, `allergy_name`) VALUES
(10, 6, 'Dairy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `common_allergens`
--
ALTER TABLE `common_allergens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `common_keywords`
--
ALTER TABLE `common_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `common_allergen_id` (`common_allergen_id`);

--
-- Indexes for table `custom_allergens`
--
ALTER TABLE `custom_allergens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `custom_keywords`
--
ALTER TABLE `custom_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_allergen_id` (`custom_allergen_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_allergies`
--
ALTER TABLE `user_allergies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `common_allergens`
--
ALTER TABLE `common_allergens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `common_keywords`
--
ALTER TABLE `common_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT for table `custom_allergens`
--
ALTER TABLE `custom_allergens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `custom_keywords`
--
ALTER TABLE `custom_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_allergies`
--
ALTER TABLE `user_allergies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `common_keywords`
--
ALTER TABLE `common_keywords`
  ADD CONSTRAINT `common_keywords_ibfk_1` FOREIGN KEY (`common_allergen_id`) REFERENCES `common_allergens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_allergens`
--
ALTER TABLE `custom_allergens`
  ADD CONSTRAINT `custom_allergens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_keywords`
--
ALTER TABLE `custom_keywords`
  ADD CONSTRAINT `custom_keywords_ibfk_1` FOREIGN KEY (`custom_allergen_id`) REFERENCES `custom_allergens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_allergies`
--
ALTER TABLE `user_allergies`
  ADD CONSTRAINT `user_allergies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
