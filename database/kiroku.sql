-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 07, 2026 at 08:29 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kiroku`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('kiroku-student-logging-system-cache-admin|127.0.0.1', 'i:1;', 1775460619),
('kiroku-student-logging-system-cache-admin|127.0.0.1:timer', 'i:1775460619;', 1775460619),
('kiroku-student-logging-system-cache-logger|127.0.0.1', 'i:1;', 1775549510),
('kiroku-student-logging-system-cache-logger|127.0.0.1:timer', 'i:1775549510;', 1775549510);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_records`
--

CREATE TABLE `log_records` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` bigint UNSIGNED DEFAULT NULL,
  `log_session_id` bigint UNSIGNED DEFAULT NULL,
  `time_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_sessions`
--

CREATE TABLE `log_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `school_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_08_052600_create_students_table', 1),
(5, '2026_01_08_025400_create_log_sessions_table', 1),
(6, '2026_01_08_025401_create_log_records_table', 1),
(7, '2026_01_12_152645_create_school_year_settings_table', 1),
(8, '2026_01_15_015822_create_settings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `school_year_settings`
--

CREATE TABLE `school_year_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `school_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_year_settings`
--

INSERT INTO `school_year_settings` (`id`, `school_year`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2025-2026', 1, '2026-03-12 22:22:12', '2026-03-12 22:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gKVmz42iv93cKeShzEmlkEnvoiNIdNb3snq16dRE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMUZGaUEwRjlEUFJseXRaTjZMQ0dBcEVyRVNvWTJLQzFJWlNtOUxNeCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775549890),
('K71ljh2yrWsqyztFJUMDCchLXoqZ8YML5xLICJKj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS01kcXN5R3J5REJycUN6Q0dXenJkVjd2SkdtN2pha2NTR2pBcmF5eiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775465598),
('rHNp30hESUre7VTi5pbfrJLhqEKxhSVahO2HheTs', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQXZwS2xvMGM3UEVzb3dYdlBPbEQ2b01LZUtKbzA0RmxGQUtFc0xOZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fX0=', 1775460606),
('u0NmuATwjSFkdfCkyfusnEIw0E9oNSU24odS2ExK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiREVvYXA0N0JrUmF2TmxVdjVmcUJJQ3M1NVp0cjlrZmZxY0ozOUNUcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1775549450),
('XxZKCu0qsOjfDX1Vz71UHRvphXb1IE2F4SgTlrsO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNjVneW5xR2FiYTlsWHY1N3ZKQXdxS0JjU1htVnJpM3dMd1hjb1Z3SSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mzg6Imh0dHA6Ly9raXJva3Utc2xzLnRlc3QvYWRtaW4tZGFzaGJvYXJkIjt9fQ==', 1773391806);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint UNSIGNED NOT NULL,
  `id_student` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year_level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `id_student`, `last_name`, `first_name`, `year_level`, `course`, `created_at`, `updated_at`) VALUES
(1, '6678990', 'Abanto', 'Jance', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:31:48', '2026-03-12 22:31:48'),
(2, '4764896', 'Abella', 'Kyla', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:32:46', '2026-03-12 22:32:46'),
(3, '7588939', 'Adaro', 'Lila Dave', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-12 22:33:29', '2026-03-12 22:33:29'),
(4, '8985454', 'Adormeo', 'Glen', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:34:31', '2026-03-12 22:34:31'),
(5, '1209984', 'Alan', 'Lexa Julla', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:38:37', '2026-03-12 22:38:37'),
(6, '2576908', 'Albano', 'Jouella Marie', '1st Year', 'Bachelor of Elementary Education', '2026-03-12 22:41:20', '2026-03-12 22:41:20'),
(7, '4787336', 'Aldohesa', 'Althea', '2nd Year', 'Bachelor of Special Needs Education', '2026-03-12 22:43:00', '2026-03-12 22:43:00'),
(8, '6878554', 'Alegado', 'Carmela Mae', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:44:17', '2026-03-12 22:44:17'),
(9, '4534556', 'Almario', 'Michelle', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:45:12', '2026-03-12 22:45:12'),
(10, '0967565', 'Almendras', 'Jhon Dave', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-12 22:46:10', '2026-03-12 22:46:10'),
(11, '5456688', 'Alviola', 'Kyla Mae', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:46:56', '2026-03-12 22:46:56'),
(12, '5435678', 'Ang', 'Jhon Audric', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-12 22:47:48', '2026-03-12 22:47:48'),
(13, '4687842', 'Apitan', 'Lei Yeizha', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:48:39', '2026-03-12 22:48:39'),
(14, '2135799', 'Arai', 'Yamato', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:49:55', '2026-03-12 22:49:55'),
(15, '3680854', 'Arellano', 'Princess Gil', '1st Year', 'Bachelor of Human Services', '2026-03-12 22:50:56', '2026-03-12 22:50:56'),
(16, '3546890', 'Avergonzado', 'Guiller James', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:51:56', '2026-03-12 22:51:56'),
(17, '5800975', 'Ayag', 'Ma. Gabrielle Cassandra', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:54:01', '2026-03-12 22:54:01'),
(18, '5679995', 'Baje', 'Leah', '3rd Year', 'Bachelor of Secondary Education', '2026-03-12 22:54:58', '2026-03-12 22:54:58'),
(19, '1346890', 'Balofinos', 'Mikaela', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 22:58:44', '2026-03-12 22:58:44'),
(20, '4567874', 'Balunto', 'Toshea Lei', '1st Year', 'Bachelor of Secondary Education', '2026-03-12 22:59:32', '2026-03-12 22:59:32'),
(21, '5677755', 'Baron', 'Tracy Adrielle', '3rd Year', 'Bachelor of Human Services', '2026-03-12 23:00:31', '2026-03-12 23:00:31'),
(22, '2579644', 'Bermejo', 'Lei Franz', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:01:18', '2026-03-12 23:01:18'),
(23, '2478753', 'Besinga', 'Aldrene', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:02:07', '2026-03-12 23:02:07'),
(24, '3798754', 'Biangco', 'Wed', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:03:03', '2026-03-12 23:03:03'),
(25, '6523486', 'Bonifacio', 'Cyril Enzo Albert', '1st Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:04:20', '2026-03-12 23:04:20'),
(26, '7642456', 'Bugarin', 'Adrian Paolo', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:05:47', '2026-03-12 23:05:47'),
(27, '3667436', 'Buloran', 'Jim Russel', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:07:05', '2026-03-12 23:10:03'),
(28, '6436455', 'Caasi', 'Jaz Lester', '2nd Year', 'Bachelor of Secondary Education', '2026-03-12 23:09:43', '2026-03-12 23:09:43'),
(29, '7467467', 'Cabarles', 'Eve Marie Virgil', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:10:59', '2026-03-12 23:10:59'),
(30, '5632675', 'Cabigas', 'Danna Angel Cassophea', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:11:59', '2026-03-12 23:11:59'),
(31, '3456788', 'Cambing', 'Ahsiyah', '1st Year', 'Bachelor of Secondary Education', '2026-03-12 23:12:58', '2026-03-12 23:12:58'),
(32, '5667854', 'Caracol', 'Yuki', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:13:40', '2026-03-12 23:13:40'),
(33, '7785654', 'Carpio', 'Nyssa', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:14:18', '2026-03-12 23:14:18'),
(34, '6754678', 'Casas', 'Jhanna Alteah', '1st Year', 'Bachelor of Human Services', '2026-03-12 23:15:19', '2026-03-12 23:15:19'),
(35, '9658565', 'Celestra', 'Ken Fumiver', '1st Year', 'Bachelor of Secondary Education', '2026-03-12 23:16:27', '2026-03-12 23:16:27'),
(36, '6734678', 'Charcos', 'Shania Janine', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:17:08', '2026-03-12 23:17:08'),
(37, '8788676', 'Chavez', 'Princess  Anne', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:17:38', '2026-03-12 23:17:38'),
(38, '5438567', 'Codinera', 'Yumi Amour', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:18:12', '2026-03-12 23:18:12'),
(39, '0357675', 'Compendio', 'Dominique Faye', '2nd Year', 'Bachelor of Human Services', '2026-03-12 23:20:24', '2026-03-12 23:20:24'),
(40, '2898543', 'Cornel', 'Geenu Rei', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:20:59', '2026-03-12 23:20:59'),
(41, '8654543', 'Cortez', 'John Christian', '2nd Year', 'Bachelor of Secondary Education', '2026-03-12 23:21:48', '2026-03-12 23:21:48'),
(42, '3578909', 'Cortez', 'Robert  Iii', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:22:45', '2026-03-12 23:22:45'),
(43, '0542886', 'Dagaas', 'Jann Miko', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:23:33', '2026-03-12 23:23:33'),
(44, '9034776', 'Dagami', 'Crissel', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:24:00', '2026-03-12 23:24:00'),
(45, '4367889', 'Dajao', 'John Wayne', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:24:58', '2026-03-12 23:24:58'),
(46, '8764459', 'Dangli', 'Catlen', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:25:26', '2026-03-12 23:25:26'),
(47, '9458785', 'Dayanan', 'Hirome', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:25:47', '2026-03-12 23:25:47'),
(48, '9885643', 'De Gracia', 'Klint Iczer Brylle', '3rd Year', 'Bachelor of Human Services', '2026-03-12 23:27:20', '2026-03-12 23:27:20'),
(49, '8676767', 'De Guzman', 'Rhoda Manelle', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:28:00', '2026-03-12 23:28:00'),
(50, '0437462', 'Delalamon', 'Vince Lucky', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:28:44', '2026-03-12 23:28:44'),
(51, '8765789', 'Delebios', 'Ramina Nina', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:29:16', '2026-03-12 23:29:16'),
(52, '9095632', 'Devilleres', 'Trisha Mae', '3rd Year', 'Bachelor of Elementary Education', '2026-03-12 23:30:10', '2026-03-12 23:30:10'),
(53, '9856342', 'Diaceno', 'April Ann', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:30:40', '2026-03-12 23:30:40'),
(54, '1590563', 'Dumasig', 'Rhea', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:31:16', '2026-03-12 23:31:16'),
(55, '8993243', 'Ebihara', 'Justin George', '2nd Year', 'Bachelor of Human Services', '2026-03-12 23:32:00', '2026-03-12 23:32:38'),
(56, '7854564', 'Ecoy', 'Angel Regena Marie', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:33:34', '2026-03-12 23:33:34'),
(57, '0455292', 'Edic', 'Kristel Ann', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:33:57', '2026-03-12 23:33:57'),
(58, '0365788', 'Escober', 'Mark Justine', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-12 23:34:57', '2026-03-12 23:34:57'),
(59, '8486565', 'Escovilla', 'Tyra Ysabella', '1st Year', 'Bachelor of Elementary Education', '2026-03-12 23:35:57', '2026-03-12 23:35:57'),
(60, '7387785', 'Esperanza', 'Sigrid Myles', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:36:41', '2026-03-12 23:36:41'),
(61, '9547654', 'Estorco', 'Vheniz Jyne Myciala', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:38:07', '2026-03-12 23:38:07'),
(62, '1238909', 'Feraren', 'Shanice Bea', '2nd Year', 'Bachelor of Elementary Education', '2026-03-12 23:38:47', '2026-03-12 23:38:47'),
(63, '6969696', 'Fortich', 'Alchiea Zynn', '2nd Year', 'Bachelor of Secondary Education', '2026-03-12 23:40:12', '2026-03-12 23:40:12'),
(64, '7569076', 'Galendez', 'Kim', '2nd Year', 'Bachelor of Human Services', '2026-03-12 23:41:00', '2026-03-12 23:41:00'),
(65, '8547846', 'Gansad', 'Clare Beryl', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:42:02', '2026-03-12 23:42:02'),
(66, '0635544', 'Georpe', 'Princess Mae', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:42:36', '2026-03-12 23:42:36'),
(67, '6575465', 'Golimlim', 'Mathew Lorenz', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:43:34', '2026-03-12 23:43:34'),
(68, '8745743', 'Goubelle', 'Margelyne Maylis Sylene', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:44:48', '2026-03-12 23:44:48'),
(69, '5684903', 'Gubatina', 'Althea Nicole', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:45:19', '2026-03-12 23:45:19'),
(70, '6859403', 'Higuchi', 'Tsuyoshi', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:46:12', '2026-03-12 23:46:12'),
(71, '5789438', 'Hintay', 'Karyll Denz', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:46:46', '2026-03-12 23:46:46'),
(72, '8909656', 'Ingay', 'Princess Jean', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:47:39', '2026-03-12 23:47:39'),
(73, '4356436', 'Isawa', 'Ryusuke', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:48:26', '2026-03-12 23:48:26'),
(74, '1256489', 'Ishii', 'Salrosnymber', '2nd Year', 'Bachelor of Secondary Education', '2026-03-12 23:49:31', '2026-03-12 23:49:31'),
(75, '6554635', 'Ishizuka', 'Ryou', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:50:33', '2026-03-12 23:50:33'),
(76, '6895867', 'Jimenez', 'Cush Hebrew', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:51:18', '2026-03-12 23:51:18'),
(77, '8795678', 'Juico', 'Jaireen Gabrielle', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:52:29', '2026-03-12 23:52:29'),
(78, '9805689', 'Juliane', 'Trisa Jen', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:53:20', '2026-03-12 23:53:20'),
(79, '8943748', 'Jumawan', 'Gersen Joyce', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:53:49', '2026-03-12 23:53:49'),
(80, '6767899', 'Jumawan', 'Kim Tracia', '3rd Year', 'Bachelor of Human Services', '2026-03-12 23:55:50', '2026-03-12 23:55:50'),
(81, '8457837', 'Jumawid', 'Aliah Jenica', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:56:31', '2026-03-12 23:56:31'),
(82, '4785478', 'Kasa', 'Reina', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:57:40', '2026-03-12 23:57:40'),
(83, '7854236', 'Kawase', 'Frencess Kyle', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:58:27', '2026-03-12 23:58:27'),
(84, '8967762', 'Koike', 'Hanna Ryza', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:58:54', '2026-03-12 23:58:54'),
(85, '3438476', 'Labiste', 'Vince Jan', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-12 23:59:21', '2026-03-12 23:59:21'),
(86, '8732788', 'Lacaran', 'Jonalyn', '3rd Year', 'Bachelor of Elementary Education', '2026-03-12 23:59:43', '2026-03-12 23:59:43'),
(87, '7878845', 'Lahoy', 'Kelvey', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:00:21', '2026-03-13 00:00:21'),
(88, '9346356', 'Langbid', 'Cassandra', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:00:50', '2026-03-13 00:00:50'),
(89, '3436787', 'Lao', 'Mark John', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:01:24', '2026-03-13 00:01:24'),
(90, '6743546', 'Ledesma', 'Jenifer', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:02:05', '2026-03-13 00:02:05'),
(91, '7675656', 'Lim', 'Maria Alexa', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:02:24', '2026-03-13 00:02:24'),
(92, '9899876', 'Macbenta', 'Nestor Iii', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:03:22', '2026-03-13 00:03:22'),
(93, '7756567', 'Macaraig', 'Pamela', '2nd Year', 'Bachelor of Human Services', '2026-03-13 00:03:56', '2026-03-13 00:03:56'),
(94, '6534787', 'Madjos', 'Richard', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:04:36', '2026-03-13 00:04:36'),
(95, '7878675', 'Masupat', 'Shaina Maeh', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:05:31', '2026-03-13 00:05:31'),
(96, '3278097', 'Moalom', 'Leslie', '2nd Year', 'Bachelor of Human Services', '2026-03-13 00:06:05', '2026-03-13 00:06:05'),
(97, '3548789', 'Muniz', 'John Cristopher', '1st Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:06:39', '2026-03-13 00:06:39'),
(98, '3426346', 'Noya', 'Irish Mae', '3rd Year', 'Bachelor of Human Services', '2026-03-13 00:07:23', '2026-03-13 00:07:23'),
(99, '3256899', 'Nuqui', 'Jose Miguel', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:07:59', '2026-03-13 00:07:59'),
(100, '6765879', 'Ompoc', 'Rica Jeane', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:08:48', '2026-03-13 00:08:48'),
(101, '6356653', 'Opsima', 'Antoniette Ella', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:09:28', '2026-03-13 00:09:28'),
(102, '8965523', 'Orcajada', 'Princess Joy', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:10:02', '2026-03-13 00:10:02'),
(103, '9076543', 'Origenes', 'Gregory Hudson', '1st Year', 'Bachelor of Human Services', '2026-03-13 00:10:36', '2026-03-13 00:10:36'),
(104, '6543345', 'Origenes', 'Ma. Chrislyn', '1st Year', 'Bachelor of Human Services', '2026-03-13 00:11:41', '2026-03-13 00:11:41'),
(105, '3664365', 'Pacio', 'Cherry Mae', '2nd Year', 'Bachelor of Human Services', '2026-03-13 00:12:13', '2026-03-13 00:12:13'),
(106, '8956756', 'Paimalan', 'Loujiel Lynn', '2nd Year', 'Bachelor of Secondary Education', '2026-03-13 00:15:46', '2026-03-13 00:15:46'),
(107, '9568590', 'Paiso', 'Najievah', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:16:39', '2026-03-13 00:16:39'),
(108, '6097690', 'Palabrica', 'Chrisma Bianca', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:17:06', '2026-03-13 00:17:06'),
(109, '0978096', 'Papaya', 'Sophia', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:17:28', '2026-03-13 00:17:28'),
(110, '3874685', 'Papillero', 'Just Sebastian', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:18:14', '2026-03-13 00:18:14'),
(111, '6798549', 'Penafiel', 'Fretzie Clyde', '2nd Year', 'Bachelor of Human Services', '2026-03-13 00:19:14', '2026-03-13 00:19:14'),
(112, '1234567', 'Penaflorida', 'Mamiko', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:19:55', '2026-03-13 00:19:55'),
(113, '0908818', 'Peserla', 'Shielo', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:21:05', '2026-03-13 00:21:05'),
(114, '4567676', 'Pingkian', 'Michaela', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:21:48', '2026-03-13 00:21:48'),
(115, '4545676', 'Polentinos', 'Joross Mark', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:22:21', '2026-03-13 00:22:21'),
(116, '2345678', 'Rada', 'Czheskha Genese', '2nd Year', 'Bachelor of Special Needs Education', '2026-03-13 00:25:50', '2026-03-13 00:25:50'),
(117, '9876553', 'Regudo', 'Hayley Von', '2nd Year', 'Bachelor of Human Services', '2026-03-13 00:26:22', '2026-03-13 00:26:22'),
(118, '4678988', 'Relatado', 'Antonette', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:26:46', '2026-03-13 00:26:46'),
(119, '6666888', 'Reyes', 'Marjori Joy', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:27:10', '2026-03-13 00:27:10'),
(120, '6767890', 'Rojas', 'Kim Jullian', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:28:04', '2026-03-13 00:28:04'),
(121, '8585885', 'Roque', 'Leigh Gabrielle', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:30:30', '2026-03-13 00:30:30'),
(122, '5995959', 'Sahid', 'Eugene James', '2nd Year', 'Bachelor of Elementary Education', '2026-03-13 00:31:00', '2026-03-13 00:31:00'),
(123, '9999999', 'Sajili', 'Daisy Jeanne', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:31:52', '2026-03-13 00:31:52'),
(124, '1111111', 'Saldana', 'Cedrick Vince', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:32:19', '2026-03-13 00:32:19'),
(125, '2222222', 'Salomon', 'Millicent', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:33:19', '2026-03-13 00:33:19'),
(126, '3333333', 'Salvaleon', 'Leslie', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:34:01', '2026-03-13 00:34:01'),
(127, '4444444', 'Sanama', 'Reham', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:34:51', '2026-03-13 00:34:51'),
(128, '5555555', 'Santos', 'Andre Vash Miguel', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:35:23', '2026-03-13 00:35:23'),
(129, '6768690', 'Sareno', 'Honey Mae', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:35:55', '2026-03-13 00:35:55'),
(130, '6666666', 'Seprado', 'Romena June', '2nd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:36:34', '2026-03-13 00:36:34'),
(131, '5656788', 'Solinap', 'Vera', '3rd Year', 'Bachelor of Elementary Education', '2026-03-13 00:37:20', '2026-03-13 00:37:20'),
(132, '8888888', 'Sumampong', 'Earl Joseph', '3rd Year', 'Bachelor of Human Services', '2026-03-13 00:38:06', '2026-03-13 00:38:06'),
(133, '8888677', 'Suzuki', 'Tatsuya', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:38:40', '2026-03-13 00:38:40'),
(134, '4677666', 'Tababa', 'Kazuma', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:39:08', '2026-03-13 00:39:08'),
(135, '8889897', 'Takahashi', 'Ai', '2nd Year', 'Bachelor of Secondary Education', '2026-03-13 00:39:49', '2026-03-13 00:39:49'),
(136, '0987654', 'Tee', 'Ilesha Gabriel', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:40:11', '2026-03-13 00:40:11'),
(137, '4569877', 'Timogtimog', 'Jhan Alfred', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:40:37', '2026-03-13 00:40:37'),
(138, '8654565', 'Tsuruoka', 'Vince Carl', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:41:11', '2026-03-13 00:41:11'),
(139, '8654560', 'Uy', 'Denise Nicole', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:41:49', '2026-03-13 00:41:49'),
(140, '4556544', 'Velasco', 'Angela', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:42:09', '2026-03-13 00:42:09'),
(141, '7556555', 'Villadores', 'Alexis Matthew', '2nd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:42:35', '2026-03-13 00:42:35'),
(142, '7666766', 'Villarin', 'Quisia Nina', '3rd Year', 'Bachelor of Elementary Education', '2026-03-13 00:43:08', '2026-03-13 00:44:19'),
(143, '6666667', 'Villaver', 'Hanah Mae', '3rd Year', 'Bachelor of Secondary Education', '2026-03-13 00:43:50', '2026-03-13 00:43:50'),
(144, '0909090', 'Watanabe', 'Tetsuya', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:44:59', '2026-03-13 00:44:59'),
(145, '7676767', 'Watanabe', 'Tomoki', '1st Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:45:21', '2026-03-13 00:45:21'),
(146, '6666689', 'Yanoc', 'Xandrie', '3rd Year', 'Bachelor of Secondary Education', '2026-03-13 00:46:00', '2026-03-13 00:46:00'),
(147, '4546767', 'Yazima', 'John Paul', '3rd Year', 'Bachelor of Science in Information Systems', '2026-03-13 00:46:25', '2026-03-13 00:46:25'),
(148, '6676799', 'Zabala', 'Edwinson', '3rd Year', 'Bachelor of Arts in International Studies', '2026-03-13 00:46:57', '2026-03-13 00:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('logger','admin','super_admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'mkd-admin', 'Administrator Account', '$2y$12$3/cZZZODuFZyn1SP7ScdG.q2aqARBbo3CJBTzEIL927cHojyVP7ZG', 'admin', NULL, '2026-03-12 22:22:12', '2026-03-12 22:22:12'),
(2, 'kamisama', 'Super Administrator Account', '$2y$12$uxIjWI2NB7rdBbTAo6hMXu4hM9t9J9rBu373gSy2EofcNtIXvwMpe', 'super_admin', NULL, '2026-03-12 22:22:12', '2026-03-12 22:22:12'),
(3, 'mkd-logger', 'Logger Account', '$2y$12$NnE/ADXXxnbpMU7jAI7gL.RSL4gvo6Pza84433Es/fxtxa8k3DsoC', 'logger', NULL, '2026-03-12 22:22:12', '2026-03-12 22:22:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_records`
--
ALTER TABLE `log_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_records_student_id_time_in_index` (`student_id`,`time_in`),
  ADD KEY `log_records_log_session_id_time_in_index` (`log_session_id`,`time_in`);

--
-- Indexes for table `log_sessions`
--
ALTER TABLE `log_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `log_sessions_date_index` (`date`),
  ADD KEY `log_sessions_school_year_index` (`school_year`),
  ADD KEY `log_sessions_date_school_year_index` (`date`,`school_year`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school_year_settings`
--
ALTER TABLE `school_year_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_year_settings_school_year_unique` (`school_year`),
  ADD KEY `school_year_settings_school_year_index` (`school_year`),
  ADD KEY `school_year_settings_is_active_index` (`is_active`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_records`
--
ALTER TABLE `log_records`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_sessions`
--
ALTER TABLE `log_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `school_year_settings`
--
ALTER TABLE `school_year_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_records`
--
ALTER TABLE `log_records`
  ADD CONSTRAINT `log_records_log_session_id_foreign` FOREIGN KEY (`log_session_id`) REFERENCES `log_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
