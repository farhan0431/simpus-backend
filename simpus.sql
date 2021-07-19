-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 19, 2021 at 02:44 AM
-- Server version: 5.7.32
-- PHP Version: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simpus`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2021_07_08_025403_create_rekam_medis_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

CREATE TABLE `rekam_medis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `no_rm` int(250) NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ktp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `berat_badan` int(11) NOT NULL,
  `tinggi_badan` int(11) NOT NULL,
  `tekanan_darah` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nadi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lingkar_perut` int(11) NOT NULL,
  `suhu` int(11) NOT NULL,
  `nafas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `riwayat_alergi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_pembayaran` int(11) NOT NULL COMMENT '1:BPJS | 2:Umum',
  `rujukan_poli` int(11) NOT NULL COMMENT '1: Umum | 2: Gigi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekam_medis`
--

INSERT INTO `rekam_medis` (`id`, `no_rm`, `nama`, `ktp`, `tanggal_lahir`, `alamat`, `telp`, `berat_badan`, `tinggi_badan`, `tekanan_darah`, `nadi`, `lingkar_perut`, `suhu`, `nafas`, `riwayat_alergi`, `status_pembayaran`, `rujukan_poli`, `created_at`, `updated_at`) VALUES
(1, 0, 'asd', '23', '2021-07-16', 'asd', '123', 2, 2, '2', '3', 3, 3, '3', 'asd', 1, 1, '2021-07-15 18:56:19', '2021-07-15 18:56:19'),
(3, 1, 'a', '12', '2021-07-09', 'asd', '12', 1, 2, '3', '3', 3, 2, '2', 'asd', 1, 1, '2021-07-15 19:19:53', '2021-07-15 19:19:53'),
(4, 2, 'a', '12', '2021-07-09', 'asd', '12', 1, 2, '3', '3', 3, 2, '2', 'asd', 1, 1, '2021-07-15 19:19:54', '2021-07-15 19:19:54'),
(5, 3, 'a', '12', '2021-07-09', 'asd', '12', 1, 2, '3', '3', 3, 2, '2', 'asd', 1, 1, '2021-07-15 19:20:09', '2021-07-15 19:20:09'),
(6, 4, 'asd', '123', '2021-07-15', 'asd', '12', 21, 2, '2', '2', 2, 2, '2', 'asd', 2, 1, '2021-07-15 19:21:50', '2021-07-15 19:21:50'),
(7, 5, 'farhan', '10293', '2021-07-16', '123', '123', 23, 23123, '876', '87', 876, 86, '876', 'asd', 1, 1, '2021-07-15 19:24:16', '2021-07-15 19:24:16'),
(8, 6, 'farhan', '10293', '2021-07-16', '123', '123', 23, 23123, '876', '87', 876, 86, '876', 'asd', 1, 1, '2021-07-15 19:24:30', '2021-07-15 19:24:30'),
(9, 7, 'farhan', '10293', '2021-07-16', '123', '123', 23, 23123, '876', '87', 876, 86, '876', 'asd', 1, 1, '2021-07-15 19:24:33', '2021-07-15 19:24:33'),
(10, 8, 'farhan', '10293', '2021-07-16', '123', '123', 23, 23123, '876', '87', 876, 86, '876', 'asd', 1, 1, '2021-07-15 19:24:43', '2021-07-15 19:24:43'),
(11, 9, 'farhan', '123089', '2021-07-16', '1asd', '12', 2, 2, '2', '2', 2, 2, '2', '2', 1, 1, '2021-07-15 19:25:44', '2021-07-15 19:25:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `thumb_avatar` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telpon` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jk` int(11) NOT NULL COMMENT '1: Laki-laki 2:Wanita',
  `jabatan` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role_id`, `thumb_avatar`, `remember_token`, `telpon`, `alamat`, `jk`, `jabatan`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'superadmin', 'superadmin@admin.com', '$2y$10$9.NXb97HXVLDykxiPv4Lh.N04U2uatDGkMAIgpa4QqyaXn39IrVAW', 1, NULL, NULL, '123', '', 0, '', '2021-04-19 22:52:26', '2021-04-24 20:50:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
