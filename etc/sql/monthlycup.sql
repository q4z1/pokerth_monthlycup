-- Adminer 4.8.0 MySQL 5.5.5-10.4.15-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `fullname` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  KEY `active` (`active`),
  KEY `last_loginl` (`last_login`),
  KEY `username_2` (`username`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `admin` (`admin_id`, `username`, `password`, `fullname`, `email`, `last_login`, `active`) VALUES
(1,	'LiLaLux',	'6d7ecca107f6d186b3d94d76b91338dc',	'',	NULL,	'2021-04-17 18:46:18',	1),
(2,	'boehmi',	'',	'',	'pokerth@email.boehmi.net',	NULL,	1),
(3,	'sp0ck',	'c23fb23d0787e5a67b783aa7fe444dc9',	'',	NULL,	'2021-04-19 16:41:40',	1);

DROP TABLE IF EXISTS `award2021`;
CREATE TABLE `award2021` (
  `award2021_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) NOT NULL,
  `type` varchar(64) NOT NULL,
  `file` longblob NOT NULL,
  `filename` varchar(64) NOT NULL,
  `mime` varchar(64) NOT NULL,
  PRIMARY KEY (`award2021_id`),
  UNIQUE KEY `month_2` (`month`,`type`),
  KEY `month` (`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
  `configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(32) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY (`configuration_id`),
  KEY `group` (`group`),
  KEY `group_2` (`group`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `configuration` (`configuration_id`, `group`, `key`, `value`) VALUES
(1,	'leftnavi',	'Startseite',	''),
(2,	'core',	'title',	'PokerTH Monthly Cup'),
(3,	'head',	'js',	'jquery-1.12.0.min'),
(4,	'head',	'css',	'font-awesome.min'),
(5,	'head',	'js',	'base'),
(6,	'head',	'css',	'base'),
(7,	'head',	'css',	'bootstrap.min'),
(8,	'head',	'css',	'bootstrap-theme.min'),
(9,	'head',	'js',	'bootstrap.min');

DROP TABLE IF EXISTS `controllerinc`;
CREATE TABLE `controllerinc` (
  `controllerinc_id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(64) NOT NULL DEFAULT 'default',
  `controller` varchar(64) NOT NULL,
  `type` set('js','css') NOT NULL,
  `filename` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`controllerinc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Controller Includes';

INSERT INTO `controllerinc` (`controllerinc_id`, `template`, `controller`, `type`, `filename`, `active`) VALUES
(1,	'default',	'main_default',	'js',	'login',	1),
(2,	'default',	'main_default',	'js',	'md5',	1),
(3,	'default',	'admin_upload',	'js',	'upload',	1),
(4,	'default',	'admin_award',	'js',	'upload',	1),
(5,	'default',	'admin_award',	'js',	'fileinput.min',	1),
(7,	'default',	'admin_award',	'css',	'fileinput.min',	1),
(8,	'default',	'admin_award',	'js',	'jquery.form.min',	1),
(9,	'default',	'admin_award',	'js',	'award',	1),
(10,	'default',	'admin_signup',	'js',	'signup',	1);

DROP TABLE IF EXISTS `player2021`;
CREATE TABLE `player2021` (
  `player2021_id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(64) NOT NULL,
  `awards` text DEFAULT NULL,
  `avatar` longblob DEFAULT NULL,
  `avatar_mime` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`player2021_id`),
  KEY `playername` (`playername`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `settings2021`;
CREATE TABLE `settings2021` (
  `settings2021_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`settings2021_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings2021` (`settings2021_id`, `type`, `value`) VALUES
(2,	'dates',	'{\"1\":\"n\\/a\",\"2\":\"n\\/a\",\"3\":\"n\\/a\",\"4\":\"2021-04-24\",\"5\":\"n\\/a\",\"6\":\"n\\/a\",\"7\":\"n\\/a\",\"8\":\"n\\/a\",\"9\":\"n\\/a\",\"10\":\"n\\/a\",\"11\":\"n\\/a\",\"12\":\"n\\/a\"}'),
(3,	'points',	'{\"first\":{\"1\":12,\"2\":9,\"3\":7,\"4\":6,\"5\":5,\"6\":4,\"7\":3,\"8\":2,\"9\":1,\"10\":0},\"final\":{\"bronze\":{\"1\":16,\"2\":11,\"3\":8,\"4\":6,\"5\":5,\"6\":4,\"7\":3,\"8\":2,\"9\":1,\"10\":0},\"silver\":{\"1\":24,\"2\":18,\"3\":14,\"4\":11,\"5\":9,\"6\":7,\"7\":5,\"8\":3,\"9\":1,\"10\":1},\"gold\":{\"1\":36,\"2\":26,\"3\":22,\"4\":17,\"5\":13,\"6\":10,\"7\":7,\"8\":5,\"9\":3,\"10\":1}}}'),
(4,	'footer',	'');

DROP TABLE IF EXISTS `signup2021`;
CREATE TABLE `signup2021` (
  `signup2021_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `playername` varchar(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `fp` varchar(64) DEFAULT NULL,
  `fpnew` varchar(64) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`signup2021_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `signup2021` (`signup2021_id`, `month`, `playername`, `date`, `ip`, `fp`, `fpnew`, `valid`) VALUES
(1,	4,	'sp0ck',	'2021-04-19 16:32:54',	'87.188.48.253',	NULL,	NULL,	1);

DROP TABLE IF EXISTS `upload2021`;
CREATE TABLE `upload2021` (
  `uploads2021_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` enum('firstround','final') NOT NULL,
  `table_` varchar(16) NOT NULL,
  `month` int(2) NOT NULL,
  `playername` varchar(32) NOT NULL,
  `position` int(2) NOT NULL,
  `points` int(2) NOT NULL,
  PRIMARY KEY (`uploads2021_id`),
  KEY `table_month` (`table_`,`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2021-04-19 14:47:59