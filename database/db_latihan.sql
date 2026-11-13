-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2026 at 08:54 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_latihan`
--

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `email`, `telepon`, `alamat`, `tanggal_lahir`, `created_at`) VALUES
(1, 'Budi Santoso', 'budi@example.com', '081234567890', NULL, NULL, '2026-11-13 05:44:18'),
(2, 'Siti Aminah', 'siti@example.com', '082345678901', NULL, NULL, '2026-11-13 05:44:18'),
(4, 'Alinda Aulia', 'alindaaulia@gmail.com', '083188878323424', NULL, NULL, '2026-11-13 05:50:39'),
(5, 'fahmi', 'fahmi@gmail.com', '08637296353', NULL, NULL, '2026-11-13 06:14:18'),
(6, 'kenan', 'kenan@gmail.com', NULL, NULL, NULL, '2026-11-13 06:16:13'),
(8, 'Adinda', 'adinda@gmail.com', '13431343143', NULL, NULL, '2026-11-13 06:31:19'),
(10, 'Rina', 'rina@example.com', '087677564674656', 'Bandung', '1998-03-12', '2026-11-13 06:50:18'),
(11, 'anida', 'anida@example.com', '081264535757', 'Jakarta', '1995-07-21', '2026-11-13 06:50:18'),
(12, 'Sari', 'sari@example.com', NULL, 'Surabaya', NULL, '2026-11-13 06:50:18'),
(13, 'Fajar', 'fajar@example.com', '0813646275869', NULL, '2000-01-05', '2026-11-13 06:50:18'),
(14, 'Dewi', 'dewi@example.com', '081461426716', 'Yogyakarta', '1999-11-30', '2026-11-13 06:50:18'),
(15, 'ahmad', 'ahmad@example.com', NULL, NULL, NULL, '2026-11-13 06:50:18'),
(16, 'Putri', 'putri@example.com', '0815862752397', 'Medan', '2001-05-17', '2026-11-13 06:50:18'),
(17, 'Rizki', 'rizki@example.com', '0816164729857', 'Bogor', '1997-09-09', '2026-11-13 06:50:18'),
(18, 'Nadia', 'nadia@example.com', NULL, 'Depok', '2002-02-14', '2026-11-13 06:50:18'),
(19, 'aldi', 'aldi@example.com', '08170985765', 'Bekasi', '1996-12-25', '2026-11-13 06:50:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
