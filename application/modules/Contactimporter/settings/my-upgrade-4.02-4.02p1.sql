INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('contactimporter_main', 'standard', 'Contact Importer Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_main_invite', 'contactimporter', 'Invite Friends', '', '{"route":"contactimporter","action":"import"}', 'contactimporter_main', '', 1),
('contactimporter_main_pending', 'contactimporter', 'Pending Invites', '', '{"route":"contactimporter_pending","action":"pending"}', 'contactimporter_main', '', 2);

UPDATE `engine4_core_modules` SET `version` = '4.02p1' WHERE `name` = 'contactimporter';