# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: nataku (MySQL 5.5.24-0ubuntu0.12.04.1)
# Database: dswoot
# Generation Time: 2012-07-11 19:33:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `history`;

CREATE TABLE `history` (
  `id` char(40) NOT NULL,
  `item_id` char(40) NOT NULL DEFAULT '',
  `comments` int(11) NOT NULL DEFAULT '0',
  `sold_out` tinyint(1) unsigned NOT NULL,
  `percent_sold` float unsigned NOT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`,`id`),
  UNIQUE KEY `id` (`id`),
  KEY `updated` (`updated`),
  CONSTRAINT `history_item_id` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `id` char(40) NOT NULL,
  `link` varchar(1000) NOT NULL,
  `condition` varchar(25) NOT NULL,
  `thread` varchar(1000) NOT NULL,
  `purchase_url` varchar(1000) NOT NULL,
  `price` float unsigned NOT NULL DEFAULT '0',
  `shipping` float unsigned NOT NULL,
  `wootoff` tinyint(1) unsigned NOT NULL,
  `title` varchar(1000) NOT NULL,
  `subtitle` varchar(1000) NOT NULL,
  `teaser` varchar(1000) NOT NULL,
  `site` enum('woot','shirt','wine','kids','moofi','sellout','home','sport') NOT NULL DEFAULT 'woot',
  `file_extension` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `site` (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` char(40) NOT NULL,
  `item_id` char(40) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`,`id`),
  UNIQUE KEY `id` (`id`),
  CONSTRAINT `product_item_id` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `status`;

CREATE TABLE `status` (
  `site` enum('woot','shirt','wine','kids','moofi','sellout','home','sport') NOT NULL,
  `locked` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;

INSERT INTO `status` (`site`, `locked`)
VALUES
	('woot',0),
	('shirt',0),
	('wine',0),
	('kids',0),
	('moofi',0),
	('sellout',0),
	('home',0),
	('sport',0);

/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
