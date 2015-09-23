ALTER TABLE `engine4_yntour_tours` ADD COLUMN `bodyid` tinytext NOT NULL DEFAULT '' AFTER `path`;
ALTER TABLE `engine4_yntour_tours` ADD COLUMN `option` tinyint(4) NOT NULL DEFAULT '0' AFTER `path`;
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ('core_mini_yntouradv', 'yntour', 'Tour Guide', 'Yntour_Plugin_Menus', '', 'advmenusystem_mini', NULL, 1, 0, 99);
