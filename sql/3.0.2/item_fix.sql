CREATE TABLE `item_new` (
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
  KEY `id` (`id`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

INSERT INTO `item_new` (`id`, `link`, `condition`, `thread`, `purchase_url`, `price`, `shipping`, `wootoff`, `title`, `subtitle`, `teaser`, `site`, `file_extension`) SELECT `id`, `link`, IF(STRCMP(`condition`,''), `condition`, 'Epic') `condition`, `thread`, `purchase_url`, `price`, `shipping`, `wootoff`, `title`, `subtitle`, `teaser`, `site`, `file_extension` FROM item;

set foreign_key_checks=0;
DROP TABLE `item`;
RENAME TABLE `item_new` TO `item`;
set foreign_key_checks=1;