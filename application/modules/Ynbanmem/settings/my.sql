INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynbanmem', 'Member Management', 'Member Management', '4.01p3', 1, 'extra') ;


--
-- Table structure for table `engine4_ynbanned_extrainfo`
--

CREATE TABLE IF NOT EXISTS `engine4_ynbanmem_extrainfo` (
  `banned_id` int(11) NOT NULL,
  `banned_type` int(11) NOT NULL,
  `admin` int(11) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`banned_id`,`banned_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- // Add menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynbanmem_main', 'standard', 'Ynbanmem Main Navigation Menu')
;

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynbanmem', 'ynbanmem', 'Member Management', 'Ynbanmem_Plugin_Menus::showBanMembers', '{"route":"ynbanmem_general"}', 'core_main', '', 4),

('core_sitemap_ynbanmem', 'ynbanmem', 'Ynbanmems', 'Ynbanmem_Plugin_Menus::showBanMembers', '{"route":"ynbanmem_general"}', 'core_sitemap', '', 4),
('ynbanmem_main_browse', 'ynbanmem', 'Banned Usernames', 'Ynbanmem_Plugin_Menus::showBanMembers', '{"route":"ynbanmem_general"}', 'ynbanmem_main', '', 1),
('ynbanmem_main_view_emails', 'ynbanmem', 'Banned Emails', 'Ynbanmem_Plugin_Menus::showBanMembers', '{"route":"ynbanmem_general","action":"view-email"}', 'ynbanmem_main', '', 2),
('ynbanmem_main_view_ips', 'ynbanmem', 'Banned Ips', 'Ynbanmem_Plugin_Menus::showBanMembers', '{"route":"ynbanmem_general","action":"view-ip"}', 'ynbanmem_main', '', 3),
('ynbanmem_main_manage_notice', 'ynbanmem', 'Manage Notices', 'Ynbanmem_Plugin_Menus::showNotices', '{"route":"ynbanmem_general","action":"notice"}', 'ynbanmem_main', '', 4),
('core_admin_main_plugins_ynbanmem', 'ynbanmem', 'Member Management', '', '{"route":"admin_default","module":"ynbanmem","controller":"level"}', 'core_admin_main_plugins', '', 999),
('ynbanmem_admin_main_level', 'ynbanmem', 'Member Level Settings', '', '{"route":"admin_default","module":"ynbanmem","controller":"level"}', 'ynbanmem_admin_main', '', 3),
('ynbanmem_profile_message', 'ynbanmem', 'Send Notice', 'Ynbanmem_Plugin_Menus::sendNotice', '{"route":"ynbanmem_general"}', 'user_profile', '', 4),
 ('ynbanmem_main_manage_users',  'ynbanmem',  'Manage Users',  'Ynbanmem_Plugin_Menus::manageUsers',  '{"route":"ynbanmem_general","action":"users"}',  'ynbanmem_main',  '', 999)
 ;


-- Add permission
Alter table `engine4_users`  ADD COLUMN `note` text(225) NULL;

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ADMIN, MODERATOR, USER
-- create, delete, edit, view, comment, max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'manage' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'add' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'view_extra' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'login' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'note' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'ban' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'action' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'remove' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
  
  INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'manage_user' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
 
 
 INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynbanmem' as `type`,
    'manage_user' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');


CREATE TABLE IF NOT EXISTS `engine4_ynbanmem_ips` (
  `ip_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `ip` varchar(20),
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`ip_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('ban', 'ynbanmem', '[host],[email],[recipient_title],[sender_email],[sender_title],[message]');


CREATE TABLE IF NOT EXISTS `engine4_ynbanmem_extramessage` (
  `message_id` int(11) NOT NULL,
  `sender_email` varchar(128) NOT NULL,
  `type` int(11) NOT NULL,
  `email_type` int(11) NOT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


