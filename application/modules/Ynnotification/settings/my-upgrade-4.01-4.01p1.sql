UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `engine4_core_modules`.`name` = 'ynnotification' LIMIT 1 ;

ALTER TABLE `engine4_activity_notifications`  
ADD COLUMN `advnotification` tinyint(1) NOT NULL default '0';

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('avdnotification.customcssobj',  '{"mes_background":"79B4D4","text_color":"FFFFFF"}');



  