-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 05:54 PM
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
-- Database: `online_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(8, 17, 5, 2, '2025-10-10 15:35:28'),
(9, 17, 4, 1, '2025-10-10 15:35:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'อิเล็กทรอนิกส์'),
(2, 'เครื่องเขียน'),
(3, 'เสื้อผ้า');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `order_date`, `status`) VALUES
(1, 2, 834.00, '2025-08-13 11:27:38', 'processing'),
(2, 17, 35.00, '2025-10-10 14:08:35', 'pending'),
(3, 13, 25.00, '2025-10-10 15:22:16', 'pending'),
(4, 13, 70.00, '2025-10-10 15:51:20', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(5, 3, 5, 1, 25.00),
(6, 4, 11, 1, 20.00),
(7, 4, 10, 1, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock`, `category_id`, `created_at`, `image`) VALUES
(4, 'ดินสอไม้', 'เขียนแล้วหล่อ', 20.00, 100, 2, '2025-10-10 15:11:32', 'product_1760109092.jpg'),
(5, 'ดินสอกด', 'ใช้แล้วเขียนสวยแน่นอน', 25.00, 100, 2, '2025-10-10 15:13:42', 'product_1760109222.jpg'),
(6, 'กล่องไส้ดินสอ', 'ไส้ไม่หักแข็งระดับไม้', 50.00, 100, 2, '2025-10-10 15:40:27', 'product_1760110827.jpg'),
(7, 'น้ำยาลบคำผิด', 'ใช้แล้วกระดาษสะอาดแน่นอน', 30.00, 80, 2, '2025-10-10 15:43:47', 'product_1760111027.jpg'),
(8, 'ยางลบ', 'ใช้แล้วลบได้ทุกอย่าง', 20.00, 100, 2, '2025-10-10 15:45:14', 'product_1760111114.jpg'),
(9, 'ไม้บรรทัด', 'ใช้แล้วหล่อเท่สาวมองเต็ม', 20.00, 100, 2, '2025-10-10 15:46:23', 'product_1760111183.jpg'),
(10, 'วงเวียนทุกแบบ', 'ใช้แล้วอาจารย์ชมแน่นอน', 50.00, 100, 2, '2025-10-10 15:46:52', 'product_1760111212.jpg'),
(11, 'กบเหลาดินสอ', 'เหลาดินสอแล้วดินสอไม่หัก', 20.00, 100, 2, '2025-10-10 15:47:38', 'product_1760111258.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `shipping_status` enum('not_shipped','shipped','delivered') DEFAULT 'not_shipped'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `address`, `city`, `postal_code`, `phone`, `shipping_status`) VALUES
(1, 1, '123 ถนนหลัก เขตเมือง', 'กรุงเทพมหานคร', '10100', '0812345678', 'shipped'),
(2, 2, '70/2', 'นครปฐม', '73130', '0912836822', 'not_shipped'),
(3, 3, '70/2', 'นครปฐม', '73130', '0912836822', 'not_shipped'),
(4, 4, '70/2', 'นครปฐม', '73130', '0912836822', 'not_shipped');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin1', 'admin_pass', 'admin1@example.com', 'Admin One', 'admin', '2025-08-13 11:27:37'),
(2, 'member1', 'member_pass', 'member1@example.com', 'John Doe', 'member', '2025-08-13 11:27:37'),
(3, 'member2', 'member_pass', 'member2@example.com', 'Jane Smith', 'member', '2025-08-13 11:27:37'),
(10, 'pongpipat1', '$2y$10$K5Gfy2u/I.uAoKEpk6dJMuvQfdf5s/2K18Mp78oGuaZ1KLs/IGyAO', 'pongpipat1@gmail.com', 'แอดมิน', 'admin', '2025-09-10 02:48:46'),
(11, 'pongpipat2', '$2y$10$mpG1Cw91tArzXZVPZoqoM.NzmaWAgsamRqAUkpIdHqnXul6mTNq8q', 'pongpipat2@gmail.com', 'พงษ์2', 'member', '2025-09-10 02:56:05'),
(12, 'พงบบบ', '$2y$10$Eu0PUQgKhIPqU1sESKgXxOncMOpIsOWUvzMIPt2L5rHJaa9VIrjEi', 'qw11@gmail.com', 'พบบบ', 'admin', '2025-09-17 13:31:53'),
(13, 'qw1', '$2y$10$1WhawVeJmJYuycSwEaVGIumqGvYxuPG4PdFsAnb9ec7DLmIHkxmQi', 'ponh1@gmail.com', 'พงเพรช3', 'admin', '2025-09-24 13:33:36'),
(14, 'qw2', '$2y$10$tuf2MqqYCtdRL6/FIqz3Ae1o0le9RhTYtWwmtAI2HbCIoY6/r1Xbi', 'qw2@gmao.com', 'เพรน', '', '2025-09-24 13:37:26'),
(15, 'user1', '$2y$10$Ngk9piSKUNWEjxKYPPDzIetptVq2IE33eS0J70iI.APT2ZwYgyPqe', 'pon@gmail.com', 'พง5', '', '2025-09-24 15:25:36'),
(16, 'admin99', '$2y$10$6WuuQW5FDnOPlG3XUdoB.O8M9qDYajcv7wzf3DbNy.TbeTCUHwHuG', 'po1@gmail.com', 'พงเพร', 'admin', '2025-09-24 15:33:23'),
(17, 'user99', '$2y$10$7vDRmGwCWkovHHlTTbh4YuAnY/YcSGduBIlh5G1SNrz4WNYjHOVAu', 'qw155@gmail.com', 'เพส', '', '2025-09-24 15:39:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
