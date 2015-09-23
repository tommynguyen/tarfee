#insert to modules
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('yntour', 'Tour Guide', 'Tour guide module', '4.01', 1, 'extra') ;

# insert to menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_admin_plugins_yntour', 'yntour', 'Tour Guide', '', '{"route":"admin_default","module":"yntour","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 3);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('yntour_admin_main_manage', 'yntour', 'Manage', '', '{"route":"admin_default","module":"yntour","controller":"manage"}', 'yntour_admin_main', '', 1, 0, 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_mini_yntour', 'yntour', 'Tour Guide', 'Yntour_Plugin_Menus', '', 'core_mini', NULL, 1, 0, 0);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_mini_yntouradv', 'yntour', 'Tour Guide', 'Yntour_Plugin_Menus', '', 'advmenusystem_mini', NULL, 1, 0, 99);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('yntour_admin_main_item', 'yntour', 'Guide Steps', 'Yntour_Plugin_Menus', '{"route":"admin_default","module":"yntour","controller":"manage","action":"item"}', 'yntour_admin_main', '', 1, 0, 9);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('yntour_admin_main_settings', 'yntour', 'Global Settings', '', '{"route":"admin_default","module":"yntour","controller":"settings"}', 'yntour_admin_main', '', 1, 0, 0);

# inset to setting
INSERT INTO `engine4_core_settings` (`name`, `value`) VALUES ('yntourmode', 'disabled');

CREATE TABLE `engine4_yntour_tours` (
	`tour_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`path_hash` VARCHAR(64) NOT NULL,
	`enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	`view_rule` ENUM('guests','members','all') NOT NULL DEFAULT 'all',
	`autoclose` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'auto close if auto run',
	`autoclose_time_delay` INT(11) UNSIGNED NOT NULL DEFAULT '10' COMMENT 'time in second(s) that show box will be closed',
	`autoplay` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`hash` VARCHAR(64) NOT NULL DEFAULT '1',
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`path` TINYTEXT NOT NULL,
	`title` VARCHAR(128) NOT NULL,
	`creation_date` DATETIME NOT NULL,
    `bodyid` tinytext NOT NULL DEFAULT '',
    `option` tinyint(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`tour_id`),
	INDEX `enabled` (`enabled`),
	INDEX `hash` (`hash`),
	INDEX `path_hash` (`path_hash`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `engine4_yntour_touritems` (
	`touritem_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`tour_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`dompath` TINYTEXT NOT NULL,
	`position` VARCHAR(50) NOT NULL DEFAULT 'top left',
	`left_position` INT(10) NOT NULL DEFAULT '20',
	`top_position` INT(10) NOT NULL DEFAULT '20',
	`time_delay` INT(10) UNSIGNED NOT NULL DEFAULT '5',
	`width` VARCHAR(50) NOT NULL DEFAULT '300px',
	`height` VARCHAR(50) NOT NULL DEFAULT 'auto',
	`priority` INT(11) UNSIGNED NOT NULL DEFAULT '99',
	`user_id` INT(11) UNSIGNED NOT NULL,
	`title` VARCHAR(128) NOT NULL,
	`body` TEXT NOT NULL,
	`creation_date` DATETIME NOT NULL,
	PRIMARY KEY (`touritem_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_yntour_itemlanguages` (
  `itemlanguage_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `language` varchar(16) NOT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`itemlanguage_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
