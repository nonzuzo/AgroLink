-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2024 at 08:38 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `AgroLinkDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cart_Items`
--

CREATE TABLE `Cart_Items` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Cart_Items`
--

INSERT INTO `Cart_Items` (`cart_item_id`, `user_id`, `product_id`, `quantity`) VALUES
(2, 30, 16, 1),
(3, 30, 19, 1),
(5, 30, 15, 1),
(22, 31, 23, 9),
(23, 31, 21, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`category_id`, `name`, `description`) VALUES
(1, 'Fruits', 'Fresh fruits including apples, bananas, oranges, etc.'),
(2, 'Vegetables', 'Fresh vegetables including carrots, potatoes, lettuce, etc.'),
(3, 'Dairy', 'Dairy products including milk, cheese, yogurt, etc.'),
(4, 'Grains', 'Grains including rice, wheat, corn, etc.');

-- --------------------------------------------------------

--
-- Table structure for table `Notifications`
--

CREATE TABLE `Notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `location` varchar(255) NOT NULL,
  `payment_method` enum('Credit Card','Debit Card') NOT NULL,
  `phone` varchar(15) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `card_expiration` date NOT NULL,
  `card_cvv` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`order_id`, `buyer_id`, `total_amount`, `order_date`, `status`, `location`, `payment_method`, `phone`, `card_number`, `card_expiration`, `card_cvv`) VALUES
(1, 30, 1218.00, '2024-11-29 16:42:29', 'Pending', '', 'Credit Card', '', '', '0000-00-00', ''),
(2, 30, 1218.00, '2024-11-29 17:22:30', 'Pending', 'tb b b h', 'Credit Card', '6789', 'vvyhv', '2024-11-21', '123'),
(3, 30, 1220.00, '2024-11-29 17:28:05', 'Pending', '8990', 'Debit Card', '998', 'ncnc,v', '2024-11-19', '123'),
(4, 33, 198.00, '2024-12-06 15:51:51', 'Pending', 'Ea eaque ratione ali', 'Credit Card', '0556261890', '7890', '2024-12-14', '123'),
(5, 33, 978.00, '2024-12-06 16:44:38', 'Pending', 'f bjg j bkkj', 'Debit Card', '6y7880', '776859430', '2024-12-27', '1223'),
(6, 33, 1765.00, '2024-12-06 20:45:52', 'Pending', 'cvbnm,', 'Debit Card', '789098765432', '34567890', '2024-12-14', '123'),
(7, 33, 1765.00, '2024-12-06 21:04:47', 'Pending', 'cvbnm,', 'Debit Card', '789098765432', '34567890', '2024-12-14', '123'),
(8, 33, 4114.00, '2024-12-07 16:52:21', 'Pending', 'yuiol', 'Credit Card', 'ghjkl;', '7890', '2024-12-06', '7890'),
(9, 39, 3550.00, '2024-12-07 21:38:02', 'Pending', '890-', 'Credit Card', '67890-', '7890', '2024-12-05', '6789'),
(10, 33, 1324.00, '2024-12-08 19:14:55', 'Pending', '7890', 'Debit Card', '67890-', '567890', '2024-11-28', '5678');

-- --------------------------------------------------------

--
-- Table structure for table `Order_Items`
--

CREATE TABLE `Order_Items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Order_Items`
--

INSERT INTO `Order_Items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 16, 1, 374.00),
(2, 2, 19, 1, 844.00),
(3, 3, 15, 1, 2.00),
(4, 3, 16, 1, 374.00),
(5, 3, 19, 1, 844.00),
(6, 4, 21, 1, 56.00),
(7, 4, 23, 1, 55.00),
(8, 4, 20, 1, 87.00),
(9, 5, 21, 1, 56.00),
(10, 5, 23, 1, 55.00),
(11, 5, 20, 1, 87.00),
(12, 5, 24, 2, 5.00),
(13, 5, 26, 2, 385.00),
(14, 6, 21, 1, 56.00),
(15, 6, 23, 1, 55.00),
(16, 6, 20, 1, 87.00),
(17, 6, 24, 4, 5.00),
(18, 6, 26, 3, 385.00),
(19, 6, 27, 1, 392.00),
(20, 7, 21, 1, 56.00),
(21, 7, 23, 1, 55.00),
(22, 7, 20, 1, 87.00),
(23, 7, 24, 4, 5.00),
(24, 7, 26, 3, 385.00),
(25, 7, 27, 1, 392.00),
(26, 8, 21, 1, 56.00),
(27, 8, 23, 1, 55.00),
(28, 8, 20, 28, 87.00),
(29, 8, 24, 4, 5.00),
(30, 8, 26, 3, 385.00),
(31, 8, 27, 1, 392.00),
(32, 9, 20, 1, 87.00),
(33, 9, 24, 1, 5.00),
(34, 9, 23, 4, 55.00),
(35, 9, 27, 3, 392.00),
(36, 9, 16, 1, 374.00),
(37, 9, 19, 2, 844.00),
(38, 10, 19, 1, 844.00),
(39, 10, 20, 5, 88.00),
(40, 10, 24, 8, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `Products`
--

CREATE TABLE `Products` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Products`
--

INSERT INTO `Products` (`product_id`, `user_id`, `name`, `description`, `price`, `quantity`, `category_id`, `location`, `image_url`, `status`, `created_at`) VALUES
(15, 30, 'oranges', 'jnjlninlkl', 2.00, 1, 1, 'accra', 'strawberries.jpg', 'approved', '2024-11-23 15:17:06'),
(16, 30, 'appley', 'Incididunt quos nesc', 374.00, 832, 1, 'Quaerat recusandae', '6742414d44838.png', 'approved', '2024-11-23 15:25:27'),
(19, 30, 'Aretha.  yhnmkolj', 'Et consectetur nemo', 844.00, 90, 4, 'hvjkhmfdmjfhju', '67424132b7ade.png', 'approved', '2024-11-23 16:09:06'),
(20, 31, 'strawbwerries', 'bjv dfk', 88.00, 9, 1, 'berekuso', '../uploads/6754e3d2b7c3f.jpg', 'rejected', '2024-11-29 17:38:21'),
(21, 31, 'apples', 'gb bkf', 56.00, 5, 2, '4n4 gfk', 'berry-smoothies.jpg', 'pending', '2024-11-29 17:39:07'),
(23, 31, 'salad', 'jbfnkfgnb', 55.00, 55, 3, 'm bk', '6749fd6cc77cd.jpeg', 'pending', '2024-11-29 17:40:10'),
(24, 34, 'lemonnnnnn', 'Tempor et quae Nam e', 5.00, 8, 1, 'accra', 'lemon.jpg', 'pending', '2024-12-06 16:07:21'),
(26, 34, 'cabbage', 'Quis illum eum corr', 385.00, 126, 2, 'kumasi', '../uploads/67536b04b13c7.jpg', 'pending', '2024-12-06 16:09:00'),
(27, 33, 'Colin Sharpe', 'Earum non et magna c', 392.00, 74, 1, 'Quidem nesciunt sin', 'blueberry.jpg', 'pending', '2024-12-06 16:55:01');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('farmer','buyer','admin') NOT NULL,
  `status` enum('registered','approved','suspended') DEFAULT 'registered',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `email`, `password`, `user_type`, `status`, `created_at`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'Aladdin Barr', 'deconajybe@mailinator.com', '$2y$10$Mu25IFUveFtFs0HWXeGIi.GXjwyES.4uAPbxDrKzyCKYZqzx1.mHS', 'buyer', 'registered', '2024-11-08 21:16:34', NULL, NULL),
(2, 'Paloma Dillon', 'xyfu@mailinator.com', '$2y$10$rWnWT/NFlAqMwYPMyEm9FO/tt39Iu44ot.xPMva2fBU6EjIqgd.1K', 'farmer', 'approved', '2024-11-08 21:20:30', NULL, NULL),
(3, 'Shay Cruz', 'kyhohuq@mailinator.com', '$2y$10$WaTQgX64sNzfIq9b1RzVrO/L5F7kC2mIvCjJyweTlS9WztIxZJW92', 'farmer', 'approved', '2024-11-08 21:30:01', NULL, NULL),
(4, 'Amela Clarke', 'qamedylo@mailinator.com', '$2y$10$7eM.JVE/HZcGeM96c/KERO3de7F3.eylUH1PlPIJYH3EdgejzLVwK', 'farmer', 'registered', '2024-11-08 21:37:23', NULL, NULL),
(5, 'dklglvkdm', 'sikhosab@gmail.com', '$2y$10$sPBtXxcxlUwx7GG/US6vQ.1mQjeBiIkBdlkKoc3AyZRnIUYWxikdm', 'farmer', 'suspended', '2024-11-08 21:44:55', NULL, NULL),
(6, 'FDGHJ', 'DFGH@HHFJDJ.COKM', '$2y$10$joDSp23qJqBwb/pO5fatJOt.dANW/oStCcxaZuQtCMOh6YJTFFkM.', 'farmer', 'suspended', '2024-11-08 21:47:59', NULL, NULL),
(7, 'Roth Melendez', 'juhuhosu@mailinator.com', '$2y$10$ZzyaiTxyNFs5Ph03N6AYWe3LHJwhIJKMMoIMKE3nFMv0H9kAgTK/i', 'farmer', 'registered', '2024-11-08 21:57:34', NULL, NULL),
(8, 'Kyla Odonnell', 'ritam@mailinator.com', '$2y$10$Yo4KRgOGXHyN1EvpPPXl6uf.1m9REFiF3fRH0CYgABrMlMLA0of9a', 'buyer', 'registered', '2024-11-08 21:59:35', NULL, NULL),
(9, 'Eric Bright', 'vebexuhis@mailinator.com', '$2y$10$d5Tj8C1qyEr0uNtzxjTDc.q2FtS4YGJCV8FwGFlFqvoe1yFwuaHFG', 'farmer', 'registered', '2024-11-12 19:14:49', NULL, NULL),
(10, 'Mcebo', 'mcebo@gmail.com', '$2y$10$RpTp1z8QS9BzG/ZeGPo3cunJmw96.hoOUMAbRXtiQpNDzP2yDwGxO', 'farmer', 'registered', '2024-11-12 19:15:48', NULL, NULL),
(11, 'Allegra Lee', 'menyziv@mailinator.com', '$2y$10$JQFBmfbFVTMm0GEv8p3RqeaHtWADM5TsJixieRLWhC5P0yceL6TaS', 'buyer', 'registered', '2024-11-12 19:20:36', NULL, NULL),
(12, 'bjvb ldn', 'ckdshcv@m.com', '$2y$10$JBhvS5OvmTFp3BsXIAjtu.RWv3jrc0HKwxDDle7r9t.QUDLPjep0y', 'buyer', 'registered', '2024-11-12 19:32:54', NULL, NULL),
(13, 'Aladdin Barr', '612@gmail.com', '$2y$10$KmNVh.l.L9pBkUBFFDAm5OQECuO5NFhGldf6e3Bc4w7VZ83gGZH4a', 'farmer', 'registered', '2024-11-12 19:33:31', NULL, NULL),
(14, 'Jocelyn Swanson', 'sypivaf@mailinator.com', '$2y$10$IkiCYeS5Nq5N8X2NGTLcH.klq1/MJCfhOglgEMthh/HaMB8rxcOGe', 'farmer', 'registered', '2024-11-12 20:38:41', NULL, NULL),
(15, 'Rae Forbes', 'fifikefulu@mailinator.com', '$2y$10$78XltPBfL.KZv0RJDUOCve3HIq4o0g5uwSrgdT2mryZZod8zzWz4i', 'buyer', 'registered', '2024-11-12 21:26:02', NULL, NULL),
(16, 'Flynn Fischer', 'gavapiz@mailinator.com', '$2y$10$93b0GqtgxL7f4A6OlEBAvOuzISAQYvtjjuoRY0RhridNhZHLMwaeO', 'farmer', 'suspended', '2024-11-12 21:27:45', NULL, NULL),
(17, 'Kuame Pollard', 'peqoqi@mailinator.com', '$2y$10$Wpqdi8eFDcK7G3uCLNdiGOVRVOCXg4ABkwclZUyqpru4Ri2Iwb6lq', 'farmer', 'registered', '2024-11-13 15:11:51', NULL, NULL),
(18, 'Talon Witt', 'fyraqyrube@mailinator.com', '$2y$10$lnYjy6mLrILgwN08rloVBebwW4iVj6I5gec6jYso/IGnwclzfoLG.', 'farmer', 'registered', '2024-11-20 15:23:18', NULL, NULL),
(19, 'Timothy Kent', 'suziwibowe@mailinator.com', '$2y$10$gEcYUwqCtHBMwqpu6Yll..KJtPZZIDFW9vtHh0vBE4tMKsaf1G6J6', 'buyer', 'registered', '2024-11-20 15:25:41', NULL, NULL),
(20, 'bgjfjrjfb Kent', 'suziwiboe@mailinator.com', '$2y$10$Od0iNHlJDtW3roiqY0u0XePFCgzaUkFTZ6/8jv2oLVchYxt7yYFRi', 'farmer', 'registered', '2024-11-20 15:58:26', NULL, NULL),
(21, 'Veda Schroeder', 'fopu@mailinator.com', '$2y$10$FahOudK.8Jx54WkhL74Ys.3oizywRIh8hiIxOJzkVm9LXy9RU2WSu', 'buyer', 'registered', '2024-11-20 17:48:31', NULL, NULL),
(22, 'Adrienne Burt', 'fyfetib@mailinator.com', '$2y$10$oY42W99/PrkJsUi7rpxXGuKLJGjrRjnrNKwv3GoYNNV2GvkGsA0Na', 'buyer', 'registered', '2024-11-20 20:09:45', NULL, NULL),
(23, 'Rudyard Mcfarland', 'rocelyqef@mailinator.com', '$2y$10$/cCSd/gUd.m4OB03neStlevY4V5FzEOH5apo7LKQK0/fJya8yeIfm', 'buyer', 'registered', '2024-11-20 20:18:21', NULL, NULL),
(24, 'Rudyard Mcfarland', 'rocel@mailinator.com', '$2y$10$Py3yqmFU8qCjzepiwJb4A.42L30K3tC/yeNWMEcqwA75SiTt3m3nu', 'buyer', 'registered', '2024-11-20 20:42:50', NULL, NULL),
(25, 'Aline Reeves', 'ra@mailinator.com', '$2y$10$RvDaKHK.836yeM/gjCpaAuwg.q/7neD73JGLgkFIW4zL74dNsAyZ.', 'farmer', 'registered', '2024-11-21 14:21:41', NULL, NULL),
(26, 'Cathleen Hood', 'qqa@mailinator.com', '$2y$10$.Ax.2u5kVlQIdDXJNoUXKuFm9trLumilZTdHnmE2xzw4Bu4UvhEo.', 'buyer', 'registered', '2024-11-21 14:22:38', NULL, NULL),
(27, 'Y Baird', 'gv@mailinator.com', '$2y$10$wh1mkh2SxLuIsUyoleMkmuhgp.sUyP4JfkYEmSjjy2GLatAAzcQK2', 'buyer', 'suspended', '2024-11-21 14:24:28', NULL, NULL),
(28, 'Gage Perkins', 'jeow@mailinator.com', '$2y$10$QQWE/420qMEJbEsA5CIgFOnQy/9kLOXRN9jlQpqbC6AJSD.56f3mW', 'buyer', 'registered', '2024-11-21 16:59:50', '4b7ed275e83408ef61f2ab1e6fdef8c0c8f0041e540da656d1fb2b8130161443', '2024-12-08 02:04:26'),
(29, 'Vincent', 'vonutamojy@mailinator.com', '$2y$10$7yDb3RtbOOdltVo83HF.DuvT2rdmWvH1z83NS7uUh4PA0D/drQwSi', 'buyer', 'registered', '2024-11-22 20:12:12', NULL, NULL),
(30, 'Richmond', 'makenuq@mailinator.com', '$2y$10$ZQqPWER1Axo8C78aYwx.SeSChKIPQvn2O3dnbrI1crXnkSiKR/mP2', 'farmer', 'registered', '2024-11-22 20:15:21', NULL, NULL),
(31, 'Maryam  Hlanze', 'sikhosananonzuzo2@gmail.com', '$2y$10$edzqWujTofnDyiwOz5W7ae9r4qW1GWnZkxOzmP8ycDUSvqI1qhITa', 'farmer', 'approved', '2024-11-29 17:36:52', NULL, NULL),
(32, 'Dorothy Sikhosana', 'sikhosananonzuzo1@gmail.com', '$2y$10$3yu7qClcSTrlLZXfI5HYZ.ZjnqXsHwIKzLZICjhfBE5lP700m8tq2', 'buyer', 'registered', '2024-11-29 17:46:35', NULL, NULL),
(33, 'Stone Hlanze', 'qigohefej@mailinator.com', '$2y$10$h157LX9vH2m2L9/U77SOCua.Ep3DuXK3dlqfa8bH7d9bSzPit/ga.', 'buyer', 'registered', '2024-12-06 15:49:01', NULL, NULL),
(34, 'Racinator Mcebo', 'rac@mailinator.com', '$2y$10$H3FatGUofzYRmZblbli.bOOlUzVUfZzTttpmjHcqgFTITyQ9YuKIi', 'farmer', 'registered', '2024-12-06 16:01:05', NULL, NULL),
(35, 'Clio Brewer', 'fuvan@mailinator.com', '$2y$10$YdxfPZaF6o93/7dKw2O85.5/bObSXM/LaahlBgE1KjzRclk/tWNQa', 'farmer', 'registered', '2024-12-06 23:35:24', NULL, NULL),
(36, 'Quentin Carpenter', 'xakase@mailinator.com', '$2y$10$bTW86YmnqwjzOOb6eZNkfe.FFD0pU/ntveLpHefGT5e5r0M1uLUSe', 'buyer', 'registered', '2024-12-06 23:36:06', NULL, NULL),
(37, 'Wylie Miller', 'nonzuzo.sikha@ashesi.edu.gh', '$2y$10$gAU8AS5EXfDm/HP0xdFjy.ULeeWNr7Kovo1RixO5nwxxDKFBBfHyi', 'buyer', 'registered', '2024-12-06 23:37:53', '40655cd9baf698ec83a60de614e6bcc8442b7e4166a40ee3ae940d0d39f60408', '2024-12-08 02:04:24'),
(39, 'Nonzuzo Sylvia Sikhosana', 'sikhosananonzuzo612@gmail.com', '$2y$09$8Vd7lWxTcUQvDTJqTiYYSuon5I3VUtI06/zSYtHzddaFw6N8mPr8u', 'buyer', 'registered', '2024-12-07 20:11:25', '014565f6a68e7a51f2bc9859278281c7f1173e72f5e5a898ed058d8bd138c150', '2024-12-08 02:04:25'),
(41, 'Sylvia', 'nonzuzo.sikhosana@ashesi.edu.gh', 'Pass@1234', 'admin', 'registered', '2024-12-08 19:22:04', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `Order_Items`
--
ALTER TABLE `Order_Items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `Categories`
--
ALTER TABLE `Categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Notifications`
--
ALTER TABLE `Notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Orders`
--
ALTER TABLE `Orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Order_Items`
--
ALTER TABLE `Order_Items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `Products`
--
ALTER TABLE `Products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Cart_Items`
--
ALTER TABLE `Cart_Items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Order_Items`
--
ALTER TABLE `Order_Items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `Products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `Products`
--
ALTER TABLE `Products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `Categories` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
