-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 07:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cadientea_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Milk Tea', 'Creamy and classic milk tea favorites.', '2026-09-05 04:39:02'),
(2, 'Fruit Tea', 'Fresh and fruity tea blends.', '2026-09-05 04:39:02'),
(3, 'Matcha', 'Smooth matcha-based drinks.', '2026-09-05 04:39:02'),
(4, 'Herbal Tea', NULL, '2026-09-10 02:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `sender` enum('customer','admin') DEFAULT 'customer',
  `parent_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `subject`, `message`, `sender`, `parent_id`, `is_read`, `created_at`) VALUES
(1, 3, 'Order Inquiry', 'Do you cater big orders?', 'customer', NULL, 1, '2026-09-10 03:52:43'),
(2, 3, 'Order Inquiry', 'Yes. Do you want to place an order?', 'admin', 1, 0, '2026-09-10 03:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `gcash_reference` varchar(50) DEFAULT NULL,
  `gcash_number` varchar(20) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `order_status` enum('pending','confirmed','preparing','ready','out_for_delivery','completed','cancelled') NOT NULL,
  `order_address` text DEFAULT NULL,
  `order_type` enum('delivery','pickup') DEFAULT 'delivery',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `subtotal`, `delivery_fee`, `total_amount`, `payment_method`, `gcash_reference`, `gcash_number`, `payment_status`, `order_status`, `order_address`, `order_type`, `created_at`) VALUES
(1, 3, 'CT-20260909-A5C664', 204.00, 50.00, 254.00, 'cash', NULL, NULL, 'pending', 'out_for_delivery', 'Dumaguete City', 'delivery', '2026-09-09 02:58:34'),
(2, 3, 'CT-20260909-B11EF7', 204.00, 50.00, 254.00, 'cash', NULL, NULL, 'pending', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-09 02:59:07'),
(3, 3, 'CT-20260909-9EDFF0', 204.00, 50.00, 254.00, 'cash', NULL, NULL, 'pending', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-09 03:04:25'),
(4, 3, 'CT-20260909-20D728', 204.00, 50.00, 254.00, 'cash', NULL, NULL, 'pending', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-09 03:08:34'),
(5, 3, 'CT-20260909-59B944', 218.00, 50.00, 268.00, 'cash', NULL, NULL, 'pending', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-09 04:05:25'),
(6, 3, 'CT-20260909-DCB69F', 95.00, 50.00, 145.00, 'cash', NULL, NULL, 'pending', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-09 08:20:45'),
(7, 5, 'CT-20260910-58B555', 204.00, 0.00, 204.00, 'gcash', NULL, NULL, 'pending', 'confirmed', 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental', 'pickup', '2026-09-10 03:17:25'),
(8, 5, 'CT-20260910-67C852', 230.00, 0.00, 230.00, 'cash', NULL, NULL, 'pending', 'ready', 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental', 'pickup', '2026-09-10 03:37:42'),
(9, 6, 'CT-20260910-0E88DB', 283.00, 50.00, 333.00, 'cash', NULL, NULL, 'pending', 'out_for_delivery', 'Bong-ao, Valencia', 'delivery', '2026-09-10 07:05:20'),
(10, 1, 'CT-20260910-CF1DA5', 89.00, 50.00, 139.00, 'cash', NULL, NULL, 'pending', 'out_for_delivery', 'Dumaguete City', 'delivery', '2026-09-10 07:08:28'),
(11, 3, 'CT-20260915-1E2157', 456.00, 50.00, 506.00, 'gcash', 'GC20260915361E2147', '09655623108', 'paid', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-15 06:19:13'),
(12, 3, 'CT-20260915-BC69C3', 1335.00, 50.00, 1385.00, 'gcash', 'GC20260915B1BC69B9', '09655623108', 'paid', 'confirmed', 'Dumaguete City', 'delivery', '2026-09-15 06:52:11'),
(13, 3, 'CT-20260915-852DBF', 178.00, 0.00, 178.00, 'gcash', 'GC202609159F852DB5', '09655623108', 'paid', 'pending', 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental', 'pickup', '2026-09-15 07:55:36'),
(14, 1, 'CT-20261105-A9BE9A', 109.00, 0.00, 109.00, 'cash', NULL, NULL, 'pending', 'pending', 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental', 'pickup', '2026-11-05 04:15:54'),
(15, 1, 'CT-20261108-108725', 89.00, 0.00, 89.00, 'gcash', 'GC20261108C81085BB', '09930830701', 'paid', 'pending', 'PICKUP - CadienTea Main Branch, Dumaguete City, Negros Oriental', 'pickup', '2026-11-08 14:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `size` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 4, 7, 'Brown Sugar Tiger', 'Large', 1, 109.00, 109.00),
(2, 4, 8, 'Pink Lychee Boba', 'Regular', 1, 95.00, 95.00),
(3, 5, 7, 'Brown Sugar Tiger', 'Large', 2, 109.00, 0.00),
(4, 6, 8, 'Pink Lychee Boba', 'Regular', 1, 95.00, 0.00),
(5, 7, 8, 'Pink Lychee Boba', 'Regular', 1, 95.00, 0.00),
(6, 7, 7, 'Brown Sugar Tiger', 'Large', 1, 109.00, 0.00),
(7, 8, 8, 'Pink Lychee Boba', 'Large', 2, 115.00, 0.00),
(8, 9, 7, 'Brown Sugar Tiger', 'Regular', 1, 89.00, 0.00),
(9, 9, 8, 'Pink Lychee Boba', 'Regular', 1, 95.00, 0.00),
(10, 9, 9, 'Taro Dream Milk Tea', 'Regular', 1, 99.00, 0.00),
(11, 10, 7, 'Brown Sugar Tiger', 'Regular', 1, 89.00, 0.00),
(12, 11, 7, 'Brown Sugar Tiger', 'Large', 2, 109.00, 0.00),
(13, 11, 9, 'Taro Dream Milk Tea', 'Large', 2, 119.00, 0.00),
(14, 12, 7, 'Brown Sugar Tiger', 'Regular', 15, 89.00, 0.00),
(15, 13, 7, 'Brown Sugar Tiger', 'Regular', 2, 89.00, 0.00),
(16, 14, 7, 'Brown Sugar Tiger', 'Large', 1, 109.00, 0.00),
(17, 15, 7, 'Brown Sugar Tiger', 'Regular', 1, 89.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 15,
  `max_per_order` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `tag`, `image`, `stock`, `max_per_order`, `created_at`) VALUES
(7, 1, 'Brown Sugar Tiger', 'Fresh milk swirled with house-made brown sugar syrup and classic pearls.', 'BESTSELLER', 'images/brownsugartiger.jpg', 15, 15, '2026-09-05 04:59:04'),
(8, 1, 'Pink Lychee Boba', 'Lychee milk tea with chewy tapioca pearls and a passionfruit jelly pop.', 'FAN FAVOURITE', 'images/pinklycheeboba.avif', 15, 15, '2026-09-05 04:59:04'),
(9, 1, 'Taro Dream Milk Tea', 'Creamy taro milk tea with purple tapioca pearls and coconut jelly.', 'SEASONAL', 'images/tarodreammilktea.jpg', 15, 15, '2026-09-05 04:59:04'),
(10, 3, 'Matcha Pearl Latte', 'Ceremonial matcha, oat milk foam and soft mochi boba pearls.', 'WELLNESS', 'images/matchapearl.jpg', 15, 15, '2026-09-05 04:59:04'),
(11, 1, 'Strawberry Pop Boba', 'Strawberry green tea with popping boba pearls and whipped cream cloud.', 'NEW', 'images/strawberrypop.webp', 15, 15, '2026-09-05 04:59:04'),
(12, 1, 'Classic Milk Tea', 'Our original black milk tea with perfectly chewy tapioca pearls.', 'CLASSIC', 'images/classicboba.jpg', 15, 15, '2026-09-05 04:59:04'),
(13, 4, 'Gensing Boba Tea', 'Premium adaptogenic ginseng brewed into a smooth tea base, layered with fresh milk and chewy brown sugar pearls.', 'NEW', 'images/product_1789008718_6aa21b4ec9161.jpg', 15, 15, '2026-09-10 02:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size`, `price`) VALUES
(7, 8, 'Regular', 95.00),
(8, 8, 'Large', 115.00),
(9, 9, 'Regular', 99.00),
(10, 9, 'Large', 119.00),
(11, 10, 'Regular', 92.00),
(12, 10, 'Large', 112.00),
(13, 11, 'Regular', 85.00),
(14, 11, 'Large', 105.00),
(15, 12, 'Regular', 79.00),
(16, 12, 'Large', 99.00),
(17, 13, 'Regular', 99.00),
(18, 13, 'Large', 190.00),
(19, 7, 'Regular', 89.00),
(20, 7, 'Large', 109.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Jode', 'An', 'cadientejodean4@gmail.com', '$2y$10$kwLEeOAMuG4n/sQ.47xhvOKL0j9vWEviNKi6ZV1v6ATdOCfWw3dbi', '09930830701', 'Dumaguete City', 'admin', '2026-09-09 03:20:22'),
(3, 'Jode-An', 'Cadiente', 'cadientejodean@gmail.com', '$2y$10$C4PZzIoSmfeYDmgcQNa7OOH0ZDHxRjs01HKFtwlFgK3yemdAkNEPu', '09655623108', 'Dumaguete City', 'customer', '2026-09-09 01:39:46'),
(5, 'Alpha', 'Capa', 'alphacapa@gmail.com', '$2y$10$GdkijEmt3rFzcWt4oqWxxO/eeXlUinxMbGpKHLNqzG3rvp3RMlZ5a', '09525485746', 'Cebu City', 'customer', '2026-09-10 03:13:26'),
(6, 'Kyle', 'Doe', 'kyle@gmail.com', '$2y$10$eWblFWo/2xUrOEnJnjbrSO4ya0gCs0zJFnPXnO815qsLSoF7xyiZ2', '09653256845', 'Bong-ao, Valencia', 'customer', '2026-09-10 07:04:17'),
(7, 'Jode-An', 'Cadiente', 'cadientejodean11@gmail.com', '$2y$10$CiyVWW8hYK9wzKQU2UPKhuyuLpCjN4JX68.cJ9QklXdqjFZ1mfWv6', '', 'Cadawinonan', 'customer', '2026-11-13 03:49:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
