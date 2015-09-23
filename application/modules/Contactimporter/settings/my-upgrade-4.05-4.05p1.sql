UPDATE `engine4_core_modules` SET `version` = '4.05p1' where `name` = 'contactimporter';

UPDATE `engine4_core_content` SET `params` = '{"title":"Top Inviters"}' where `name` = 'contactimporter.top-inviters';
UPDATE `engine4_core_content` SET `params` = '{"title":"Inviter Statistics"}' where `name` = 'contactimporter.statistics';