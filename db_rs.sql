-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2026 at 05:15 AM
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
-- Database: `db_rs`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_tagihan`
--

CREATE TABLE `detail_tagihan` (
  `id_detail` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `total_harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_tagihan`
--

INSERT INTO `detail_tagihan` (`id_detail`, `id_tagihan`, `id_pasien`, `id_item`, `jumlah`, `total_harga`) VALUES
(1, 1, 1, 1, 2, 300000),
(2, 1, 1, 6, 2, 150000),
(3, 2, 2, 1, 1, 150000),
(4, 2, 2, 4, 1, 25000),
(5, 2, 2, 3, 1, 15000),
(6, 3, 3, 2, 1, 300000),
(7, 3, 3, 5, 1, 250000),
(8, 3, 3, 6, 1, 75000),
(9, 3, 3, 4, 1, 25000),
(10, 4, 4, 1, 1, 150000),
(11, 4, 4, 3, 1, 15000),
(12, 5, 5, 2, 1, 300000),
(13, 5, 5, 1, 1, 150000),
(14, 6, 6, 3, 1, 15000),
(15, 6, 6, 2, 1, 300000),
(16, 6, 6, 1, 1, 150000);

-- --------------------------------------------------------

--
-- Table structure for table `item_medis`
--

CREATE TABLE `item_medis` (
  `id` int(11) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_medis`
--

INSERT INTO `item_medis` (`id`, `nama_item`, `harga`, `stok`) VALUES
(1, 'Konsultasi Dokter Umum', 150000, 9993),
(2, 'Konsultasi Dokter Spesialis', 300000, 9996),
(3, 'Paracetamol 500mg', 15000, 497),
(4, 'Amoxicillin', 25000, 298),
(5, 'Cek Darah Lengkap', 250000, 9998),
(6, 'Infus Set', 75000, 147);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_rm` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nama`, `alamat`, `no_rm`) VALUES
(1, '-', '-', '-'),
(2, '-', '-', '-'),
(3, '-', '-', '-'),
(4, 'Chesta Mahardika', 'Grandrertro', '028328392832983'),
(5, '-', '-', '-'),
(6, '-', '-', '-');

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `no_nota` varchar(30) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `kasir` varchar(50) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `kembali` int(11) NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id_tagihan`, `no_nota`, `id_pasien`, `kasir`, `total_bayar`, `bayar`, `kembali`, `tanggal`) VALUES
(1, 'RS-20260927043911', 1, 'Admin RS', 450000, 500000, 50000, '2026-09-27 04:39:11'),
(2, 'RS-20260927044320', 2, 'Admin RS', 190000, 200000, 10000, '2026-09-27 04:43:20'),
(3, 'RS-20260927044431', 3, 'Admin RS', 650000, 700000, 50000, '2026-09-27 04:44:31'),
(4, 'RS-20260927044621', 4, 'Admin RS', 165000, 165000, 0, '2026-09-27 04:46:21'),
(5, 'RS-20260927044815', 5, 'Admin RS', 450000, 450000, 0, '2026-09-27 04:48:15'),
(6, 'RS-20260927051200', 6, 'Admin RS', 465000, 700000, 235000, '2026-09-27 05:12:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  ADD PRIMARY KEY (`id_detail`);

--
-- Indexes for table `item_medis`
--
ALTER TABLE `item_medis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_tagihan`
--
ALTER TABLE `detail_tagihan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `item_medis`
--
ALTER TABLE `item_medis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
