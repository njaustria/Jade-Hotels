-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2020 at 10:41 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `room_categories`
--

CREATE TABLE `room_categories` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` float NOT NULL,
  `cover_img` text NOT NULL,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `room_categories`
--

INSERT INTO `room_categories` (`id`, `name`, `price`, `cover_img`, `description`, `amenities`) VALUES
(2, 'Deluxe Room', 4500, 'hotel_deluxeroom.jpg', 'Experience comfort and elegance in our Deluxe Room, perfect for two guests. Enjoy a spacious, well-appointed room featuring a cozy queen-size bed.', ' '),
(4, 'Family Room', 7000, 'hotel_familyroom.jpg', 'Perfect for a relaxing family getaway, our Family Room offers spacious comfort with multiple beds, ideal for parents and kids. ', ' '),
(6, 'Twin Bed Room', 5500, 'hotel_twinroom.jpg', 'Ideal for families or groups, our Twin Bedroom accommodates up to 4 guests with two comfortable double beds.', ' ');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `room` varchar(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Available , 1= Unavailable',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `room_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room`, `category_id`, `status`) VALUES
(1, 'Room-301', 4, 0),
(4, 'Room-201', 6, 0),
(5, 'Room-302', 4, 0),
(6, 'Room-102', 2, 0),
(7, 'Room-202', 6, 0),
(8, 'Room-101', 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `booked`
--

CREATE TABLE `booked` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `id_type` VARCHAR(50) NULL COMMENT 'Type of ID document (Passport, Driver License, etc.)',
  `id_number` VARCHAR(100) NULL COMMENT 'ID document number',
  `category` int(30) NOT NULL,
  `check_in` datetime NOT NULL,
  `check_out` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Booked, 1=Checked-in, 2=Checked-out, 3=Cancelled',
  `payment_method` ENUM('card', 'ewallet') NULL,
  `payment_status` VARCHAR(50) DEFAULT 'paid',
  `card_number` VARCHAR(20) NULL,
  `ewallet_provider` ENUM('gcash', 'paymaya') NULL,
  `account_number` VARCHAR(50) NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `check_in` (`check_in`),
  KEY `idx_id_number` (`id_number`),
  CONSTRAINT `booked_ibfk_1` FOREIGN KEY (`category`) REFERENCES `room_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `checked`
--

CREATE TABLE `checked` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `room_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `id_type` VARCHAR(50) COMMENT 'Type of ID (e.g., Passport, Driver License, National ID)',
  `id_number` VARCHAR(100) COMMENT 'ID number corresponding to the ID type',
  `date_in` datetime NOT NULL,
  `date_out` datetime NOT NULL,
  `booked_cid` int(30) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1=checked in , 2 = checked out',
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `idx_id_number` (`id_number`),
  CONSTRAINT `checked_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_facilities`
--

CREATE TABLE `hotel_facilities` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `facility_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `operating_hours` varchar(100) NOT NULL,
  `location` varchar(200) NOT NULL,
  `image` varchar(200) DEFAULT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hotel_facilities`
--

INSERT INTO `hotel_facilities` (`id`, `facility_name`, `description`, `operating_hours`, `location`, `image`, `date_updated`) VALUES
(1, 'Gym', 'The Jade Hotels gym offers a modern, fully equipped fitness center designed for guests seeking a convenient and energizing workout.', '6:00AM - 12:00PM', 'Second Floor', 'hotel_gym.jpg', '2025-06-16 22:07:39'),
(2, 'Misto', 'Jade Hotels Misto is the signature all-day dining restaurant. Offering a delightful fusion of international and Filipino flavors.', '6:00AM - 10:00PM', 'Ground Floor', 'hotel_misto.jpg', '2025-06-16 22:08:04'),
(3, 'Swimming Pool', 'Jade Hotels swimming pool offers a refreshing retreat for guests looking to relax or stay active during their stay. Surrounded by comfortable lounge chairs and a peaceful ambiance.', '6:00AM - 8:00PM', 'Ground Floor', 'hotel_pool.jpg', '2025-06-16 22:08:27');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `hotel_name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `about_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `hotel_name`, `email`, `contact`, `about_content`) VALUES
(1, 'Jade Hotels', 'jadehotels@hotel.com', '+6391 345 6789', 'Indulge your senses at our signature fine-dining restaurant, where internationally acclaimed chefs curate exquisite culinary experiences. Unwind at the rooftop infinity pool, rejuvenate at the full-service spa, or sip handcrafted cocktails in the ambient Jade Lounge.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=admin , 2 = staff',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
(1, 'CEO', 'ceo', 'ceo123', 1),
(2, 'Co-CEO', 'co-ceo', 'co-ceo1234', 1),
(3, 'Manager', 'manager', 'manager1234', 1);

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `bio` text,
  `image` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `position`, `bio`, `image`, `linkedin_url`, `twitter_url`, `display_order`, `status`, `date_created`, `date_updated`) VALUES
(1, 'Neian Austria', 'General Manager', 'Hotel General Manager is responsible for overseeing all aspects of hotel operations to ensure guest satisfaction, profitability, and team performance', 'hotel_manager.jpg', 'https://www.linkedin.com/', 'https://x.com/', 3, 1, '2025-06-08 00:51:26', '2025-06-13 12:10:07'),
(2, 'Jade Tolentino', 'Chief Executive Officer', 'Hotel Chief Executive Officer (CEO) is the highest-ranking executive in the hotel or hospitality organization, responsible for setting and executing the strategic vision, driving profitability, and ensuring excellence across all aspects of the business.', 'hotel_coceo.jpg', 'https://www.linkedin.com/', 'https://x.com/', 1, 1, '2025-06-08 01:08:37', '2025-06-13 12:31:46'),
(3, 'Aldrich Jobog', 'Co-Chief Executive Officer', 'Hotel Co-CEO shares executive leadership responsibilities with the other Co-CEO to oversee the overall strategy, growth, and operations of the hotel or hotel group.', 'hotel_ceo.jpg', 'https://www.linkedin.com/', 'https://x.com/', 0, 1, '2025-06-08 01:13:30', '2025-06-13 12:10:31');

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booked`
--
ALTER TABLE `booked`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checked`
--
ALTER TABLE `checked`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_categories`
--
ALTER TABLE `room_categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hotel_facilities`
--
ALTER TABLE `hotel_facilities`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;