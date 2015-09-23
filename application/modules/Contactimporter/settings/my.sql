/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Younet
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Bootstrap.php 7244 2010-09-28 01:49:53Z son $
 * @author     Son
 */

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Delete old database settings (except users_signup table; update at next step)
--

DELETE FROM `engine4_core_menuitems` WHERE `name`='core_main_contact-importer'OR `name`='core_admin_main_plugins_contact-importer' OR `name`='core_main_contactimporter'OR `name`='core_admin_main_plugins_contactimporter' OR `module` = 'contactimporter' OR `module` = 'contact-importer';

-- --------------------------------------------------------

--
-- Delete data for table `engine4_core_modules`
--
DELETE FROM `engine4_core_modules` WHERE `name`='contact-importer' OR `name`='contactimporter';
DELETE FROM `engine4_core_menuitems` WHERE `name`='contactimporter_admin_main_level' OR `name`='contactimporter_admin_main_settings' OR `name`='contactimporter_admin_main_providers' OR `name`='contactimporter_admin_main' OR `module`= 'contactimporter' OR `module`= 'contact-importer';

-- Delete permissions

DELETE FROM `engine4_authorization_permissions` WHERE `type`='contactimporter';

DROP TABLE IF EXISTS `engine4_contactimporter_providers` ;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

DELETE FROM `engine4_core_menuitems` WHERE `name`='core_main_contactimporter'OR `name`='core_admin_main_plugins_contactimporter';

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_contactimporter', 'contactimporter', 'Inviter', '', '{"route":"contactimporter","module":"contactimporter"}', 'core_main', '', 99),
('core_admin_main_plugins_contactimporter', 'contactimporter', 'YN - Contact Importer', '', '{"route":"admin_default","module":"contactimporter","controller":"settings"}', 'core_admin_main_plugins', '', 999);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('contactimporter', 'Contactimporter', 'YN - Contact Importer; ', '4.05', 1, 'extra');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_admin_main_level', 'contactimporter', 'Member Level Settings', '', '{"route":"admin_default","module":"contactimporter","controller":"settings","action":"level"}', 'contactimporter_admin_main', '', 2);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_admin_main_settings', 'contactimporter', 'Global Settings', '', '{"route":"admin_default","module":"contactimporter","controller":"settings","action":"index"}', 'contactimporter_admin_main', '', 1);
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_admin_main_providers', 'contactimporter', 'Providers', '', '{"route":"admin_default","module":"contactimporter","controller":"manage"}', 'contactimporter_admin_main', '', 3);


INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
    (1, 'contactimporter', 'max', 3, '60'),
    (2, 'contactimporter', 'max', 3, '60'),
    (3, 'contactimporter', 'max', 3, '60'),
    (4, 'contactimporter', 'max', 3, '60'),
    (5, 'contactimporter', 'max', 3, '60');

-- Update sign-up invitation

UPDATE `engine4_user_signup` SET `class` = 'Contactimporter_Plugin_Signup_Invite', `enable` = 1 WHERE `class` = 'User_Plugin_Signup_Invite' OR `class` = 'ContactImporter_Plugin_Signup_Invite';

--
-- Table structure for table `engine4_contactimporter_providers`
--

DROP TABLE IF EXISTS `engine4_contactimporter_providers` ;

CREATE TABLE IF NOT EXISTS `engine4_contactimporter_providers` (
  `name` varchar(10) NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `enable` int(2) NOT NULL DEFAULT '1',
  `status` int(2) NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `requirement` varchar(20) DEFAULT NULL,
  `check_url` varchar(100) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `base_version` varchar(20) DEFAULT NULL,
  `supported_domain` longtext,
  `order` int(2) NOT NULL DEFAULT '200',
  `default_domain` varchar(20) DEFAULT NULL,
  `photo_import` int(1) NOT NULL DEFAULT '0',
  `photo_enable` int(1) NOT NULL DEFAULT '0',
  `o_type` varchar(20) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `engine4_contactimporter_providers` (`name`, `title`, `logo`, `enable`, `status`, `type`, `description`, `requirement`, `check_url`, `version`, `base_version`, `supported_domain`, `order`, `default_domain`, `photo_import`, `photo_enable`, `o_type`) VALUES
('facebook', 'Facebook', 'facebook', 1, 1, 'social', 'Get the contacts from a Facebook account', 'email', 'http://apps.facebook.com/causes/', '1.2.7', '1.8.0', 'a:0:{}', 1, '', 1, 1, 'social'),
('hotmail', 'Live/Hotmail', 'hotmail', 1, 1, 'email', 'Get the contacts from a Windows Live/Hotmail account', 'email', 'http://login.live.com/login.srf?id=2', '1.6.4', '1.8.0', 'a:4:{i:0;s:7:"hotmail";i:1;s:4:"live";i:2;s:3:"msn";i:3;s:8:"chaishop";}', 3, 'hotmail.com', 0, 0, 'email'),
('yahoo', 'Yahoo!', 'yahoo', 1, 1, 'email', 'Get the contacts from a Yahoo! account', 'email', 'http://mail.yahoo.com', '1.5.4', '1.8.0', 'a:3:{i:0;s:5:"yahoo";i:1;s:5:"ymail";i:2;s:10:"rocketmail";}', 2, 'yahoo.com', 0, 0, 'email'),
('gmail', 'GMail', 'gmail', 1, 1, 'email', 'Get the contacts from a GMail account', 'email', 'http://google.com', '1.4.8', '1.6.3', 'a:2:{i:0;s:5:"gmail";i:1;s:10:"googlemail";}', 1, 'gmail.com', 0, 0, 'email'),
('twitter', 'Twitter', 'twitter', 1, 1, 'social', 'Get the contacts from a Twitter account', 'user', 'http://twitter.com', '1.1.1', '1.8.0', 'a:0:{}', 2, '', 1, 1, 'social');


INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('contactimporter_main', 'standard', 'Contact Importer Main Navigation Menu');

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('contactimporter_main_invite', 'contactimporter', 'Invite Friends', '', '{"route":"contactimporter","action":"import"}', 'contactimporter_main', '', 1),
('contactimporter_main_queue_email', 'contactimporter', 'Queue Emails', '', '{"route":"contactimporter_general","action":"queue-email"}', 'contactimporter_main', '', 2),
('contactimporter_main_queue_message', 'contactimporter', 'Queue Messages', '', '{"route":"contactimporter_general","action":"queue-message"}', 'contactimporter_main', '', 3),
('contactimporter_main_pending_invitations', 'contactimporter', 'Pending Invitations', '', '{"route":"contactimporter_general","action":"pending-invitation"}', 'contactimporter_main', '', 4);


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