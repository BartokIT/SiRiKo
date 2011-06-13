-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 10 giu, 2011 at 10:29 AM
-- Versione MySQL: 5.5.8
-- Versione PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `siriko`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `gamer_country_info`
--

CREATE TABLE IF NOT EXISTS `gamer_country_info` (
  `ext_id_game` int(11) NOT NULL,
  `ext_iso_country` varchar(3) NOT NULL,
  `porder` int(11) NOT NULL,
  `number_units` int(11) NOT NULL,
  PRIMARY KEY (`ext_id_game`,`ext_iso_country`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `game_country`
--

CREATE TABLE IF NOT EXISTS `game_country` (
  `iso_code` varchar(3) NOT NULL,
  `continent` varchar(2) NOT NULL,
  `neighbors` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` int(11) NOT NULL,
  PRIMARY KEY (`iso_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `game_participants`
--

CREATE TABLE IF NOT EXISTS `game_participants` (
  `ext_game` int(11) NOT NULL,
  `user_session` varchar(100) NOT NULL,
  `porder` int(11) NOT NULL,
  `nickname` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`nickname`),
  UNIQUE KEY `user_session` (`user_session`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `game_status`
--

CREATE TABLE IF NOT EXISTS `game_status` (
  `id_game` int(11) NOT NULL AUTO_INCREMENT,
  `round` int(11) NOT NULL,
  `gamer` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `substatus` varchar(20) NOT NULL,
  `data` varchar(500) NOT NULL,
  `game_name` varchar(30) NOT NULL,
  PRIMARY KEY (`id_game`),
  UNIQUE KEY `game_name` (`game_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;


INSERT INTO `game_country` (`iso_code`, `continent`, `neighbors`, `name`, `color`) VALUES
('ITA', 'EU', 'FRA;CHE', 'Italy Italia', 1),
('FRA', 'EU', 'DEU;ITA;ESP;CHE', 'France Francia', 2),
('DEU', 'EU', 'FRA;CHE', 'Germany Germania', 4),
('CHE', 'EU', 'ITA;FRA;DEU', 'Switzerland Svizzera', 3),
('PRT', 'EU', 'ESP', 'Portugal Portogallo', 2),
('ESP', 'EU', 'PRT;FRA', 'Spain Spagna', 1);
