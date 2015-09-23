UPDATE `engine4_core_modules` SET `version` = '4.07p2' WHERE `name` = 'advalbum';
ALTER TABLE `engine4_album_albums` ADD `like_count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `view_count` ;
ALTER TABLE `engine4_album_photos` ADD `like_count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `view_count`;