-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 17 lug, 2009 at 03:56 PM
-- Versione MySQL: 5.0.75
-- Versione PHP: 5.2.6-3ubuntu4.1
 


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bse3`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `authors_book`
--

CREATE TABLE IF NOT EXISTS `authors_book` (
  `abid` bigint(20) NOT NULL auto_increment,
  `author` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`abid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=36506 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `authors_titles_music`
--

CREATE TABLE IF NOT EXISTS `authors_titles_music` (
  `atmid` bigint(20) NOT NULL auto_increment,
  `title` text collate utf8_unicode_ci NOT NULL,
  `author` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`atmid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contiene gli autori e i rispettivi titoli delle parti' AUTO_INCREMENT=114149 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `authors_titles_recordings`
--

CREATE TABLE IF NOT EXISTS `authors_titles_recordings` (
  `atrid` bigint(20) NOT NULL auto_increment,
  `title` text collate utf8_unicode_ci NOT NULL,
  `author` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`atrid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Contiene gli autori e i rispettivi titoli delle parti' AUTO_INCREMENT=79535 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `book`
--

CREATE TABLE IF NOT EXISTS `book` (
  `bid` bigint(20) NOT NULL,
  `scheda` text collate utf8_unicode_ci NOT NULL,
  `collocazione` varchar(128) collate utf8_unicode_ci NOT NULL,
  `titolo` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `equivalenze`
--

CREATE TABLE IF NOT EXISTS `equivalenze` (
  `eid` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tipo` smallint(6) NOT NULL,
  PRIMARY KEY  (`eid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `executor_recordings`
--

CREATE TABLE IF NOT EXISTS `executor_recordings` (
  `erid` int(11) NOT NULL auto_increment,
  `executor` tinytext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`erid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=127203 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_authors_book`
--

CREATE TABLE IF NOT EXISTS `idx_authors_book` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5704 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_authors_music`
--

CREATE TABLE IF NOT EXISTS `idx_authors_music` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parola` (`parola`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38396 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_authors_recordings`
--

CREATE TABLE IF NOT EXISTS `idx_authors_recordings` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parola` (`parola`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7523 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_executor_recordings`
--

CREATE TABLE IF NOT EXISTS `idx_executor_recordings` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parola` (`parola`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=77603 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_subjects_book`
--

CREATE TABLE IF NOT EXISTS `idx_subjects_book` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1030 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_titles_book`
--

CREATE TABLE IF NOT EXISTS `idx_titles_book` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10705 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_titles_music`
--

CREATE TABLE IF NOT EXISTS `idx_titles_music` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parola` (`parola`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=242874 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `idx_titles_recordings`
--

CREATE TABLE IF NOT EXISTS `idx_titles_recordings` (
  `id` int(11) NOT NULL auto_increment,
  `parola` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ids` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parola` (`parola`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62375 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `log_ricerche`
--

CREATE TABLE IF NOT EXISTS `log_ricerche` (
  `Id` int(11) NOT NULL auto_increment,
  `frase` varchar(255) NOT NULL,
  `occorrenze` tinyint(4) NOT NULL,
  `type` char(1) NOT NULL,
  PRIMARY KEY  (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `music`
--

CREATE TABLE IF NOT EXISTS `music` (
  `mid` bigint(20) NOT NULL,
  `scheda` text collate utf8_unicode_ci NOT NULL,
  `collocazione` varchar(128) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `plays_recordings`
--

CREATE TABLE IF NOT EXISTS `plays_recordings` (
  `rid` int(11) NOT NULL,
  `erid` int(11) NOT NULL,
  PRIMARY KEY  (`rid`,`erid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `recordings`
--

CREATE TABLE IF NOT EXISTS `recordings` (
  `rid` bigint(20) NOT NULL,
  `scheda` text collate utf8_unicode_ci NOT NULL,
  `collocazione` varchar(128) collate utf8_unicode_ci NOT NULL,
  `supporto` tinyint(4) NOT NULL,
  `titolo_ordinamento` text collate utf8_unicode_ci NOT NULL,
  `intestazione` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `refer_to_book`
--

CREATE TABLE IF NOT EXISTS `refer_to_book` (
  `sid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  PRIMARY KEY  (`sid`,`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `refer_to_music`
--

CREATE TABLE IF NOT EXISTS `refer_to_music` (
  `mid` int(11) NOT NULL,
  `atmid` int(11) NOT NULL,
  PRIMARY KEY  (`mid`,`atmid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `refer_to_recordings`
--

CREATE TABLE IF NOT EXISTS `refer_to_recordings` (
  `rid` int(11) NOT NULL,
  `atrid` int(11) NOT NULL,
  PRIMARY KEY  (`rid`,`atrid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `rel_equiv`
--

CREATE TABLE IF NOT EXISTS `rel_equiv` (
  `reid` int(11) NOT NULL auto_increment,
  `eid1` int(11) NOT NULL,
  `eid2` int(11) NOT NULL,
  `bidirezionale` smallint(6) NOT NULL,
  PRIMARY KEY  (`reid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `subjects_book`
--

CREATE TABLE IF NOT EXISTS `subjects_book` (
  `sbid` int(11) NOT NULL auto_increment,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`sbid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3557 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `write_book`
--

CREATE TABLE IF NOT EXISTS `write_book` (
  `bid` int(11) NOT NULL,
  `abid` int(11) NOT NULL,
  PRIMARY KEY  (`bid`,`abid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
