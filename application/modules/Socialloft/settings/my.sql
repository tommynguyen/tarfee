INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('socialloft', 'SocialLOFT\'s Core', 'SocialLOFT\'s Core', '1.0.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_socialloft', 'socialloft', 'SocialLOFT Dashboard', '', '{"route":"admin_default","module":"socialloft","controller":"ajax"}', 'core_admin_main_plugins', '', 999);


CREATE TABLE IF NOT EXISTS `engine4_socialloft_myplugins` (
  `myplugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `des` text COLLATE utf8_unicode_ci,
  `key_info` text COLLATE utf8_unicode_ci,
  `version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intalled` int(11) NOT NULL DEFAULT '0',
  `packages` text COLLATE utf8_unicode_ci,
  `license_key` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`myplugin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;