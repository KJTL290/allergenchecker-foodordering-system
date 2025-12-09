-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 01:40 PM
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
(8, 'Fish', '🐟');

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
(3, 2, 'Milk'),
(4, 2, 'Whole Milk Powder'),
(5, 2, 'Skim Milk Powder'),
(6, 2, 'Cream'),
(7, 2, 'Butter'),
(8, 2, 'Ghee'),
(9, 2, 'Cheese'),
(10, 2, 'Yogurt'),
(11, 2, 'Whey'),
(12, 2, 'Casein'),
(13, 2, 'Lactose'),
(14, 2, 'Buttermilk'),
(15, 2, 'Milkfat'),
(16, 2, 'Curds'),
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
(52, 4, 'Wheat'),
(53, 4, 'Barley'),
(54, 4, 'Rye'),
(55, 4, 'Triticale'),
(56, 4, 'Wheat Flour'),
(57, 4, 'Whole Wheat'),
(58, 4, 'Wheat Starch'),
(59, 4, 'Barley Malt'),
(60, 4, 'Rye Flour'),
(61, 4, 'Brewer\'s Yeast'),
(62, 4, 'Modified Food Starch'),
(63, 4, 'Soy Sauce'),
(64, 4, 'Gravies'),
(65, 4, 'Beer'),
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
(103, 6, 'Soybeans'),
(104, 6, 'Soy Flour'),
(105, 6, 'Soy Milk'),
(106, 6, 'Soy Protein'),
(107, 6, 'Soy Protein Concentrate'),
(108, 6, 'Soy Sauce'),
(109, 6, 'Tamari'),
(110, 6, 'Miso'),
(111, 6, 'Tofu'),
(112, 6, 'Tempeh'),
(113, 6, 'Edamame');

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

-- --------------------------------------------------------

--
-- Table structure for table `custom_keywords`
--

CREATE TABLE `custom_keywords` (
  `id` int(11) NOT NULL,
  `custom_allergen_id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(7, 6, 'Shellfish'),
(8, 6, 'Peanuts');

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
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `common_keywords`
--
ALTER TABLE `common_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `custom_allergens`
--
ALTER TABLE `custom_allergens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `custom_keywords`
--
ALTER TABLE `custom_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_allergies`
--
ALTER TABLE `user_allergies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
