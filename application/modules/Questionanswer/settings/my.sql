

-- --------------------------------------------------------

--
-- Table structure for table `engine4_m2b_qa_answers`
--

CREATE TABLE `engine4_questionanswer_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`answer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_questionanswer_cats`
--

CREATE TABLE `engine4_questionanswer_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `engine4_questionanswer_cats`
--

insert  into `engine4_questionanswer_cats`(`id`,cat_name) values (1,'general');

-- --------------------------------------------------------

--
-- Table structure for table `engine4_m2b_qa_questions`
--

CREATE TABLE `engine4_questionanswer_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `answers` int(11) DEFAULT '0',
  `like` int(11) DEFAULT '1',
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_questionanswer', 'questionanswer', 'Q&A', '', '{"route":"default","module":"qa"}', 'core_main', '', 10),
('core_admin_main_plugins_questionanswer', 'questionanswer', 'Q&A', '', '{"route":"admin_default","module":"questionanswer","controller":"manage"}', 'core_admin_main_plugins', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('questionanswer', 'Q&A', 'Question & Answer', '4.01', 1, 'extra');

-- --------------------------------------------------------------

--
-- Table structure for table `engine4_questionanswer_questionvotes`
--

CREATE TABLE `engine4_questionanswer_questionvotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_questionanswer_reports`
--

CREATE TABLE `engine4_questionanswer_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `report_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `report_url` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `posted_date` datetime DEFAULT NULL,
  `is_read` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE  INTO `engine4_activity_actiontypes`(`type`,`module`,`body`,`enabled`,displayable,`attachable`,commentable,shareable,is_generated) 
VALUES ('answer_new','questionanswer','{item:$subject} has answered "{var:$body}" to question "{var:$question}"',1,5,1,1,1,1),
('question_new','questionanswer','{item:$subject} has posted question "{var:$body}" to the Q&A',1,5,1,1,1,1);

--
-- Dumping data for table `engine4_activity_notificationtypes`
--

INSERT IGNORE  INTO `engine4_activity_notificationtypes`(`type`,`module`,`body`,`is_request`,handler) 
VALUES ('answer_posted','questionanswer','{item:$subject} has posted an answer on a {item:$object:$label} you created.',0,'');

--
-- Dumping data for table `engine4_core_pages`
--
INSERT IGNORE  INTO `engine4_core_pages`(`name`,displayname,`url`,`title`,`description`,`keywords`,`custom`,fragment,layout,view_count) 
VALUES ('questionanswer_index_index','Q&A Page','','Question & Answer','This is the page question & answer','',0,0,'',0);

--
-- Dumping data for table `engine4_core_content`
--
INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'), 'container', 'main', NULL, '2', '[""]', NULL);

INSERT IGNORE INTO `engine4_core_content`(`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, attribs)
VALUES ((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'), 'container', 'left', (SELECT LAST_INSERT_ID()) , '4', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'), 'container', 'middle', (SELECT LAST_INSERT_ID()) , '6', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'), 'container', 'right', (SELECT LAST_INSERT_ID()) , '5', '[""]', NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'),'widget','questionanswer.top-answer',(SELECT LAST_INSERT_ID() + 1),3,'{\"title\":\"Top Answers\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'),'widget','questionanswer.full-question-answer',(SELECT LAST_INSERT_ID() + 2),5,'{\"title\":\"Q&A\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'),'widget','questionanswer.top-question',(SELECT LAST_INSERT_ID() + 3),7,'{\"title\":\"Top Questions\"}',NULL),
((SELECT `page_id` FROM `engine4_core_pages` WHERE `name`='questionanswer_index_index'),'widget','questionanswer.top-user',(SELECT LAST_INSERT_ID() + 3),8,'{\"title\":\"Top Users\"}',NULL);

