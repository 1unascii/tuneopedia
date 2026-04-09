-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 01:26 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `artist_album`
--

CREATE TABLE `artist_album` (
  `artist_album_id` int(11) NOT NULL,
  `artist_id` int(11) DEFAULT NULL,
  `album_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `collection_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `published_date` date DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_shared` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection_tune`
--

CREATE TABLE `collection_tune` (
  `collection_tune_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `tune_id` int(11) NOT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `composer`
--

CREATE TABLE `composer` (
  `composer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `composer`
--

INSERT INTO `composer` (`composer_id`, `name`) VALUES
(1, 'Traditional'),
(2, '');

-- --------------------------------------------------------

--
-- Table structure for table `discussion_thread`
--

CREATE TABLE `discussion_thread` (
  `discussion_thread_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `file_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `setting_id` int(11) DEFAULT NULL,
  `file_type` varchar(50) NOT NULL,
  `location` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE `relationship` (
  `relationship_id` int(11) NOT NULL,
  `tune_id_1` int(11) DEFAULT NULL,
  `tune_id_2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `setting_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `default_note_length` varchar(10) DEFAULT '1/8',
  `time_signature` varchar(7) NOT NULL DEFAULT '4/4',
  `key_signature` varchar(50) DEFAULT NULL,
  `abc_transcription` text DEFAULT NULL,
  `notes` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `tune`
--

CREATE TABLE `tune` (
  `tune_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `tune_type_id` int(11) DEFAULT NULL,
  `composer_id` int(11) DEFAULT NULL,
  `composer` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `tune_alias`
--

CREATE TABLE `tune_alias` (
  `alias_id` int(11) NOT NULL,
  `tune_id` int(11) NOT NULL,
  `alias_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tune_track`
--

CREATE TABLE `tune_track` (
  `tune_track_id` int(11) NOT NULL,
  `tune_id` int(11) DEFAULT NULL,
  `track_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(4, 'Hornpipe'),
(2, 'Jig'),
(78, 'March'),
(53, 'Other'),
(3, 'Polka'),
(1, 'Reel'),
(5, 'Slip Jig'),
(52, 'Strathspey'),
(51, 'Waltz');

-- --------------------------------------------------------

--
-- Table structure for table `tune_video`
--

CREATE TABLE `tune_video` (
  `video_id` int(11) NOT NULL,
  `tune_id` int(11) NOT NULL,
  `youtube_id` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Joseph', 'Weiner', '1unascii', 'josepheaorle@gmail.com', '?h?OT\Zh???C???');

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
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`collection_id`);

--
-- Indexes for table `collection_tune`
--
ALTER TABLE `collection_tune`
  ADD PRIMARY KEY (`collection_tune_id`),
  ADD KEY `collection_id` (`collection_id`),
  ADD KEY `tune_id` (`tune_id`);

--
-- Indexes for table `composer`
--
ALTER TABLE `composer`
  ADD PRIMARY KEY (`composer_id`);

--
-- Indexes for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  ADD PRIMARY KEY (`discussion_thread_id`),
  ADD KEY `tune_id` (`tune_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `tune_id` (`tune_id`),
  ADD KEY `setting_id` (`setting_id`);

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
  ADD KEY `tune_type_id` (`tune_type_id`),
  ADD KEY `tune_ibfk_composer` (`composer_id`);

--
-- Indexes for table `tunebook`
--
ALTER TABLE `tunebook`
  ADD PRIMARY KEY (`tunebook_id`),
  ADD UNIQUE KEY `unique_user_tune` (`user_id`,`tune_id`),
  ADD KEY `tune_id` (`tune_id`);

--
-- Indexes for table `tune_alias`
--
ALTER TABLE `tune_alias`
  ADD PRIMARY KEY (`alias_id`),
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
  ADD PRIMARY KEY (`tune_type_id`),
  ADD UNIQUE KEY `unique_name` (`name`);

--
-- Indexes for table `tune_video`
--
ALTER TABLE `tune_video`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `tune_id` (`tune_id`);

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
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artist`
--
ALTER TABLE `artist`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artist_album`
--
ALTER TABLE `artist_album`
  MODIFY `artist_album_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `collection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection_tune`
--
ALTER TABLE `collection_tune`
  MODIFY `collection_tune_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `composer`
--
ALTER TABLE `composer`
  MODIFY `composer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  MODIFY `discussion_thread_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `relationship`
--
ALTER TABLE `relationship`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_vote`
--
ALTER TABLE `setting_vote`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `track`
--
ALTER TABLE `track`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tune`
--
ALTER TABLE `tune`
  MODIFY `tune_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tunebook`
--
ALTER TABLE `tunebook`
  MODIFY `tunebook_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tune_alias`
--
ALTER TABLE `tune_alias`
  MODIFY `alias_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tune_track`
--
ALTER TABLE `tune_track`
  MODIFY `tune_track_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tune_type`
--
ALTER TABLE `tune_type`
  MODIFY `tune_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1985;

--
-- AUTO_INCREMENT for table `tune_video`
--
ALTER TABLE `tune_video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `collection_tune`
--
ALTER TABLE `collection_tune`
  ADD CONSTRAINT `collection_tune_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `collection` (`collection_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `collection_tune_ibfk_2` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE;

--
-- Constraints for table `discussion_thread`
--
ALTER TABLE `discussion_thread`
  ADD CONSTRAINT `discussion_thread_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `discussion_thread_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_ibfk_2` FOREIGN KEY (`setting_id`) REFERENCES `setting` (`setting_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `tune_ibfk_1` FOREIGN KEY (`tune_type_id`) REFERENCES `tune_type` (`tune_type_id`),
  ADD CONSTRAINT `tune_ibfk_composer` FOREIGN KEY (`composer_id`) REFERENCES `composer` (`composer_id`);

--
-- Constraints for table `tunebook`
--
ALTER TABLE `tunebook`
  ADD CONSTRAINT `tunebook_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tunebook_ibfk_2` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE;

--
-- Constraints for table `tune_alias`
--
ALTER TABLE `tune_alias`
  ADD CONSTRAINT `tune_alias_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE;

--
-- Constraints for table `tune_track`
--
ALTER TABLE `tune_track`
  ADD CONSTRAINT `tune_track_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`),
  ADD CONSTRAINT `tune_track_ibfk_2` FOREIGN KEY (`track_id`) REFERENCES `track` (`track_id`);

--
-- Constraints for table `tune_video`
--
ALTER TABLE `tune_video`
  ADD CONSTRAINT `tune_video_ibfk_1` FOREIGN KEY (`tune_id`) REFERENCES `tune` (`tune_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
