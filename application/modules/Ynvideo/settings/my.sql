/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynvideo', 'YouNet Video Plugin', 'YouNet Video Plugin', '4.01p3', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES ('ynvideo_main', 'standard', 'Advanced Video Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynvideo_admin_main_level', 'ynvideo', 'Member Level Settings', '', '{"route":"admin_default","module":"ynvideo","controller":"settings","action":"level"}', 'ynvideo_admin_main', '', 1, 0, 4),
('ynvideo_admin_main_categories', 'ynvideo', 'Categories', '', '{"route":"admin_default","module":"ynvideo","controller":"settings","action":"categories"}', 'ynvideo_admin_main', '', 1, 0, 5),
('ynvideo_main_browse', 'ynvideo', 'Video Home', '', '{"route":"video_general"}', 'ynvideo_main', '', 1, 0, 900),
('ynvideo_main_listings', 'ynvideo', 'Listings', '', '{"route":"video_general","action":"list"}', 'ynvideo_main', '', 1, 0, 901),
('ynvideo_main_manage', 'ynvideo', 'My Videos', 'Ynvideo_Plugin_Menus', '{"route":"video_general","action":"manage"}', 'ynvideo_main', '', 1, 0, 902),
('ynvideo_main_favorite_video', 'ynvideo', 'Favorite Videos', 'Ynvideo_Plugin_Menus', '{"route":"video_favorite","action":"index"}', 'ynvideo_main', '', 1, 0, 904),
('ynvideo_main_playlist', 'ynvideo', 'My Playlists', 'Ynvideo_Plugin_Menus', '{"route":"video_playlist","action":"index"}', 'ynvideo_main', '', 1, 0, 905),
('ynvideo_main_watch_later', 'ynvideo', 'Watch Later', 'Ynvideo_Plugin_Menus', '{"route":"video_watch_later","action":"index"}', 'ynvideo_main', '', 1, 0, 906),
('ynvideo_main_create', 'ynvideo', 'Post New Video', 'Ynvideo_Plugin_Menus', '{"route":"video_general","action":"create"}', 'ynvideo_main', '', 1, 0, 907),
('ynvideo_admin_main_manage', 'ynvideo', 'Manage Videos', '', '{"route":"admin_default","module":"ynvideo","controller":"manage"}', 'ynvideo_admin_main', '', 1, 0, 1),
('ynvideo_admin_main_utility', 'ynvideo', 'Video Utilities', '', '{"route":"admin_default","module":"ynvideo","controller":"settings","action":"utility"}', 'ynvideo_admin_main', '', 1, 0, 2),
('ynvideo_admin_main_settings', 'ynvideo', 'Global Settings', '', '{"route":"admin_default","module":"ynvideo","controller":"settings"}', 'ynvideo_admin_main', '', 1, 0, 3);

ALTER TABLE `engine4_video_categories`
ADD `parent_id` INT DEFAULT '0',
ADD `ordering` SMALLINT(5) DEFAULT '0',
ADD `photo_url` TINYTEXT;

CREATE TABLE IF NOT EXISTS `engine4_video_favorites` (
  `favorite_id` int(10) NOT NULL AUTO_INCREMENT,
  `video_id` int(10) DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`favorite_id`),
  UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_video_playlistassoc` (
  `playlistassoc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL DEFAULT '0',
  `video_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`playlistassoc_id`),
  UNIQUE KEY `playlist_id_video_id` (`playlist_id`,`video_id`),
  KEY `creation_time` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_video_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `creation_date` datetime NOT NULL,
  `ordering` smallint(8) unsigned NOT NULL DEFAULT '999',
  `search` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `title` varchar(256) NOT NULL DEFAULT '',
  `photo_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `modified_date` datetime NOT NULL,
  `video_count` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playlist_id`),
  KEY `user_id` (`user_id`),
  KEY `creation_date` (`creation_date`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_video_signatures` (
  `signature_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `video_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`signature_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `engine4_video_videos`
ADD `featured` TINYINT(1) DEFAULT '0',
ADD `sponsored` TINYINT(1) DEFAULT '0',
ADD `favorite_count` int(11) DEFAULT '0',
ADD `large_photo_id` int(11) NULL,
ADD `subcategory_id` int(11) DEFAULT '0';

CREATE TABLE IF NOT EXISTS `engine4_video_watchlaters` (
  `watchlater_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `watched` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `watched_date` datetime NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`watchlater_id`),
  UNIQUE KEY `video_id_user_id` (`video_id`,`user_id`),
  KEY `video_id` (`video_id`),
  KEY `user_id` (`user_id`),
  KEY `watched` (`watched`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- insert authorization_permissions
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_favorite' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_favorite' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_favorite' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_favorite' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'create' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user', 'public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlistasso' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlistasso' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'max' as `name`,
    3 as `value`,
    100 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'max' as `name`,
    3 as `value`,
    500 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'remove' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
--
-- Dumping data for table `engine4_activity_actiontypes`
--
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynvideo_video_new', 'ynvideo', '{item:$subject} posted a new video:', 1, 5, 1, 3, 1, 0),
('ynvideo_add_favorite', 'ynvideo', '{item:$subject} add a video to his/her favorite playlist', 1, 7, 1, 3, 1, 0),
('ynvideo_playlist_new', 'ynvideo', '{item:$subject} posted a new playlist:', 1, 5, 1, 3, 1, 0),
('ynvideo_comment_video', 'ynvideo', '{item:$subject} commented on {item:$owner}\'s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0),
('comment_ynvideo_playlist', 'ynvideo', '{item:$subject} commented on {item:$owner}\'s {item:$object:video}: {body:$body}', 1, 1, 1, 1, 1, 0),
('ynvideo_playlist_add_video', 'ynvideo', '{item:$subject} add a video to the playlist {item:$object}', 1, 5, 1, 3, 1, 0),
('ynvideo_add_video_new_playlist', 'ynvideo', '{item:$subject} created a new {item:$object:playlist} and added a video to this playlist', 1, 5, 1, 3, 1, 0);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES
('ynvideo_processed', 'ynvideo', 'Your {item:$object:video} is ready to be viewed.', 0, '', 1),
('ynvideo_processed_failed', 'ynvideo', 'Your {item:$object:video} has failed to process.', 0, '', 1);

--
-- Dumping data for table `engine4_core_mailtemplates`
--
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynvideo_processed', 'ynvideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynvideo_processed_failed', 'ynvideo', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description].[messge]'),
('ynvideo_send_video_to_friends', 'ynvideo', '[host],[email],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');

--
-- Dumping data for table `engine4_core_jobtypes`
--
INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `form`, `enabled`, `priority`, `multi`) VALUES
('Advanced Video Encode', 'ynvideo_encode', 'ynvideo', 'Ynvideo_Plugin_Job_Encode', NULL, 1, 75, 2),
('Rebuild Advanced Video Privacy', 'ynvideo_maintenance_rebuild_privacy', 'ynvideo', 'Ynvideo_Plugin_Job_Maintenance_RebuildPrivacy', NULL, 1, 50, 1);

--
-- Dumping data for table `engine4_core_settings`
--
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('ynvideo.ffmpeg.path', ''),
('ynvideo.jobs', 2),
('ynvideo.embeds', 1);

-- Update video auth_view,auth_comment
UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'video' and `name` = 'auth_view';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'video' and `name` = 'auth_comment';