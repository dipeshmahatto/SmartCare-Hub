-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2024 at 06:27 AM
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
-- Database: `hms`
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
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`aid`, `pid`, `category`, `doctor`, `app_time`, `status`) VALUES
(2, 1, 'Dental', 'Dr.Sachin', '2 PM', 0),
(3, 1, 'Dental', 'Dr.Surya', '11 AM', 0),
(4, 1, 'Dental', 'Dr.Sachin', '11 AM', 0);

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
(5, 'Dr.Dipesh', 'dipeshmahatto@gmail.com', '9745837908', 31, 2003, 'kalimati', 'leg', 'mbbs', '12345678', 'm'),
(6, 'Dr.jatin', 'jatin@gmail.com', '9811223344', 26, 1998, 'baneswor', 'bone', 'md', 'jatin123', 'm'),
(9, 'Dr.Surya', 'suryanarayan2056@gmail.com', '9812002295', 31, 1993, 'kalimati', 'Dental', 'MD', 'surya123', 'm'),
(10, 'Dr.Sachin', 'sachin@gamil.com', '9819800670', 35, 1992, 'kathmandu', 'Dental', 'MD', 'sachin123', 'm'),
(11, 'Rnjish', 'ranjish12@gmail.com', '9874562130', 42, 1992, 'kalimati', 'Ophthalmology', 'BDS', '12345678', 'm');

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
(2, 'sachin', 'sachin@gmail.com', '9845837908', 24, 2001, 'gaushala-11', 'patient', 'm'),
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
  MODIFY `aid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `doctor_approval`
--
ALTER TABLE `doctor_approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
