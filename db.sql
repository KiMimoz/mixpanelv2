-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 29, 2022 at 10:23 PM
-- Server version: 10.6.7-MariaDB-log
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panou`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(25) NOT NULL,
  `steamid64` bigint(35) NOT NULL,
  `auth` varchar(35) NOT NULL,
  `name` varchar(33) NOT NULL,
  `email` varchar(100) NOT NULL,
  `online` int(1) NOT NULL DEFAULT 0,
  `warn` int(2) NOT NULL DEFAULT 0,
  `password` varchar(15) DEFAULT NULL,
  `access` varchar(35) DEFAULT 'z',
  `flags` varchar(5) NOT NULL DEFAULT 'ce',
  `Boss` int(1) NOT NULL DEFAULT 0,
  `Admin` int(2) NOT NULL DEFAULT 0,
  `IP` varchar(45) NOT NULL DEFAULT 'UNKNOWN',
  `LastIP` varchar(45) NOT NULL DEFAULT 'UNKNOWN',
  `FirstPanelRegister` varchar(25) NOT NULL DEFAULT 'UNDEFINED',
  `LastPanelLogin` varchar(25) NOT NULL DEFAULT 'UNDEFINED'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `amx`
--

CREATE TABLE `amx` (
  `auth` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `access` varchar(32) NOT NULL,
  `flags` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='AMX Mod X Admins';

-- --------------------------------------------------------

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `id` int(11) NOT NULL,
  `victim_id` int(11) NOT NULL DEFAULT 0,
  `victim_name` varchar(33) NOT NULL DEFAULT 'UNSPECIFIED',
  `victim_steamid` varchar(35) NOT NULL DEFAULT 'UNSPECIFIED',
  `victim_ip` varchar(45) NOT NULL DEFAULT 'UNSPECIFIED',
  `banlength` int(10) NOT NULL DEFAULT 5,
  `unbantime` varchar(25) DEFAULT NULL,
  `reason` varchar(35) DEFAULT 'UNSPECIFIED',
  `admin_id` int(11) NOT NULL DEFAULT 0,
  `admin_name` varchar(33) NOT NULL,
  `admin_steamid` varchar(35) DEFAULT NULL,
  `admin_ip` varchar(45) NOT NULL,
  `date` varchar(25) DEFAULT 'UNKNOWN'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `count_players`
--

CREATE TABLE `count_players` (
  `id` int(11) NOT NULL,
  `online` varchar(255) CHARACTER SET latin1 NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mix_player_stats`
--

CREATE TABLE `mix_player_stats` (
  `ID` int(11) NOT NULL,
  `SteamID` varchar(35) NOT NULL,
  `Name` varchar(33) NOT NULL,
  `Wins` int(11) NOT NULL DEFAULT 0,
  `Lose` int(11) NOT NULL DEFAULT 0,
  `Kills` int(11) NOT NULL DEFAULT 0,
  `Deaths` int(11) NOT NULL DEFAULT 0,
  `HS` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mix_sys_match`
--

CREATE TABLE `mix_sys_match` (
  `MatchID` int(11) NOT NULL,
  `Players` varchar(512) DEFAULT NULL,
  `Duration` varchar(25) NOT NULL DEFAULT 'UNKNOWN',
  `Map` varchar(30) DEFAULT NULL,
  `Winner` varchar(6) DEFAULT 'NONE',
  `CTScore` int(11) NOT NULL DEFAULT 0,
  `TScore` int(11) NOT NULL DEFAULT 0,
  `Rounds` int(11) NOT NULL DEFAULT 0,
  `Status` int(1) NOT NULL DEFAULT 0,
  `Started` varchar(35) NOT NULL DEFAULT 'NEVER',
  `Mode` varchar(35) NOT NULL DEFAULT 'UNKNOWN'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mix_sys_stats`
--

CREATE TABLE `mix_sys_stats` (
  `ID` int(25) NOT NULL,
  `MatchID` int(25) NOT NULL,
  `SteamID` varchar(35) NOT NULL,
  `Name` varchar(33) NOT NULL,
  `Wins` int(11) NOT NULL DEFAULT 0,
  `Lose` int(11) NOT NULL DEFAULT 0,
  `Kills` int(11) NOT NULL DEFAULT 0,
  `Deaths` int(11) NOT NULL DEFAULT 0,
  `HS` int(11) NOT NULL DEFAULT 0,
  `Duration` varchar(25) NOT NULL DEFAULT 'UNKNOWN',
  `Team` varchar(6) DEFAULT 'NONE',
  `Dropped` int(1) NOT NULL DEFAULT 0,
  `Points` int(11) NOT NULL DEFAULT 0,
  `MVP` int(1) NOT NULL DEFAULT 0,
  `Map` varchar(25) NOT NULL DEFAULT 'UNKNOWN',
  `Rounds_Wins` int(11) NOT NULL DEFAULT 0,
  `Rounds_Lose` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_groups`
--

CREATE TABLE `panel_groups` (
  `groupID` int(11) NOT NULL,
  `groupAdmin` int(11) NOT NULL,
  `groupColor` varchar(35) NOT NULL,
  `groupName` varchar(35) NOT NULL,
  `groupFlags` varchar(35) NOT NULL,
  `funcIcon` varchar(35) NOT NULL DEFAULT 'fa-solid fa-stamp',
  `funcColor` varchar(35) NOT NULL DEFAULT 'blue',
  `funcFontFamily` varchar(35) NOT NULL DEFAULT 'verdana'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `panel_groups`
--

INSERT INTO `panel_groups` (`groupID`, `groupAdmin`, `groupColor`, `groupName`, `groupFlags`, `funcIcon`, `funcColor`, `funcFontFamily`) VALUES
(1, 2, '#0080ff', 'Guard', 'bcdefghijklmnopqr', 'fa-solid fa-stamp', 'white', 'verdana'),
(2, 3, '#008040', 'Moderator', 'bcdefghijklmnopqrs', 'fa-solid fa-stamp', 'white', 'verdana'),
(3, 4, '#990000', 'Coordonator', 'bcdefghijklmnopqrst', 'fa-solid fa-stamp', 'white', 'verdana'),
(5, 5, '#660000', 'Lider', 'bcdefghijklmnopqrstu', 'fa-solid fa-stamp', 'white', 'verdana'),
(6, 1, '#666699', 'Admin', 'bcdefghijklmnopq', 'fa-solid fa-stamp', 'white', 'verdana'),
(7, 0, 'gray', 'User', 'z', 'fa-solid fa-stamp', 'white', 'verdana');

-- --------------------------------------------------------

--
-- Table structure for table `panel_logs`
--

CREATE TABLE `panel_logs` (
  `logID` int(11) NOT NULL,
  `logText` varchar(256) NOT NULL,
  `logBy` int(11) NOT NULL,
  `logIP` varchar(45) NOT NULL,
  `logDate` varchar(25) NOT NULL DEFAULT 'UNDEFINED'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `panel_news`
--

CREATE TABLE `panel_news` (
  `id` int(11) NOT NULL,
  `text` mediumtext NOT NULL,
  `date` varchar(25) NOT NULL DEFAULT 'UNKNOWN',
  `by` varchar(33) NOT NULL DEFAULT 'UNKNOWN',
  `title` varchar(30) NOT NULL,
  `LastEdit_Date` varchar(25) NOT NULL DEFAULT 'NEVER',
  `LastEdit_By_Name` varchar(33) NOT NULL DEFAULT 'NONE',
  `Status` int(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_notifications`
--

CREATE TABLE `panel_notifications` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `UserName` varchar(33) NOT NULL,
  `Notification` mediumtext NOT NULL,
  `FromID` int(11) NOT NULL,
  `FromName` varchar(35) NOT NULL,
  `Seen` int(11) NOT NULL DEFAULT 0,
  `Url` varchar(100) NOT NULL DEFAULT 'UNDEFINED',
  `Date` varchar(25) NOT NULL DEFAULT 'UNDEFINED',
  `Readed` varchar(25) NOT NULL DEFAULT 'NEVER'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_recovery`
--

CREATE TABLE `panel_recovery` (
  `RecoverKey` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(35) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_settings`
--

CREATE TABLE `panel_settings` (
  `ID` int(11) NOT NULL,
  `IPLoginVerify` int(11) NOT NULL,
  `Maintenance` int(1) NOT NULL,
  `ServersOfTheWeek` mediumtext DEFAULT 'NONE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `panel_settings`
--

INSERT INTO `panel_settings` (`ID`, `IPLoginVerify`, `Maintenance`, `ServersOfTheWeek`) VALUES
(1, 0, 0, '<p><strong>DEVELAB Panel - Este un panel creat pentru un joc mai placut si mai lejer, momentan panelul este in stadiul de teste, orice problema, eroare, bug, va rugam sa ne anuntati prin intermediul TS-ului: DEVELAB STOP COPY AND PASTE</strong></p>\n');

-- --------------------------------------------------------

--
-- Table structure for table `panel_shop`
--

CREATE TABLE `panel_shop` (
  `itemID` int(11) NOT NULL,
  `itemName` mediumtext NOT NULL,
  `itemPrice` mediumtext NOT NULL,
  `itemPriceL` mediumtext NOT NULL,
  `itemText` mediumtext NOT NULL,
  `itemMethod` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_suspend`
--

CREATE TABLE `panel_suspend` (
  `spID` int(11) NOT NULL,
  `spAdmin` int(11) NOT NULL,
  `spPlayer` int(11) NOT NULL,
  `spReason` varchar(125) NOT NULL,
  `spDays` int(10) NOT NULL,
  `spDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_topics`
--

CREATE TABLE `panel_topics` (
  `id` int(11) NOT NULL,
  `Topic` mediumtext NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `panel_updates`
--

CREATE TABLE `panel_updates` (
  `id` int(11) NOT NULL,
  `title` mediumtext NOT NULL,
  `text` mediumtext NOT NULL,
  `textshort` varchar(300) NOT NULL,
  `admin` int(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `points_sys`
--

CREATE TABLE `points_sys` (
  `ID` int(11) NOT NULL,
  `SteamID` varchar(35) NOT NULL,
  `Name` varchar(35) NOT NULL,
  `Rank` varchar(35) NOT NULL DEFAULT 'Noob',
  `RankID` int(11) NOT NULL DEFAULT 0,
  `Points` int(11) NOT NULL DEFAULT 0,
  `Mode` varchar(35) DEFAULT 'Normal',
  `Country` varchar(35) DEFAULT 'UNKNOWN',
  `Matches` int(11) NOT NULL DEFAULT 0,
  `FirstJoined` varchar(35) NOT NULL DEFAULT 'NOT YET',
  `LastOnline` varchar(35) NOT NULL DEFAULT 'NOT YET'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`,`auth`,`name`,`email`,`steamid64`) USING BTREE;

--
-- Indexes for table `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`,`admin_id`,`victim_id`) USING BTREE;

--
-- Indexes for table `count_players`
--
ALTER TABLE `count_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setting` (`online`,`last_updated`);

--
-- Indexes for table `mix_player_stats`
--
ALTER TABLE `mix_player_stats`
  ADD PRIMARY KEY (`SteamID`,`Name`,`ID`) USING BTREE;

--
-- Indexes for table `mix_sys_match`
--
ALTER TABLE `mix_sys_match`
  ADD PRIMARY KEY (`MatchID`);

--
-- Indexes for table `mix_sys_stats`
--
ALTER TABLE `mix_sys_stats`
  ADD PRIMARY KEY (`MatchID`,`SteamID`,`ID`,`Name`) USING BTREE;

--
-- Indexes for table `panel_groups`
--
ALTER TABLE `panel_groups`
  ADD PRIMARY KEY (`groupID`,`groupAdmin`,`groupColor`,`groupName`,`groupFlags`,`funcIcon`,`funcColor`,`funcFontFamily`) USING BTREE;

--
-- Indexes for table `panel_logs`
--
ALTER TABLE `panel_logs`
  ADD PRIMARY KEY (`logID`);

--
-- Indexes for table `panel_news`
--
ALTER TABLE `panel_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_notifications`
--
ALTER TABLE `panel_notifications`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `panel_recovery`
--
ALTER TABLE `panel_recovery`
  ADD PRIMARY KEY (`RecoverKey`);

--
-- Indexes for table `panel_settings`
--
ALTER TABLE `panel_settings`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `panel_shop`
--
ALTER TABLE `panel_shop`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexes for table `panel_suspend`
--
ALTER TABLE `panel_suspend`
  ADD PRIMARY KEY (`spID`);

--
-- Indexes for table `panel_topics`
--
ALTER TABLE `panel_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_updates`
--
ALTER TABLE `panel_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `points_sys`
--
ALTER TABLE `points_sys`
  ADD PRIMARY KEY (`ID`,`SteamID`,`Name`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `count_players`
--
ALTER TABLE `count_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panel_groups`
--
ALTER TABLE `panel_groups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `panel_logs`
--
ALTER TABLE `panel_logs`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panel_news`
--
ALTER TABLE `panel_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `panel_notifications`
--
ALTER TABLE `panel_notifications`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `panel_settings`
--
ALTER TABLE `panel_settings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
