-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 29 mag, 2011 at 01:30 PM
-- Versione MySQL: 5.1.54
-- Versione PHP: 5.3.5-1ubuntu7.2

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
-- Struttura della tabella `game_participants`
--

CREATE TABLE IF NOT EXISTS `game_participants` (
  `ext_game` int(11) NOT NULL,
  `user_session` varchar(100) NOT NULL,
  `porder` int(11) NOT NULL,
  `nickname` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ext_game`,`user_session`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `game_participants`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `game_status`
--

CREATE TABLE IF NOT EXISTS `game_status` (
  `id_game` int(11) NOT NULL,
  `round` int(11) NOT NULL,
  `gamer` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `substatus` varchar(20) NOT NULL,
  `data` varchar(100) NOT NULL,
  PRIMARY KEY (`id_game`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `game_status`
--

INSERT INTO `game_status` (`id_game`, `round`, `gamer`, `status`, `substatus`, `data`) VALUES
(0, 0, 1, 'init', '', '');
