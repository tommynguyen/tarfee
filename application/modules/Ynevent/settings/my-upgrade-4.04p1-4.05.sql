UPDATE `engine4_core_modules` SET `version` = '4.05' WHERE `name` = 'ynevent';

-- Update `engine4_activity_actiontypes`
UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} posted a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_create' AND `module` = 'ynevent';

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} replied to a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_reply' AND `module` = 'ynevent';


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('ynevent_admin_main_reviews', 'ynevent', 'Reviews', '', '{"route":"admin_default","module":"ynevent","controller":"reviews"}', 'ynevent_admin_main', '', 1, 0, 6),
('ynevent_admin_main_fields', 'ynevent', 'Custom Fields', '', '{"route":"admin_default","module":"ynevent","controller":"fields"}', 'ynevent_admin_main', '', 1, 0, 5);

CREATE TABLE IF NOT EXISTS `engine4_event_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned DEFAULT '1',
  `search` tinyint(1) unsigned DEFAULT '0',
  `show` tinyint(1) unsigned DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `engine4_event_events` 
ADD `event_of_date` date DEFAULT NULL,
ADD `metadata` varchar(255) collate utf8_unicode_ci default NULL,
ADD `cover_photo` int(11) ,
ADD `online` tinyint(1) DEFAULT 0;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('friend_host', 'ynevent', 'You have been added as host of event {item:$object}', 0, ''),
('event_import_blog', 'ynevent', 'Your blog {item:$subject} has been added to event {item:$object}', 0, '');

ALTER TABLE `engine4_event_photos` 
ADD `is_featured` tinyint(1) DEFAULT 1;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_highlights` (
  `highlight_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`highlight_id`, `event_id`, `item_id`),
  KEY `user_id` (`event_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_announcements` (
  `announcement_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `report_count`int(11) DEFAULT 0,
  `creation_date` datetime NOT NULL,
  
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `engine4_ynevent_reviewreports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynevent_main_calendar';

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Advanced Event Notification', 'ynevent', 'Ynevent_Plugin_Task_Notification', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('ynevent_notify_start', 'ynevent', 'The event {item:$object} already started.', 0, '', 1),
('ynevent_notify_end', 'ynevent', 'The event {item:$object} almost ended.', 0, '', 1);

INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('notify_ynevent_notify_start', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynevent_notify_end', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]');