-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 09:00 PM
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
-- Database: `ets`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `leave_request_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`leave_request_id`, `staff_id`, `leave_date`, `status`, `reason`, `admin_id`) VALUES
(1, 1, '2024-12-11', 'Approved', 'sdsdsds', NULL),
(2, 1, '2025-01-10', 'Pending', 'dsadasd', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `shift` enum('5:00-11:00','11:00-17:00','17:00-23:00') NOT NULL,
  `status` enum('Scheduled','On Leave') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `staff_id`, `work_date`, `shift`, `status`) VALUES
(2, 1, '2024-12-15', '5:00-11:00', 'Scheduled'),
(3, 2, '2024-12-16', '11:00-17:00', 'Scheduled'),
(4, 3, '2024-12-17', '17:00-23:00', 'Scheduled'),
(5, 1, '2024-12-08', '5:00-11:00', 'Scheduled'),
(6, 1, '2024-12-09', '5:00-11:00', 'Scheduled'),
(7, 1, '2024-12-10', '5:00-11:00', 'Scheduled'),
(8, 3, '2024-12-10', '17:00-23:00', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_change_requests`
--

CREATE TABLE `schedule_change_requests` (
  `change_request_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `current_shift` enum('5:00-11:00','11:00-17:00','17:00-23:00') NOT NULL,
  `new_shift` enum('5:00-11:00','11:00-17:00','17:00-23:00') NOT NULL,
  `request_date` date NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `reason` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_requests`
--

CREATE TABLE `schedule_requests` (
  `request_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `current_date` date NOT NULL,
  `requested_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `shift` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_requests`
--

INSERT INTO `schedule_requests` (`request_id`, `staff_id`, `current_date`, `requested_date`, `reason`, `status`, `shift`) VALUES
(1, 1, '2024-12-11', '2024-12-11', 'idk la why', 'Approved', '17:00-23:00'),
(2, 1, '2024-12-11', '2024-12-11', 'entah la', 'Approved', '5:00-11:00'),
(3, 1, '2024-12-11', '2024-12-11', 'entah la', 'Approved', '5:00-11:00'),
(4, 1, '2024-12-12', '2024-12-04', 'd', 'Approved', '5:00-11:00'),
(5, 1, '2024-12-11', '2024-12-11', 'sds', 'Approved', '5:00-11:00'),
(6, 3, '2024-12-11', '2024-12-11', 'dsadsad', 'Pending', '5:00-11:00'),
(7, 1, '2024-12-13', '2024-12-04', 'sdasdsa', 'Pending', '5:00-11:00');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `position` enum('Driver','Stewardess','Customer Service') NOT NULL,
  `shift` enum('5:00-11:00','11:00-17:00','17:00-23:00') NOT NULL,
  `off_day` tinyint(1) DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL,
  `change_request_id` int(11) DEFAULT NULL,
  `leave_request_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `request_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `email`, `password`, `gender`, `position`, `shift`, `off_day`, `admin_id`, `change_request_id`, `leave_request_id`, `schedule_id`, `request_id`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', 'password123', 'Male', 'Driver', '5:00-11:00', 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Jane', 'Smith', 'jane.smith@example.com', 'password456', 'Female', 'Stewardess', '11:00-17:00', 1, NULL, NULL, NULL, NULL, NULL),
(3, 'Alice', 'Johnson', 'alice.johnson@example.com', 'password789', 'Female', 'Customer Service', '17:00-23:00', 0, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`leave_request_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `fk_leave_admin_id` (`admin_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `schedule_change_requests`
--
ALTER TABLE `schedule_change_requests`
  ADD PRIMARY KEY (`change_request_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `fk_schedule_admin_id` (`admin_id`);

--
-- Indexes for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admin_id` (`admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `leave_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedule_change_requests`
--
ALTER TABLE `schedule_change_requests`
  MODIFY `change_request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `schedule_change_requests`
--
ALTER TABLE `schedule_change_requests`
  ADD CONSTRAINT `fk_schedule_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `schedule_change_requests_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `schedule_requests`
--
ALTER TABLE `schedule_requests`
  ADD CONSTRAINT `schedule_requests_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
