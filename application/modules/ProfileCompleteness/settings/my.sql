DROP TABLE IF EXISTS `engine4_profilecompleteness_weights`;
CREATE TABLE `engine4_profilecompleteness_weights` (
  `profileweight_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_id` INT(11) UNSIGNED NOT NULL,
  `field_id` INT(11) UNSIGNED NOT NULL,
  `weight` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`profileweight_id`)
) ENGINE=INNODB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `engine4_profilecompleteness_settings`;
CREATE TABLE `engine4_profilecompleteness_settings` (
  `setting_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `view` TINYINT(1) NOT NULL DEFAULT '1',
  `color` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FF0000',
  PRIMARY KEY (`setting_id`)
) ENGINE=INNODB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_profilecompleteness_weights` (`type_id`, `field_id`, `weight`) VALUES (0,0,2);

INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(4, 'widget', 'profile-completeness.profile-completeness', 410, 2, '{"title":"Profile Completeness"}', NULL);

INSERT IGNORE INTO `engine4_profilecompleteness_settings` () VALUES ();

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_profilecompleteness', 'profile-completeness', 'Profile Completeness', '', '{"route":"admin_default","module":"profile-completeness","controller":"manage"}', 'core_admin_main_plugins', '', 699),
('profilecompleteness_admin_main_manage', 'profile-completeness', 'Weight Settings', '', '{"route":"admin_default","module":"profile-completeness","controller":"manage"}', 'profilecompleteness_admin_main', '', 1),
('profilecompleteness_admin_main_setting', 'profile-completeness', 'Global Settings', '', '{"route":"admin_default","module":"profile-completeness","controller":"setting"}', 'profilecompleteness_admin_main', '', 2);

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('profile-completeness', 'Profile Completeness', 'displays percent your profile completed', '4.01p2', 1, 'extra') ;