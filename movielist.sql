CREATE DATABASE IF NOT EXISTS `movielist` /*!40100 DEFAULT CHARACTER SET latin1 */;

CREATE TABLE `clients` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `clientID` varchar(255) NOT NULL,
  `ipAddr` varchar(15) NOT NULL,
  `port` int(6) NOT NULL,
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `configuration` (
  `plexMovies` int(10) NOT NULL DEFAULT '0',
  `lastupdate` int(10) NOT NULL,
  `maingenres` varchar(255) NOT NULL,
  `displaytype` int(11) DEFAULT '1',
  `plexusername` varchar(100) DEFAULT NULL,
  `plexpassword` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO configuration (plexMovies, lastupdate, maingenres, displaytype, plexusername, plexpassword) VALUES (0, 0, "action,comedy,", 1, NULL, NULL);

CREATE TABLE `movies` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `genre` varchar(100) NOT NULL,
  `movietitle` varchar(256) NOT NULL,
  `displaytitle` varchar(255) NOT NULL,
  `year` int(4) NOT NULL,
  `actors` text NOT NULL,
  `media` varchar(100) NOT NULL,
  `plexPoster` varchar(100) DEFAULT NULL,
  `plexSummary` text,
  `plexMediaID` int(255) DEFAULT NULL,
  `writers` text,
  `runtime` int(4) DEFAULT NULL,
  `directors` text,
  `size` float DEFAULT NULL,
  `tmdbid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=latin1;

