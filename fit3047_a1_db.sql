-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 01:48 PM
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
-- Database: `cake_project_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
                          `id` int(11) NOT NULL,
                          `first_name` varchar(255) NOT NULL,
                          `last_name` varchar(255) NOT NULL,
                          `email` varchar(255) NOT NULL,
                          `password` varchar(255) NOT NULL,
                          `nonce` varchar(255) DEFAULT NULL,
                          `nonce_expiry` datetime DEFAULT NULL,
                          `created` datetime DEFAULT current_timestamp(),
                          `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                          `type` varchar(50) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`) VALUES
    (4, 'Nemobyte', 'team071', 'team071@gmail.com', '$2y$10$2Nx/9hEpu4xqwbp7euCiWeO2YhJ5bU8cFveL2yg2UX5FrXOWIq.zO', NULL, NULL, '2025-03-24 02:43:16', '2025-03-24 02:43:16', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
                            `id` int(11) NOT NULL,
                            `booking_name` varchar(255) DEFAULT NULL,
                            `booking_date` date NOT NULL,
                            `total_cost` decimal(10,2) NOT NULL,
                            `remaining_cost` decimal(10,2) NOT NULL,
                            `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_name`, `booking_date`, `total_cost`, `remaining_cost`, `customer_id`) VALUES
                                                                                                                 (7, 'Test Booking', '2025-04-03', 10.00, 10.00, 6),
                                                                                                                 (8, 'Double Test', '2025-04-06', 10.00, 10.00, 6);

-- --------------------------------------------------------

--
-- Table structure for table `bookings_stylists`
--

CREATE TABLE `bookings_stylists` (
                                     `id` int(11) NOT NULL,
                                     `stylist_date` date NOT NULL,
                                     `start_time` time NOT NULL,
                                     `end_time` time NOT NULL,
                                     `selected_cost` decimal(10,2) NOT NULL,
                                     `booking_id` int(11) NOT NULL,
                                     `stylist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings_stylists`
--

INSERT INTO `bookings_stylists` (`id`, `stylist_date`, `start_time`, `end_time`, `selected_cost`, `booking_id`, `stylist_id`) VALUES
                                                                                                                                  (5, '2025-04-03', '21:00:00', '22:00:00', 10.00, 7, 2),
                                                                                                                                  (6, '2025-04-06', '21:00:00', '22:00:00', 10.00, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
                            `id` int(11) NOT NULL,
                            `first_name` varchar(255) NOT NULL,
                            `last_name` varchar(255) NOT NULL,
                            `email` varchar(255) NOT NULL,
                            `phone_number` varchar(10) NOT NULL,
                            `message` text NOT NULL,
                            `replied` tinyint(1) DEFAULT 0,
                            `created` datetime DEFAULT current_timestamp(),
                            `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `message`, `replied`, `created`, `modified`) VALUES
    (2, 'John', 'Wick ', 'boogeyman69@gmail.com', '0987654321', 'what is this', 0, '2025-03-26 22:58:46', '2025-03-26 22:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
                             `id` int(11) NOT NULL,
                             `first_name` varchar(255) NOT NULL,
                             `last_name` varchar(255) NOT NULL,
                             `email` varchar(255) NOT NULL,
                             `password` varchar(255) NOT NULL,
                             `nonce` varchar(255) DEFAULT NULL,
                             `nonce_expiry` datetime DEFAULT NULL,
                             `created` datetime DEFAULT current_timestamp(),
                             `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                             `type` varchar(50) NOT NULL DEFAULT 'customer',
                             `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`, `profile_picture`) VALUES
                                                                                                                                                              (5, 'Chay Fong', 'Hong', 'chayfong9009@gmail.com', '$2y$10$OEDCACaiUaYKhHkeHFuQmOLwDX9oJ4HiTD6K3bmAhkODBpG7zI6t6', NULL, NULL, '2025-03-26 11:10:12', '2025-03-26 11:10:12', 'customer', NULL),
                                                                                                                                                              (6, 'Christian', 'Cochrane', 'cakephp@example.com', '$2y$10$4oCG2ResnEQbYk2rgtdTGe1faLZPOu29GZma4EfRmQ.B6vyHOk7u6', NULL, NULL, '2025-04-04 02:31:18', '2025-04-05 02:17:37', 'customer', '45568_Capture.PNG');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
                            `id` int(11) NOT NULL,
                            `service_name` varchar(255) NOT NULL,
                            `service_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_cost`) VALUES
                                                                  (1, 'Makeup', 6.50),
                                                                  (2, 'Hair Dressing', 7.50),
                                                                  (3, 'Dress Making', 8.50);

-- --------------------------------------------------------

--
-- Table structure for table `stylists`
--

CREATE TABLE `stylists` (
                            `id` int(11) NOT NULL,
                            `first_name` varchar(255) NOT NULL,
                            `last_name` varchar(255) NOT NULL,
                            `email` varchar(255) NOT NULL,
                            `password` varchar(255) NOT NULL,
                            `nonce` datetime DEFAULT NULL,
                            `nonce_expiry` datetime DEFAULT NULL,
                            `created` datetime DEFAULT current_timestamp(),
                            `modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                            `type` varchar(50) NOT NULL DEFAULT 'stylist',
                            `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stylists`
--

INSERT INTO `stylists` (`id`, `first_name`, `last_name`, `email`, `password`, `nonce`, `nonce_expiry`, `created`, `modified`, `type`, `profile_picture`) VALUES
                                                                                                                                                             (2, 'Lucy', 'Reig', 'lucyr@chiccharm.com.au', '$2y$10$UaR/Z4ljlvG1LztxsODz8uWljiN0Hv8MpN8mKZjZ5NEBOfchCFwei', NULL, NULL, '2025-04-05 11:51:05', '2025-04-05 15:24:22', 'stylist', NULL),
                                                                                                                                                             (4, 'Michael ', 'Jackson', 'hehe@gmail.com', '$2y$10$QAXdlH9eRlnPjL83P300BuDDNzHyDjchXuSxfoJTVNg3Suv8Me/UW', NULL, NULL, '2025-04-05 04:23:35', '2025-04-05 04:23:35', 'stylist', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stylists_services`
--

CREATE TABLE `stylists_services` (
                                     `id` int(11) NOT NULL,
                                     `stylist_id` int(11) NOT NULL,
                                     `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stylists_services`
--

INSERT INTO `stylists_services` (`id`, `stylist_id`, `service_id`) VALUES
                                                                       (3, 2, 2),
                                                                       (4, 2, 3),
                                                                       (7, 4, 1),
                                                                       (8, 4, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
    ADD PRIMARY KEY (`id`),
  ADD KEY `customerForeignKey` (`customer_id`);

--
-- Indexes for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
    ADD PRIMARY KEY (`id`),
  ADD KEY `bookingForeignKey` (`booking_id`),
  ADD KEY `stylistBookingForeignKey` (`stylist_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
    ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stylists`
--
ALTER TABLE `stylists`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `stylists_services`
--
ALTER TABLE `stylists_services`
    ADD PRIMARY KEY (`id`),
  ADD KEY `stylistForeignKey` (`stylist_id`),
  ADD KEY `serviceForeignKey` (`service_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stylists`
--
ALTER TABLE `stylists`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stylists_services`
--
ALTER TABLE `stylists_services`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
    ADD CONSTRAINT `customerForeignKey` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bookings_stylists`
--
ALTER TABLE `bookings_stylists`
    ADD CONSTRAINT `bookingForeignKey` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stylistBookingForeignKey` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stylists_services`
--
ALTER TABLE `stylists_services`
    ADD CONSTRAINT `serviceForeignKey` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `stylistForeignKey` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
