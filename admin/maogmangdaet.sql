-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2025 at 05:46 PM
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
-- Database: `maogmangdaet`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `slug`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Power Interruption Advisory', 'power-interruption-advisory', 'The Local Government of Daet, through CANORECO, informs the public of a scheduled power interruption on October 2, 2025, from 8:00 AM to 5:00 PM. This is due to line maintenance and system upgrading. Affected barangays are encouraged to prepare accordingly.', NULL, '2025-09-28 15:51:08', '2025-09-28 15:51:08'),
(2, 'Road Closure Notice', 'road-closure-notice', 'Please be advised that portion of Vinzons Avenue (near the Municipal Plaza) will be temporarily closed on October 5, 2025, from 6:00 AM to 12:00 NN to give way for the fun run activity organized by the Municipal Youth Development Office. Motorists are advised to take alternate routes.', NULL, '2025-09-28 15:51:18', '2025-09-28 15:51:18'),
(3, 'Health Mission in Barangay Pamorangon', 'health-mission-in-barangay-pamorangon', 'The Local Government of Daet, in partnership with the Provincial Health Office, will conduct a Medical and Dental Mission on October 10, 2025, at Barangay Hall Pamorangon. Free consultations, dental check-ups, and basic medicines will be provided to residents.', NULL, '2025-09-28 15:51:29', '2025-09-28 15:51:29');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `ua` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `event`, `ip`, `ua`, `created_at`) VALUES
(1, 1, 'Session expired due to inactivity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:48:55'),
(2, 1, 'Successful login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:49:10'),
(3, 1, 'Created Announcements record #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:51:08'),
(4, 1, 'Created Announcements record #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:51:18'),
(5, 1, 'Created Announcements record #3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:51:29'),
(6, 1, 'Created Resolutions record #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:54:38'),
(7, 1, 'Created Resolutions record #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:55:05'),
(8, 1, 'Created Ordinances record #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:56:48'),
(9, 1, 'Created Ordinances record #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:57:08'),
(10, 1, 'Created Executive Issuances record #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:58:17'),
(11, 1, 'Created Executive Issuances record #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 15:58:36'),
(12, 1, 'Created Public Hearings record #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 16:00:12'),
(13, 1, 'Created Public Hearings record #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 16:00:26'),
(14, 1, 'Created news article #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 16:02:38'),
(15, 1, 'Created news article #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 16:07:07'),
(16, 1, 'Created news article #3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 16:08:45'),
(17, 1, 'Session expired due to inactivity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 17:02:27'),
(18, 1, 'Successful login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 20:46:20'),
(19, 1, 'Session expired due to inactivity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 22:01:40'),
(20, 1, 'Successful login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 22:02:12'),
(21, 1, 'Session expired due to inactivity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 22:55:47'),
(22, 1, 'Successful login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0', '2025-09-28 22:55:50');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_tickets`
--

CREATE TABLE `feedback_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `category` enum('Health','Permit','Social Services','Others') NOT NULL,
  `message` text NOT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_updates`
--

CREATE TABLE `feedback_updates` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') NOT NULL,
  `admin_response` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_attachments`
--

CREATE TABLE `feedback_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `executive_issuances`
--

CREATE TABLE `executive_issuances` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `executive_issuances`
--

INSERT INTO `executive_issuances` (`id`, `title`, `slug`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Executive Order No. 2025-01', 'executive-order-no-2025-01', 'An executive order creating the Municipal Task Force on Disaster Preparedness and Response.', 'uploads/20250928095817_c3187a0683bc0a57.pdf', '2025-09-28 15:58:17', '2025-09-28 15:58:17'),
(2, 'Executive Order No. 2025-02', 'executive-order-no-2025-02', 'An executive order implementing traffic rerouting scheme during major municipal events.', 'uploads/20250928095836_ea540c60f3c266a2.pdf', '2025-09-28 15:58:36', '2025-09-28 15:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `slug`, `content`, `image_path`, `created_at`, `updated_at`) VALUES
(1, '𝐌𝐀𝐍𝐆𝐑𝐎𝐕𝐄 𝐓𝐑𝐄𝐄 𝐏𝐋𝐀𝐍𝐓𝐈𝐍𝐆 𝐀𝐂𝐓𝐈𝐕𝐈𝐓𝐘', 'news', 'Isinagawa po natin ngayong umaga ang Mangrove Tree Planting Activity sa Bagasbas Beach katuwang ang Municipal Agriculture Office, Philippine Coast Guard, Municipal Rural Improvement Club (MRIC) at BAEW, bilang isang mahalagang hakbang para sa pangangalaga ng ating kapaligiran at kalikasan. Ang pagtatanim po ng bakawan ay napakahalaga sapagkat ito ay nagsisilbing depensa laban sa malalakas na alon at storm surge.\r\nSa pamamagitan ng pagtutulungan ng iba’t ibang sektor gaya ng LGU, Coast Guard, at mga organisasyon tulad ng MRIC at BAEW ipinapakita po natin ang sama-samang pagkilos tungo sa mas ligtas, mas malinis, at mas maunlad na bayan ng DAET. Ang ganitong inisyatibo ay nagsisilbing inspirasyon sa ating lahat upang maging mas responsable sa pangangalaga ng kalikasan para sa kasalukuyan at sa mga susunod na henerasyon.', '/admin/uploads/news/20250928100238_3e71ca55d0c3.png', '2025-09-28 16:02:38', '2025-09-28 16:02:38'),
(2, '𝐃𝐢𝐬𝐭𝐫𝐢𝐛𝐮𝐭𝐢𝐨𝐧 𝐨𝐟 𝐂𝐨𝐦𝐩𝐥𝐞𝐭𝐞 𝐅𝐞𝐫𝐭𝐢𝐥𝐢𝐳𝐞𝐫 𝐚𝐧𝐝 𝐔𝐫𝐞𝐚', 'istribution-of-omplete-ertilizer-and-rea', 'Bilang bahagi ng patuloy na suporta sa sektor ng agrikultura, isinagawa ng Lokal na Pamahalaan ng Daet ang unang pamamahagi ng kabuuang (900) fertilizers at urea sa ating mga magsasaka. Ang mga beneficiaries ngayong araw ay mula sa Brgy. Alawihao at Lag-on. Layunin ng programang ito na mapabuti ang ani, mapataas ang kita, at matiyak ang sapat na suplay ng pagkain para sa komunidad. \r\n\r\nKatuwang ang Municipal Agriculture Office, patuloy na magsasagawa ng mga ganitong inisyatiba ang LGU upang matulungan ang ating mga magsasaka.', '/admin/uploads/news/20250928100707_77c5b62d34fb.jpg', '2025-09-28 16:07:07', '2025-09-28 20:46:42'),
(3, '𝟏𝟐𝟓𝐓𝐇 𝐏𝐇𝐈𝐋𝐈𝐏𝐏𝐈𝐍𝐄 𝐂𝐈𝐕𝐈𝐋 𝐒𝐄𝐑𝐕𝐈𝐂𝐄 𝐀𝐍𝐍𝐈𝐕𝐄𝐑𝐒𝐀𝐑𝐘 𝐎𝐏𝐄𝐍𝐈𝐍𝐆 𝐏𝐀𝐑𝐀𝐃𝐄', '125', 'Opisyal pong binuksan kahapon ang pagdiriwang ng Ika-125 Anibersaryo ng Philippine Civil Service na may temang “Bawat Kawani, Lingkod Bayani: Puso, Dangal at Galing para sa Bayan.” Layunin ng temang ito na kilalanin at ipagdiwang ang dedikasyon at propesyonalismo ng mga lingkod-bayan. Kasama rin po natin sa aktibidad na ito sina Acting Governor Joseph Ascutia at Provincial Administator Don Padilla at iba’t-ibang sektor ng pamahalaan at mga kapwa natin lingkod bayan.', '/admin/uploads/news/20250928100845_64b254f4ad40.jpg', '2025-09-28 16:08:45', '2025-09-28 16:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `ordinances`
--

CREATE TABLE `ordinances` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ordinances`
--

INSERT INTO `ordinances` (`id`, `title`, `slug`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Ordinance No. 2025-101', 'ordinance-no-2025-101', 'An ordinance regulating the use of single-use plastics within the Municipality of Daet.', 'uploads/20250928095648_bd8be12a79905502.pdf', '2025-09-28 15:56:48', '2025-09-28 15:56:48'),
(2, 'Ordinance No. 2025-102', 'ordinance-no-2025-102', 'An ordinance establishing a curfew for minors within the Municipality of Daet.', 'uploads/20250928095708_a002093f2694d469.pdf', '2025-09-28 15:57:08', '2025-09-28 15:57:08');

-- --------------------------------------------------------

--
-- Table structure for table `public_hearings`
--

CREATE TABLE `public_hearings` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `public_hearings`
--

INSERT INTO `public_hearings` (`id`, `title`, `slug`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Public Hearing No. 2025-01', 'public-hearing-no-2025-01', 'A public hearing on the proposed increase of business permit fees within the Municipality of Daet.', 'uploads/20250928100012_33aaec8a0c10d6b4.pdf', '2025-09-28 16:00:12', '2025-09-28 16:00:12'),
(2, 'Public Hearing No. 2025-02', 'public-hearing-no-2025-02', 'A public hearing on the proposed zoning ordinance amendments in the Municipality of Daet.', 'uploads/20250928100026_f28f58d40a85d826.pdf', '2025-09-28 16:00:26', '2025-09-28 16:00:26');

-- --------------------------------------------------------

--
-- Table structure for table `resolutions`
--

CREATE TABLE `resolutions` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resolutions`
--

INSERT INTO `resolutions` (`id`, `title`, `slug`, `description`, `pdf_path`, `created_at`, `updated_at`) VALUES
(1, 'Resolution No. 2025-001', 'resolution-no-2025-001', 'A resolution approving the allocation of funds for the improvement of public parks within the Municipality of Daet.', 'uploads/20250928095438_4c875610e419a358.pdf', '2025-09-28 15:54:38', '2025-09-28 15:54:38'),
(2, 'Resolution No. 2025-002', 'resolution-no-2025-002', 'A resolution declaring October 15, 2025, as Municipal Clean-Up Day in Daet.', 'uploads/20250928095505_376263540b26a975.pdf', '2025-09-28 15:55:05', '2025-09-28 15:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `department` varchar(100) DEFAULT NULL,
  `failed_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `role`, `department`, `failed_attempts`, `locked_until`, `last_login_at`, `created_at`) VALUES
(1, 'admin@example.com', '$argon2id$v=19$m=65536,t=4,p=1$efv2ob26vUTg0OHRfyvqoA$x5+0+D6hvvHIHO22VzfUrRoVaDtD/P4xi+hxm2krRqk', 'admin', NULL, 0, NULL, '2025-09-28 22:55:50', '2025-09-28 15:48:30');
--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_tickets`
--
ALTER TABLE `feedback_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`);

--
-- Indexes for table `feedback_updates`
--
ALTER TABLE `feedback_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_updates_ticket_id_foreign` (`ticket_id`);

--
-- Indexes for table `feedback_attachments`
--
ALTER TABLE `feedback_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_attachments_ticket_id_foreign` (`ticket_id`);

--
-- Indexes for table `executive_issuances`
--
ALTER TABLE `executive_issuances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `ordinances`
--
ALTER TABLE `ordinances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `public_hearings`
--
ALTER TABLE `public_hearings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `resolutions`
--
ALTER TABLE `resolutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_tickets`
--
ALTER TABLE `feedback_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_updates`
--
ALTER TABLE `feedback_updates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_attachments`
--
ALTER TABLE `feedback_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `executive_issuances`
--
ALTER TABLE `executive_issuances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ordinances`
--
ALTER TABLE `ordinances`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `public_hearings`
--
ALTER TABLE `public_hearings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `resolutions`
--
ALTER TABLE `resolutions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
  ALTER TABLE `feedback_updates`
  ADD CONSTRAINT `fk_feedback_updates_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `feedback_tickets` (`id`) ON DELETE CASCADE;

ALTER TABLE `feedback_attachments`
  ADD CONSTRAINT `fk_feedback_attachments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `feedback_tickets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
