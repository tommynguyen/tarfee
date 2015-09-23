-- 2014-07-21
ALTER TABLE `engine4_slprofileverify_requires` ADD `image_number` TINYINT NOT NULL DEFAULT '1' AFTER `image`;
ALTER TABLE `engine4_slprofileverify_customs` ADD `image_number` TINYINT NOT NULL DEFAULT '1' AFTER `image`;
ALTER TABLE `engine4_slprofileverify_slprofileverifies` CHANGE `file_id` `file_id` CHAR(50) NULL DEFAULT NULL;
ALTER TABLE `engine4_slprofileverify_slprofileverifies` CHANGE `file_id_cus` `file_id_cus` CHAR(50) NULL DEFAULT NULL;