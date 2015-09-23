INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('sladvsubscription', 'SocialLOFT\'s Advsubscription ', 'SocialLOFT\'s Advsubscription ', '1.0.0', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_admin_main_payment_compare_subscriptions', 'sladvsubscription', 'Compare Subscription Packages', '', '{"route":"admin_default","module":"sladvsubscription","controller":"subscription","action":"compare"}', 'core_admin_main_payment', '', 1, 0, 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_sladvsubscription', 'sladvsubscription', 'Adv Subscription', '', '{"route":"admin_default","module":"sladvsubscription","controller":"settings"}', 'core_admin_main_plugins', '', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sladvsubscription_admin_main_settings', 'sladvsubscription', 'Global Settings', '', '{"route":"admin_default","module":"sladvsubscription","controller":"settings"}', 'sladvsubscription_admin_main', '', 1);

CREATE TABLE IF NOT EXISTS `engine4_sladvsubscription_compares` (
  `compare_id` int(11) NOT NULL AUTO_INCREMENT,
  `compare_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `compare_value` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`compare_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_user_signup` (`class`, `order`, `enable`) VALUES
('Sladvsubscription_Plugin_Signup_Subscription', 0, 0)
;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('core_mini_advsubscription', 'sladvsubscription', 'Upgrade now', 'Sladvsubscription_Plugin_Menus::showUpgrade', '', 'core_mini', '', 1, 0, 999);


CREATE TABLE IF NOT EXISTS `engine4_socialloft_myplugins` (
  `myplugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `des` text COLLATE utf8_unicode_ci,
  `key_info` text COLLATE utf8_unicode_ci,
  `version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intalled` int(11) NOT NULL DEFAULT '0',
  `packages` text COLLATE utf8_unicode_ci,
  `license_key` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`myplugin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_socialloft_myplugins` (`name`, `des`, `key_info`, `version`, `intalled`, `packages`) VALUES
('Adv Subscription', 'Des', 'key info', '1', 0, 'advsubscription');

ALTER TABLE  `engine4_authorization_levels` ADD  `order` INT NOT NULL;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('sladvsubscription_admin_main_order', 'sladvsubscription', 'Order Levels', '', '{"route":"admin_default","module":"sladvsubscription","controller":"settings","action":"order"}', 'sladvsubscription_admin_main', '', 2);