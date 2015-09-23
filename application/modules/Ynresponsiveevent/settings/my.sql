INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynresponsiveevent', 'YN - Responsive Event Theme', 'YN - Responsive Event Theme', '4.01p4', 1, 'extra') ;

UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-hot-events' where `name` = 'ynresponsive1.event-hot-events';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-popular-events' where `name` = 'ynresponsive1.event-popular-events';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-top-sponsors' where `name` = 'ynresponsive1.event-top-sponsors';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-mini-menu' where `name` = 'ynresponsive1.event-mini-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-main-menu' where `name` = 'ynresponsive1.event-main-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-footer-about' where `name` = 'ynresponsive1.event-footer-about';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-footer-menu' where `name` = 'ynresponsive1.event-footer-menu';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-slide-events' where `name` = 'ynresponsive1.event-slide-events';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-search-events' where `name` = 'ynresponsive1.event-search-events';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-personalize' where `name` = 'ynresponsive1.event-personalize';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-search-listing' where `name` = 'ynresponsive1.event-search-listing';
UPDATE `engine4_core_content` SET `name` = 'ynresponsiveevent.event-categories' where `name` = 'ynresponsive1.event-categories';

UPDATE `engine4_core_pages` SET `name` = 'ynresponsiveevent_index_event' where `name` = 'ynresponsive1_index_event';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_ynresponsiveevent', 'ynresponsiveevent', 'YN - Responsive Event', '', '{"route":"admin_default","module":"ynresponsiveevent","controller":"manage-events"}', 'core_admin_main_plugins', '', 999);

DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_events';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'ynresponsive1_admin_main_manage_sponsors';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynresponsiveevent_admin_main_manage_events', 'ynresponsiveevent', 'Manage Events', '', '{"route":"admin_default","module":"ynresponsiveevent","controller":"manage-events"}', 'ynresponsiveevent_admin_main', '', 1, 0, 1),
('ynresponsiveevent_admin_main_manage_sponsors', 'ynresponsiveevent', 'Manage Sponsors', '', '{"route":"admin_default","module":"ynresponsiveevent","controller":"manage-sponsors"}', 'ynresponsiveevent_admin_main', '', 1, 0, 2);

CREATE TABLE IF NOT EXISTS `engine4_ynresponsive1_events` (
  `event_id` int(11) NOT NULL COMMENT 'reference from event_id',
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `location` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `engine4_ynresponsive1_sponsors` (
  `sponsor_id` int(11) NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL COMMENT 'reference from event_id',
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

