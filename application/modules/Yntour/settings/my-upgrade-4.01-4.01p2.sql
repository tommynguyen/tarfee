CREATE TABLE IF NOT EXISTS `engine4_yntour_itemlanguages` (
  `itemlanguage_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `language` varchar(16) NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`itemlanguage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;