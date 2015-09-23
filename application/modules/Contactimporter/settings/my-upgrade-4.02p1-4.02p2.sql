DELETE FROM `engine4_contactimporter_providers` 
WHERE `name` NOT IN
(SELECT b.`name` FROM
	(SELECT * FROM `engine4_contactimporter_providers`) as b
 WHERE 
b.`name` like '%gmail%' or 
b.`name` like '%yahoo%' or 
b.`name` like '%aol%' or 
b.`name` like '%rambler%' or 
b.`name` like '%sapo%' or 
b.`name` like '%mail2world%' or 
b.`name` like '%azet%' or 
b.`name` like '%bigstring%' or 
b.`name` like '%linkedin%' or 
b.`name` like '%mynet%' or 
b.`name` like '%plaxo%' or 
b.`name` like '%youtube%' or 
b.`name` like '%facebook%' or 
b.`name` like '%famiva%' or 
b.`name` like '%fdcareer%' or 
b.`name` like '%friendfeed%' or 
b.`name` like '%hi5%' or 
b.`name` like '%hyves%' or 
b.`name` like '%kincafe%' or 
b.`name` like '%myspace%' or 
b.`name` like '%netlog%' or 
b.`name` like '%perfspot%' or 
b.`name` like '%twitter%');

UPDATE `engine4_core_modules` SET `version` = '4.02p2' WHERE `name` = 'contactimporter';
