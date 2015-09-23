DELETE FROM `engine4_contactimporter_providers` 
WHERE `name` NOT IN
(SELECT b.`name` FROM
	(SELECT * FROM `engine4_contactimporter_providers`) as b
 WHERE 
b.`name` like '%gmail%' or 
b.`name` like '%yahoo%' or 
b.`name` like '%linkedin%' or   
b.`name` like '%hotmail%' or 
b.`name` like '%facebook%' or 
b.`name` like '%twitter%');