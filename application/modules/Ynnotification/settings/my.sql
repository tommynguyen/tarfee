INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES  
('core_admin_main_plugins_ynnotification', 'ynnotification', 'Adv Notification', '', '{"route":"admin_default","module":"ynnotification","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('ynnotification_admin_main_settings', 'ynnotification', 'Notification Settings', '', '{"route":"admin_default","module":"ynnotification","controller":"settings"}', 'ynnotification_admin_main', '', 2),
('ynnotification_admin_main_styles', 'ynnotification', 'Style Settings', '', '{"route":"admin_default","module":"ynnotification","controller":"styles"}', 'ynnotification_admin_main', '', 3),
('ynnotification_admin_main_sounds', 'ynnotification', 'Sound Alert Settings', '', '{"route":"admin_default","module":"ynnotification","controller":"sounds"}', 'ynnotification_admin_main', '', 4),
('user_settings_sound_notification', 'user', 'Sound Notification Settings', 'Ynnotification_Plugin_Menus', '{"route":"default","module":"ynnotification","controller":"index","action":"sound-settings"}', 'user_settings', '', 999)
;

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('ynnotification.time.refresh',  '120000'),
('ynnotification.time.deplay',  '10000'),
('ynnotification.photo.notification',  1),
('ynnotification.sound.setting',  0),
('avdnotification.customcssobj',  '{"mes_background":"79B4D4","text_color":"FFFFFF"}'),
('ynnotification.user.sound.setting',  1)
;
INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynnotification', 'Advanced Notification', 'Advanced Notification', '4.01p2', 1, 'extra') ;

ALTER TABLE `engine4_activity_notifications`  
ADD COLUMN `advnotification` tinyint(1) NOT NULL default '0';



