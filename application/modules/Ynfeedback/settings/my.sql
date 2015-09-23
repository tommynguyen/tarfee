-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_mailtemplates`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_mailtemplates` (
`mailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(255) NOT NULL,
`vars` varchar(255) NOT NULL,
PRIMARY KEY (`mailtemplate_id`),
UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `engine4_ynfeedback_mailtemplates`
--

INSERT IGNORE INTO `engine4_ynfeedback_mailtemplates` (`type`, `vars`) VALUES
('ynfeedback_email_followers', '[website_name],[website_link],[feedback_link],[feedback_name],[message]')
;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_comments`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_comments` (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
  `resource_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `poster_id` int(11) unsigned NULL,
  `poster_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `poster_email` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_likes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_likes` (
  `like_id` int(11) unsigned NOT NULL auto_increment,
  `resource_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `poster_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`like_id`),
  KEY `resource_type` (`resource_type`, `resource_id`),
  KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_notes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_notes` (
  `note_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idea_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `content` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `creation_date` datetime,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------
--
-- Table structure for table `engine4_ynfeedback_votes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `idea_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_authors`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_authors` (
`author_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`idea_id` int(11) unsigned NOT NULL,
`user_id` int(11) unsigned NULL,
`name` text NULL,
PRIMARY KEY (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_follows`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_follows` (
`follow_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL DEFAULT '0',
`idea_id` int(11) unsigned NOT NULL DEFAULT '0',
`creation_date` datetime NOT NULL,
PRIMARY KEY (`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_idea_fields_maps`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_idea_fields_maps` (
`field_id` int(11) unsigned NOT NULL,
`option_id` int(11) unsigned NOT NULL,
`child_id` int(11) unsigned NOT NULL,
`order` smallint(6) NOT NULL,
PRIMARY KEY (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_ynfeedback_idea_fields_maps`
--

INSERT IGNORE INTO `engine4_ynfeedback_idea_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES
(0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_idea_fields_meta`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_idea_fields_meta` (
`field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
`label` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
`description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
`alias` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
`required` tinyint(1) NOT NULL DEFAULT '0',
`display` tinyint(1) unsigned NOT NULL,
`publish` tinyint(1) unsigned NOT NULL DEFAULT '0',
`search` tinyint(1) unsigned NOT NULL DEFAULT '0',
`order` smallint(3) unsigned NOT NULL DEFAULT '999',
`config` text COLLATE utf8_unicode_ci,
`validators` text COLLATE utf8_unicode_ci,
`filters` text COLLATE utf8_unicode_ci,
`style` text COLLATE utf8_unicode_ci,
`error` text COLLATE utf8_unicode_ci,
PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynfeedback_idea_fields_meta`
--

INSERT IGNORE INTO `engine4_ynfeedback_idea_fields_meta` (`field_id`, `type`, `label`, `description`, `alias`, `required`, `display`, `publish`, `search`, `order`, `config`, `validators`, `filters`, `style`, `error`) VALUES
(1, 'profile_type', 'Profile Type', '', 'profile_type', 1, 0, 0, 2, 999, '', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_idea_fields_search`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_idea_fields_search` (
`item_id` int(11) unsigned NOT NULL,
`profile_type` enum('1','4') COLLATE utf8_unicode_ci DEFAULT NULL,
`first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
`last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
`gender` smallint(6) unsigned DEFAULT NULL,
`birthdate` date DEFAULT NULL,
PRIMARY KEY (`item_id`),
KEY `first_name` (`first_name`),
KEY `last_name` (`last_name`),
KEY `gender` (`gender`),
KEY `birthdate` (`birthdate`),
KEY `profile_type` (`profile_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_idea_fields_values`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_idea_fields_values` (
`item_id` int(11) unsigned NOT NULL,
`field_id` int(11) unsigned NOT NULL,
`index` smallint(3) unsigned NOT NULL DEFAULT '0',
`value` text COLLATE utf8_unicode_ci NOT NULL,
`privacy` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_idea_fields_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_idea_fields_options` (
`option_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`field_id` int(11) unsigned NOT NULL,
`label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '999',
PRIMARY KEY (`option_id`),
KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_categories`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_categories` (
`category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(11) unsigned NOT NULL,
`parent_id` int(11) unsigned DEFAULT NULL,
`pleft` int(11) unsigned NOT NULL,
`pright` int(11) unsigned NOT NULL,
`level` int(11) unsigned NOT NULL DEFAULT '0',
`title` varchar(64) NOT NULL,
`order` smallint(6) NOT NULL DEFAULT '0',
`option_id` int(11) NOT NULL,
PRIMARY KEY (`category_id`),
KEY `user_id` (`user_id`),
KEY `parent_id` (`parent_id`),
KEY `pleft` (`pleft`),
KEY `pright` (`pright`),
KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynfeedback_categories`
--

INSERT IGNORE INTO `engine4_ynfeedback_categories` (`category_id`, `user_id`, `parent_id`, `pleft`, `pright`, `level`, `title`) VALUES
(1, 0, NULL, 1, 4, 0, 'All Categories');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_polls`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_polls` (
  `poll_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  `vote_count` int(11) unsigned NOT NULL default '0',
  `show` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_polls_options`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_polls_options` (
  `poll_option_id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL,
  `poll_option` text NOT NULL,
  `votes` smallint(4) unsigned NOT NULL,
  PRIMARY KEY  (`poll_option_id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_polls_votes`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_polls_votes` (
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `poll_option_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  KEY `poll_option_id` (`poll_option_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynfeedback_ideas`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_ideas` (
  	`idea_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`user_id` int(11) unsigned NOT NULL,
  	`guest_name` VARCHAR(128) NULL,
  	`guest_email` VARCHAR(128) NULL,
  	`title` text NOT NULL,
  	`description` text NOT NULL,
  	`decision` text NOT NULL,
  	`decision_owner_id` int(11) unsigned NOT NULL DEFAULT 0,
  	`severity` int(11) unsigned NOT NULL DEFAULT 0,
  	`category_id` INT(11) NOT NULL,
  	`status_id` INT(11) NOT NULL DEFAULT 1,
  	`creation_date` datetime NOT NULL,
	`modified_date` datetime DEFAULT NULL,
	`like_count` int(11) NOT NULL DEFAULT 0,
	`comment_count` int(11) NOT NULL DEFAULT 0,
	`view_count` int(11) NOT NULL DEFAULT 0,
	`follow_count` int(11) NOT NULL DEFAULT 0,
	`vote_count` int(11) NOT NULL DEFAULT 0,
	`highlighted` tinyint(1) NOT NULL DEFAULT '0',
	`deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idea_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynfeedback_severities`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_severities` (
  `severity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  PRIMARY KEY (`severity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_ynfeedback_severities` (`title`) VALUES
('Blocker'),
('Critical'),
('Major'),
('Minor'),
('Trivial');

--
-- Table structure for table `engine4_ynfeedback_status`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_status` (
  	`status_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`title` text NOT NULL,
  	`color` text NOT NULL,
  	`creation_date` datetime NOT NULL,
	`modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_ynfeedback_status`
--

INSERT IGNORE INTO `engine4_ynfeedback_status` (`title`, `creation_date`, `modified_date`) VALUES
('Unknown', NOW(), NOW());

--
-- Table structure for table `engine4_ynfeedback_authors`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_authors` (
  	`author_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`user_id` int(11) NOT NULL,
  	`idea_id` int(11) NOT NULL,
  	`creation_date` datetime NOT NULL,
	`modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Table structure for table `engine4_ynfeedback_files`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_files` (
  	`file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`storagefile_id` int(11) NOT NULL,
  	`idea_id` int(11) NOT NULL,
  	`title` VARCHAR(128) NOT NULL,
  	`creation_date` datetime NOT NULL,
	`modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_ynfeedback_screenshots`
--

CREATE TABLE IF NOT EXISTS `engine4_ynfeedback_screenshots` (
  	`screenshot_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  	`photo_id` int(11) NOT NULL,
  	`idea_id` int(11) NOT NULL,
  	`title` VARCHAR(128) NOT NULL,
  	`creation_date` datetime NOT NULL,
	`modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`screenshot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- change length of column type
ALTER TABLE `engine4_authorization_permissions` MODIFY `type` VARCHAR(64);
ALTER TABLE `engine4_authorization_permissions` MODIFY `name` VARCHAR(64);
ALTER TABLE `engine4_activity_notifications` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_notificationtypes` MODIFY  `type` VARCHAR(64) NOT NULL;
ALTER TABLE `engine4_activity_actiontypes` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_actions` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;
ALTER TABLE `engine4_activity_stream` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ;

-- add main menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`, `order`) VALUES
('ynfeedback_main', 'standard', 'YN Feedback Main Navigation Menu', 999),
('ynfeedback_quick', 'standard', 'YN Feedback Quick Navigation Menu', 999);

-- insert quick menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('ynfeedback_quick_create', 'ynfeedback', 'Create New Feedback', 'Ynfeedback_Plugin_Menus::canCreateFeedback', '{"route":"ynfeedback_general","action":"create"}', 'ynfeedback_quick', '', 1);

-- insert back-end menu items
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynfeedback', 'ynfeedback', 'YN - Feedback', '', '{"route":"admin_default","module":"ynfeedback","controller":"settings", "action":"global"}', 'core_admin_main_plugins', '', 999),
('ynfeedback_admin_settings_global', 'ynfeedback', 'Global Settings', '', '{"route":"admin_default","module":"ynfeedback","controller":"settings", "action":"global"}', 'ynfeedback_admin_main', '', 1),
('ynfeedback_admin_settings_level', 'ynfeedback', 'Member Level Settings', '', '{"route":"admin_default","module":"ynfeedback","controller":"settings", "action":"level"}', 'ynfeedback_admin_main', '', 2),
('ynfeedback_admin_main_categories', 'ynfeedback', 'Categories', '', '{"route":"admin_default","module":"ynfeedback","controller":"category", "action":"index"}', 'ynfeedback_admin_main', '', 3),
('ynfeedback_admin_main_feedbacks', 'ynfeedback', 'Manage Feedback', '', '{"route":"admin_default","module":"ynfeedback","controller":"feedbacks", "action":"index"}', 'ynfeedback_admin_main', '', 4),
('ynfeedback_admin_main_button', 'ynfeedback', 'Manage Feedback Button', '', '{"route":"admin_default","module":"ynfeedback","controller":"button", "action":"index"}', 'ynfeedback_admin_main', '', 5),
('ynfeedback_admin_main_polls', 'ynfeedback', 'Manage Poll', '', '{"route":"admin_default","module":"ynfeedback","controller":"polls", "action": "index"}', 'ynfeedback_admin_main', '', 6),
('ynfeedback_admin_main_severity', 'ynfeedback', 'Manage Severity', '', '{"route":"admin_default","module":"ynfeedback","controller":"severity", "action": "index"}', 'ynfeedback_admin_main', '', 7),
('ynfeedback_admin_main_status', 'ynfeedback', 'Manage Status', '', '{"route":"admin_default","module":"ynfeedback","controller":"status", "action":"index"}', 'ynfeedback_admin_main', '', 8);

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_ynfeedback', 'ynfeedback', 'Feedback', '', '{"route":"ynfeedback_general"}', 'core_main', '', 999),
('ynfeedback_main_browse', 'ynfeedback', 'Browse Feedback', 'Ynfeedback_Plugin_Menus::canViewFeedback', '{"route":"ynfeedback_general"}', 'ynfeedback_main', '', 1),
('ynfeedback_main_manage', 'ynfeedback', 'My Feedback', 'Ynfeedback_Plugin_Menus::canManageFeedback', '{"route":"ynfeedback_general","action":"manage"}', 'ynfeedback_main', '', 2),
('ynfeedback_main_create', 'ynfeedback', 'Create New Feedback', 'Ynfeedback_Plugin_Menus::canCreateFeedback', '{"route":"ynfeedback_general","action":"create"}', 'ynfeedback_main', '', 3);

-- Activity Type
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynfeedback_feedback_create', 'ynfeedback', '{item:$subject} create the feedback {item:$object}', 1, 5, 1, 1, 1, 1);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynfeedback_idea_new_screenshot', 'ynfeedback', 'The feedback {item:$subject} that you follow has added new screenshots.', 0, ''),
('ynfeedback_idea_new_file', 'ynfeedback', 'The feedback {item:$subject} that you follow has added new files.', 0, ''),
('ynfeedback_idea_new_comment', 'ynfeedback', 'The feedback {item:$subject} that you follow has new comments.', 0, ''),
('ynfeedback_idea_edit', 'ynfeedback', 'The feedback {item:$subject} has been edited.', 0, ''),
('ynfeedback_idea_change_status', 'ynfeedback', 'The feedback {item:$subject} has been changed status.', 0, ''),
('ynfeedback_idea_merge_follow', 'ynfeedback', 'The feedback {item:$subject} that you follow has been merged into feedback {item:$object}.', 0, ''),
('ynfeedback_idea_merge_owner', 'ynfeedback', 'Your feedback {item:$subject} has been merged.', 0, ''),
('ynfeedback_idea_merge', 'ynfeedback', 'The feedback {item:$subject} has been merged into feedback {item:$object}.', 0, ''),
('ynfeedback_idea_signupmerge', 'ynfeedback', 'We have merged your email with all previous feedbacks and comments. {url:$params:$innerHTML}', 0, '');

--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynfeedback_idea_new_screenshot', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_new_file', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_new_comment', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_edit', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_change_status', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_merge_follow', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_merge_owner', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_merge', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynfeedback_idea_signupmerge', 'ynfeedback', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');


-- set default authorization for member level settings
-- ALL
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynfeedback_idea' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynfeedback_idea' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN - MODERATOR
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_screenshot' as `name`,
	3 as `value`,
	5 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_screenshotsize' as `name`,
	3 as `value`,
	1000 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_file' as `name`,
	3 as `value`,
	5 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_filesize' as `name`,
	3 as `value`,
	1000 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'file_ext' as `name`,
	3 as `value`,
	'' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'edit' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'delete' as `name`,
	2 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_screenshot' as `name`,
	3 as `value`,
	5 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_screenshotsize' as `name`,
	3 as `value`,
	1000 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_file' as `name`,
	3 as `value`,
	5 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'max_filesize' as `name`,
	3 as `value`,
	1000 as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'file_ext' as `name`,
	3 as `value`,
	'' as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'view' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'create' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'edit' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'delete' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
	level_id as `level_id`,
	'ynfeedback_idea' as `type`,
	'comment' as `name`,
	1 as `value`,
	NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynfeedback_idea' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynfeedback_idea' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
SELECT
    level_id as `level_id`,
    'ynfeedback_idea' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
FROM `engine4_authorization_levels` WHERE `type` IN('public');
