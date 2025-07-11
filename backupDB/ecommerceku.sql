-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 03:09 PM
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
-- Database: `ecommerceku`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_category`, `name`) VALUES
(5, 'Chair'),
(3, 'Headset'),
(7, 'IEM'),
(2, 'Keyboard'),
(6, 'Microphone'),
(1, 'Mouse'),
(4, 'Speaker');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_from_admin` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `id_user`, `message`, `is_from_admin`, `created_at`) VALUES
(1, 1, 'testing', 0, '2025-07-10 20:55:30'),
(2, 1, 'halo terimakasih sudah menghubungi tempat belanja kami', 1, '2025-07-10 20:58:42'),
(3, 3, 'halo min', 0, '2025-07-10 21:07:18'),
(4, 3, 'halo juga terimakasih sudah menghubungi kami !!', 1, '2025-07-10 21:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id_news` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `published_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id_news`, `title`, `content`, `image`, `published_at`, `updated_at`) VALUES
(2, 'PRX WON AGAINTS SENTINELS', 'PaperRex berhasil mengambing ngambingi Sentinels malam tadi pada Valorant Championship Tournament Master Toronto', 'PRX-SEN-semi-VALORANT-Masters-Toronto-2025-968x544.jpg', '2025-06-18 10:41:15', '2025-06-18 05:41:15'),
(3, 'TEST 2', 'TEST 2', NULL, '2025-06-18 09:51:15', '2025-06-18 09:51:15'),
(4, 'TEST 3', 'TEST 3', NULL, '2025-06-18 09:51:24', '2025-06-18 09:51:24'),
(5, 'TEST 4', 'TEST 4', NULL, '2025-06-18 09:51:32', '2025-06-18 09:51:32'),
(6, 'TEST 5', 'TEST 5', NULL, '2025-06-18 09:53:49', '2025-06-18 09:53:49'),
(7, 'TEST 6', 'TEST 6', NULL, '2025-06-18 09:53:55', '2025-06-18 09:53:55'),
(8, 'TEST 7', 'TEST 7', NULL, '2025-06-18 09:54:03', '2025-06-18 09:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('pending','paid','confirmed','shipped','accepted','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `id_user`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 600000.00, 'shipped', '2025-06-12 13:05:01'),
(2, 1, 700000.00, 'completed', '2025-06-12 13:31:17'),
(5, 1, 2000000.00, 'paid', '2025-06-13 12:02:00'),
(6, 3, 650000.00, 'paid', '2025-06-13 12:02:37'),
(7, 1, 250000.00, 'cancelled', '2025-07-10 13:29:18'),
(8, 1, 250000.00, 'cancelled', '2025-07-10 13:43:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id_order_detail` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id_order_detail`, `id_order`, `id_product`, `quantity`, `price`) VALUES
(2, 2, 2, 1, 700000.00),
(4, 6, 3, 1, 650000.00),
(11, 5, 7, 1, 1750000.00),
(12, 5, 5, 1, 250000.00),
(13, 7, 4, 1, 250000.00),
(14, 8, 5, 1, 250000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payment` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','success','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payment`, `id_order`, `payment_method`, `amount`, `payment_date`, `status`) VALUES
(1, 1, 'E-Wallet (OVO/DANA/Gopay)', 600000.00, '2025-06-12 13:30:53', 'success'),
(2, 2, 'Transfer Bank', 700000.00, '2025-06-12 13:31:21', 'success'),
(3, 6, 'COD', 650000.00, '2025-06-13 12:02:48', 'success'),
(4, 5, 'E-Wallet (OVO/DANA/Gopay)', 2000000.00, '2025-06-18 03:49:15', 'success'),
(5, 7, 'E-Wallet (OVO/DANA/Gopay)', 250000.00, '2025-07-10 13:40:32', 'success'),
(6, 8, 'E-Wallet (OVO/DANA/Gopay)', 250000.00, '2025-07-10 13:44:21', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id_product`, `name`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(2, 'Headset Hyper X', 'Headsetnya anak gamers', 700000.00, 4, 'prod_684ad0233cb47.png', '2025-06-12 13:03:31'),
(3, 'Rexux Gaming Chair', 'Kursinya para gamers', 650000.00, 0, 'prod_684ad03ac93c8.png', '2025-06-12 13:03:54'),
(4, 'Keyboard Mechanic', 'Keyboard cetak cetuk', 250000.00, 2, 'prod_684ad054600a3.webp', '2025-06-12 13:04:20'),
(5, 'Rexus Mouse', 'Mouse Keren', 250000.00, 3, 'prod_684ad8a15966d.png', '2025-06-12 13:39:45'),
(6, 'Logitech G203 Lightsync', 'Mouse Murah enak', 400000.00, 0, 'prod_684ad8c698dee.jpg', '2025-06-12 13:40:22'),
(7, 'Playstation Gaming Chair', 'Kursinya anak Console', 1750000.00, 4, 'prod_6852344793a61.webp', '2025-06-18 03:36:39'),
(8, 'Office Chair', 'Nyaman dan cocok untuk dipakai sehari hari', 750000.00, 5, 'prod_685234a12983f.jpeg', '2025-06-18 03:38:09'),
(9, 'Rexus Gaming Headset', 'Headset Rexus nih boss', 600000.00, 5, 'prod_6852351567f88.jpeg', '2025-06-18 03:40:05'),
(10, 'Headset Warnet', 'Kamu anak warnet? bugdet pas pas an? ini cocok buat kamu', 200000.00, 10, 'prod_6852353252702.jpg', '2025-06-18 03:40:34');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `id_product`, `id_category`) VALUES
(2, 2, 3),
(4, 4, 2),
(10, 5, 1),
(12, 6, 1),
(15, 3, 5),
(16, 7, 5),
(17, 8, 5),
(18, 9, 3),
(19, 10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id_review`, `id_product`, `id_user`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'Bagus banget produknya', '2025-06-12 13:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'melsencb', 'bagaskaramelsen@gmail.com', '$2y$10$fUyTgIImw9w11XfmSH1eGetIZX9cHwEUxK2rkU4j6y5xJnV61Wh8i', 'Melsen Candika Bagaskara', '0895395251660', 'JL Raya Sukabumi No 35 Cianjur (Cikaret)', 'customer', '2025-06-12 12:58:07'),
(2, 'admin', 'tricity@gmail.com', '$2y$10$U9PQTvvx2C1Ppp8ORJy7j.PCpIn42OXH1CwkY4Kmtf5N2A9NXgMEK', 'Tricity Corporation', '325351', 'PT. Tricity Cianjur', 'admin', '2025-06-12 12:58:26'),
(3, 'gundismis', 'gundismis25@gmail.com', '$2y$10$8PNULKTsSE3iI7BItTbKPekcP2xx6ob7rFusgLKHhLI39hzIrg88S', 'Sigun', '0895395251661', 'Cikaret no 35 Cianjur', 'customer', '2025-06-13 12:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id_news`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `id_order` (`id_order`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_category` (`id_category`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_product` (`id_product`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id_news` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id_order_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `products` (`id_product`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
