-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2025 at 06:54 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_2025A_akua_amofa`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int NOT NULL,
  `brand_cat` int DEFAULT NULL,
  `brand_name` varchar(100) NOT NULL,
  `organizer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_cat`, `brand_name`, `organizer_id`) VALUES
(5, 5, 'DettyDecember', 4),
(6, 6, 'Eat Healthy', 4),
(7, 9, 'Snapchat Pop-Ups', 4),
(8, 8, 'Bank of America(QnA)', 4),
(9, 7, 'Dee\'s Molds', 4);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int UNSIGNED NOT NULL,
  `p_id` int NOT NULL,
  `ip_add` varchar(50) DEFAULT NULL,
  `c_id` int DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `organizer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `organizer_id`) VALUES
(5, 'Music Festivals', 4),
(6, 'Food Parties', 4),
(7, 'Arts and Crafts Events', 4),
(8, 'Talkshows', 4),
(9, 'Market Pop-Ups', 4);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int NOT NULL,
  `super_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`, `super_admin`) VALUES
(1, 'Cece', 'cece@gmail.com', '$2y$10$U8xQtKPugkCQU8ODDDHILuCtFT0lk/MBw3TR7Kt1RNJkdoKV9roUi', 'Ghana', 'Accra', '02345875673', NULL, 2, 0),
(2, 'Akau', 'amofaakua@gmail.com', '$2y$10$UbH/.BIIJCrvnrBM0YCpieEn0DjP3KN5VhsGxSsEBdcGWLMpBgnmu', 'Ghana', 'Accra', '0599533750', NULL, 2, 0),
(3, 'Marge', 'marge@gmail.com', '$2y$10$rwxWXk3EZlvsqgXNf4NMceR3lCeeTp9xNQaUal0QmkFOcfYzH96eS', 'Ghana', 'Accra', '0599533750', NULL, 1, 0),
(4, 'Serwaa', 'serwaa@gmail.com', '$2y$10$0zEhbQ2Tkw1TO1JfjM.HFuq/VJ/ia7cfGcXe3mLcE3I291Urw8Ucm', 'Ghana', 'Accra', '0936287654', NULL, 1, 0),
(5, 'Kiki', 'kiki@gmail.com', '$2y$10$Ke6AIKoMGc1Ysx9zq5imn.dN1ogrmYMeKLxrorXsLlvIE.I.UOyDS', 'Ghana', 'Accra', '0243576382', NULL, 1, 0),
(6, 'Abena Mensah', 'abena@gmail.com', '$2y$10$hjP8NGVnT8ZIuNs35BEMN.usfdmGvMtp1izh3RQUXyy6a.iQ14ot.', 'Ghana', 'Kumasi', '02345875673', NULL, 2, 0),
(7, 'Kofi Amofa', 'kamofa@gmail.com', '$2y$10$ohTjb0gs1rfaNcq8xvhEMemzJxEUO/WQZ8nlCL7sMgAHFauwrSGIG', 'Ghana', 'Kumasi', '0936287654', NULL, 2, 0),
(8, 'Kiki o', 'ama@gmail.com', '$2y$10$JEUuCnbm.HFcZOJwyAFjkOERPOn1/W/dwoLxYA/yODWF3ary/VdwC', 'Ghana', 'Accra', '0768357386', NULL, 1, 0),
(9, 'Asantewaa', 'asantewaa@gmail.com', '$2y$10$DGBLYj7RZC2ehhAKVAj5U.mA7AJhXW1zqnr9DcfEkthloSPhurxwi', 'Ghana', 'Kumasi', '0768357386', NULL, 1, 0),
(10, 'Super Admin', 'akua@gmail.com', 'f5380503027b3bde522e4aab6c3939dc', 'Ghana', 'Accra', '0000000000', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`order_id`, `product_id`, `qty`, `unit_price`) VALUES
(8, 13, 1, 0.00),
(9, 14, 1, 0.00),
(10, 14, 2, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `invoice_no` varchar(32) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `invoice_no`, `order_date`, `order_status`) VALUES
(1, 4, 'INV-5C993EA3', '2025-11-12', 'Paid'),
(2, 4, 'INV-723A38B7', '2025-11-12', 'Paid'),
(3, 4, 'INV-1A9E743A', '2025-11-12', 'Paid'),
(4, 6, 'INV-02166B87', '2025-11-12', 'Paid'),
(5, 6, 'INV-EE6BE9D2', '2025-11-12', 'Paid'),
(6, 6, 'INV-59FA26A3', '2025-11-27', 'Paid'),
(7, 6, 'INV-20251129-3195F9', '2025-11-29', 'Paid'),
(8, 6, 'INV-20251130-D74C59', '2025-11-30', 'Paid'),
(9, 6, 'INV-20251130-F0D212', '2025-11-30', 'Paid'),
(10, 6, 'INV-20251130-F6C4F7', '2025-11-30', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `pay_id` int NOT NULL,
  `amt` double NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'Payment method: paystack, cash, bank_transfer, etc.',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Paystack transaction reference',
  `authorization_code` varchar(100) DEFAULT NULL COMMENT 'Authorization code from payment gateway',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'Payment channel: card, mobile_money, etc.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`pay_id`, `amt`, `customer_id`, `order_id`, `currency`, `payment_date`, `payment_method`, `transaction_ref`, `authorization_code`, `payment_channel`) VALUES
(6, 300, 6, 8, 'GHS', '2025-11-30', 'paystack', 'AYA-6-1764472735', 'AUTH_0in7s56erl', 'mobile_money'),
(7, 50, 6, 9, 'GHS', '2025-11-30', 'paystack', 'AYA-6-1764508171', 'AUTH_u9hps5d8hy', 'mobile_money'),
(8, 100, 6, 10, 'GHS', '2025-11-30', 'paystack', 'AYA-6-1764522283', 'AUTH_co11cdsc5k', 'mobile_money');

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `request_id` int NOT NULL,
  `organizer_id` int NOT NULL,
  `organizer_name` varchar(100) NOT NULL,
  `total_tickets_sold` int DEFAULT '0',
  `gross_revenue` decimal(10,2) DEFAULT '0.00',
  `commission_amount` decimal(10,2) DEFAULT '0.00',
  `net_amount` decimal(10,2) DEFAULT '0.00',
  `payment_method` varchar(50) DEFAULT NULL,
  `account_details` text,
  `request_status` enum('pending','processing','paid','rejected') DEFAULT 'pending',
  `request_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `processed_date` timestamp NULL DEFAULT NULL,
  `admin_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment_requests`
--

INSERT INTO `payment_requests` (`request_id`, `organizer_id`, `organizer_name`, `total_tickets_sold`, `gross_revenue`, `commission_amount`, `net_amount`, `payment_method`, `account_details`, `request_status`, `request_date`, `processed_date`, `admin_notes`) VALUES
(1, 4, 'Serwaa', 1, 50.00, 2.50, 47.50, 'Mobile Money', '0507500530', 'paid', '2025-11-30 16:36:16', '2025-11-30 17:11:03', ''),
(2, 4, 'Serwaa', 3, 150.00, 7.50, 142.50, 'Bank Transfer', '0507500530', 'rejected', '2025-11-30 17:52:46', '2025-11-30 17:53:05', 'Payment already sent'),
(3, 4, 'Serwaa', 3, 150.00, 7.50, 142.50, 'Mobile Money', '0507500530', 'paid', '2025-11-30 17:53:30', '2025-11-30 17:53:41', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `product_cat` int NOT NULL,
  `product_brand` int NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `ticket_quantity` int DEFAULT '0',
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL,
  `product_location` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `organizer_id` int DEFAULT NULL,
  `organizer_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_cat`, `product_brand`, `product_title`, `product_price`, `ticket_quantity`, `product_desc`, `product_image`, `product_keywords`, `product_location`, `event_date`, `event_time`, `organizer_id`, `organizer_name`) VALUES
(13, 5, 5, 'DettyDecember 2025', 300, 100, 'dfknsf', 'prod_692bb74822b240.47556519.jpg', 'detty december, december, concert', 'The Park', '2025-12-19', '16:00:00', 3, 'Marge'),
(14, 9, 7, 'Tiwadeli Jeans', 50, 2, 'Get to shop from a wide range of jeans at the accra good market.', 'prod_692c67b3bce4f5.84356220.jpg', 'accra, jeans, clothes, pop up', 'Accra Goods Market, Spintex', '2025-12-01', '11:00:00', 4, '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `uq_brand_cat_name` (`brand_cat`,`brand_name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `uq_cart_user_product` (`c_id`,`p_id`),
  ADD UNIQUE KEY `uq_cart_ip_product` (`ip_add`,`p_id`),
  ADD KEY `p_id` (`p_id`),
  ADD KEY `c_id` (`c_id`),
  ADD KEY `idx_cart_user` (`c_id`),
  ADD KEY `idx_cart_ip` (`ip_add`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_payment_method` (`payment_method`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `product_cat` (`product_cat`),
  ADD KEY `product_brand` (`product_brand`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `pay_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `fk_brands_categories` FOREIGN KEY (`brand_cat`) REFERENCES `categories` (`cat_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`p_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD CONSTRAINT `payment_requests_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
