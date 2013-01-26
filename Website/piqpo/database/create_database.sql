-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 18, 2013 at 10:47 PM
-- Server version: 5.5.19
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `piqpo`
--

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
CREATE TABLE IF NOT EXISTS `device` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_type` enum('winpc') NOT NULL,
  `profile_id` int(11) NOT NULL,
  `device_name` varchar(80) NOT NULL,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `guid` (`guid`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `feed`
--

DROP TABLE IF EXISTS `feed`;
CREATE TABLE IF NOT EXISTS `feed` (
  `feed_id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_definition_file` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pause` int(11) NOT NULL DEFAULT '0',
  `feed_type` enum('plain','rss') NOT NULL DEFAULT 'rss',
  `poll_frequency` int(11) NOT NULL DEFAULT '0',
  `template_file` varchar(80) NOT NULL DEFAULT '',
  `max_slides` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`feed_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `feed_parameter`
--

DROP TABLE IF EXISTS `feed_parameter`;
CREATE TABLE IF NOT EXISTS `feed_parameter` (
  `feed_parameter_id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `parameter_name` varchar(80) NOT NULL,
  `parameter_value` text NOT NULL,
  PRIMARY KEY (`feed_parameter_id`),
  UNIQUE KEY `feed_parameter` (`feed_id`,`parameter_name`),
  KEY `feed_id` (`feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `feed_queue`
--

DROP TABLE IF EXISTS `feed_queue`;
CREATE TABLE IF NOT EXISTS `feed_queue` (
  `feed_queue_id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL DEFAULT '0',
  `next_poll` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`feed_queue_id`),
  KEY `feed_id` (`feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `feed_stream`
--

DROP TABLE IF EXISTS `feed_stream`;
CREATE TABLE IF NOT EXISTS `feed_stream` (
  `feed_stream_id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL DEFAULT '0',
  `stream_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`feed_stream_id`),
  KEY `feed_id` (`feed_id`),
  KEY `stream_id` (`stream_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activation_code` varchar(20) NOT NULL,
  `status` enum('orphan','pending','active') NOT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `activation_code` (`activation_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile_stream`
--

DROP TABLE IF EXISTS `profile_stream`;
CREATE TABLE IF NOT EXISTS `profile_stream` (
  `profile_stream_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `stream_id` int(11) NOT NULL,
  PRIMARY KEY (`profile_stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `slide`
--

DROP TABLE IF EXISTS `slide`;
CREATE TABLE IF NOT EXISTS `slide` (
  `slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `target_link` text NOT NULL,
  `guid` text NOT NULL,
  `title` varchar(200) NOT NULL,
  `publication_date` datetime NOT NULL,
  `generation_date` datetime NOT NULL,
  `feed_id` int(11) NOT NULL DEFAULT '0',
  `pause` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`slide_id`),
  KEY `feed_id` (`feed_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3498 ;

-- --------------------------------------------------------

--
-- Table structure for table `slide_follow`
--

DROP TABLE IF EXISTS `slide_follow`;
CREATE TABLE IF NOT EXISTS `slide_follow` (
  `slide_follow_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `slide_id` int(11) NOT NULL,
  `follow_time` datetime NOT NULL,
  `source` enum('screensaver','browser') NOT NULL,
  PRIMARY KEY (`slide_follow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `slide_view`
--

DROP TABLE IF EXISTS `slide_view`;
CREATE TABLE IF NOT EXISTS `slide_view` (
  `slide_view_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `slide_id` int(11) NOT NULL,
  `view_time` datetime NOT NULL,
  `source` enum('screensaver','browser') NOT NULL,
  PRIMARY KEY (`slide_view_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `stream`
--

DROP TABLE IF EXISTS `stream`;
CREATE TABLE IF NOT EXISTS `stream` (
  `stream_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`stream_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `stream_slide`
--

DROP TABLE IF EXISTS `stream_slide`;
CREATE TABLE IF NOT EXISTS `stream_slide` (
  `stream_slide_id` int(11) NOT NULL AUTO_INCREMENT,
  `stream_id` int(11) NOT NULL DEFAULT '0',
  `slide_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stream_slide_id`),
  KEY `slide_id` (`slide_id`),
  KEY `stream_id` (`stream_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=71 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(40) NOT NULL,
  `email` varchar(80) NOT NULL DEFAULT '',
  `token` varchar(100) NOT NULL,
  `status` enum('pending','active','disabled') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_stream`
--

DROP TABLE IF EXISTS `user_stream`;
CREATE TABLE IF NOT EXISTS `user_stream` (
  `user_stream_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `stream_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_stream_id`),
  KEY `user_id` (`user_id`),
  KEY `stream_id` (`stream_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `feed_queue`
--
ALTER TABLE `feed_queue`
  ADD CONSTRAINT `FEED_QUEUE_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`feed_id`);

--
-- Constraints for table `feed_stream`
--
ALTER TABLE `feed_stream`
  ADD CONSTRAINT `FEED_STREAM_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`feed_id`),
  ADD CONSTRAINT `FEED_STREAM_ibfk_2` FOREIGN KEY (`stream_id`) REFERENCES `stream` (`stream_id`);

--
-- Constraints for table `slide`
--
ALTER TABLE `slide`
  ADD CONSTRAINT `SLIDE_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`feed_id`);

--
-- Constraints for table `stream_slide`
--
ALTER TABLE `stream_slide`
  ADD CONSTRAINT `STREAM_SLIDE_ibfk_2` FOREIGN KEY (`slide_id`) REFERENCES `slide` (`slide_id`),
  ADD CONSTRAINT `STREAM_SLIDE_ibfk_3` FOREIGN KEY (`stream_id`) REFERENCES `stream` (`stream_id`);

--
-- Constraints for table `user_stream`
--
ALTER TABLE `user_stream`
  ADD CONSTRAINT `USER_STREAM_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `USER_STREAM_ibfk_2` FOREIGN KEY (`stream_id`) REFERENCES `stream` (`stream_id`);
