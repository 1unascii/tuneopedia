-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2026 at 06:31 AM
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
-- Database: `tuneopedia`
--

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `album_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cover_art` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`album_id`, `name`, `cover_art`) VALUES
(1, 'The Vertical Records', 'cover_01.jpg'),
(2, 'Heathery Breeze', 'heathery.jpg'),
(3, 'If the Cap Fits', 'cap_fits.jpg'),
(4, '1975', 'bothy_1975.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE `artist` (
  `artist_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist`
--

INSERT INTO `artist` (`artist_id`, `first_name`, `middle_name`, `last_name`) VALUES
(1, 'Seán', NULL, 'Ó Riada'),
(2, 'Mary', NULL, 'Bergin'),
(3, 'Matt', NULL, 'Molloy'),
(4, 'Matt', NULL, 'Molloy'),
(5, 'Kevin', NULL, 'Burke'),
(6, 'Micheál', NULL, 'Ó Súilleabháin'),
(7, 'The', NULL, 'Bothy Band');

-- --------------------------------------------------------

--
-- Table structure for table `artist_album`
--

CREATE TABLE `artist_album` (
  `artist_album_id` int(11) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist_album`
--

INSERT INTO `artist_album` (`artist_album_id`, `artist_id`, `album_id`) VALUES
(1, 1, 1),
(2, 4, 1),
(3, 5, 2),
(4, 7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `discussion_thread`
--

CREATE TABLE `discussion_thread` (
  `discussion_thread_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussion_thread`
--

INSERT INTO `discussion_thread` (`discussion_thread_id`, `tune_id`, `user_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `thread_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `thread_id`, `user_id`, `body`, `created_at`) VALUES
(1, 1, 2, 'Does anyone have the sheet music for the B-part of this tune?', '2026-03-19 19:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE `relationship` (
  `relationship_id` int(11) NOT NULL,
  `tune_id_1` int(11) DEFAULT NULL,
  `tune_id_2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relationship`
--

INSERT INTO `relationship` (`relationship_id`, `tune_id_1`, `tune_id_2`) VALUES
(1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `reply_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`reply_id`, `post_id`, `user_id`, `body`, `created_at`) VALUES
(1, 1, 1, 'Check the settings table above! I just uploaded the ABC notation.', '2026-03-19 19:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `setting_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `key_signature` varchar(50) DEFAULT NULL,
  `abc_transcription` text DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`setting_id`, `tune_id`, `user_id`, `name`, `key_signature`, `abc_transcription`, `file_type`, `file_url`) VALUES
(1, 1, 1, 'Standard Setting', 'EDor', 'E2BE dEBE|E2BE AFDF|...', NULL, NULL),
(2, 2, 1, 'Standard Reel', 'D', 'B2 BA B2 BA | dBBA B2 BA | d2 fd e2 fe | dBAF E2 D2 |', NULL, NULL),
(3, 2, 2, 'Sligo Variation', 'D', 'f~d3 B2BA | G2BG FAA2 | feed e2fe | dBAF E2D2 |', NULL, NULL),
(4, 2, 1, 'Ornamented', 'D', 'A2AB AF~F2 | ABde fded | Beed e2de | f2af edBd |', NULL, NULL),
(5, 3, 1, 'Basic Jig', 'G', 'G3 GAB | A3 ABd | edB gdB | BAG ABA |', NULL, NULL),
(6, 3, 2, 'Session Version', 'G', '~G3 GAB | ~A3 ABd | edB gdB | AGF G2A |', NULL, NULL),
(7, 3, 1, 'Double Jig Style', 'G', 'G2G GAB | A2A ABd | ege dBA | BGG G2D |', NULL, NULL),
(8, 4, 2, 'Traditional', 'EDor', 'E2BE cEBE | E2BE AFDF | E2BE cEBE | BABd AFD2 |', NULL, NULL),
(9, 4, 1, 'High Octave', 'EDor', 'bee bee | afe d2f | gfe dBA | BAG FGA |', NULL, NULL),
(10, 4, 2, 'Fast Session', 'EDor', '~E3 ~B3 | ~E3 AFD | GBG FAF | GFE FED |', NULL, NULL),
(11, 5, 3, 'Standard Reel', 'G', 'G2 GD GABd | edBd edBA | G2 GD GABd | edBA GEDE |', NULL, NULL),
(12, 5, 6, 'Ornamented', 'G', '~G3 GD GABd | edBd edBA | ~G3 GD GABd | edBA GEDE |', NULL, NULL),
(13, 7, 5, 'Basic Slip Jig', 'Em', 'B2E G2E F3 | B2E G2E FED | B2E G2E F2A | B2c d2B AFD |', NULL, NULL),
(14, 7, 1, 'Extended Variation', 'Em', 'B2E G2E F3 | B2E G2E FED | G2B d2B BAB | d2B G2B AFD |', NULL, NULL),
(15, 7, 4, 'Session Version', 'Em', 'B2E G2E FGA | B2E G2E FED | B2E G2E F2A | B2c d2B AFD |', NULL, NULL),
(16, 9, 2, 'Hornpipe Style', 'D', 'A2FA DAFA | defe dcBA | e2ce A2ce | f2df A2df |', NULL, NULL),
(17, 9, 6, 'Sligo Style', 'D', 'A2FA DAFA | defe dcBA | e2ce A2ce | afge d2 :|', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting_vote`
--

CREATE TABLE `setting_vote` (
  `vote_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL,
  `vote_value` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_vote`
--

INSERT INTO `setting_vote` (`vote_id`, `user_id`, `setting_id`, `vote_value`, `created_at`) VALUES
(1, 1, 2, -1, '2026-03-19 20:34:00'),
(2, 2, 9, 1, '2026-03-19 20:34:00'),
(3, 1, 10, 1, '2026-03-19 20:38:35'),
(4, 2, 10, -1, '2026-03-19 20:38:35'),
(5, 4, 10, 1, '2026-03-19 20:38:35'),
(6, 5, 10, 1, '2026-03-19 20:38:35'),
(7, 2, 12, -1, '2026-03-19 20:38:35'),
(8, 3, 12, -1, '2026-03-19 20:38:35'),
(9, 6, 12, -1, '2026-03-19 20:38:35'),
(10, 1, 15, 1, '2026-03-19 20:38:35'),
(11, 4, 15, 1, '2026-03-19 20:38:35'),
(12, 5, 15, -1, '2026-03-19 20:38:35'),
(13, 3, 15, -1, '2026-03-19 20:38:35'),
(14, 5, 1, -1, '2026-03-19 20:41:55'),
(15, 1, 1, -1, '2026-03-19 20:41:55'),
(16, 3, 1, 1, '2026-03-19 20:41:55'),
(17, 6, 1, -1, '2026-03-19 20:41:55'),
(18, 7, 1, 1, '2026-03-19 20:41:55'),
(19, 8, 1, 1, '2026-03-19 20:41:55'),
(20, 2, 1, -1, '2026-03-19 20:41:55'),
(21, 4, 1, 1, '2026-03-19 20:41:55'),
(22, 5, 2, -1, '2026-03-19 20:41:55'),
(23, 3, 2, 1, '2026-03-19 20:41:55'),
(24, 6, 2, -1, '2026-03-19 20:41:55'),
(25, 7, 2, 1, '2026-03-19 20:41:55'),
(26, 8, 2, -1, '2026-03-19 20:41:55'),
(27, 2, 2, -1, '2026-03-19 20:41:55'),
(28, 4, 2, 1, '2026-03-19 20:41:55'),
(29, 5, 3, 1, '2026-03-19 20:41:55'),
(30, 1, 3, -1, '2026-03-19 20:41:55'),
(31, 3, 3, -1, '2026-03-19 20:41:55'),
(32, 6, 3, 1, '2026-03-19 20:41:55'),
(33, 7, 3, 1, '2026-03-19 20:41:55'),
(34, 8, 3, 1, '2026-03-19 20:41:55'),
(35, 2, 3, -1, '2026-03-19 20:41:55'),
(36, 4, 3, -1, '2026-03-19 20:41:55'),
(37, 5, 4, 1, '2026-03-19 20:41:55'),
(38, 1, 4, -1, '2026-03-19 20:41:55'),
(39, 3, 4, 1, '2026-03-19 20:41:55'),
(40, 6, 4, -1, '2026-03-19 20:41:55'),
(41, 7, 4, 1, '2026-03-19 20:41:55'),
(42, 8, 4, 1, '2026-03-19 20:41:55'),
(43, 2, 4, -1, '2026-03-19 20:41:55'),
(44, 4, 4, 1, '2026-03-19 20:41:55'),
(45, 5, 5, -1, '2026-03-19 20:41:55'),
(46, 1, 5, -1, '2026-03-19 20:41:55'),
(47, 3, 5, 1, '2026-03-19 20:41:55'),
(48, 6, 5, -1, '2026-03-19 20:41:55'),
(49, 7, 5, 1, '2026-03-19 20:41:55'),
(50, 8, 5, -1, '2026-03-19 20:41:55'),
(51, 2, 5, -1, '2026-03-19 20:41:55'),
(52, 4, 5, 1, '2026-03-19 20:41:55'),
(53, 5, 6, -1, '2026-03-19 20:41:55'),
(54, 1, 6, -1, '2026-03-19 20:41:55'),
(55, 3, 6, -1, '2026-03-19 20:41:55'),
(56, 6, 6, 1, '2026-03-19 20:41:55'),
(57, 7, 6, -1, '2026-03-19 20:41:55'),
(58, 8, 6, -1, '2026-03-19 20:41:55'),
(59, 2, 6, -1, '2026-03-19 20:41:55'),
(60, 4, 6, 1, '2026-03-19 20:41:55'),
(61, 5, 7, -1, '2026-03-19 20:41:55'),
(62, 1, 7, 1, '2026-03-19 20:41:55'),
(63, 3, 7, 1, '2026-03-19 20:41:55'),
(64, 6, 7, -1, '2026-03-19 20:41:55'),
(65, 7, 7, 1, '2026-03-19 20:41:55'),
(66, 8, 7, 1, '2026-03-19 20:41:55'),
(67, 2, 7, 1, '2026-03-19 20:41:55'),
(68, 4, 7, -1, '2026-03-19 20:41:55'),
(69, 5, 8, -1, '2026-03-19 20:41:55'),
(70, 1, 8, -1, '2026-03-19 20:41:55'),
(71, 3, 8, 1, '2026-03-19 20:41:55'),
(72, 6, 8, 1, '2026-03-19 20:41:55'),
(73, 7, 8, 1, '2026-03-19 20:41:55'),
(74, 8, 8, 1, '2026-03-19 20:41:55'),
(75, 2, 8, -1, '2026-03-19 20:41:55'),
(76, 4, 8, -1, '2026-03-19 20:41:55'),
(77, 5, 9, -1, '2026-03-19 20:41:55'),
(78, 1, 9, 1, '2026-03-19 20:41:55'),
(79, 3, 9, -1, '2026-03-19 20:41:55'),
(80, 6, 9, -1, '2026-03-19 20:41:55'),
(81, 7, 9, 1, '2026-03-19 20:41:55'),
(82, 8, 9, 1, '2026-03-19 20:41:55'),
(83, 4, 9, -1, '2026-03-19 20:41:55'),
(84, 3, 10, 1, '2026-03-19 20:41:55'),
(85, 6, 10, -1, '2026-03-19 20:41:55'),
(86, 7, 10, 1, '2026-03-19 20:41:55'),
(87, 8, 10, 1, '2026-03-19 20:41:55'),
(88, 5, 11, -1, '2026-03-19 20:41:55'),
(89, 1, 11, 1, '2026-03-19 20:41:55'),
(90, 3, 11, -1, '2026-03-19 20:41:55'),
(91, 6, 11, 1, '2026-03-19 20:41:55'),
(92, 7, 11, 1, '2026-03-19 20:41:55'),
(93, 8, 11, 1, '2026-03-19 20:41:55'),
(94, 2, 11, -1, '2026-03-19 20:41:55'),
(95, 4, 11, 1, '2026-03-19 20:41:55'),
(96, 5, 12, 1, '2026-03-19 20:41:55'),
(97, 1, 12, 1, '2026-03-19 20:41:55'),
(98, 7, 12, 1, '2026-03-19 20:41:55'),
(99, 8, 12, -1, '2026-03-19 20:41:55'),
(100, 4, 12, 1, '2026-03-19 20:41:55'),
(101, 5, 13, 1, '2026-03-19 20:41:55'),
(102, 1, 13, 1, '2026-03-19 20:41:55'),
(103, 3, 13, -1, '2026-03-19 20:41:55'),
(104, 6, 13, -1, '2026-03-19 20:41:55'),
(105, 7, 13, 1, '2026-03-19 20:41:55'),
(106, 8, 13, 1, '2026-03-19 20:41:55'),
(107, 2, 13, -1, '2026-03-19 20:41:55'),
(108, 4, 13, -1, '2026-03-19 20:41:55'),
(109, 5, 14, -1, '2026-03-19 20:41:55'),
(110, 1, 14, 1, '2026-03-19 20:41:55'),
(111, 3, 14, -1, '2026-03-19 20:41:55'),
(112, 6, 14, -1, '2026-03-19 20:41:55'),
(113, 7, 14, 1, '2026-03-19 20:41:55'),
(114, 8, 14, -1, '2026-03-19 20:41:55'),
(115, 2, 14, -1, '2026-03-19 20:41:55'),
(116, 4, 14, 1, '2026-03-19 20:41:55'),
(117, 6, 15, -1, '2026-03-19 20:41:55'),
(118, 7, 15, 1, '2026-03-19 20:41:55'),
(119, 8, 15, -1, '2026-03-19 20:41:55'),
(120, 2, 15, -1, '2026-03-19 20:41:55'),
(121, 5, 16, 1, '2026-03-19 20:41:55'),
(122, 1, 16, 1, '2026-03-19 20:41:55'),
(123, 3, 16, 1, '2026-03-19 20:41:55'),
(124, 6, 16, 1, '2026-03-19 20:41:55'),
(125, 7, 16, 1, '2026-03-19 20:41:55'),
(126, 8, 16, -1, '2026-03-19 20:41:55'),
(127, 2, 16, 1, '2026-03-19 20:41:55'),
(128, 4, 16, -1, '2026-03-19 20:41:55'),
(129, 5, 17, -1, '2026-03-19 20:41:55'),
(130, 1, 17, -1, '2026-03-19 20:41:55'),
(131, 3, 17, -1, '2026-03-19 20:41:55'),
(132, 6, 17, 1, '2026-03-19 20:41:55'),
(133, 7, 17, 1, '2026-03-19 20:41:55'),
(134, 8, 17, 1, '2026-03-19 20:41:55'),
(135, 2, 17, 1, '2026-03-19 20:41:55'),
(136, 4, 17, -1, '2026-03-19 20:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE `track` (
  `track_id` int(11) NOT NULL,
  `album_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `track_number` int(11) DEFAULT NULL,
  `tune_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `track`
--

INSERT INTO `track` (`track_id`, `album_id`, `name`, `track_number`, `tune_id`) VALUES
(1, 1, 'Drowsy Maggie', 1, 1),
(2, 2, 'Wind That Shakes the Barley', 4, 2),
(3, 4, 'The Kesh Jig', 1, 3),
(4, 3, 'Morrisons', 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tune`
--

CREATE TABLE `tune` (
  `tune_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `tune_type_id` int(11) DEFAULT NULL,
  `composer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tune`
--

INSERT INTO `tune` (`tune_id`, `name`, `tune_type_id`, `composer`) VALUES
(1, 'The Drowsy Maggie', 1, 'Traditional'),
(2, 'The Wind That Shakes the Barley', 1, 'Traditional'),
(3, 'The Kesh Jig', 2, 'Traditional'),
(4, 'Morrison\'s Jig', 2, 'Traditional'),
(5, 'The Banshee', 1, 'James Morrison'),
(7, 'The Butterfly', 1, 'Traditional'),
(9, 'Harvest Home', 4, 'Traditional');

-- --------------------------------------------------------

--
-- Table structure for table `tunebook`
--

CREATE TABLE `tunebook` (
  `tunebook_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tune_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tunebook`
--

INSERT INTO `tunebook` (`tunebook_id`, `user_id`, `tune_id`, `date_added`) VALUES
(1, 1, 3, '2026-03-19 19:45:58'),
(2, 1, 4, '2026-03-19 19:45:58');

-- --------------------------------------------------------

--
-- Table structure for table `tune_track`
--

CREATE TABLE `tune_track` (
  `tune_track_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `track_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tune_track`
--

INSERT INTO `tune_track` (`tune_track_id`, `tune_id`, `track_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tune_type`
--

CREATE TABLE `tune_type` (
  `tune_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tune_type`
--

INSERT INTO `tune_type` (`tune_type_id`, `name`) VALUES
(1, 'Reel'),
(2, 'Jig'),
(3, 'Polka'),
(4, 'Hornpipe'),
(5, 'Slip Jig');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `user_name`, `email`, `password`) VALUES
(1, NULL, NULL, 'TradFan99', 'fan@example.com', 'hashed_pass_1'),
(2, NULL, NULL, 'MusicScholar', 'scholar@example.com', 'hashed_pass_2'),
(3, NULL, NULL, 'FiddleMaster', 'fiddle@example.com', 'pass1'),
(4, NULL, NULL, 'TinWhistleGuy', 'whistle@example.com', 'pass2'),
(5, NULL, NULL, 'BodhranBeats', 'drum@example.com', 'pass3'),
(6, NULL, NULL, 'FluteFan', 'flute@example.com', 'pass4'),
(7, NULL, NULL, 'Harpist92', 'harp@example.com', 'pass5'),
(8, NULL, NULL, 'PiperPro', 'pipes@example.com', 'pass6'),
(9, 'Joseph', 'Weiner', 'josepheaorle@gmail.com', '1unascii', '?h?OT\Zh???C???'),
(10, 'Joseph', 'Weiner', '1unascii', 'josepheaorle@gmail.com', '?h?OT\Zh???C???');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`album_id`);

--
-- Indexes for table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`artist_id`);

--
-- Indexes for table `artist_album`
--
ALTER TABLE `artist_album`
  ADD PRIMARY KEY (`artist_album_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `album_id` (`album_id`);

--
-- Indexes for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  ADD PRIMARY KEY (`discussion_thread_id`),
  ADD KEY `tune_id` (`tune_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `thread_id` (`thread_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `relationship`
--
ALTER TABLE `relationship`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `tune_id_1` (`tune_id_1`),
  ADD KEY `tune_id_2` (`tune_id_2`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `tune_id` (`tune_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `setting_vote`
--
ALTER TABLE `setting_vote`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `unique_user_vote` (`user_id`,`setting_id`),
  ADD KEY `setting_id` (`setting_id`);

--
-- Indexes for table `track`
--
ALTER TABLE `track`
  ADD PRIMARY KEY (`track_id`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `tune_id` (`tune_id`);

--
-- Indexes for table `tune`
--
ALTER TABLE `tune`
  ADD PRIMARY KEY (`tune_id`),
  ADD KEY `tune_type_id` (`tune_type_id`);

--
-- Indexes for table `tunebook`
--
ALTER TABLE `tunebook`
  ADD PRIMARY KEY (`tunebook_id`),
  ADD UNIQUE KEY `unique_user_tune` (`user_id`,`tune_id`),
  ADD KEY `tune_id` (`tune_id`);

--
-- Indexes for table `tune_track`
--
ALTER TABLE `tune_track`
  ADD PRIMARY KEY (`tune_track_id`),
  ADD KEY `tune_id` (`tune_id`),
  ADD KEY `track_id` (`track_id`);

--
-- Indexes for table `tune_type`
--
ALTER TABLE `tune_type`
  ADD PRIMARY KEY (`tune_type_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `artist`
--
ALTER TABLE `artist`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `artist_album`
--
ALTER TABLE `artist_album`
  MODIFY `artist_album_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  MODIFY `discussion_thread_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `relationship`
--
ALTER TABLE `relationship`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `setting_vote`
--
ALTER TABLE `setting_vote`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `track`
--
ALTER TABLE `track`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tune`
--
ALTER TABLE `tune`
  MODIFY `tune_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tunebook`
--
ALTER TABLE `tunebook`
  MODIFY `tunebook_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tune_track`
--
ALTER TABLE `tune_track`
  MODIFY `tune_track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tune_type`
--
ALTER TABLE `tune_type`
  MODIFY `tune_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artist_album`
--
ALTER TABLE `artist_album`
  ADD CONSTRAINT `artist_album_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`artist_id`),
  ADD CONSTRAINT `artist_album_ibfk_2` FOREIGN KEY (`album_id`) REFERENCES `album` (`album_id`);

--
-- Constraints for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  ADD CONSTRAINT `discussion_thread_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `discussion_thread_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`thread_id`) REFERENCES `discussion_thread` (`discussion_thread_id`),
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `relationship`
--
ALTER TABLE `relationship`
  ADD CONSTRAINT `relationship_ibfk_1` FOREIGN KEY (`tune_id_1`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `relationship_ibfk_2` FOREIGN KEY (`tune_id_2`) REFERENCES `tune` (`tune_id`);

--
-- Constraints for table `reply`
--
ALTER TABLE `reply`
  ADD CONSTRAINT `reply_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`),
  ADD CONSTRAINT `reply_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `setting`
--
ALTER TABLE `setting`
  ADD CONSTRAINT `setting_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `setting_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `setting_vote`
--
ALTER TABLE `setting_vote`
  ADD CONSTRAINT `setting_vote_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `setting_vote_ibfk_2` FOREIGN KEY (`setting_id`) REFERENCES `setting` (`setting_id`) ON DELETE CASCADE;

--
-- Constraints for table `track`
--
ALTER TABLE `track`
  ADD CONSTRAINT `track_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `album` (`album_id`),
  ADD CONSTRAINT `track_ibfk_2` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`);

--
-- Constraints for table `tune`
--
ALTER TABLE `tune`
  ADD CONSTRAINT `tune_ibfk_1` FOREIGN KEY (`tune_type_id`) REFERENCES `tune_type` (`tune_type_id`);

--
-- Constraints for table `tunebook`
--
ALTER TABLE `tunebook`
  ADD CONSTRAINT `tunebook_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tunebook_ibfk_2` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE;

--
-- Constraints for table `tune_track`
--
ALTER TABLE `tune_track`
  ADD CONSTRAINT `tune_track_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `tune_track_ibfk_2` FOREIGN KEY (`track_id`) REFERENCES `track` (`track_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
