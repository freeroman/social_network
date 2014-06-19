SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE DATABASE IF NOT EXISTS `network` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `network`;

CREATE TABLE IF NOT EXISTS `employees` (
  `id_employees` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `birth_dt` date NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `id_walls` int(11) NOT NULL,
  `visible_first_name` tinyint(1) NOT NULL DEFAULT '1',
  `visible_login` tinyint(1) NOT NULL DEFAULT '1',
  `visible_birth_dt` tinyint(1) NOT NULL DEFAULT '1',
  `visible_job_title` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

CREATE TABLE IF NOT EXISTS `events` (
  `id_events` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `created_dt` datetime NOT NULL,
  `starting_dt` datetime NOT NULL,
  `ending_dt` datetime DEFAULT NULL,
  `id_employees` int(11) NOT NULL,
  `id_walls` int(11) NOT NULL,
  PRIMARY KEY (`id_events`),
  KEY `id_employees` (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

CREATE TABLE IF NOT EXISTS `events_employees` (
  `id_events` int(11) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `attendance` tinyint(1) DEFAULT NULL,
  `created_dt` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  KEY `id_events` (`id_events`),
  KEY `id_employees` (`id_employees`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `files` (
  `id_files` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `id_sharing_types` int(11) NOT NULL,
  `created_dt` datetime NOT NULL,
  `type` char(1) NOT NULL,
  `visible_from` datetime NOT NULL,
  `visible_to` datetime NOT NULL,
  `id_messages` int(11) NOT NULL,
  PRIMARY KEY (`id_files`),
  KEY `id_employees` (`id_employees`),
  KEY `id_messages` (`id_messages`),
  KEY `id_sharing_types` (`id_sharing_types`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

CREATE TABLE IF NOT EXISTS `groups` (
  `id_groups` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `id_walls` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_groups`),
  KEY `id_employees` (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

CREATE TABLE IF NOT EXISTS `groups_employees` (
  `id_groups` int(11) NOT NULL,
  `id_employees` int(11) NOT NULL,
  PRIMARY KEY (`id_groups`,`id_employees`),
  KEY `id_groups` (`id_groups`),
  KEY `id_employees` (`id_employees`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messages` (
  `id_messages` int(11) NOT NULL AUTO_INCREMENT,
  `id_walls` int(11) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `text` varchar(1000) NOT NULL,
  `id_sharing_types` int(11) NOT NULL,
  `created_dt` datetime NOT NULL,
  `visible_from` datetime NOT NULL,
  `visible_to` datetime NOT NULL,
  PRIMARY KEY (`id_messages`),
  KEY `id_employees` (`id_employees`),
  KEY `id_sharing_types` (`id_sharing_types`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;

CREATE TABLE IF NOT EXISTS `relationships` (
  `id_relationships` int(11) NOT NULL AUTO_INCREMENT,
  `id_employees1` int(11) NOT NULL,
  `id_employees2` int(11) NOT NULL,
  `created_dt` datetime NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_relationships`),
  KEY `id_employees1` (`id_employees1`),
  KEY `id_employees2` (`id_employees2`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

CREATE TABLE IF NOT EXISTS `sharing_types` (
  `id_sharing_types` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_sharing_types`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `walls` (
  `id_walls` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharing_types` int(11) NOT NULL,
  PRIMARY KEY (`id_walls`),
  KEY `id_sharing_types` (`id_sharing_types`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;


ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

ALTER TABLE `events_employees`
  ADD CONSTRAINT `events_employees_ibfk_1` FOREIGN KEY (`id_events`) REFERENCES `events` (`id_events`),
  ADD CONSTRAINT `events_employees_ibfk_2` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`);

ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `files_ibfk_4` FOREIGN KEY (`id_messages`) REFERENCES `messages` (`id_messages`),
  ADD CONSTRAINT `files_ibfk_5` FOREIGN KEY (`id_sharing_types`) REFERENCES `sharing_types` (`id_sharing_types`);

ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

ALTER TABLE `groups_employees`
  ADD CONSTRAINT `groups_employees_ibfk_1` FOREIGN KEY (`id_groups`) REFERENCES `groups` (`id_groups`),
  ADD CONSTRAINT `groups_employees_ibfk_2` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`);

ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_sharing_types`) REFERENCES `sharing_types` (`id_sharing_types`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

ALTER TABLE `relationships`
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY (`id_employees1`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY (`id_employees2`) REFERENCES `employees` (`id_employees`);

ALTER TABLE `walls`
  ADD CONSTRAINT `walls_ibfk_1` FOREIGN KEY (`id_sharing_types`) REFERENCES `sharing_types` (`id_sharing_types`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
