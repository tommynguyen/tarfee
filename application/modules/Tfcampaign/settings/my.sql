-- --------------------------------------------------------

--
-- Change table permissions (change length of column type)
--

ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- --------------------------------------------------------
--
-- Table structure for table `engine4_tfcampaign_saves`
--

CREATE TABLE IF NOT EXISTS `engine4_tfcampaign_saves` (
`save_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`campaign_id` int(11) unsigned NOT NULL DEFAULT '0',
`active` tinyint(1) NOT NULL default '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`save_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_tfcampaign_submissions`
--

CREATE TABLE IF NOT EXISTS `engine4_tfcampaign_submissions` (
`submission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`player_id` int(11) NOT NULL,
`campaign_id` int(11) NOT NULL,
`title` text COLLATE utf8_unicode_ci NOT NULL,
`description` text COLLATE utf8_unicode_ci NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
`hided` tinyint(1) NOT NULL default '0',
`reason_id` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_tfcampaign_campaigns`
--

CREATE TABLE IF NOT EXISTS `engine4_tfcampaign_campaigns` (
`campaign_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`photo_id` int(11) NOT NULL DEFAULT '0',
`title` text COLLATE utf8_unicode_ci NOT NULL,
`description` text COLLATE utf8_unicode_ci NOT NULL,
`creation_date` datetime NOT NULL,
`modified_date` datetime NOT NULL,
`start_date` datetime NOT NULL,
`end_date` datetime NOT NULL,

`from_age` varchar(16) NULL,
`to_age` varchar(16) NULL,
`gender` varchar(16) NULL,
`category_id` int(11) unsigned NULL default '0',
`position_id` int(11) unsigned NULL default '0',
`referred_foot` tinyint(1) NULL,
`country_id` int(11) unsigned NULL default '0',
`province_id` int(11) unsigned NULL default '0',
`city_id` int(11) unsigned NULL default '0',
`languages` VARCHAR(128) NULL COLLATE 'utf8_unicode_ci',
`deleted` tinyint(1) NOT NULL default '0',
`view_count` int(11) NOT NULL DEFAULT '0',
`percentage` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_tfcampaign_reasons`
--

CREATE TABLE IF NOT EXISTS `engine4_tfcampaign_reasons` (
  `reason_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('tfcampaign_alert_player', 'tfcampaign', 'Your player card {item:$subject} is matched the scout {item:$object}.', 0, '');

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_tfcampaign_alert_player', 'tfcampaign', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

-- --------------------------------------------------------

--
-- Insert back-end menu items
--

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('tfcampaign_main', 'standard', 'Campaign Main Navigation Menu', 999);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_tfcampaign', 'tfcampaign', 'Campaigns', '', '{"route":"admin_default","module":"tfcampaign","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('tfcampaign_admin_settings_global', 'tfcampaign', 'Global Settings', '', '{"route":"admin_default","module":"tfcampaign","controller":"settings", "action":"global"}', 'tfcampaign_admin_main', '', 1),
('tfcampaign_admin_settings_level', 'tfcampaign', 'Member Level Settings', '', '{"route":"admin_default","module":"tfcampaign","controller":"settings", "action":"level"}', 'tfcampaign_admin_main', '', 2),
('tfcampaign_admin_reasons', 'tfcampaign', 'Reasons', '', '{"route":"admin_default","module":"tfcampaign","controller":"reasons", "action":"index"}', 'tfcampaign_admin_main', '', 3);


-- --------------------------------------------------------

-- set default permissions for level settings of listing

-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","registered","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');


-- ADMIN - MODERATOR


INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


-- USER

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');


-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'tfcampaign_campaign' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

