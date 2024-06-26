-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2024 at 03:06 PM
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
-- Database: `has`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(55) NOT NULL,
  `password` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'admin2', 'admin2');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `aid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `category` varchar(55) NOT NULL,
  `doctor` varchar(55) NOT NULL,
  `app_time` varchar(255) NOT NULL,
  `day` varchar(11) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`aid`, `pid`, `category`, `doctor`, `app_time`, `day`, `status`) VALUES
(22, 1, 'Radiology', 'Dr.Aaryan', '10 AM', 'TUESDAY', 1),
(23, 1, 'Radiology', 'Dr.Aaryan', '12 PM', 'TUESDAY', 0);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `age` int(2) NOT NULL,
  `birthYear` int(4) NOT NULL,
  `address` varchar(105) NOT NULL,
  `speciality` varchar(55) NOT NULL,
  `qualification` varchar(55) NOT NULL,
  `password` varchar(55) NOT NULL,
  `gender` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`id`, `fullName`, `email`, `phoneNumber`, `age`, `birthYear`, `address`, `speciality`, `qualification`, `password`, `gender`) VALUES
(15, 'Dr.Aaryan', 'aaryan@gmail.com', '9819800000', 31, 1993, 'bafal', 'Radiology', 'PHD', '12345678', 'M'),
(19, 'Dr.Dipesh', 'dipesh@gmail.com', '9803643491', 29, 1991, 'kathmandu', 'Ophthalmology', 'MBBS', 'susmita@dipesh', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_approval`
--

CREATE TABLE `doctor_approval` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `age` int(2) NOT NULL,
  `birthYear` int(4) NOT NULL,
  `address` varchar(105) NOT NULL,
  `speciality` varchar(55) NOT NULL,
  `qualification` varchar(55) NOT NULL,
  `password` varchar(55) NOT NULL,
  `gender` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_approval`
--

INSERT INTO `doctor_approval` (`id`, `fullName`, `email`, `phoneNumber`, `age`, `birthYear`, `address`, `speciality`, `qualification`, `password`, `gender`) VALUES
(21, 'Dr.Ram', 'ram@gmail.com', '9824651784', 33, 1992, 'bhartpur', 'Ophthalmology', 'MD', '12345678', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNumber` varchar(11) NOT NULL,
  `age` int(2) NOT NULL,
  `birthYear` int(4) NOT NULL,
  `address` varchar(105) NOT NULL,
  `password` varchar(55) NOT NULL,
  `gender` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`id`, `fullName`, `email`, `phoneNumber`, `age`, `birthYear`, `address`, `password`, `gender`) VALUES
(1, 'Dipesh patient', 'patient@gmail.com', '9745837908', 22, 2003, 'kathmandu', 'patient', 'm'),
(4, 'surya mahato', 'suryanarayan2056@gmail.com', '9819800670', 25, 1999, 'kalimati', '12345678', 'm');

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE `times` (
  `tid` int(11) NOT NULL,
  `times` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `times`
--

INSERT INTO `times` (`tid`, `times`) VALUES
(1, '10 AM'),
(2, '11 AM'),
(3, '12 PM'),
(4, '1 PM'),
(5, '2 PM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`aid`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_approval`
--
ALTER TABLE `doctor_approval`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `times`
--
ALTER TABLE `times`
  ADD PRIMARY KEY (`tid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `doctor_approval`
--
ALTER TABLE `doctor_approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `times`
--
ALTER TABLE `times`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
