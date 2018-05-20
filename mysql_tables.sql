-- MySQL dump 9.11
--
-- Host: localhost    Database: kiosk
-- ------------------------------------------------------
-- Server version	4.0.24_Debian-2-log

--
-- Table structure for table `history_salg`
--

DROP TABLE IF EXISTS `history_salg`;
CREATE TABLE `history_salg` (
  `ID` int(11) NOT NULL auto_increment,
  `salesperson` int(11) default '1',
  `logUNIX` int(25) default '0',
  `wareID` varchar(50) default NULL,
  `warePrice` int(5) default NULL,
  `crewSalg` tinyint(1) default '0',
  `kasse` int(11) default '1',
  `rabatt` smallint(1) default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `history_salg_overall`
--

DROP TABLE IF EXISTS `history_salg_overall`;
CREATE TABLE `history_salg_overall` (
  `ID` int(11) NOT NULL auto_increment,
  `salesperson` int(11) default NULL,
  `crewsalg` tinyint(1) default NULL,
  `money` int(10) default NULL,
  `kasse` int(11) default '1',
  `rabatt` smallint(1) default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `kasselog`
--

DROP TABLE IF EXISTS `kasselog`;
CREATE TABLE `kasselog` (
  `ID` int(11) NOT NULL auto_increment,
  `logtext` text,
  `logtime` int(15) default '0',
  `userID` int(11) default '1',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `kasser`
--

DROP TABLE IF EXISTS `kasser`;
CREATE TABLE `kasser` (
  `ID` int(11) NOT NULL auto_increment,
  `kassenavn` varchar(35) default '0',
  `innhold` int(10) default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `meny_innhold`
--

DROP TABLE IF EXISTS `meny_innhold`;
CREATE TABLE `meny_innhold` (
  `menyID` int(11) NOT NULL default '0',
  `wareID` int(11) NOT NULL default '0',
  `amount` tinyint(2) default '1',
  PRIMARY KEY  (`menyID`,`wareID`)
) TYPE=MyISAM;

--
-- Table structure for table `menyer`
--

DROP TABLE IF EXISTS `menyer`;
CREATE TABLE `menyer` (
  `ID` int(11) NOT NULL auto_increment,
  `menynavn` varchar(35) default NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `rabatter`
--

DROP TABLE IF EXISTS `rabatter`;
CREATE TABLE `rabatter` (
  `ID` int(11) NOT NULL auto_increment,
  `name` varchar(35) default NULL,
  `active` smallint(1) default '0',
  `wareID` varchar(50) default NULL,
  `startTime` int(15) default '0',
  `stopTime` int(15) default '0',
  `newPrice` int(5) default NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `sID` varchar(32) NOT NULL default '',
  `userID` int(11) default NULL,
  `IP` varchar(15) default '000.000.000.000',
  `logUNIX` int(25) unsigned default '0',
  `crewSalg` int(11) default '0',
  `kasse` int(11) NOT NULL default '1',
  PRIMARY KEY  (`sID`)
) TYPE=MyISAM;

--
-- Table structure for table `temp_kurv`
--

DROP TABLE IF EXISTS `temp_kurv`;
CREATE TABLE `temp_kurv` (
  `ID` int(11) NOT NULL auto_increment,
  `sID` varchar(50) default NULL,
  `wareID` varchar(50) default NULL,
  `amount` int(5) default '1',
  `unixtime` int(15) default NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `temp_ko`;
CREATE TABLE `temp_ko` (
  `ID` int(11) NOT NULL auto_increment,
  `brukernavn` varchar(50) default NULL,
  `wareID` varchar(50) default NULL,
  `amount` int(5) default '1',
  `unixtime` int(15) default NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL default '',
  `nick` varchar(25) NOT NULL default '',
  `password` varchar(60) NOT NULL default '',
  `level` smallint(1) default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

--
-- Table structure for table `warez`
--

DROP TABLE IF EXISTS `warez`;
CREATE TABLE `warez` (
  `barcode` varchar(50) NOT NULL default '',
  `name` varchar(50) default NULL,
  `color` varchar(15) default 'black',
  `must_prepare` tinyint(1) default '0',
  `prepared` int(5) default '0',
  `groupbase` varchar(50) default '0',
  `groupbase_multiplier` int(2) default '1',
  `price` int(5) default NULL,
  `cPrice` int(5) default '0',
  `inPrice` int(5) default '0',
  `active` smallint(1) default '1',
  PRIMARY KEY  (`barcode`)
) TYPE=MyISAM;



INSERT INTO users SET ID = 1, name = 'Logg ut', nick = 'Anonym', level = -1;
INSERT INTO users SET ID = 2, name = 'admin', nick = 'admin', password = '21232f297a57a5a743894a0e4a801fc3', level = 1;
INSERT INTO kasser SET ID = 1, kassenavn = 'Standardkasse', innhold = 0;
