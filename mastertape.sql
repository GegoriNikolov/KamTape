-- KamTape SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kamtape`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` varchar(15) NOT NULL,
  `title` text DEFAULT NULL,
  `posted` datetime NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL,
  `author` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `cid` varchar(14) NOT NULL COMMENT 'comment id',
  `post_date` datetime NOT NULL DEFAULT current_timestamp(),
  `vidon` varchar(14) NOT NULL COMMENT 'Vid it was commented on',
  `vid` varchar(14) DEFAULT NULL COMMENT 'Vid attached to it',
  `body` varchar(500) NOT NULL COMMENT 'body of comment',
  `uid` varchar(20) NOT NULL COMMENT 'user who posted it',
  `is_reply` int(11) NOT NULL,
  `reply_to` varchar(14) NOT NULL,
  `master_comment` varchar(14) NOT NULL,
  `removed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `c_guidelines`
--

CREATE TABLE `c_guidelines` (
  `number` int(11) NOT NULL,
  `guideline` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `fid` varchar(12) NOT NULL COMMENT 'favorite id',
  `added` timestamp NOT NULL DEFAULT current_timestamp(),
  `uid` varchar(12) NOT NULL,
  `vid` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='lol the whole table is ids';

-- --------------------------------------------------------

--
-- Table structure for table `ip_bans`
--

CREATE TABLE `ip_bans` (
  `id` int(11) NOT NULL,
  `ip` text NOT NULL,
  `banned` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kamtape_web`
--

CREATE TABLE `kamtape_web` (
  `version` varchar(59) NOT NULL,
  `logo` varchar(100) NOT NULL DEFAULT 'logo_sm.gif',
  `slogan` varchar(52) NOT NULL DEFAULT 'Upload, tag and share your videos worldwide!',
  `notice` varchar(99) NOT NULL,
  `maintenance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Website configuration';

--
-- Dumping data for table `kamtape_web`
--

INSERT INTO `kamtape_web` (`version`, `logo`, `slogan`, `notice`, `maintenance`) VALUES
('kamtape', 'flashing_kt.gif', 'Upload, tag and share your videos worldwide!', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `sender` varchar(12) NOT NULL COMMENT 'uid of whom is sending the message',
  `receiver` varchar(12) NOT NULL COMMENT 'uid of the recipient',
  `subject` text NOT NULL COMMENT '(up to 75 characters) the title of the message, encrypted',
  `attached` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `body` text NOT NULL COMMENT '(up to 50,000 characters) the text of the message encrypted',
  `pmid` varchar(12) NOT NULL COMMENT 'id of the private message',
  `isRead` int(11) NOT NULL COMMENT 'If receiver saw it, mark 1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='private messages';

-- --------------------------------------------------------

--
-- Table structure for table `picks`
--

CREATE TABLE `picks` (
  `video` varchar(12) NOT NULL,
  `featured` datetime NOT NULL DEFAULT current_timestamp(),
  `special` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions_and_answers`
--

CREATE TABLE `questions_and_answers` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` varchar(15) NOT NULL,
  `rating` int(11) NOT NULL,
  `user` varchar(16) NOT NULL,
  `video` varchar(16) NOT NULL,
  `done` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rejections`
--

CREATE TABLE `rejections` (
  `uid` varchar(20) NOT NULL COMMENT 'id of video poster',
  `vid` varchar(20) NOT NULL COMMENT 'video id',
  `cdn` int(11) NOT NULL,
  `uploaded` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `tags` varchar(900) NOT NULL,
  `ch1` int(11) NOT NULL DEFAULT 5,
  `ch2` int(11) DEFAULT NULL,
  `ch3` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `file` text NOT NULL,
  `time` int(11) NOT NULL,
  `converted` int(11) NOT NULL,
  `privacy` int(11) NOT NULL DEFAULT 1,
  `priva_group` int(11) DEFAULT NULL,
  `recorddate` datetime DEFAULT NULL,
  `address` text DEFAULT NULL,
  `addrcountry` text DEFAULT NULL,
  `comms_allow` int(11) NOT NULL DEFAULT 1,
  `allow_votes` int(11) NOT NULL DEFAULT 1,
  `views` int(11) NOT NULL,
  `comm_count` int(11) NOT NULL,
  `fav_count` int(11) NOT NULL,
  `ratings` int(11) NOT NULL,
  `age_restrict` int(11) NOT NULL,
  `reason` int(11) NOT NULL DEFAULT 1,
  `copyright_holder` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `relationships`
--

CREATE TABLE `relationships` (
  `relationship` varchar(16) NOT NULL COMMENT 'id of the relationship',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1 equals normal friend 2 equals familia',
  `sender` varchar(18) NOT NULL,
  `respondent` varchar(18) NOT NULL,
  `accepted` int(11) NOT NULL,
  `sent` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `who` varchar(20) NOT NULL,
  `what` text NOT NULL,
  `where` varchar(3) NOT NULL DEFAULT 'I',
  `when` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` text NOT NULL,
  `subscriber` text NOT NULL,
  `subscribed_to` text NOT NULL,
  `subscribed_type` text NOT NULL DEFAULT 'user_uploads',
  `subscribed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket` int(11) NOT NULL,
  `sender` text NOT NULL,
  `subject` int(11) NOT NULL,
  `message` text NOT NULL,
  `submitted` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` varchar(20) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` longtext NOT NULL,
  `old_pass` longtext DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `confirm_id` text NOT NULL,
  `confirm_expire` datetime DEFAULT NULL,
  `joined` datetime NOT NULL DEFAULT current_timestamp(),
  `em_confirmation` text NOT NULL DEFAULT 'false',
  `emailprefs_vdocomments` int(11) NOT NULL DEFAULT 1,
  `emailprefs_wklytape` int(11) NOT NULL,
  `emailprefs_privatem` int(11) NOT NULL DEFAULT 1,
  `lastlogin` datetime NOT NULL DEFAULT current_timestamp(),
  `last_act` datetime NOT NULL DEFAULT current_timestamp(),
  `failed_login` datetime DEFAULT NULL,
  `termination` int(11) NOT NULL COMMENT 'if equals 1 then theyre terminated',
  `birthday` date DEFAULT NULL,
  `name` varchar(500) NOT NULL,
  `relationship` int(11) NOT NULL,
  `gender` int(11) NOT NULL,
  `about` varchar(2500) NOT NULL,
  `website` varchar(255) NOT NULL,
  `hometown` varchar(500) NOT NULL,
  `city` varchar(500) NOT NULL,
  `country` varchar(500) NOT NULL,
  `occupations` varchar(500) NOT NULL,
  `companies` varchar(500) NOT NULL,
  `schools` varchar(500) NOT NULL,
  `hobbies` varchar(500) NOT NULL,
  `fav_media` varchar(500) NOT NULL,
  `playlists` int(11) NOT NULL,
  `subscriptions` int(11) NOT NULL,
  `subscribers` int(11) NOT NULL,
  `profile_views` int(11) NOT NULL,
  `fav_count` int(11) NOT NULL,
  `pub_vids` int(11) NOT NULL,
  `priv_vids` int(11) NOT NULL,
  `friends_count` int(11) NOT NULL,
  `vids_watched` int(11) NOT NULL,
  `music` varchar(500) NOT NULL,
  `books` varchar(500) NOT NULL,
  `staff` int(11) NOT NULL,
  `sysadmin` int(11) NOT NULL,
  `ip` varchar(500) NOT NULL,
  `priv_id` varchar(35) DEFAULT NULL COMMENT 'non-public version of the user id used for things like email verif',
  `blazing` int(11) NOT NULL COMMENT '1 sets the cooldown limit to just 1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `uid` varchar(20) NOT NULL COMMENT 'id of video poster',
  `vid` varchar(20) NOT NULL COMMENT 'video id',
  `cdn` int(11) NOT NULL,
  `uploaded` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `tags` varchar(900) NOT NULL,
  `ch1` int(11) NOT NULL DEFAULT 5,
  `ch2` int(11) DEFAULT NULL,
  `ch3` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(10000) NOT NULL,
  `file` text NOT NULL,
  `time` int(11) NOT NULL,
  `converted` int(11) NOT NULL,
  `privacy` int(11) NOT NULL DEFAULT 1,
  `priva_group` int(11) DEFAULT NULL,
  `recorddate` datetime DEFAULT NULL,
  `address` text DEFAULT NULL,
  `addrcountry` text DEFAULT NULL,
  `comms_allow` int(11) NOT NULL DEFAULT 1,
  `allow_votes` int(11) NOT NULL DEFAULT 1,
  `age_restrict` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `comm_count` int(11) NOT NULL,
  `fav_count` int(11) NOT NULL,
  `ratings` int(11) NOT NULL,
  `rejected` int(11) NOT NULL,
  `reason` int(11) NOT NULL,
  `copyright_holder` varchar(65) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `view_id` varchar(35) NOT NULL,
  `viewed` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'when it got viewed',
  `location` varchar(5) DEFAULT 'US',
  `ip` text NOT NULL,
  `vid` varchar(12) NOT NULL COMMENT 'the video that was viewed',
  `uid` varchar(12) DEFAULT NULL COMMENT 'user who viewed the video',
  `referer` text NOT NULL COMMENT 'HTTP referer',
  `sid` text NOT NULL COMMENT 'session id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `c_guidelines`
--
ALTER TABLE `c_guidelines`
  ADD PRIMARY KEY (`number`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`fid`);

--
-- Indexes for table `ip_bans`
--
ALTER TABLE `ip_bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kamtape_web`
--
ALTER TABLE `kamtape_web`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`pmid`);

--
-- Indexes for table `picks`
--
ALTER TABLE `picks`
  ADD PRIMARY KEY (`video`);

--
-- Indexes for table `questions_and_answers`
--
ALTER TABLE `questions_and_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `rejections`
--
ALTER TABLE `rejections`
  ADD PRIMARY KEY (`vid`);

--
-- Indexes for table `relationships`
--
ALTER TABLE `relationships`
  ADD PRIMARY KEY (`relationship`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD UNIQUE KEY `subscription_id` (`subscription_id`) USING HASH;

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`vid`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`view_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `c_guidelines`
--
ALTER TABLE `c_guidelines`
  MODIFY `number` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip_bans`
--
ALTER TABLE `ip_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions_and_answers`
--
ALTER TABLE `questions_and_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
