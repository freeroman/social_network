-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Ned 27. dub 2014, 20:51
-- Verze serveru: 5.5.33
-- Verze PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `social_network`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `albums`
--

CREATE TABLE `albums` (
  `id_albums` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `id_employees` int(11) NOT NULL,
  `id_walls` int(11) NOT NULL,
  `created_dt` datetime NOT NULL,
  `visible_from` datetime NOT NULL,
  `visible_to` datetime NOT NULL,
  PRIMARY KEY (`id_albums`),
  KEY `id_employees` (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `employees`
--

CREATE TABLE `employees` (
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
  PRIMARY KEY (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `events`
--

CREATE TABLE `events` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `events_employees`
--

CREATE TABLE `events_employees` (
  `id_events` int(11) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `attendance` tinyint(1) DEFAULT NULL,
  `created_dt` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  KEY `id_events` (`id_events`),
  KEY `id_employees` (`id_employees`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `files`
--

CREATE TABLE `files` (
  `id_files` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `id_sharing_type` int(11) NOT NULL,
  `created_dt` datetime NOT NULL,
  `id_albums` int(11) DEFAULT NULL,
  `type` char(1) NOT NULL,
  `visible_from` datetime NOT NULL,
  `visible_to` datetime NOT NULL,
  `id_walls` int(11) NOT NULL,
  PRIMARY KEY (`id_files`),
  KEY `id_employees` (`id_employees`),
  KEY `id_sharing_type` (`id_sharing_type`),
  KEY `id_albums` (`id_albums`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `groups`
--

CREATE TABLE `groups` (
  `id_groups` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `id_employees` int(11) NOT NULL,
  `id_walls` int(11) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_groups`),
  KEY `id_employees` (`id_employees`),
  KEY `id_walls` (`id_walls`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `messages`
--

CREATE TABLE `messages` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `relationships`
--

CREATE TABLE `relationships` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `sharing_types`
--

CREATE TABLE `sharing_types` (
  `id_sharing_types` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_sharing_types`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `walls`
--

CREATE TABLE `walls` (
  `id_walls` int(11) NOT NULL AUTO_INCREMENT,
  `id_sharing_types` int(11) NOT NULL,
  PRIMARY KEY (`id_walls`),
  KEY `id_sharing_types` (`id_sharing_types`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `albums_ibfk_2` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

--
-- Omezení pro tabulku `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

--
-- Omezení pro tabulku `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

--
-- Omezení pro tabulku `events_employees`
--
ALTER TABLE `events_employees`
  ADD CONSTRAINT `events_employees_ibfk_1` FOREIGN KEY (`id_events`) REFERENCES `events` (`id_events`),
  ADD CONSTRAINT `events_employees_ibfk_2` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`);

--
-- Omezení pro tabulku `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`id_sharing_type`) REFERENCES `sharing_types` (`id_sharing_types`),
  ADD CONSTRAINT `files_ibfk_3` FOREIGN KEY (`id_albums`) REFERENCES `albums` (`id_albums`),
  ADD CONSTRAINT `files_ibfk_4` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

--
-- Omezení pro tabulku `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`);

--
-- Omezení pro tabulku `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`id_walls`) REFERENCES `walls` (`id_walls`),
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`id_employees`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`id_sharing_types`) REFERENCES `sharing_types` (`id_sharing_types`);

--
-- Omezení pro tabulku `relationships`
--
ALTER TABLE `relationships`
  ADD CONSTRAINT `relationships_ibfk_1` FOREIGN KEY (`id_employees1`) REFERENCES `employees` (`id_employees`),
  ADD CONSTRAINT `relationships_ibfk_2` FOREIGN KEY (`id_employees2`) REFERENCES `employees` (`id_employees`);

--
-- Omezení pro tabulku `walls`
--
ALTER TABLE `walls`
  ADD CONSTRAINT `walls_ibfk_1` FOREIGN KEY (`id_sharing_types`) REFERENCES `sharing_types` (`id_sharing_types`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
