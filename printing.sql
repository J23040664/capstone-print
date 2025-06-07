-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 07, 2025 at 09:32 AM
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
-- Database: `printing`
--

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` varchar(8) NOT NULL,
  `order_id` varchar(8) DEFAULT NULL,
  `item_id` varchar(8) DEFAULT NULL,
  `file_name` varchar(50) DEFAULT NULL,
  `file_path` longblob DEFAULT NULL,
  `file_type` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`file_id`, `order_id`, `item_id`, `file_name`, `file_path`, `file_type`) VALUES
('F0000001', 'O0000001', 'I0000001', 'assigment2025.pdf', 0x286c6f6e67626c6f6229, 'pdf'),
('F0000002', 'O0000002', 'I0000002', 'group2.pdf', 0x286c6f6e67626c6f6229, 'pdf'),
('F0000003', 'O0000003', 'I0000003', 'newdocument.pdf', 0x286c6f6e67626c6f6229, 'pdf');

-- --------------------------------------------------------

--
-- Table structure for table `finishing_list`
--

CREATE TABLE `finishing_list` (
  `finishing_id` varchar(4) NOT NULL,
  `finishing_type` varchar(50) DEFAULT NULL,
  `finishing_price` decimal(6,2) DEFAULT NULL,
  `finishing_status` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finishing_list`
--

INSERT INTO `finishing_list` (`finishing_id`, `finishing_type`, `finishing_price`, `finishing_status`) VALUES
('F001', 'Lamination-A3', 2.50, 'Available'),
('F002', 'Lamination-A4', 5.00, 'Not Available'),
('F003', 'Stapler-Top Left', 0.20, 'Available'),
('F004', 'Stapler-Top Right', 0.20, 'Available'),
('F005', 'Stapler-Side Center(2)', 0.50, 'Available'),
('F006', 'Comb Binding', 3.00, 'Available'),
('F007', 'Wire O Binding', 5.00, 'Available'),
('F008', 'Hard Cover-Front&Back', 1.50, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` varchar(8) NOT NULL,
  `item_id` varchar(8) DEFAULT NULL,
  `service_total_price` decimal(6,2) DEFAULT NULL,
  `finishing_quantity` int(1) DEFAULT NULL,
  `finishing_total_price` decimal(6,2) DEFAULT NULL,
  `total_price` decimal(6,2) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `order_status` varchar(9) DEFAULT NULL,
  `customer_id` int(8) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `staff_id` int(8) DEFAULT NULL,
  `staff_name` varchar(100) DEFAULT NULL,
  `payment_id` varchar(8) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `item_id`, `service_total_price`, `finishing_quantity`, `finishing_total_price`, `total_price`, `order_date`, `order_status`, `customer_id`, `customer_name`, `staff_id`, `staff_name`, `payment_id`, `payment_status`) VALUES
('O0000001', 'I0000001', 10.00, 2, 4.50, 14.50, '2025-01-06', 'Completed', 10000003, 'Mickie Joe', 10000001, 'Michael Bay', 'P0000001', 'Completed'),
('O0000002', 'I0000002', 80.00, 0, NULL, 80.00, '2025-02-06', 'Completed', 10000004, 'Johny Deep', 10000002, 'Jackson Mirana', 'P0000002', 'Completed'),
('O0000003', 'I0000003', 10.00, 0, NULL, 10.00, '2025-03-06', 'Pending', 10000005, 'Brunice Kee', 10000002, 'Jackson Mirana', 'P0000003', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `item_id` varchar(8) NOT NULL,
  `order_id` varchar(8) DEFAULT NULL,
  `service_id` varchar(4) DEFAULT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `service_price` decimal(6,2) DEFAULT NULL,
  `quantity` int(4) DEFAULT NULL,
  `size` varchar(4) DEFAULT NULL,
  `colour` varchar(4) DEFAULT NULL,
  `service_total_price` decimal(6,2) DEFAULT NULL,
  `finishing_1` varchar(4) DEFAULT NULL,
  `finishing_type1` varchar(50) DEFAULT NULL,
  `finishing_price1` decimal(6,2) DEFAULT 0.00,
  `finishing_2` varchar(4) DEFAULT NULL,
  `finishing_type2` varchar(50) DEFAULT NULL,
  `finishing_price2` decimal(6,2) DEFAULT 0.00,
  `finishing_3` varchar(4) DEFAULT NULL,
  `finishing_type3` varchar(50) DEFAULT NULL,
  `finishing_price3` decimal(6,2) DEFAULT 0.00,
  `item_price` decimal(6,2) DEFAULT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`item_id`, `order_id`, `service_id`, `service_type`, `service_price`, `quantity`, `size`, `colour`, `service_total_price`, `finishing_1`, `finishing_type1`, `finishing_price1`, `finishing_2`, `finishing_type2`, `finishing_price2`, `finishing_3`, `finishing_type3`, `finishing_price3`, `item_price`, `created_at`) VALUES
('I0000001', 'O0000001', 'S001', 'Print-1side', 0.20, 50, 'A4', 'BW', 10.00, 'F006', 'Comb Binding', 3.00, 'F008', 'Hard Cover-Front&Back', 1.50, NULL, '', NULL, 14.50, '2025-01-06'),
('I0000002', 'O0000002', 'S003', 'Photocopy-1side', 0.20, 100, 'A3', 'CL', 80.00, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, 80.00, '2025-02-06'),
('I0000003', 'O0000003', 'S009', 'Lamination(Photocopy)', 2.50, 2, 'A4', 'CL', 10.00, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, 10.00, '2025-03-06');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` varchar(8) NOT NULL,
  `order_id` varchar(8) DEFAULT NULL,
  `total_price` decimal(6,2) DEFAULT NULL,
  `payment_type` varchar(7) DEFAULT NULL,
  `payment_status` varchar(9) DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `total_price`, `payment_type`, `payment_status`, `payment_date`) VALUES
('P0000001', 'O0000001', 14.50, 'Online', 'Completed', '2025-01-06'),
('P0000002', 'O0000002', 80.00, 'Online', 'Completed', '2025-02-06'),
('P0000003', 'O0000003', 10.00, 'Walk In', 'Completed', '2025-03-06');

-- --------------------------------------------------------

--
-- Table structure for table `service_list`
--

CREATE TABLE `service_list` (
  `service_id` varchar(4) NOT NULL,
  `service_type` varchar(50) DEFAULT NULL,
  `service_price` decimal(6,2) DEFAULT NULL,
  `service_status` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_list`
--

INSERT INTO `service_list` (`service_id`, `service_type`, `service_price`, `service_status`) VALUES
('S001', 'Print-1side', 0.20, 'Available'),
('S002', 'Print-2side', 0.20, 'Not Available'),
('S003', 'Photocopy-1side', 0.20, 'Available'),
('S004', 'Photocopy-2side', 0.20, 'Not Available'),
('S005', 'Colour', 2.00, 'Available'),
('S006', 'Black and White', 1.00, 'Available'),
('S007', 'A3', 2.00, 'Available'),
('S008', 'A4', 1.00, 'Available'),
('S009', 'Lamination(Photocopy)', 2.50, 'Available'),
('S010', 'Lamination(Print)', 2.50, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` bigint(8) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `role` varchar(8) DEFAULT NULL,
  `create_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `password`, `name`, `phone_number`, `role`, `create_date`) VALUES
(10000001, 'test@mail.com', '$2y$10$GTuZZOBJIUa/rdTyshHFIOQgJLxr5oiGnOr1mb3TGpnG.AsBptn8q', 'test1', '0123456789', 'Customer', '2025-06-07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `file_order_FK` (`order_id`),
  ADD KEY `file_order_detail_FK` (`item_id`);

--
-- Indexes for table `finishing_list`
--
ALTER TABLE `finishing_list`
  ADD PRIMARY KEY (`finishing_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `order_order_detail_FK` (`item_id`),
  ADD KEY `order_user_FK` (`customer_id`),
  ADD KEY `order_user_FK_1` (`staff_id`),
  ADD KEY `order_payment_FK` (`payment_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_detail_order_FK` (`order_id`),
  ADD KEY `order_detail_service_list_FK` (`service_id`),
  ADD KEY `order_detail_finishing_list_FK` (`finishing_1`),
  ADD KEY `order_detail_finishing_list_FK_1` (`finishing_2`),
  ADD KEY `order_detail_finishing_list_FK_2` (`finishing_3`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` bigint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000002;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_order_FK` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `file_order_detail_FK` FOREIGN KEY (`item_id`) REFERENCES `order_detail` (`item_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_order_detail_FK` FOREIGN KEY (`item_id`) REFERENCES `order_detail` (`item_id`),
  ADD CONSTRAINT `order_payment_FK` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_finishing_list_FK` FOREIGN KEY (`finishing_1`) REFERENCES `finishing_list` (`finishing_id`),
  ADD CONSTRAINT `order_detail_finishing_list_FK_1` FOREIGN KEY (`finishing_2`) REFERENCES `finishing_list` (`finishing_id`),
  ADD CONSTRAINT `order_detail_finishing_list_FK_2` FOREIGN KEY (`finishing_3`) REFERENCES `finishing_list` (`finishing_id`),
  ADD CONSTRAINT `order_detail_order_FK` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`),
  ADD CONSTRAINT `order_detail_service_list_FK` FOREIGN KEY (`service_id`) REFERENCES `service_list` (`service_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
