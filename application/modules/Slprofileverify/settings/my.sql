INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('slprofileverify', 'Sl profile verify', 'Profile verify', '1.0.0', 1, 'extra') ;

-- Table menuitems
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_slprofileverify', 'slprofileverify', 'Profile verify', '', '{"route":"admin_default","module":"slprofileverify","controller":"manage","action":"index"}', 'core_admin_main_plugins', '', 999),
('user_settings_verification', 'slprofileverify', 'Verification', 'Slprofileverify_Plugin_Menus::disable', '{"route":"default","module":"slprofileverify","controller":"index","action":"setting-verification"}', 'user_settings', '', 999),
('user_profile_verified', 'slprofileverify', 'Send verification request', 'Slprofileverify_Plugin_Menus', '', 'user_profile', NULL , 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('slprofileverify_admin_main_verify', 'slprofileverify', 'Verification Requests', '', '{"route":"admin_default","module":"slprofileverify","controller":"manage","action":"index"}', 'slprofileverify_admin_main', '', 1),
('slprofileverify_admin_main_setting', 'slprofileverify', 'Global Settings', '', '{"route":"admin_default","module":"slprofileverify","controller":"setting","action":"index"}', 'slprofileverify_admin_main', '', 2),
('slprofileverify_admin_main_level', 'slprofileverify', 'Member Level Settings', '', '{"route":"admin_default","module":"slprofileverify","controller":"setting","action":"level"}', 'slprofileverify_admin_main', '', 3),
('slprofileverify_admin_main_reason', 'slprofileverify', 'Reasons', '', '{"route":"admin_default","module":"slprofileverify","controller":"reason","action":"index"}', 'slprofileverify_admin_main', '', 4),
('slprofileverify_admin_main_profileverify', 'slprofileverify', 'Identity Verification Settings', '', '{"route":"admin_default","module":"slprofileverify","controller":"verify","action":"index"}', 'slprofileverify_admin_main', '', 5),
('slprofileverify_admin_main_custom', 'slprofileverify', 'Custom Verification Steps', '', '{"route":"admin_default","module":"slprofileverify","controller":"fields","action":"index"}', 'slprofileverify_admin_main', '', 6);

-- table slprofileverifies
DROP TABLE IF EXISTS `engine4_slprofileverify_slprofileverifies`;
CREATE TABLE `engine4_slprofileverify_slprofileverifies` (
        `slprofileverify_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL,
	`approval` ENUM('verified', 'unverified', 'pending', 'default') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
	`verified_date` DATETIME NOT NULL,
	`request_date` DATETIME NOT NULL,
	`file_id` CHAR(50) NULL DEFAULT NULL,
        `file_id_cus` CHAR(50) NULL DEFAULT NULL,
	`reason` VARCHAR(16) NULL,
	PRIMARY KEY (`slprofileverify_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
--
INSERT IGNORE INTO `engine4_slprofileverify_slprofileverifies` (`slprofileverify_id` ,`user_id` ,`approval` ,`verified_date` ,`request_date` ,`file_id` ,`file_id_cus` ,`reason`)
VALUES ('1' , '1', 'default', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', NULL);

-- table reason
DROP TABLE IF EXISTS `engine4_slprofileverify_reasons`;
CREATE TABLE `engine4_slprofileverify_reasons` (
	`reason_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`description` TEXT NULL,
	`create_date` DATETIME NOT NULL,
	PRIMARY KEY (`reason_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

DROP TABLE IF EXISTS `engine4_slprofileverify_users`;
CREATE TABLE `engine4_slprofileverify_users` (
        `user_id` INT(11) UNSIGNED NOT NULL,
        `value` LONGTEXT,
        PRIMARY KEY (`user_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- Insert new table 15/10/2013
DROP TABLE IF EXISTS `engine4_slprofileverify_customs`;
CREATE TABLE `engine4_slprofileverify_customs` (
        `custom_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `option_id` VARCHAR(32) NOT NULL,
        `exp_document` LONGTEXT NOT NULL,
        `image` TEXT NOT NULL,
        `image_number` TINYINT NOT NULL DEFAULT '1',
        PRIMARY KEY (`custom_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- Insert new table 24/10/2013
DROP TABLE IF EXISTS `engine4_slprofileverify_requires`;
CREATE TABLE `engine4_slprofileverify_requires` (
        `require_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `option_id` VARCHAR(32) NOT NULL,
        `enable_profile` TINYINT(1) NOT NULL,
        `exp_document` LONGTEXT NOT NULL,
        `image` TEXT NOT NULL,
        `image_number` TINYINT NOT NULL DEFAULT '1',
        `required` LONGTEXT NOT NULL,
        PRIMARY KEY (`require_id`)
)ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('slprofileverify_verify', 'slprofileverify', 'Your {item:$object:profile} has been verified.', 0, ''),
('slprofileverify_deny', 'slprofileverify', 'Your {item:$object:profile} has been denied.', 0, ''),
('slprofileverify_unverify', 'slprofileverify', 'Your {item:$object:profile} has been unverified.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('slprofileverify_verified', 'slprofileverify', '[host],[sender_link]'),
('slprofileverify_unverified', 'slprofileverify', '[host],[sender_messages],[sender_link]'),
('slprofileverify_denied', 'slprofileverify', '[host],[sender_messages],[sender_link]'),
('slprofileverify_change_profile', 'slprofileverify', '[host],[sender_link]'),
('slprofileverify_request', 'slprofileverify', '[host],[sender_title],[sender_link]'),
('slprofileverify_sending_verify', 'slprofileverify', '');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('slprofileverify_verify', 'slprofileverify', '{item:$object} is verified.', 1, 5, 0, 1, 1, 1);

--

DROP TABLE IF EXISTS `engine4_slprofileverify_fields_maps`;
CREATE TABLE `engine4_slprofileverify_fields_maps` (
        `field_id` int(11) unsigned NOT NULL,
        `option_id` int(11) unsigned NOT NULL,
        `child_id` int(11) unsigned NOT NULL,
        `order` smallint(6) NOT NULL,
        PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_slprofileverify_fields_maps` (`field_id` ,`option_id` ,`child_id` ,`order`)
VALUES ('0', '0', '1', '1');

--

DROP TABLE IF EXISTS `engine4_slprofileverify_fields_meta`;
CREATE TABLE `engine4_slprofileverify_fields_meta` (
        `field_id` int(11) unsigned NOT NULL auto_increment,
        `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `label` varchar(64) NOT NULL,
        `description` varchar(255) NOT NULL default '',
        `alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
        `required` tinyint(1) NOT NULL default '0',
        `display` tinyint(1) unsigned NOT NULL,
        `publish` tinyint(1) unsigned NOT NULL default '0',
        `search` tinyint(1) unsigned NOT NULL default '0',
        `show` tinyint(1) unsigned NOT NULL default '1',
        `order` smallint(3) unsigned NOT NULL default '999',
        `config` text NULL,
        `validators` text NULL,
        `filters` text NULL,
        `style` text NULL,
        `error` text NULL,
        PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_slprofileverify_fields_meta` (`field_id` ,`type` ,`label` ,`description` ,`alias` ,`required` ,`display` ,`publish` ,`search` ,`show` ,`order` ,`config` ,`validators` ,`filters` ,`style` ,`error`)
VALUES ('1', 'profile_type', 'Profile Type', '', 'profile_type', '1', '0', '0', '2', '1', '999', NULL , NULL , NULL , NULL , NULL);

--

DROP TABLE IF EXISTS `engine4_slprofileverify_fields_options`;
CREATE TABLE `engine4_slprofileverify_fields_options` (
        `option_id` int(11) unsigned NOT NULL auto_increment,
        `field_id` int(11) unsigned NOT NULL,
        `label` varchar(255) NOT NULL,
        `order` smallint(6) NOT NULL default '999',
        PRIMARY KEY  (`option_id`),
        KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
-- --------------------------------------------------------
INSERT IGNORE INTO `engine4_slprofileverify_fields_options` (`option_id` ,`field_id` ,`label` ,`order`)
VALUES ('1', '1', 'Sample profile type', '999');

--

DROP TABLE IF EXISTS `engine4_slprofileverify_fields_values`;
CREATE TABLE `engine4_slprofileverify_fields_values` (
        `item_id` int(11) unsigned NOT NULL,
        `field_id` int(11) unsigned NOT NULL,
        `index` smallint(3) unsigned NOT NULL default '0',
        `value` text NOT NULL,
        `privacy` varchar(64) default NULL,
        PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- 

DROP TABLE IF EXISTS `engine4_slprofileverify_fields_search`;
CREATE TABLE IF NOT EXISTS `engine4_slprofileverify_fields_search` (
        `item_id` int(11) unsigned NOT NULL,
        `profile_type` smallint(11) unsigned NULL,
        `first_name` varchar(255) NULL,
        `last_name` varchar(255) NULL,
        `gender` smallint(6) unsigned NULL,
        `birthdate` date NULL,
        PRIMARY KEY  (`item_id`),
        KEY (`profile_type`),
        KEY (`first_name`),
        KEY (`last_name`),
        KEY (`gender`),
        KEY (`birthdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- permissions

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'slprofileverify' as `type`,
    'send' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
