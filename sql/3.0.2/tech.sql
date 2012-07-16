ALTER TABLE `status` CHANGE `site` `site` ENUM('woot','shirt','wine','kids','moofi','sellout','home','sport','tech')  NOT NULL;
ALTER TABLE `item` CHANGE `site` `site` ENUM('woot','shirt','wine','kids','moofi','sellout','home','sport','tech')  NOT NULL  DEFAULT 'woot';
INSERT INTO `status` (`site`, `locked`) VALUES ('tech', '0');
