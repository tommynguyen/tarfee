-- Menu item
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynevent_profile_transfer', 'ynevent', 'Transfer Owner', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '',1,0,7);
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynevent_transfer', 'ynevent', '{item:$subject} has became the owner of the event {item:$object}', 1, 3, 1, 1, 1, 1);
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynevent_transfer', 'ynevent', 'You were set to become the owner of the event {item:$object}.', 0, '');