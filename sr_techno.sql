-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 08 أكتوبر 2025 الساعة 04:46
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sr_techno`
--
-- --------------------------------------------------------
--
-- بنية الجدول `cart`
--
CREATE TABLE
  `cart` (
    `id` int (11) NOT NULL,
    `user_id` int (11) NOT NULL,
    `product_id` int (11) NOT NULL,
    `quantity` int (11) NOT NULL DEFAULT 1,
    `added_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- بنية الجدول `products`
--
CREATE TABLE
  `products` (
    `id` int (11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `price` decimal(10, 2) NOT NULL,
    `image` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `category` varchar(50) DEFAULT 'electronics',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- بنية الجدول `users`
--
CREATE TABLE
  `users` (
    `id` int (11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--
INSERT INTO
  `users` (`id`, `name`, `email`, `password`, `created_at`)
VALUES
  (
    2,
    'احمد',
    'Ahmed@gamil',
    '$2y$10$bmyR7iuGBi15MSwkA35Vve/KnVCMur1n70LnwedqCmJZCbEZJBylO',
    '2025-10-06 02:51:18'
  ),
  (
    3,
    'سياف',
    'sayaf@gamil.com',
    '$2y$10$gFFAd2U9Du/Udun9qYnGb.f7XzqR.xBkNGBhB9VndIsXbRkwWeZj6',
    '2025-10-06 03:27:15'
  ),
  (
    4,
    'اسامة',
    'Osame@gamil',
    '$2y$10$LJ.iK7qqtCg9LSlwd15XZuisO2OM2MgG9RLC1.iQXWxTIfPzq3lu2',
    '2025-10-06 03:46:12'
  );

--
-- Indexes for dumped tables
--
--
-- Indexes for table `cart`
--
ALTER TABLE `cart` ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products` ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users` ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

--
-- قيود الجداول المُلقاة.
--
--
-- قيود الجداول `cart`
--
ALTER TABLE `cart` ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;