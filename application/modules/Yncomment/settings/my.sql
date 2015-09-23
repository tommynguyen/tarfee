INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('yncomment', 'YN - Advanced Comment', 'Advanced Comment Plugin - Nested Comments, Replies, Voting & Attachments', '4.01', 1, 'extra') ;

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_admin_main_plugins_yncomment', 'yncomment', 'YN - Advanced Comment', '', '{"route":"admin_default","module":"yncomment","controller":"settings"}', 'core_admin_main_plugins', '', 999),
('yncomment_admin_main_settings', 'yncomment', 'Global Settings', '', '{"route":"admin_default","module":"yncomment","controller":"settings"}', 'yncomment_admin_main', '', 1),
('yncomment_admin_main_activitySettings', 'yncomment', 'Advanced Activity Settings', 'Yncomment_Plugin_Core', '{"route":"admin_default","module":"yncomment","controller":"settings", "action":"activity-settings"}', 'yncomment_admin_main', '', 2);

CREATE TABLE IF NOT EXISTS `engine4_yncomment_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `resource_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `params` varchar(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT '{"taggingContent":["friends"],"showComposerOptions":["addLink","addPhoto"],"showAsNested":"1","showAsLike":"1","showDislikeUsers":"0","showLikeWithoutIcon":"0","showLikeWithoutIconInReplies":"0","loaded_by_ajax":"1"}',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `module` (`module`,`resource_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_yncomment_modules` (`module`, `resource_type`, `enabled`, `params`) VALUES 
("ynfeed", "ynfeed", "1", '{"module":"ynfeed","resource_type":"ynfeed","taggingContent":["friends"],"ynfeed_comment_show_bottom_post":"1","ynfeed_comment_like_box":"0","showComposerOptions":["addSmilies","addLink","addPhoto"],"showAsLike":"1","showDislikeUsers":"1","showLikeWithoutIcon":"1","showLikeWithoutIconInReplies":"1"}');

CREATE TABLE IF NOT EXISTS `engine4_yncomment_dislikes` (
  `dislike_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`dislike_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `poster_type` (`poster_type`,`poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_yncomment_emoticons` (
`emoticon_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
`title` char(100) NOT NULL,
`text` varchar(200) NOT NULL,
`image` char(100) NOT NULL,
`ordering` smallint(4) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (`emoticon_id`),
UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `engine4_yncomment_emoticons`
--

INSERT IGNORE INTO `engine4_yncomment_emoticons` (`emoticon_id`, `title`, `text`, `image`, `ordering`) VALUES
(1, 'angel', '(angel)', 'angel.gif', 0),
(2, 'angry', ':@', 'angry.gif', 0),
(3, 'bearhug', '(hug)', 'bearhug.gif', 0),
(4, 'beer', '(beer)', 'beer.gif', 0),
(5, 'blush', '(blush)', 'blush.gif', 0),
(6, 'bow', '(bow)', 'bow.gif', 0),
(7, 'boxing', '(punch)', 'boxing.gif', 0),
(8, 'brokenheart', '(u)', 'brokenheart.gif', 0),
(9, 'cake', '(^)', 'cake.gif', 0),
(10, 'callme', '(call)', 'callme.gif', 0),
(11, 'cash', '(cash)', 'cash.gif', 0),
(12, 'cellphone', '(mp)', 'cellphone.gif', 0),
(13, 'clapping', '(clap)', 'clapping.gif', 0),
(14, 'coffee', '(coffee)', 'coffee.gif', 0),
(15, 'cool', '8-)', 'cool.gif', 0),
(16, 'crying', ';(', 'crying.gif', 0),
(17, 'dance', '(dance)', 'dance.gif', 0),
(18, 'devil', '(devil)', 'devil.gif', 0),
(19, 'doh', '(doh)', 'doh.gif', 0),
(20, 'drink', '(d)', 'drink.gif', 0),
(21, 'dull', '|-(', 'dull.gif', 0),
(22, 'emo', '(emo)', 'emo.gif', 0),
(23, 'evilgrin', ']:)', 'evilgrin.gif', 0),
(24, 'flex', '(flex)', 'flex.gif', 0),
(25, 'flower', '(F)', 'flower.gif', 0),
(26, 'giggle', '(chuckle)', 'giggle.gif', 0),
(27, 'handshake', '(handshake)', 'handshake.gif', 0),
(28, 'happy', '(happy)', 'happy.gif', 0),
(29, 'heart', '(h)', 'heart.gif', 0),
(30, 'hi', '(wave)', 'hi.gif', 0),
(31, 'inlove', '(inlove)', 'inlove.gif', 0),
(32, 'itwasntme', '(wasntme)', 'itwasntme.gif', 0),
(33, 'jealous', '(envy)', 'jealous.gif', 0),
(34, 'kiss', ':*', 'kiss.gif', 0),
(35, 'laughing', ':D', 'laughing.gif', 0),
(36, 'mail', '(e)', 'mail.gif', 0),
(37, 'makeup', '(makeup)', 'makeup.gif', 0),
(38, 'mmm', '(mm)', 'mmm.gif', 0),
(39, 'music', '(music)', 'music.gif', 0),
(40, 'nerd', '8-|', 'nerd.gif', 0),
(41, 'no', '(n)', 'no.gif', 0),
(42, 'nod', '(nod)', 'nod.gif', 0),
(43, 'nospeak', ':x', 'nospeak.gif', 0),
(44, 'party', '(party)', 'party.gif', 0),
(45, 'puke', '(puke)', 'puke.gif', 0),
(46, 'rofl', '(rofl)', 'rofl.gif', 0),
(47, 'sad', ':(', 'sad.gif', 0),
(48, 'shakeno', '(shake)', 'shakeno.gif', 0),
(49, 'smile', ':)', 'smile.gif', 0),
(50, 'speechless', ':-|', 'speechless.gif', 0),
(51, 'sweating', '(sweat)', 'sweating.gif', 0),
(52, 'thinking', '(think)', 'thinking.gif', 0),
(53, 'tongue out', ':p', 'tongueout.gif', 0),
(54, 'wait', '(wait)', 'wait.gif', 0),
(55, 'whew', '(whew)', 'whew.gif', 0),
(56, 'wink', ';)', 'wink.gif', 0),
(57, 'worried', ':S', 'worried.gif', 0),
(58, 'yes', '(y)', 'yes.gif', 0);

CREATE TABLE IF NOT EXISTS `engine4_yncomment_hide` (
`user_id` INT( 11 ) NOT NULL ,
`hide_resource_type` VARCHAR( 128 ) NOT NULL ,
`hide_resource_id` INT( 11 ) NOT NULL ,
PRIMARY KEY ( `user_id` , `hide_resource_type`, `hide_resource_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;