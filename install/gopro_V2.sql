-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2017 at 12:58 PM
-- Server version: 5.5.55-0+deb8u1-log
-- PHP Version: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gopro`
--
CREATE DATABASE IF NOT EXISTS `gopro` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `gopro`;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE IF NOT EXISTS `file` (
`id` int(11) NOT NULL,
  `video` tinyint(1) NOT NULL,
  `filename` text NOT NULL,
  `path` int(11) NOT NULL,
  `dt` datetime DEFAULT NULL,
  `md5` char(32) NOT NULL,
  `location` text,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `meta` mediumtext,
  `star` tinyint(1) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `htagQty` int(11) DEFAULT NULL,
  `htags` text,
  `fps` decimal(9,6) DEFAULT NULL,
  `aspect` text,
  `set` int(11) DEFAULT NULL,
  `groupmember` tinyint(1) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `children` varchar(255) DEFAULT NULL,
  `child` tinyint(1) NOT NULL DEFAULT '0',
  `seq` int(11) DEFAULT NULL,
  `exposure` text,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `old_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL,
  `loc` varchar(255) NOT NULL,
  `count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `old`
--

CREATE TABLE IF NOT EXISTS `old` (
`ID` int(11) NOT NULL,
  `filename` text NOT NULL,
  `dt` timestamp NULL DEFAULT NULL,
  `location` text,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `meta` mediumtext,
  `star` tinyint(1) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `md5` char(32) DEFAULT NULL,
  `htagQty` int(11) DEFAULT NULL,
  `htags` text,
  `fps` decimal(9,6) DEFAULT NULL,
  `aspect` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `path`
--

CREATE TABLE IF NOT EXISTS `path` (
`id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE IF NOT EXISTS `words` (
`id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `old`
--
ALTER TABLE `old`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `path`
--
ALTER TABLE `path`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `path` (`path`);

--
-- Indexes for table `words`
--
ALTER TABLE `words`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `keyword` (`keyword`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `old`
--
ALTER TABLE `old`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `path`
--
ALTER TABLE `path`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `words`
--
ALTER TABLE `words`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
