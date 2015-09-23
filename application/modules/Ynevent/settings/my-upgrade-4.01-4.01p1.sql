UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'ynevent';


alter table    `engine4_event_categories` add column `parent_id` int(11) unsigned not null default 0;
