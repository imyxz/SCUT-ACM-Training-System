-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-07-18 13:17:04
-- 服务器版本： 10.1.19-MariaDB
-- PHP Version: 7.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `acm_training_system`
--

-- --------------------------------------------------------

--
-- 表的结构 `acm_contest`
--

CREATE TABLE `acm_contest` (
  `contest_id` int(11) NOT NULL,
  `contest_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `contest_description` text COLLATE utf8_general_mysql500_ci NOT NULL,
  `contest_problem_count` int(11) NOT NULL,
  `contest_begin_time` datetime NOT NULL,
  `contest_end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `acm_contest_board`
--

CREATE TABLE `acm_contest_board` (
  `board_log_id` int(11) NOT NULL,
  `contest_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `problem_solved` int(11) NOT NULL,
  `rank_index` int(11) NOT NULL,
  `penalty` int(11) NOT NULL,
  `ac_info` text COLLATE utf8_general_mysql500_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `acm_contest_board_submission`
--

CREATE TABLE `acm_contest_board_submission` (
  `submission_id` int(11) NOT NULL,
  `contest_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `problem_index` int(11) NOT NULL,
  `ac_status` int(11) NOT NULL,
  `submit_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `acm_contest_summary`
--

CREATE TABLE `acm_contest_summary` (
  `summary_id` int(11) NOT NULL,
  `contest_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `problem_index` int(11) NOT NULL,
  `ac_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `acm_group`
--

CREATE TABLE `acm_group` (
  `group_id` int(11) NOT NULL,
  `group_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `group_vj_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `group_zoj_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL DEFAULT '',
  `group_seoj_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `group_csoj_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `group_leader_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `acm_player`
--

CREATE TABLE `acm_player` (
  `player_id` int(11) NOT NULL,
  `player_name` char(128) COLLATE utf8_general_mysql500_ci NOT NULL,
  `player_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_mysql500_ci;

-- --------------------------------------------------------

--
-- 表的结构 `remember_info`
--

CREATE TABLE `remember_info` (
  `remember_id` bigint(20) UNSIGNED NOT NULL,
  `remember_pass` char(32) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `login_time` int(11) NOT NULL,
  `login_ip` char(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE `user_info` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `player_id` int(11) NOT NULL,
  `user_name` char(64) NOT NULL,
  `user_email` char(64) NOT NULL,
  `user_nickname` char(64) NOT NULL,
  `user_password` char(32) NOT NULL,
  `reg_time` int(11) NOT NULL DEFAULT '0',
  `reg_ip` char(16) NOT NULL DEFAULT '',
  `login_ip` char(16) NOT NULL DEFAULT '',
  `login_time` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acm_contest`
--
ALTER TABLE `acm_contest`
  ADD PRIMARY KEY (`contest_id`);

--
-- Indexes for table `acm_contest_board`
--
ALTER TABLE `acm_contest_board`
  ADD PRIMARY KEY (`board_log_id`),
  ADD KEY `contest_id` (`contest_id`);

--
-- Indexes for table `acm_contest_board_submission`
--
ALTER TABLE `acm_contest_board_submission`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `player_id` (`group_id`),
  ADD KEY `contest_id` (`contest_id`);

--
-- Indexes for table `acm_contest_summary`
--
ALTER TABLE `acm_contest_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD KEY `contest_id` (`contest_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `acm_group`
--
ALTER TABLE `acm_group`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `group_vj_name` (`group_vj_name`),
  ADD KEY `group_zoj_name` (`group_zoj_name`),
  ADD KEY `group_seoj_name` (`group_seoj_name`),
  ADD KEY `group_csoj_name` (`group_csoj_name`);

--
-- Indexes for table `acm_player`
--
ALTER TABLE `acm_player`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `player_group_id` (`player_group_id`);

--
-- Indexes for table `remember_info`
--
ALTER TABLE `remember_info`
  ADD PRIMARY KEY (`remember_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name_2` (`user_name`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `player_id` (`player_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `acm_contest`
--
ALTER TABLE `acm_contest`
  MODIFY `contest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- 使用表AUTO_INCREMENT `acm_contest_board`
--
ALTER TABLE `acm_contest_board`
  MODIFY `board_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
--
-- 使用表AUTO_INCREMENT `acm_contest_board_submission`
--
ALTER TABLE `acm_contest_board_submission`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;
--
-- 使用表AUTO_INCREMENT `acm_contest_summary`
--
ALTER TABLE `acm_contest_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- 使用表AUTO_INCREMENT `acm_group`
--
ALTER TABLE `acm_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- 使用表AUTO_INCREMENT `acm_player`
--
ALTER TABLE `acm_player`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- 使用表AUTO_INCREMENT `remember_info`
--
ALTER TABLE `remember_info`
  MODIFY `remember_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- 使用表AUTO_INCREMENT `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
