-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 04:01 PM
-- Server version: 8.0.44
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffeebean`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','branch_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'branch_admin',
  `branch_id` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `password`, `full_name`, `role`, `branch_id`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Pa$$word1', 'Beeg Admin', 'super_admin', NULL, 1, NULL, '2025-12-12 18:43:20', '2025-12-12 18:43:20');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branch_id` int UNSIGNED NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plus_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `map_embed` mediumtext COLLATE utf8mb4_unicode_ci,
  `hours_json` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branch_id`, `name`, `address`, `plus_code`, `phone`, `facebook_url`, `lat`, `lng`, `map_embed`, `hours_json`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Don Macchiatos Dasmariñas Cavite', '8XC7+MH7, Congressional Ave, Dasmariñas, Cavite', '8XC7+MH7, Dasmariñas, Cavite', NULL, NULL, 14.3216820, 120.9639198, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15463.294720365526!2d120.9448666871582!3d14.321672300000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d5007b70118f%3A0xc7e9110263ce1b50!2sDon%20Macchiatos%20Dasmari%C3%B1as%20Cavite!5e0!3m2!1sen!2sph!4v1765530899108!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–10 PM\", \"Monday\": \"10 AM–10 PM\", \"Sunday\": \"Closed\", \"Tuesday\": \"10 AM–10 PM\", \"Saturday\": \"10 AM–10 PM\", \"Thursday\": \"10 AM–10 PM\", \"Wednesday\": \"10 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(3, 'Don Macchiatos - GenTri, Cavite', 'Near Phoenix Station, 2F Will-rizz Bldg, F. Manalo Rd, General Trias, Cavite', '9VHQ+WM, General Trias, Cavite', '09923449778', 'https://www.facebook.com/profile.php?id=61581465937069', 14.3798316, 120.8891754, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15459.281322612642!2d120.87012898715818!3d14.379805000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d300784d459f%3A0x175059389b93a8b0!2sDon%20Macchiatos%20-%20GenTri%2C%20Cavite!5e0!3m2!1sen!2sph!4v1765530929128!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–8 PM\", \"Monday\": \"10 AM–8 PM\", \"Sunday\": \"Closed\", \"Tuesday\": \"10 AM–8 PM\", \"Saturday\": \"10 AM–8 PM\", \"Thursday\": \"10 AM–8 PM\", \"Wednesday\": \"10 AM–8 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(4, 'Don Macchiatos Muntinlupa', 'Parkhomes Subdivision, Golden Gate St, Muntinlupa, 1773 Metro Manila', '92FV+8J, Muntinlupa, Metro Manila', '09660487804', 'https://www.facebook.com/profile.php?id=61554258173210&mibextid=2JQ9oc', 14.3733281, 121.0441009, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15459.7319145413!2d121.02495928715821!3d14.373289799999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d1002536c06f%3A0x85b5f4a99b1284d4!2sDon%20Macchiatos%20Muntinlupa!5e0!3m2!1sen!2sph!4v1765531077660!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–10 PM\", \"Monday\": \"10 AM–10 PM\", \"Sunday\": \"10 AM–10 PM\", \"Tuesday\": \"10 AM–10 PM\", \"Saturday\": \"10 AM–10 PM\", \"Thursday\": \"10 AM–10 PM\", \"Wednesday\": \"10 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(5, 'Don Macchiatos - Dita Santa Rosa', '1715 Dita, City of Santa Rosa, 4026 Laguna', '74J6+XM, City of Santa Rosa, Laguna', '09989833901', NULL, 14.2824012, 121.1116276, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d61863.99468104935!2d121.03541534863282!3d14.282375899999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d9f8f44394c5%3A0x5e7ced6b2373734f!2sDon%20Macchiatos%20-%20Dita%20Santa%20Rosa!5e0!3m2!1sen!2sph!4v1765531139229!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–12 AM\", \"Monday\": \"5:30 AM–12 AM\", \"Sunday\": \"8 AM–12 AM\", \"Tuesday\": \"8 AM–12 AM\", \"Saturday\": \"8 AM–12 AM\", \"Thursday\": \"8 AM–12 AM\", \"Wednesday\": \"8 AM–12 AM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(6, 'Don Macchiatos Filinvest', 'Blk 3 Lot 13 Phase, 1 Filinvest W Ave, Tanza, Cavite', '8VC6+9M, Tanza, Cavite', NULL, NULL, 14.3209529, 120.8616729, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d469.47297288212087!2d120.86129780682722!3d14.320920975730258!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33962b005c5ad47d%3A0xe9d24c24dc541aa3!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531206237!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–10 PM\", \"Monday\": \"8 AM–10 PM\", \"Sunday\": \"8 AM–10 PM\", \"Tuesday\": \"8 AM–10 PM\", \"Saturday\": \"8 AM–10 PM\", \"Thursday\": \"8 AM–10 PM\", \"Wednesday\": \"8 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(7, 'Don Macchiatos Parañaque Branch', 'Dr Arcadio Santos Ave, Parañaque, 1700 Metro Manila', 'FXHX+M7, Parañaque, Metro Manila', '09497485585', NULL, 14.4792593, 120.9981332, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d482.88687225758247!2d120.99756010091177!3d14.479235300000012!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cfef44d68029%3A0xcce8ee3d190a2912!2sDon%20Macchiatos%20Para%C3%B1aque%20Branch!5e0!3m2!1sen!2sph!4v1765531231847!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–8:30 PM\", \"Monday\": \"10 AM–8:30 PM\", \"Sunday\": \"Closed\", \"Tuesday\": \"10 AM–8:30 PM\", \"Saturday\": \"10 AM–8:30 PM\", \"Thursday\": \"10 AM–8:30 PM\", \"Wednesday\": \"10 AM–8:30 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(8, 'Don Macchiatos - Pasig Rotonda', '104 GF, RNC Building, Dr. Sixto Antonio Ave., Pasig, 1606 Metro Manila', 'H38G+FG, Pasig, Metro Manila', '09992280359', NULL, 14.5662110, 121.0763267, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d482.6971006746077!2d121.0757030495987!3d14.566176600000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9bf49b5baa1%3A0x51361c668c867c37!2sDon%20Macchiatos%20-%20Pasig%20Rotonda!5e0!3m2!1sen!2sph!4v1765531371604!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–11:59 PM\", \"Monday\": \"10 AM–11:59 PM\", \"Sunday\": \"10 AM–11:59 PM\", \"Tuesday\": \"10 AM–11:59 PM\", \"Saturday\": \"10 AM–11:59 PM\", \"Thursday\": \"10 AM–11:59 PM\", \"Wednesday\": \"10 AM–11:59 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(9, 'Don Macchiatos Sampaloc', '2225 Legarda St, Corner Gastambide St., Sampaloc, Manila', 'JX2R+8G, Manila, Metro Manila', NULL, NULL, 14.6008424, 120.9912555, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d788.561847853491!2d120.99058281361621!3d14.600917641086426!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9000382c24d%3A0xeebf16cb4a3238ff!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531420625!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(10, 'Don Macchiatos — San Andres Bukid Branch', '1760 A. Francisco St, San Andres Bukid, Manila, 1009 Metro Manila', 'H2F3+XW, Manila, Metro Manila', NULL, 'https://www.facebook.com/share/1BbCGKBKB4/?mibextid=wwXIfr', 14.5750261, 121.0047992, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1987.2824363506197!2d121.0032366154127!3d14.575317337594331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c90010f95eaf%3A0x6242fe962f15d0ff!2sDon%20Macchiatos%20%E2%80%94%20San%20Andres%20Bukid%20Branch!5e0!3m2!1sen!2sph!4v1765531458086!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"9 AM–12 AM\", \"Monday\": \"9 AM–11:30 PM\", \"Sunday\": \"9 AM–12 AM\", \"Tuesday\": \"9 AM–11:30 PM\", \"Saturday\": \"9 AM–12 AM\", \"Thursday\": \"9 AM–11:30 PM\", \"Wednesday\": \"9 AM–11:30 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(11, 'Don Macchiatos Tondo Wagas Market', '349 Wagas St, Corner P. Herrera 1st St, Tondo, Manila, 1012 Metro Manila', 'JX47+5Q, Manila, Metro Manila', NULL, 'https://www.facebook.com/profile.php?id=61561160578797', 14.6054969, 120.9644011, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.4440234241554!2d120.96197640624673!3d14.605452799999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cb00225636eb%3A0x22832b0bef02251b!2sDon%20Macchiatos%20Tondo%20Wagas%20Market!5e0!3m2!1sen!2sph!4v1765531514007!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–2 AM\", \"Monday\": \"10 AM–1 AM\", \"Sunday\": \"10 AM–2 AM\", \"Tuesday\": \"10 AM–1 AM\", \"Saturday\": \"10 AM–2 AM\", \"Thursday\": \"10 AM–1 AM\", \"Wednesday\": \"10 AM–1 AM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(12, 'Don Macchiatos - Intramuros, Manila', 'Sta. Potenciana, corner Magallanes St, Intramuros, Manila, 1002 Metro Manila', 'HXQG+RQ, Manila, Metro Manila', NULL, 'https://www.facebook.com/profile.php?id=61552318430052', 14.5896531, 120.9769973, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.5833269055686!2d120.97454039839477!3d14.589577799999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cb33848b5559%3A0x4249ac249a86785c!2sDon%20Macchiatos%20-%20Intramuros%2C%20Manila!5e0!3m2!1sen!2sph!4v1765531549331!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–10 PM\", \"Monday\": \"10 AM–10 PM\", \"Sunday\": \"11 AM–11 PM\", \"Tuesday\": \"10 AM–10 PM\", \"Saturday\": \"11 AM–11 PM\", \"Thursday\": \"10 AM–10 PM\", \"Wednesday\": \"10 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(13, 'Don Macchiatos Mandaluyong City', '83 Rt. Rev. G. Aglipay, Mandaluyong City, Metro Manila', NULL, '09275304193', 'https://www.facebook.com/profile.php?id=61553857520479', 14.5861954, 121.0259764, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.6133294778801!2d121.02354089839477!3d14.586156500000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9edda903c53%3A0xbf2d7552c552c0a9!2sDon%20Macchiatos%20Mandaluyong%20City!5e0!3m2!1sen!2sph!4v1765531577222!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10:30 AM–1 AM\", \"Monday\": \"10:30 AM–12:30 AM\", \"Sunday\": \"10:30 AM–12:30 AM\", \"Tuesday\": \"10:30 AM–12:30 AM\", \"Saturday\": \"10:30 AM–12:30 AM\", \"Thursday\": \"10:30 AM–1 AM\", \"Wednesday\": \"10:30 AM–12:30 AM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(14, 'Don Macchiatos - Cabuyao Laguna', 'Blk 13, Lot 37 Katapatan Rd, Cabuyao City, 4025 Laguna', '744H+CJ, Cabuyao City, Laguna', '09989833901', NULL, 14.2561308, 121.1290343, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1933.4758452487893!2d121.12665789839478!3d14.25603980000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd63df283d0de5%3A0x981d522b4f2acb95!2sDon%20Macchiatos%20-%20Cabuyao%20Laguna!5e0!3m2!1sen!2sph!4v1765531612660!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–8 PM\", \"Monday\": \"8 AM–8 PM\", \"Sunday\": \"8 AM–8 PM\", \"Tuesday\": \"8 AM–8 PM\", \"Saturday\": \"8 AM–8 PM\", \"Thursday\": \"8 AM–8 PM\", \"Wednesday\": \"8 AM–8 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(15, 'Don Macchiatos New Panaderos', '567 New Panaderos, Manila, 1550 Metro Manila', 'H2RF+9R, Manila, Metro Manila', NULL, NULL, 14.5910477, 121.0246087, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.5709583667503!2d121.02223759839478!3d14.59098800000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c90d1fd98b65%3A0xf64724f088922163!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531664682!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"9 AM–9 PM\", \"Monday\": \"9 AM–9 PM\", \"Sunday\": \"8 AM–9 PM\", \"Tuesday\": \"9 AM–9 PM\", \"Saturday\": \"8 AM–9 PM\", \"Thursday\": \"9 AM–9 PM\", \"Wednesday\": \"9 AM–9 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(16, 'Don Macchiatos Taft Avenue', '984, 1000 Taft Ave, Ermita, Manila, 1000 Metro Manila', 'HXMM+JJ, Manila, Metro Manila', NULL, NULL, 14.5841775, 120.9840601, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.631704878636!2d120.98165149839477!3d14.584060700000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397cb0019b6c0d7%3A0x5f6b4cd720397442!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531700888!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(17, 'Don Macchiatos - Cogeo Antipolo', 'Barangay, upper sto nino, No. 22 Lucban Road, Sta. Cruz, Antipolo, 1870 Rizal', 'J589+MW, Antipolo, Rizal', NULL, NULL, 14.6168327, 121.1697933, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1986.9074830088725!2d121.16824173512754!3d14.616829991218493!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b900781095eb%3A0x90fa5ff2f516198e!2sDon%20Macchiatos%20-%20Cogeo%20Antipolo!5e0!3m2!1sen!2sph!4v1765531740356!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–8 PM\", \"Monday\": \"8 AM–8 PM\", \"Sunday\": \"8 AM–8 PM\", \"Tuesday\": \"8 AM–8 PM\", \"Saturday\": \"8 AM–8 PM\", \"Thursday\": \"8 AM–8 PM\", \"Wednesday\": \"8 AM–8 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(18, 'Don Macchiatos West Zamora', 'HXMX+GFC, W Zamora St, Pandacan, Manila, Metro Manila', 'HXMX+GFC, Manila, Metro Manila', NULL, NULL, 14.5839398, 120.9986439, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.633971156926!2d120.99617630629736!3d14.583802200000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9005679a95d%3A0x83a8eb30ee45078f!2sDon%20Macchiatos%20West%20Zamora!5e0!3m2!1sen!2sph!4v1765531770033!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(19, 'Don Macchiatos Target Street', 'G3W4+3M8, Target Street, Taguig, Metro Manila', NULL, NULL, 'https://www.facebook.com/DonMacchiatosStaffhousePembo', 14.5452317, 121.0566838, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.9722471298064!2d121.05430739839477!3d14.5451668!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9007fc317b7%3A0x6a15fe354f972e89!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531814519!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"9 AM–5 PM\", \"Monday\": \"9 AM–10 PM\", \"Sunday\": \"9 AM–10 PM\", \"Tuesday\": \"9 AM–10 PM\", \"Saturday\": \"6–10 PM\", \"Thursday\": \"9 AM–10 PM\", \"Wednesday\": \"9 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(20, 'Don Macchiatos Pinagsama Taguig', 'Lot 1, Block 54 Pinagsama Phase 2, Taguig, Metro Manila', 'G3G4+CM, Taguig, Metro Manila', NULL, NULL, 14.5261680, 121.0566615, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1931.1390193489744!2d121.05426899839478!3d14.52608230000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9006d474d51%3A0x3b34fcfe3378112d!2sDon%20Macchiatos!5e0!3m2!1sen!2sph!4v1765531848747!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–11 PM\", \"Monday\": \"8 AM–11 PM\", \"Sunday\": \"8 AM–11 PM\", \"Tuesday\": \"8 AM–11 PM\", \"Saturday\": \"8 AM–11 PM\", \"Thursday\": \"8 AM–11 PM\", \"Wednesday\": \"8 AM–11 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(21, 'DON MACCHIATOS Dominga Malate', '1004 Dominga Street, Malate, Manila, 1004 Metro Manila', 'HX6W+WJ, Manila, Metro Manila', NULL, NULL, 14.5623807, 120.9965237, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.8223423322356!2d120.99420629839479!3d14.562300200000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9006421ab4b%3A0x6cc8a1bf5728af8a!2sDON%20MACCHIATOS!5e0!3m2!1sen!2sph!4v1765531883609!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(22, 'Don Macchiatos-Marikina Branch', 'Champaca, corner East Dr, Marikina, 1800 Metro Manila', 'M429+J4, Marikina, Metro Manila', '09958493249', NULL, 14.6516006, 121.1179168, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.0388054848813!2d121.11547059839477!3d14.651535700000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b96f4566275b%3A0x6ffdf6773980222c!2sDon%20Macchiatos-Marikina%20Branch!5e0!3m2!1sen!2sph!4v1765531905026!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"12 PM–12 AM\", \"Monday\": \"12 PM–12 AM\", \"Sunday\": \"Closed\", \"Tuesday\": \"12 PM–12 AM\", \"Saturday\": \"12 PM–12 AM\", \"Thursday\": \"12 PM–12 AM\", \"Wednesday\": \"12:30 PM–12 AM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(23, 'Don Macchiatos MCU - Morning Breeze', 'Loreto Street, corner Paz (Caloocan, Metro Manila)', 'MX5Q+4P, Caloocan, Metro Manila', '09622678876', 'https://www.facebook.com/DonMacchiatosMCUMorningBreeze?mibextid=kFxxJD', 14.6580064, 120.9893308, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1929.9830643703344!2d120.98692749839476!3d14.657863699999988!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b70055232135%3A0x16612925bbe83240!2sDon%20Macchiatos%20MCU%20-%20Morning%20Breeze!5e0!3m2!1sen!2sph!4v1765531946952!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–11 PM\", \"Monday\": \"8 AM–11 PM\", \"Sunday\": \"8 AM–11 PM\", \"Tuesday\": \"8 AM–11 PM\", \"Saturday\": \"8 AM–11 PM\", \"Thursday\": \"8 AM–11 PM\", \"Wednesday\": \"8 AM–11 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(24, 'Don Macchiatos-Antipolo', 'M.L. Quezon, corner M. Gatlabayan St, Antipolo, 1870 Rizal', 'H5JG+R8, Antipolo, Rizal', NULL, NULL, 14.5821937, 121.1757705, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.6492782457462!2d121.1734154983948!3d14.582056100000008!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397bf6a09a14f7d%3A0x13661247decba3fc!2sDon%20Macchiatos-Antipolo!5e0!3m2!1sen!2sph!4v1765532060121!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"8 AM–10 PM\", \"Monday\": \"6 AM–10 PM\", \"Sunday\": \"8 AM–10 PM\", \"Tuesday\": \"6 AM–10 PM\", \"Saturday\": \"8 AM–10 PM\", \"Thursday\": \"8 AM–10 PM\", \"Wednesday\": \"8 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(25, 'Don Macchiatos - Fairview Branch', 'M3W8+V7R, Omega Ave, Novaliches, Quezon City, 1118 Metro Manila', NULL, NULL, NULL, 14.6973473, 121.0656833, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1929.6357205765594!2d121.06326929839477!3d14.697235799999993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1003db8bb93%3A0x7e8b7b02399d580e!2sDon%20Macchiatos%20-%20Fairview%20Branch!5e0!3m2!1sen!2sph!4v1765532119225!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–10 PM\", \"Monday\": \"10 AM–10 PM\", \"Sunday\": \"10 AM–10 PM\", \"Tuesday\": \"10 AM–10 PM\", \"Saturday\": \"10 AM–10 PM\", \"Thursday\": \"10 AM–10 PM\", \"Wednesday\": \"10 AM–10 PM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(26, 'Don Macchiatos - Bagbag Novaliches', 'M2VH+7XQ, Quezon City, Metro Manila', NULL, NULL, NULL, 14.6933157, 121.0299445, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1929.671238587107!2d121.02760559839479!3d14.693214500000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1002807c6c7%3A0x2330290bed856b61!2sDon%20Macchiatos%20-%20Bagbag%20Novaliches!5e0!3m2!1sen!2sph!4v1765532147963!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55'),
(27, 'Don Macchiatos - Quezon Street Tondo Branch', '106 Quezon St, Tondo, Manila, Metro Manila', 'JX87+62, Manila, Metro Manila', NULL, 'https://www.facebook.com/profile.php?id=61567223434272', 14.6156535, 120.9624371, '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.3551402120127!2d120.96013039839475!3d14.615573100000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b50023eeb43d%3A0xc27e3e2e7d13741e!2sDon%20Macchiatos%20-%20Quezon%20Street%20Tondo%20Branch!5e0!3m2!1sen!2sph!4v1765532170182!5m2!1sen!2sph\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '{\"Friday\": \"10 AM–1 AM\", \"Monday\": \"10 AM–12 AM\", \"Sunday\": \"10 AM–1 AM\", \"Tuesday\": \"10 AM–12 AM\", \"Saturday\": \"10 AM–1 AM\", \"Thursday\": \"10 AM–12 AM\", \"Wednesday\": \"10 AM–12 AM\"}', 1, '2025-12-12 18:47:55', '2025-12-12 18:47:55');

-- --------------------------------------------------------

--
-- Table structure for table `branch_product_availability`
--

CREATE TABLE `branch_product_availability` (
  `branch_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `updated_by` int UNSIGNED DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_product_availability`
--

INSERT INTO `branch_product_availability` (`branch_id`, `product_id`, `is_available`, `updated_by`, `updated_at`) VALUES
(2, 1, 1, NULL, '2025-12-12 18:48:33'),
(2, 2, 1, NULL, '2025-12-12 18:48:33'),
(2, 3, 1, NULL, '2025-12-12 18:48:33'),
(2, 4, 1, NULL, '2025-12-12 18:48:33'),
(2, 5, 1, NULL, '2025-12-12 18:48:33'),
(2, 6, 1, NULL, '2025-12-12 18:48:33'),
(2, 7, 1, NULL, '2025-12-12 18:48:33'),
(2, 8, 1, NULL, '2025-12-12 18:48:33'),
(2, 9, 1, NULL, '2025-12-12 18:48:33'),
(2, 10, 1, NULL, '2025-12-12 18:48:33'),
(2, 11, 1, NULL, '2025-12-12 18:48:33'),
(2, 12, 1, NULL, '2025-12-12 18:48:33'),
(3, 1, 1, NULL, '2025-12-12 18:48:33'),
(3, 2, 1, NULL, '2025-12-12 18:48:33'),
(3, 3, 1, NULL, '2025-12-12 18:48:33'),
(3, 4, 1, NULL, '2025-12-12 18:48:33'),
(3, 5, 1, NULL, '2025-12-12 18:48:33'),
(3, 6, 1, NULL, '2025-12-12 18:48:33'),
(3, 7, 1, NULL, '2025-12-12 18:48:33'),
(3, 8, 1, NULL, '2025-12-12 18:48:33'),
(3, 9, 1, NULL, '2025-12-12 18:48:33'),
(3, 10, 1, NULL, '2025-12-12 18:48:33'),
(3, 11, 1, NULL, '2025-12-12 18:48:33'),
(3, 12, 1, NULL, '2025-12-12 18:48:33'),
(4, 1, 1, NULL, '2025-12-12 18:48:33'),
(4, 2, 1, NULL, '2025-12-12 18:48:33'),
(4, 3, 1, NULL, '2025-12-12 18:48:33'),
(4, 4, 1, NULL, '2025-12-12 18:48:33'),
(4, 5, 1, NULL, '2025-12-12 18:48:33'),
(4, 6, 1, NULL, '2025-12-12 18:48:33'),
(4, 7, 1, NULL, '2025-12-12 18:48:33'),
(4, 8, 1, NULL, '2025-12-12 18:48:33'),
(4, 9, 1, NULL, '2025-12-12 18:48:33'),
(4, 10, 1, NULL, '2025-12-12 18:48:33'),
(4, 11, 1, NULL, '2025-12-12 18:48:33'),
(4, 12, 1, NULL, '2025-12-12 18:48:33'),
(5, 1, 1, NULL, '2025-12-12 18:48:33'),
(5, 2, 1, NULL, '2025-12-12 18:48:33'),
(5, 3, 1, NULL, '2025-12-12 18:48:33'),
(5, 4, 1, NULL, '2025-12-12 18:48:33'),
(5, 5, 1, NULL, '2025-12-12 18:48:33'),
(5, 6, 1, NULL, '2025-12-12 18:48:33'),
(5, 7, 1, NULL, '2025-12-12 18:48:33'),
(5, 8, 1, NULL, '2025-12-12 18:48:33'),
(5, 9, 1, NULL, '2025-12-12 18:48:33'),
(5, 10, 1, NULL, '2025-12-12 18:48:33'),
(5, 11, 1, NULL, '2025-12-12 18:48:33'),
(5, 12, 1, NULL, '2025-12-12 18:48:33'),
(6, 1, 1, NULL, '2025-12-12 18:48:33'),
(6, 2, 1, NULL, '2025-12-12 18:48:33'),
(6, 3, 1, NULL, '2025-12-12 18:48:33'),
(6, 4, 1, NULL, '2025-12-12 18:48:33'),
(6, 5, 1, NULL, '2025-12-12 18:48:33'),
(6, 6, 1, NULL, '2025-12-12 18:48:33'),
(6, 7, 1, NULL, '2025-12-12 18:48:33'),
(6, 8, 1, NULL, '2025-12-12 18:48:33'),
(6, 9, 1, NULL, '2025-12-12 18:48:33'),
(6, 10, 1, NULL, '2025-12-12 18:48:33'),
(6, 11, 1, NULL, '2025-12-12 18:48:33'),
(6, 12, 1, NULL, '2025-12-12 18:48:33'),
(7, 1, 1, NULL, '2025-12-12 18:48:33'),
(7, 2, 1, NULL, '2025-12-12 18:48:33'),
(7, 3, 1, NULL, '2025-12-12 18:48:33'),
(7, 4, 1, NULL, '2025-12-12 18:48:33'),
(7, 5, 1, NULL, '2025-12-12 18:48:33'),
(7, 6, 1, NULL, '2025-12-12 18:48:33'),
(7, 7, 1, NULL, '2025-12-12 18:48:33'),
(7, 8, 1, NULL, '2025-12-12 18:48:33'),
(7, 9, 1, NULL, '2025-12-12 18:48:33'),
(7, 10, 1, NULL, '2025-12-12 18:48:33'),
(7, 11, 1, NULL, '2025-12-12 18:48:33'),
(7, 12, 1, NULL, '2025-12-12 18:48:33'),
(8, 1, 1, NULL, '2025-12-12 18:48:33'),
(8, 2, 1, NULL, '2025-12-12 18:48:33'),
(8, 3, 1, NULL, '2025-12-12 18:48:33'),
(8, 4, 1, NULL, '2025-12-12 18:48:33'),
(8, 5, 1, NULL, '2025-12-12 18:48:33'),
(8, 6, 1, NULL, '2025-12-12 18:48:33'),
(8, 7, 1, NULL, '2025-12-12 18:48:33'),
(8, 8, 1, NULL, '2025-12-12 18:48:33'),
(8, 9, 1, NULL, '2025-12-12 18:48:33'),
(8, 10, 1, NULL, '2025-12-12 18:48:33'),
(8, 11, 1, NULL, '2025-12-12 18:48:33'),
(8, 12, 1, NULL, '2025-12-12 18:48:33'),
(9, 1, 1, NULL, '2025-12-12 18:48:33'),
(9, 2, 1, NULL, '2025-12-12 18:48:33'),
(9, 3, 1, NULL, '2025-12-12 18:48:33'),
(9, 4, 1, NULL, '2025-12-12 18:48:33'),
(9, 5, 1, NULL, '2025-12-12 18:48:33'),
(9, 6, 1, NULL, '2025-12-12 18:48:33'),
(9, 7, 1, NULL, '2025-12-12 18:48:33'),
(9, 8, 1, NULL, '2025-12-12 18:48:33'),
(9, 9, 1, NULL, '2025-12-12 18:48:33'),
(9, 10, 1, NULL, '2025-12-12 18:48:33'),
(9, 11, 1, NULL, '2025-12-12 18:48:33'),
(9, 12, 1, NULL, '2025-12-12 18:48:33'),
(10, 1, 1, NULL, '2025-12-12 18:48:33'),
(10, 2, 1, NULL, '2025-12-12 18:48:33'),
(10, 3, 1, NULL, '2025-12-12 18:48:33'),
(10, 4, 1, NULL, '2025-12-12 18:48:33'),
(10, 5, 1, NULL, '2025-12-12 18:48:33'),
(10, 6, 1, NULL, '2025-12-12 18:48:33'),
(10, 7, 1, NULL, '2025-12-12 18:48:33'),
(10, 8, 1, NULL, '2025-12-12 18:48:33'),
(10, 9, 1, NULL, '2025-12-12 18:48:33'),
(10, 10, 1, NULL, '2025-12-12 18:48:33'),
(10, 11, 1, NULL, '2025-12-12 18:48:33'),
(10, 12, 1, NULL, '2025-12-12 18:48:33'),
(11, 1, 1, NULL, '2025-12-12 18:48:33'),
(11, 2, 1, NULL, '2025-12-12 18:48:33'),
(11, 3, 1, NULL, '2025-12-12 18:48:33'),
(11, 4, 1, NULL, '2025-12-12 18:48:33'),
(11, 5, 1, NULL, '2025-12-12 18:48:33'),
(11, 6, 1, NULL, '2025-12-12 18:48:33'),
(11, 7, 1, NULL, '2025-12-12 18:48:33'),
(11, 8, 1, NULL, '2025-12-12 18:48:33'),
(11, 9, 1, NULL, '2025-12-12 18:48:33'),
(11, 10, 1, NULL, '2025-12-12 18:48:33'),
(11, 11, 1, NULL, '2025-12-12 18:48:33'),
(11, 12, 1, NULL, '2025-12-12 18:48:33'),
(12, 1, 1, NULL, '2025-12-12 18:48:33'),
(12, 2, 1, NULL, '2025-12-12 18:48:33'),
(12, 3, 1, NULL, '2025-12-12 18:48:33'),
(12, 4, 1, NULL, '2025-12-12 18:48:33'),
(12, 5, 1, NULL, '2025-12-12 18:48:33'),
(12, 6, 1, NULL, '2025-12-12 18:48:33'),
(12, 7, 1, NULL, '2025-12-12 18:48:33'),
(12, 8, 1, NULL, '2025-12-12 18:48:33'),
(12, 9, 1, NULL, '2025-12-12 18:48:33'),
(12, 10, 1, NULL, '2025-12-12 18:48:33'),
(12, 11, 1, NULL, '2025-12-12 18:48:33'),
(12, 12, 1, NULL, '2025-12-12 18:48:33'),
(13, 1, 1, NULL, '2025-12-12 18:48:33'),
(13, 2, 1, NULL, '2025-12-12 18:48:33'),
(13, 3, 1, NULL, '2025-12-12 18:48:33'),
(13, 4, 1, NULL, '2025-12-12 18:48:33'),
(13, 5, 1, NULL, '2025-12-12 18:48:33'),
(13, 6, 1, NULL, '2025-12-12 18:48:33'),
(13, 7, 1, NULL, '2025-12-12 18:48:33'),
(13, 8, 1, NULL, '2025-12-12 18:48:33'),
(13, 9, 1, NULL, '2025-12-12 18:48:33'),
(13, 10, 1, NULL, '2025-12-12 18:48:33'),
(13, 11, 1, NULL, '2025-12-12 18:48:33'),
(13, 12, 1, NULL, '2025-12-12 18:48:33'),
(14, 1, 1, NULL, '2025-12-12 18:48:33'),
(14, 2, 1, NULL, '2025-12-12 18:48:33'),
(14, 3, 1, NULL, '2025-12-12 18:48:33'),
(14, 4, 1, NULL, '2025-12-12 18:48:33'),
(14, 5, 1, NULL, '2025-12-12 18:48:33'),
(14, 6, 1, NULL, '2025-12-12 18:48:33'),
(14, 7, 1, NULL, '2025-12-12 18:48:33'),
(14, 8, 1, NULL, '2025-12-12 18:48:33'),
(14, 9, 1, NULL, '2025-12-12 18:48:33'),
(14, 10, 1, NULL, '2025-12-12 18:48:33'),
(14, 11, 1, NULL, '2025-12-12 18:48:33'),
(14, 12, 1, NULL, '2025-12-12 18:48:33'),
(15, 1, 1, NULL, '2025-12-12 18:48:33'),
(15, 2, 1, NULL, '2025-12-12 18:48:33'),
(15, 3, 1, NULL, '2025-12-12 18:48:33'),
(15, 4, 1, NULL, '2025-12-12 18:48:33'),
(15, 5, 1, NULL, '2025-12-12 18:48:33'),
(15, 6, 1, NULL, '2025-12-12 18:48:33'),
(15, 7, 1, NULL, '2025-12-12 18:48:33'),
(15, 8, 1, NULL, '2025-12-12 18:48:33'),
(15, 9, 1, NULL, '2025-12-12 18:48:33'),
(15, 10, 1, NULL, '2025-12-12 18:48:33'),
(15, 11, 1, NULL, '2025-12-12 18:48:33'),
(15, 12, 1, NULL, '2025-12-12 18:48:33'),
(16, 1, 1, NULL, '2025-12-12 18:48:33'),
(16, 2, 1, NULL, '2025-12-12 18:48:33'),
(16, 3, 1, NULL, '2025-12-12 18:48:33'),
(16, 4, 1, NULL, '2025-12-12 18:48:33'),
(16, 5, 1, NULL, '2025-12-12 18:48:33'),
(16, 6, 1, NULL, '2025-12-12 18:48:33'),
(16, 7, 1, NULL, '2025-12-12 18:48:33'),
(16, 8, 1, NULL, '2025-12-12 18:48:33'),
(16, 9, 1, NULL, '2025-12-12 18:48:33'),
(16, 10, 1, NULL, '2025-12-12 18:48:33'),
(16, 11, 1, NULL, '2025-12-12 18:48:33'),
(16, 12, 1, NULL, '2025-12-12 18:48:33'),
(17, 1, 1, NULL, '2025-12-12 18:48:33'),
(17, 2, 1, NULL, '2025-12-12 18:48:33'),
(17, 3, 1, NULL, '2025-12-12 18:48:33'),
(17, 4, 1, NULL, '2025-12-12 18:48:33'),
(17, 5, 1, NULL, '2025-12-12 18:48:33'),
(17, 6, 1, NULL, '2025-12-12 18:48:33'),
(17, 7, 1, NULL, '2025-12-12 18:48:33'),
(17, 8, 1, NULL, '2025-12-12 18:48:33'),
(17, 9, 1, NULL, '2025-12-12 18:48:33'),
(17, 10, 1, NULL, '2025-12-12 18:48:33'),
(17, 11, 1, NULL, '2025-12-12 18:48:33'),
(17, 12, 1, NULL, '2025-12-12 18:48:33'),
(18, 1, 1, NULL, '2025-12-12 18:48:33'),
(18, 2, 1, NULL, '2025-12-12 18:48:33'),
(18, 3, 1, NULL, '2025-12-12 18:48:33'),
(18, 4, 1, NULL, '2025-12-12 18:48:33'),
(18, 5, 1, NULL, '2025-12-12 18:48:33'),
(18, 6, 1, NULL, '2025-12-12 18:48:33'),
(18, 7, 1, NULL, '2025-12-12 18:48:33'),
(18, 8, 1, NULL, '2025-12-12 18:48:33'),
(18, 9, 1, NULL, '2025-12-12 18:48:33'),
(18, 10, 1, NULL, '2025-12-12 18:48:33'),
(18, 11, 1, NULL, '2025-12-12 18:48:33'),
(18, 12, 1, NULL, '2025-12-12 18:48:33'),
(19, 1, 1, NULL, '2025-12-12 18:48:33'),
(19, 2, 1, NULL, '2025-12-12 18:48:33'),
(19, 3, 1, NULL, '2025-12-12 18:48:33'),
(19, 4, 1, NULL, '2025-12-12 18:48:33'),
(19, 5, 1, NULL, '2025-12-12 18:48:33'),
(19, 6, 1, NULL, '2025-12-12 18:48:33'),
(19, 7, 1, NULL, '2025-12-12 18:48:33'),
(19, 8, 1, NULL, '2025-12-12 18:48:33'),
(19, 9, 1, NULL, '2025-12-12 18:48:33'),
(19, 10, 1, NULL, '2025-12-12 18:48:33'),
(19, 11, 1, NULL, '2025-12-12 18:48:33'),
(19, 12, 1, NULL, '2025-12-12 18:48:33'),
(20, 1, 1, NULL, '2025-12-12 18:48:33'),
(20, 2, 1, NULL, '2025-12-12 18:48:33'),
(20, 3, 1, NULL, '2025-12-12 18:48:33'),
(20, 4, 1, NULL, '2025-12-12 18:48:33'),
(20, 5, 1, NULL, '2025-12-12 18:48:33'),
(20, 6, 1, NULL, '2025-12-12 18:48:33'),
(20, 7, 1, NULL, '2025-12-12 18:48:33'),
(20, 8, 1, NULL, '2025-12-12 18:48:33'),
(20, 9, 1, NULL, '2025-12-12 18:48:33'),
(20, 10, 1, NULL, '2025-12-12 18:48:33'),
(20, 11, 1, NULL, '2025-12-12 18:48:33'),
(20, 12, 1, NULL, '2025-12-12 18:48:33'),
(21, 1, 1, NULL, '2025-12-12 18:48:33'),
(21, 2, 1, NULL, '2025-12-12 18:48:33'),
(21, 3, 1, NULL, '2025-12-12 18:48:33'),
(21, 4, 1, NULL, '2025-12-12 18:48:33'),
(21, 5, 1, NULL, '2025-12-12 18:48:33'),
(21, 6, 1, NULL, '2025-12-12 18:48:33'),
(21, 7, 1, NULL, '2025-12-12 18:48:33'),
(21, 8, 1, NULL, '2025-12-12 18:48:33'),
(21, 9, 1, NULL, '2025-12-12 18:48:33'),
(21, 10, 1, NULL, '2025-12-12 18:48:33'),
(21, 11, 1, NULL, '2025-12-12 18:48:33'),
(21, 12, 1, NULL, '2025-12-12 18:48:33'),
(22, 1, 1, NULL, '2025-12-12 18:48:33'),
(22, 2, 1, NULL, '2025-12-12 18:48:33'),
(22, 3, 1, NULL, '2025-12-12 18:48:33'),
(22, 4, 1, NULL, '2025-12-12 18:48:33'),
(22, 5, 1, NULL, '2025-12-12 18:48:33'),
(22, 6, 1, NULL, '2025-12-12 18:48:33'),
(22, 7, 1, NULL, '2025-12-12 18:48:33'),
(22, 8, 1, NULL, '2025-12-12 18:48:33'),
(22, 9, 1, NULL, '2025-12-12 18:48:33'),
(22, 10, 1, NULL, '2025-12-12 18:48:33'),
(22, 11, 1, NULL, '2025-12-12 18:48:33'),
(22, 12, 1, NULL, '2025-12-12 18:48:33'),
(23, 1, 1, NULL, '2025-12-12 18:48:33'),
(23, 2, 1, NULL, '2025-12-12 18:48:33'),
(23, 3, 1, NULL, '2025-12-12 18:48:33'),
(23, 4, 1, NULL, '2025-12-12 18:48:33'),
(23, 5, 1, NULL, '2025-12-12 18:48:33'),
(23, 6, 1, NULL, '2025-12-12 18:48:33'),
(23, 7, 1, NULL, '2025-12-12 18:48:33'),
(23, 8, 1, NULL, '2025-12-12 18:48:33'),
(23, 9, 1, NULL, '2025-12-12 18:48:33'),
(23, 10, 1, NULL, '2025-12-12 18:48:33'),
(23, 11, 1, NULL, '2025-12-12 18:48:33'),
(23, 12, 1, NULL, '2025-12-12 18:48:33'),
(24, 1, 1, NULL, '2025-12-12 18:48:33'),
(24, 2, 1, NULL, '2025-12-12 18:48:33'),
(24, 3, 1, NULL, '2025-12-12 18:48:33'),
(24, 4, 1, NULL, '2025-12-12 18:48:33'),
(24, 5, 1, NULL, '2025-12-12 18:48:33'),
(24, 6, 1, NULL, '2025-12-12 18:48:33'),
(24, 7, 1, NULL, '2025-12-12 18:48:33'),
(24, 8, 1, NULL, '2025-12-12 18:48:33'),
(24, 9, 1, NULL, '2025-12-12 18:48:33'),
(24, 10, 1, NULL, '2025-12-12 18:48:33'),
(24, 11, 1, NULL, '2025-12-12 18:48:33'),
(24, 12, 1, NULL, '2025-12-12 18:48:33'),
(25, 1, 1, NULL, '2025-12-12 18:48:33'),
(25, 2, 1, NULL, '2025-12-12 18:48:33'),
(25, 3, 1, NULL, '2025-12-12 18:48:33'),
(25, 4, 1, NULL, '2025-12-12 18:48:33'),
(25, 5, 1, NULL, '2025-12-12 18:48:33'),
(25, 6, 1, NULL, '2025-12-12 18:48:33'),
(25, 7, 1, NULL, '2025-12-12 18:48:33'),
(25, 8, 1, NULL, '2025-12-12 18:48:33'),
(25, 9, 1, NULL, '2025-12-12 18:48:33'),
(25, 10, 1, NULL, '2025-12-12 18:48:33'),
(25, 11, 1, NULL, '2025-12-12 18:48:33'),
(25, 12, 1, NULL, '2025-12-12 18:48:33'),
(26, 1, 1, NULL, '2025-12-12 18:48:33'),
(26, 2, 1, NULL, '2025-12-12 18:48:33'),
(26, 3, 1, NULL, '2025-12-12 18:48:33'),
(26, 4, 1, NULL, '2025-12-12 18:48:33'),
(26, 5, 1, NULL, '2025-12-12 18:48:33'),
(26, 6, 1, NULL, '2025-12-12 18:48:33'),
(26, 7, 1, NULL, '2025-12-12 18:48:33'),
(26, 8, 1, NULL, '2025-12-12 18:48:33'),
(26, 9, 1, NULL, '2025-12-12 18:48:33'),
(26, 10, 1, NULL, '2025-12-12 18:48:33'),
(26, 11, 1, NULL, '2025-12-12 18:48:33'),
(26, 12, 1, NULL, '2025-12-12 18:48:33'),
(27, 1, 1, NULL, '2025-12-12 18:48:33'),
(27, 2, 1, NULL, '2025-12-12 18:48:33'),
(27, 3, 1, NULL, '2025-12-12 18:48:33'),
(27, 4, 1, NULL, '2025-12-12 18:48:33'),
(27, 5, 1, NULL, '2025-12-12 18:48:33'),
(27, 6, 1, NULL, '2025-12-12 18:48:33'),
(27, 7, 1, NULL, '2025-12-12 18:48:33'),
(27, 8, 1, NULL, '2025-12-12 18:48:33'),
(27, 9, 1, NULL, '2025-12-12 18:48:33'),
(27, 10, 1, NULL, '2025-12-12 18:48:33'),
(27, 11, 1, NULL, '2025-12-12 18:48:33'),
(27, 12, 1, NULL, '2025-12-12 18:48:33');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int UNSIGNED NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `sort_order`, `is_active`) VALUES
(1, 'Hot Coffee', 1, 1),
(2, 'Iced Coffee', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` bigint UNSIGNED NOT NULL,
  `order_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_id` int UNSIGNED NOT NULL,
  `customer_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fulfillment_mode` enum('pickup','delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pickup',
  `delivery_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','preparing','out_for_delivery','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `cancel_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_by` int UNSIGNED DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_code`, `branch_id`, `customer_name`, `customer_phone`, `fulfillment_mode`, `delivery_address`, `notes`, `status`, `cancel_reason`, `cancelled_by`, `cancelled_at`, `subtotal`, `delivery_fee`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'DM-000001', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'completed', NULL, NULL, NULL, 78.00, 0.00, 78.00, '2025-12-13 17:36:11', '2025-12-13 22:51:55'),
(2, 'DM-000002', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'pending', NULL, NULL, NULL, 78.00, 0.00, 78.00, '2025-12-14 00:17:09', '2025-12-14 00:17:09'),
(3, 'DM-000003', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', 'asdadsads', 'pending', NULL, NULL, NULL, 78.00, 0.00, 78.00, '2025-12-14 00:19:21', '2025-12-14 00:19:21'),
(4, 'DM-000004', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'pending', NULL, NULL, NULL, 39.00, 0.00, 39.00, '2025-12-14 00:20:34', '2025-12-14 00:20:34'),
(5, 'DM-000005', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'pending', NULL, NULL, NULL, 39.00, 0.00, 39.00, '2025-12-14 00:26:05', '2025-12-14 00:26:05'),
(7, 'DM-000007', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'pending', NULL, NULL, NULL, 39.00, 0.00, 39.00, '2025-12-14 00:37:39', '2025-12-14 00:37:39'),
(8, 'DM-000008', 2, 'adasda', '09293811134', 'pickup', 'PICKUP (see branch details)', '', 'pending', NULL, NULL, NULL, 39.00, 0.00, 39.00, '2025-12-14 00:42:41', '2025-12-14 00:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `item_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '39.00',
  `qty` int NOT NULL DEFAULT '1',
  `with_coffee` tinyint(1) NOT NULL DEFAULT '1',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `item_name`, `unit_price`, `qty`, `with_coffee`, `line_total`) VALUES
(1, 1, 1, 'Hot Caramel', 39.00, 1, 1, 39.00),
(2, 1, 2, 'Hot Darko', 39.00, 1, 1, 39.00),
(3, 2, 1, 'Hot Caramel', 39.00, 1, 1, 39.00),
(4, 2, 2, 'Hot Darko', 39.00, 1, 1, 39.00),
(5, 3, 1, 'Hot Caramel', 39.00, 1, 1, 39.00),
(6, 3, 2, 'Hot Darko', 39.00, 1, 1, 39.00),
(7, 4, 2, 'Hot Darko', 39.00, 1, 1, 39.00),
(8, 5, 2, 'Hot Darko', 39.00, 1, 1, 39.00),
(9, 7, 1, 'Hot Caramel', 39.00, 1, 1, 39.00),
(10, 8, 2, 'Hot Darko', 39.00, 1, 1, 39.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `history_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','preparing','out_for_delivery','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `changed_by` int UNSIGNED DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`history_id`, `order_id`, `status`, `note`, `changed_by`, `changed_at`) VALUES
(1, 1, 'pending', 'Order placed', NULL, '2025-12-13 17:36:11'),
(2, 1, 'preparing', 'Moved to Preparing by admin', 1, '2025-12-13 22:51:42'),
(3, 1, 'out_for_delivery', 'Moved to Out For Delivery by admin', 1, '2025-12-13 22:51:45'),
(4, 1, 'completed', 'Moved to Completed by admin', 1, '2025-12-13 22:51:55'),
(5, 2, 'pending', 'Order placed', NULL, '2025-12-14 00:17:09'),
(6, 3, 'pending', 'Order placed', NULL, '2025-12-14 00:19:21'),
(7, 4, 'pending', 'Order placed', NULL, '2025-12-14 00:20:34'),
(8, 5, 'pending', 'Order placed', NULL, '2025-12-14 00:26:05'),
(10, 7, 'pending', 'Order placed', NULL, '2025-12-14 00:37:39'),
(11, 8, 'pending', 'Order placed', NULL, '2025-12-14 00:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `method` enum('cod','paymongo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `status` enum('unpaid','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `provider_ref` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `method`, `status`, `provider_ref`, `paid_at`, `created_at`) VALUES
(1, 1, 'paymongo', 'unpaid', NULL, NULL, '2025-12-13 17:36:11'),
(2, 2, 'paymongo', 'unpaid', NULL, NULL, '2025-12-14 00:17:09'),
(3, 3, 'cod', 'unpaid', NULL, NULL, '2025-12-14 00:19:21'),
(4, 4, 'paymongo', 'unpaid', NULL, NULL, '2025-12-14 00:20:34'),
(5, 5, 'paymongo', 'unpaid', NULL, NULL, '2025-12-14 00:26:05'),
(6, 7, 'paymongo', 'paid', NULL, '2025-12-13 17:37:39', '2025-12-14 00:37:39'),
(7, 8, 'paymongo', 'paid', NULL, '2025-12-13 17:42:41', '2025-12-14 00:42:41');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '39.00',
  `allow_no_coffee` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `image_path`, `price`, `allow_no_coffee`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hot Caramel', NULL, 'assets/img/hotcaramel.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(2, 1, 'Hot Darko', NULL, 'assets/img/hotdarko.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(3, 1, 'Don Barako', NULL, 'assets/img/hotdonbarako.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(4, 2, 'Iced Caramel Macchiato', NULL, 'assets/img/icedcaramacchiato.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(5, 2, 'Spanish Latte', NULL, 'assets/img/icedspanlatte.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(6, 2, 'Don Pistachio', NULL, 'assets/img/iceddonpistachio.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(7, 2, 'Donya Berry', NULL, 'assets/img/iceddonberry.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(8, 2, 'Don Matcha', NULL, 'assets/img/iceddonmatcha.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(9, 2, 'Matcha Berry', NULL, 'assets/img/icedmatchaberry.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(10, 2, 'Oreo Coffee', NULL, 'assets/img/icedoreocoffee.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(11, 2, 'Don Darko', NULL, 'assets/img/iceddondarko.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24'),
(12, 2, 'Black Forest', NULL, 'assets/img/icedblackforest.png', 39.00, 1, 1, '2025-12-12 18:44:24', '2025-12-12 18:44:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `uq_admin_username` (`username`),
  ADD KEY `idx_admin_branch` (`branch_id`),
  ADD KEY `fk_admin_created_by` (`created_by`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branch_id`),
  ADD UNIQUE KEY `uq_branch_name` (`name`);

--
-- Indexes for table `branch_product_availability`
--
ALTER TABLE `branch_product_availability`
  ADD PRIMARY KEY (`branch_id`,`product_id`),
  ADD KEY `idx_bpa_product` (`product_id`),
  ADD KEY `fk_bpa_updated_by` (`updated_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `uq_category_name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `uq_order_code` (`order_code`),
  ADD KEY `idx_orders_branch_status` (`branch_id`,`status`,`created_at`),
  ADD KEY `fk_orders_cancelled_by` (`cancelled_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_hist_order` (`order_id`,`changed_at`),
  ADD KEY `fk_hist_admin` (`changed_by`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `uq_payment_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_products_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branch_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `history_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `fk_admin_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_admin_created_by` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `branch_product_availability`
--
ALTER TABLE `branch_product_availability`
  ADD CONSTRAINT `fk_bpa_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bpa_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bpa_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_cancelled_by` FOREIGN KEY (`cancelled_by`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_hist_admin` FOREIGN KEY (`changed_by`) REFERENCES `admin_users` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hist_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
