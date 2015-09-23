DELETE FROM `engine4_contactimporter_providers` 
WHERE `name` NOT IN
(SELECT b.`name` FROM
	(SELECT * FROM `engine4_contactimporter_providers`) as b
 WHERE 
b.`name` like '%gmail%' or 
b.`name` like '%yahoo%' or 
b.`name` like '%sapo%' or 
b.`name` like '%mail2world%' or 
b.`name` like '%linkedin%' or   
b.`name` like '%facebook%' or 
b.`name` like '%hyves%' or 
b.`name` like '%kincafe%' or 
b.`name` like '%myspace%' or 
b.`name` like '%netlog%' or 
b.`name` like '%perfspot%' or 
b.`name` like '%twitter%');

INSERT IGNORE INTO `engine4_contactimporter_providers` (`name`, `title`, `logo`, `enable`, `status`, `type`, `description`, `requirement`, `check_url`, `version`, `base_version`, `supported_domain`, `order`, `default_domain`, `photo_import`, `photo_enable`, `o_type`) VALUES
('hotmail', 'Live/Hotmail', 'hotmail', 1, 1, 'email', 'Get the contacts from a Windows Live/Hotmail account', 'email', 'http://login.live.com/login.srf?id=2', '1.6.4', '1.8.0', 'a:4:{i:0;s:7:"hotmail";i:1;s:4:"live";i:2;s:3:"msn";i:3;s:8:"chaishop";}', 3, 'hotmail.com', 0, 0, 'email');

UPDATE `engine4_core_modules` SET `version` = '4.03' WHERE `name` = 'contactimporter';
DELETE FROM `engine4_core_menuitems` WHERE `engine4_core_menuitems`.`name` = 'contactimporter_admin_main_fbsetting';

DROP TABLE `engine4_contactimporter_apisettings`, `engine4_contactimporter_configs`;

UPDATE `engine4_contactimporter_providers` SET `name` = 'facebook',
`logo` = 'facebook' WHERE `engine4_contactimporter_providers`.`name` = 'facebook_';