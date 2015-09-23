UPDATE `engine4_core_modules` SET `version` = '4.05' where `name` = 'contactimporter';
DELETE FROM `engine4_core_menuitems` WHERE `name` = 'contactimporter_main_pending';
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_main_queue_email', 'contactimporter', 'Queue Emails', '', '{"route":"contactimporter_general","action":"queue-email"}', 'contactimporter_main', '', 2),
('contactimporter_main_queue_message', 'contactimporter', 'Queue Messages', '', '{"route":"contactimporter_general","action":"queue-message"}', 'contactimporter_main', '', 3),
('contactimporter_main_pending_invitation', 'contactimporter', 'Pending Invitations', '', '{"route":"contactimporter_general","action":"pending-invitation"}', 'contactimporter_main', '', 4);

CREATE TABLE IF NOT EXISTS `engine4_contactimporter_invitations` (
  `invitation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inviter_id` int(11) NOT NULL,
  `uid` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `uname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `service` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `mail_id` INT(11),
  `message` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `creation_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `inviter_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invitation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `engine4_contactimporter_joined` (
  `joined_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inviter_id` int(11) NOT NULL DEFAULT '0',
  `recipient_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`joined_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


