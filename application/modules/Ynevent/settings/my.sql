INSERT IGNORE INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES  ('ynevent', 'YN - Advanced Event', 'Advanced Event', '4.05', 1, 'extra') ;

-- Core menu
INSERT IGNORE INTO `engine4_core_menus` (`name`, `type`, `title`) VALUES
('ynevent_main', 'standard', 'Advanced Event Main Navigation Menu'),
('ynevent_profile', 'standard', 'Advanced Event Profile Options Menu')
;
-- Menu item
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('mobi_browse_ynevent', 'ynevent', 'Events', '', '{"route":"event_general"}', 'mobi_browse', '', 1, 0, 7),
('ynevent_quick_create', 'ynevent', 'Create New Event', 'Ynevent_Plugin_Menus::canCreateEvents', '{"route":"event_general","action":"create","class":"buttonlink icon_event_new"}', 'ynevent_quick', '', 1, 0, 1),
('ynevent_profile_style', 'ynevent', 'Edit Styles', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 2),
('ynevent_profile_share', 'ynevent', 'Share', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 5),
('ynevent_profile_report', 'ynevent', 'Report Event', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 4),
('ynevent_profile_message', 'ynevent', 'Message Members', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 7),
('ynevent_profile_member', 'ynevent', 'Member', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 3),
('ynevent_profile_invite', 'ynevent', 'Invite', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 6),
('ynevent_profile_edit', 'ynevent', 'Edit Profile', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 1),
('ynevent_profile_delete', 'ynevent', 'Delete Event', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '', 1, 0, 8),
('ynevent_main_upcoming', 'ynevent', 'Upcoming Events', '', '{"route":"event_upcoming"}', 'ynevent_main', '', 1, 0, 1),
('ynevent_main_past', 'ynevent', 'Past Events', '', '{"route":"event_past"}', 'ynevent_main', '', 1, 0, 2),
('ynevent_main_manage', 'ynevent', 'My Events', 'Ynevent_Plugin_Menus', '{"route":"event_general","action":"manage"}', 'ynevent_main', '', 1, 0, 3),
('ynevent_main_create', 'ynevent', 'Create New Event', 'Ynevent_Plugin_Menus', '{"route":"event_general","action":"create"}', 'ynevent_main', '', 1, 0, 5),
('ynevent_admin_main_manage', 'ynevent', 'Manage Events', '', '{"route":"admin_default","module":"ynevent","controller":"manage"}', 'ynevent_admin_main', '', 1, 0, 1),
('ynevent_admin_main_level', 'ynevent', 'Member Level Settings', '', '{"route":"ynevent_admin_default","action":"level"}', 'ynevent_admin_main', '', 1, 0, 2),
('ynevent_admin_main_categories', 'ynevent', 'Categories', '', '{"route":"admin_default","module":"ynevent","controller":"settings","action":"categories"}', 'ynevent_admin_main', '', 1, 0, 3),
('core_sitemap_ynevent', 'ynevent', 'Events', '', '{"route":"event_general"}', 'core_sitemap', '', 1, 0, 6),
('core_main_ynevent', 'ynevent', 'Events', '', '{"route":"event_upcoming"}', 'core_main', '', 1, 0, 6),
('core_admin_main_plugins_ynevent', 'ynevent', 'YN - Advanced Events', '', '{"route":"admin_default","module":"ynevent","controller":"manage"}', 'core_admin_main_plugins', '', 1, 0, 999),
('authorization_admin_level_ynevent', 'ynevent', 'Events', '', '{"route":"admin_default","module":"ynevent","controller":"level","action":"index"}', 'authorization_admin_level', '', 1, 0, 999);
-- Alter event table

ALTER TABLE `engine4_event_events` 
ADD `rating` FLOAT NOT NULL DEFAULT '0';

ALTER TABLE `engine4_event_categories` 
add `parent_id` int(11) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `engine4_event_categories` 
ADD KEY(`parent_id`);

-- Notification type
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('ynevent_accepted', 'ynevent', 'Your request to join the event {item:$object} has been approved.', 0, '', 1),
('ynevent_approve', 'ynevent', '{item:$subject} has requested to join the event {item:$object}.', 0, '', 1),
('ynevent_change_details', 'ynevent', 'Event {item:$object} is changed by {item:$subject}.', 0, '', 1),
('ynevent_discussion_reply', 'ynevent', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::event topic} you posted on.', 0, '', 1),
('ynevent_discussion_response', 'ynevent', '{item:$subject} has {item:$object:posted} on a {itemParent:$object::event topic} you created.', 0, '', 1),
('ynevent_invite', 'ynevent', '{item:$subject} has invited you to the event {item:$object}.', 1, 'ynevent.widget.request-event', 1),
('ynevent_invite_message', 'ynevent', '{item:$subject} has invited you to the event {item:$object}.', 1, '', 1),
('ynevent_remind', 'ynevent', 'Reminder: the event {item:$object} will start at {item:$object:$label}.', 0, '', 1),
('ynevent_notify_start', 'ynevent', 'The event {item:$object} already started.', 0, '', 1),
('ynevent_notify_end', 'ynevent', 'The event {item:$object} almost ended.', 0, '', 1);

-- Activity Type
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES 
('ynevent_join', 'ynevent', '{item:$subject} joined the event {item:$object}', 1, 3, 1, 1, 1, 1),
('ynevent_create', 'ynevent', '{item:$subject} created a new event:', 1, 5, 1, 1, 1, 1),
('ynevent_topic_reply', 'ynevent', '{item:$subject} replied to a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}', 1, 3, 1, 1, 1, 1),
('ynevent_photo_upload', 'ynevent', '{item:$subject} added {var:$count} photo(s).', 1, 3, 2, 1, 1, 1),
('ynevent_topic_create', 'ynevent', '{item:$subject} posted a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}', 1, 3, 1, 1, 1, 1),
('ynevent_video_create', 'ynevent', '{item:$subject} posted a new video:', 1, 3, 1, 1, 1, 1)
;
 
--
-- Dumping data for table `engine4_core_jobtypes`
INSERT IGNORE INTO `engine4_core_jobtypes` (`title`, `type`, `module`, `plugin`, `priority`) VALUES
('Rebuild Advanced Event Privacy', 'ynevent_maintenance_rebuild_privacy', 'ynevent', 'Ynevent_Plugin_Job_Maintenance_RebuildPrivacy', 50); 

-- Mail template
INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES
('notify_ynevent_accepted', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynevent_approve', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynevent_discussion_response', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynevent_discussion_reply', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynevent_invite', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'),
('notify_ynevent_invite_message', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynevent_change_details', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynevent_remind', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynevent_notify_start', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]'),
('notify_ynevent_notify_end', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]');

-- Ratings
CREATE TABLE IF NOT EXISTS `engine4_event_ratings` (
  `event_id` int(10) unsigned NOT NULL,
  `user_id` int(9) unsigned NOT NULL,
  `rating` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  KEY `INDEX` (`event_id`)
);

-- Follow
CREATE TABLE IF NOT EXISTS `engine4_event_follow` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL, 
  `follow` tinyint(3) NOT NULL DEFAULT '3',
   PRIMARY KEY (`resource_id`,`user_id`),
  KEY `REVERSE` (`user_id`)
);

-- Remind
CREATE TABLE IF NOT EXISTS `engine4_event_remind` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `remain_time` int(11) NOT NULL,
  `remind_time` datetime NOT NULL,
  `is_read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`resource_id`,`user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `engine4_event_sponsors` (
  `sponsor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `engine4_event_countries`
--
-- LONGL - START HERE 

CREATE TABLE IF NOT EXISTS `engine4_event_countries` (
  `country_id` int(11) NOT NULL,
  `name` varchar(128) character set utf8 collate utf8_bin NOT NULL,
  `iso_code_2` varchar(2) character set utf8 collate utf8_bin NOT NULL default '',
  `iso_code_3` varchar(3) character set utf8 collate utf8_bin NOT NULL default '',
  `address_format` text character set utf8 collate utf8_bin NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `engine4_event_countries`
--
INSERT IGNORE INTO `engine4_event_countries` (`country_id`, `name`, `iso_code_2`, `iso_code_3`, `address_format`, `postcode_required`, `status`) VALUES
(1, 'Afghanistan', 'AF', 'AFG', '', 0, 1),
(2, 'Albania', 'AL', 'ALB', '', 0, 1),
(3, 'Algeria', 'DZ', 'DZA', '', 0, 1),
(4, 'American Samoa', 'AS', 'ASM', '', 0, 1),
(5, 'Andorra', 'AD', 'AND', '', 0, 1),
(6, 'Angola', 'AO', 'AGO', '', 0, 1),
(7, 'Anguilla', 'AI', 'AIA', '', 0, 1),
(8, 'Antarctica', 'AQ', 'ATA', '', 0, 1),
(9, 'Antigua and Barbuda', 'AG', 'ATG', '', 0, 1),
(10, 'Argentina', 'AR', 'ARG', '', 0, 1),
(11, 'Armenia', 'AM', 'ARM', '', 0, 1),
(12, 'Aruba', 'AW', 'ABW', '', 0, 1),
(13, 'Australia', 'AU', 'AUS', '', 0, 1),
(14, 'Austria', 'AT', 'AUT', '', 0, 1),
(15, 'Azerbaijan', 'AZ', 'AZE', '', 0, 1),
(16, 'Bahamas', 'BS', 'BHS', '', 0, 1),
(17, 'Bahrain', 'BH', 'BHR', '', 0, 1),
(18, 'Bangladesh', 'BD', 'BGD', '', 0, 1),
(19, 'Barbados', 'BB', 'BRB', '', 0, 1),
(20, 'Belarus', 'BY', 'BLR', '', 0, 1),
(21, 'Belgium', 'BE', 'BEL', '', 0, 1),
(22, 'Belize', 'BZ', 'BLZ', '', 0, 1),
(23, 'Benin', 'BJ', 'BEN', '', 0, 1),
(24, 'Bermuda', 'BM', 'BMU', '', 0, 1),
(25, 'Bhutan', 'BT', 'BTN', '', 0, 1),
(26, 'Bolivia', 'BO', 'BOL', '', 0, 1),
(27, 'Bosnia and Herzegowina', 'BA', 'BIH', '', 0, 1),
(28, 'Botswana', 'BW', 'BWA', '', 0, 1),
(29, 'Bouvet Island', 'BV', 'BVT', '', 0, 1),
(30, 'Brazil', 'BR', 'BRA', '', 0, 1),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', '', 0, 1),
(32, 'Brunei Darussalam', 'BN', 'BRN', '', 0, 1),
(33, 'Bulgaria', 'BG', 'BGR', '', 0, 1),
(34, 'Burkina Faso', 'BF', 'BFA', '', 0, 1),
(35, 'Burundi', 'BI', 'BDI', '', 0, 1),
(36, 'Cambodia', 'KH', 'KHM', '', 0, 1),
(37, 'Cameroon', 'CM', 'CMR', '', 0, 1),
(38, 'Canada', 'CA', 'CAN', '', 0, 1),
(39, 'Cape Verde', 'CV', 'CPV', '', 0, 1),
(40, 'Cayman Islands', 'KY', 'CYM', '', 0, 1),
(41, 'Central African Republic', 'CF', 'CAF', '', 0, 1),
(42, 'Chad', 'TD', 'TCD', '', 0, 1),
(43, 'Chile', 'CL', 'CHL', '', 0, 1),
(44, 'China', 'CN', 'CHN', '', 0, 1),
(45, 'Christmas Island', 'CX', 'CXR', '', 0, 1),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', '', 0, 1),
(47, 'Colombia', 'CO', 'COL', '', 0, 1),
(48, 'Comoros', 'KM', 'COM', '', 0, 1),
(49, 'Congo', 'CG', 'COG', '', 0, 1),
(50, 'Cook Islands', 'CK', 'COK', '', 0, 1),
(51, 'Costa Rica', 'CR', 'CRI', '', 0, 1),
(52, 'Cote D''Ivoire', 'CI', 'CIV', '', 0, 1),
(53, 'Croatia', 'HR', 'HRV', '', 0, 1),
(54, 'Cuba', 'CU', 'CUB', '', 0, 1),
(55, 'Cyprus', 'CY', 'CYP', '', 0, 1),
(56, 'Czech Republic', 'CZ', 'CZE', '', 0, 1),
(57, 'Denmark', 'DK', 'DNK', '', 0, 1),
(58, 'Djibouti', 'DJ', 'DJI', '', 0, 1),
(59, 'Dominica', 'DM', 'DMA', '', 0, 1),
(60, 'Dominican Republic', 'DO', 'DOM', '', 0, 1),
(61, 'East Timor', 'TP', 'TMP', '', 0, 1),
(62, 'Ecuador', 'EC', 'ECU', '', 0, 1),
(63, 'Egypt', 'EG', 'EGY', '', 0, 1),
(64, 'El Salvador', 'SV', 'SLV', '', 0, 1),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', '', 0, 1),
(66, 'Eritrea', 'ER', 'ERI', '', 0, 1),
(67, 'Estonia', 'EE', 'EST', '', 0, 1),
(68, 'Ethiopia', 'ET', 'ETH', '', 0, 1),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', '', 0, 1),
(70, 'Faroe Islands', 'FO', 'FRO', '', 0, 1),
(71, 'Fiji', 'FJ', 'FJI', '', 0, 1),
(72, 'Finland', 'FI', 'FIN', '', 0, 1),
(73, 'France', 'FR', 'FRA', '', 0, 1),
(74, 'France, Metropolitan', 'FX', 'FXX', '', 0, 1),
(75, 'French Guiana', 'GF', 'GUF', '', 0, 1),
(76, 'French Polynesia', 'PF', 'PYF', '', 0, 1),
(77, 'French Southern Territories', 'TF', 'ATF', '', 0, 1),
(78, 'Gabon', 'GA', 'GAB', '', 0, 1),
(79, 'Gambia', 'GM', 'GMB', '', 0, 1),
(80, 'Georgia', 'GE', 'GEO', '', 0, 1),
(81, 'Germany', 'DE', 'DEU', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 1, 1),
(82, 'Ghana', 'GH', 'GHA', '', 0, 1),
(83, 'Gibraltar', 'GI', 'GIB', '', 0, 1),
(84, 'Greece', 'GR', 'GRC', '', 0, 1),
(85, 'Greenland', 'GL', 'GRL', '', 0, 1),
(86, 'Grenada', 'GD', 'GRD', '', 0, 1),
(87, 'Guadeloupe', 'GP', 'GLP', '', 0, 1),
(88, 'Guam', 'GU', 'GUM', '', 0, 1),
(89, 'Guatemala', 'GT', 'GTM', '', 0, 1),
(90, 'Guinea', 'GN', 'GIN', '', 0, 1),
(91, 'Guinea-bissau', 'GW', 'GNB', '', 0, 1),
(92, 'Guyana', 'GY', 'GUY', '', 0, 1),
(93, 'Haiti', 'HT', 'HTI', '', 0, 1),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', '', 0, 1),
(95, 'Honduras', 'HN', 'HND', '', 0, 1),
(96, 'Hong Kong', 'HK', 'HKG', '', 0, 1),
(97, 'Hungary', 'HU', 'HUN', '', 0, 1),
(98, 'Iceland', 'IS', 'ISL', '', 0, 1),
(99, 'India', 'IN', 'IND', '', 0, 1),
(100, 'Indonesia', 'ID', 'IDN', '', 0, 1),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', '', 0, 1),
(102, 'Iraq', 'IQ', 'IRQ', '', 0, 1),
(103, 'Ireland', 'IE', 'IRL', '', 0, 1),
(104, 'Israel', 'IL', 'ISR', '', 0, 1),
(105, 'Italy', 'IT', 'ITA', '', 0, 1),
(106, 'Jamaica', 'JM', 'JAM', '', 0, 1),
(107, 'Japan', 'JP', 'JPN', '', 0, 1),
(108, 'Jordan', 'JO', 'JOR', '', 0, 1),
(109, 'Kazakhstan', 'KZ', 'KAZ', '', 0, 1),
(110, 'Kenya', 'KE', 'KEN', '', 0, 1),
(111, 'Kiribati', 'KI', 'KIR', '', 0, 1),
(112, 'North Korea', 'KP', 'PRK', '', 0, 1),
(113, 'Korea, Republic of', 'KR', 'KOR', '', 0, 1),
(114, 'Kuwait', 'KW', 'KWT', '', 0, 1),
(115, 'Kyrgyzstan', 'KG', 'KGZ', '', 0, 1),
(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', '', 0, 1),
(117, 'Latvia', 'LV', 'LVA', '', 0, 1),
(118, 'Lebanon', 'LB', 'LBN', '', 0, 1),
(119, 'Lesotho', 'LS', 'LSO', '', 0, 1),
(120, 'Liberia', 'LR', 'LBR', '', 0, 1),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', '', 0, 1),
(122, 'Liechtenstein', 'LI', 'LIE', '', 0, 1),
(123, 'Lithuania', 'LT', 'LTU', '', 0, 1),
(124, 'Luxembourg', 'LU', 'LUX', '', 0, 1),
(125, 'Macau', 'MO', 'MAC', '', 0, 1),
(126, 'FYROM', 'MK', 'MKD', '', 0, 1),
(127, 'Madagascar', 'MG', 'MDG', '', 0, 1),
(128, 'Malawi', 'MW', 'MWI', '', 0, 1),
(129, 'Malaysia', 'MY', 'MYS', '', 0, 1),
(130, 'Maldives', 'MV', 'MDV', '', 0, 1),
(131, 'Mali', 'ML', 'MLI', '', 0, 1),
(132, 'Malta', 'MT', 'MLT', '', 0, 1),
(133, 'Marshall Islands', 'MH', 'MHL', '', 0, 1),
(134, 'Martinique', 'MQ', 'MTQ', '', 0, 1),
(135, 'Mauritania', 'MR', 'MRT', '', 0, 1),
(136, 'Mauritius', 'MU', 'MUS', '', 0, 1),
(137, 'Mayotte', 'YT', 'MYT', '', 0, 1),
(138, 'Mexico', 'MX', 'MEX', '', 0, 1),
(139, 'Micronesia, Federated States of', 'FM', 'FSM', '', 0, 1),
(140, 'Moldova, Republic of', 'MD', 'MDA', '', 0, 1),
(141, 'Monaco', 'MC', 'MCO', '', 0, 1),
(142, 'Mongolia', 'MN', 'MNG', '', 0, 1),
(143, 'Montserrat', 'MS', 'MSR', '', 0, 1),
(144, 'Morocco', 'MA', 'MAR', '', 0, 1),
(145, 'Mozambique', 'MZ', 'MOZ', '', 0, 1),
(146, 'Myanmar', 'MM', 'MMR', '', 0, 1),
(147, 'Namibia', 'NA', 'NAM', '', 0, 1),
(148, 'Nauru', 'NR', 'NRU', '', 0, 1),
(149, 'Nepal', 'NP', 'NPL', '', 0, 1),
(150, 'Netherlands', 'NL', 'NLD', '', 0, 1),
(151, 'Netherlands Antilles', 'AN', 'ANT', '', 0, 1),
(152, 'New Caledonia', 'NC', 'NCL', '', 0, 1),
(153, 'New Zealand', 'NZ', 'NZL', '', 0, 1),
(154, 'Nicaragua', 'NI', 'NIC', '', 0, 1),
(155, 'Niger', 'NE', 'NER', '', 0, 1),
(156, 'Nigeria', 'NG', 'NGA', '', 0, 1),
(157, 'Niue', 'NU', 'NIU', '', 0, 1),
(158, 'Norfolk Island', 'NF', 'NFK', '', 0, 1),
(159, 'Northern Mariana Islands', 'MP', 'MNP', '', 0, 1),
(160, 'Norway', 'NO', 'NOR', '', 0, 1),
(161, 'Oman', 'OM', 'OMN', '', 0, 1),
(162, 'Pakistan', 'PK', 'PAK', '', 0, 1),
(163, 'Palau', 'PW', 'PLW', '', 0, 1),
(164, 'Panama', 'PA', 'PAN', '', 0, 1),
(165, 'Papua New Guinea', 'PG', 'PNG', '', 0, 1),
(166, 'Paraguay', 'PY', 'PRY', '', 0, 1),
(167, 'Peru', 'PE', 'PER', '', 0, 1),
(168, 'Philippines', 'PH', 'PHL', '', 0, 1),
(169, 'Pitcairn', 'PN', 'PCN', '', 0, 1),
(170, 'Poland', 'PL', 'POL', '', 0, 1),
(171, 'Portugal', 'PT', 'PRT', '', 0, 1),
(172, 'Puerto Rico', 'PR', 'PRI', '', 0, 1),
(173, 'Qatar', 'QA', 'QAT', '', 0, 1),
(174, 'Reunion', 'RE', 'REU', '', 0, 1),
(175, 'Romania', 'RO', 'ROM', '', 0, 1),
(176, 'Russian Federation', 'RU', 'RUS', '', 0, 1),
(177, 'Rwanda', 'RW', 'RWA', '', 0, 1),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', '', 0, 1),
(179, 'Saint Lucia', 'LC', 'LCA', '', 0, 1),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', '', 0, 1),
(181, 'Samoa', 'WS', 'WSM', '', 0, 1),
(182, 'San Marino', 'SM', 'SMR', '', 0, 1),
(183, 'Sao Tome and Principe', 'ST', 'STP', '', 0, 1),
(184, 'Saudi Arabia', 'SA', 'SAU', '', 0, 1),
(185, 'Senegal', 'SN', 'SEN', '', 0, 1),
(186, 'Seychelles', 'SC', 'SYC', '', 0, 1),
(187, 'Sierra Leone', 'SL', 'SLE', '', 0, 1),
(188, 'Singapore', 'SG', 'SGP', '', 0, 1),
(189, 'Slovak Republic', 'SK', 'SVK', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{zone}\r\n{country}', 0, 1),
(190, 'Slovenia', 'SI', 'SVN', '', 0, 1),
(191, 'Solomon Islands', 'SB', 'SLB', '', 0, 1),
(192, 'Somalia', 'SO', 'SOM', '', 0, 1),
(193, 'South Africa', 'ZA', 'ZAF', '', 0, 1),
(194, 'South Georgia & South Sandwich Islands', 'GS', 'SGS', '', 0, 1),
(195, 'Spain', 'ES', 'ESP', '', 0, 1),
(196, 'Sri Lanka', 'LK', 'LKA', '', 0, 1),
(197, 'St. Helena', 'SH', 'SHN', '', 0, 1),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', '', 0, 1),
(199, 'Sudan', 'SD', 'SDN', '', 0, 1),
(200, 'Suriname', 'SR', 'SUR', '', 0, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '', 0, 1),
(202, 'Swaziland', 'SZ', 'SWZ', '', 0, 1),
(203, 'Sweden', 'SE', 'SWE', '', 0, 1),
(204, 'Switzerland', 'CH', 'CHE', '', 0, 1),
(205, 'Syrian Arab Republic', 'SY', 'SYR', '', 0, 1),
(206, 'Taiwan', 'TW', 'TWN', '', 0, 1),
(207, 'Tajikistan', 'TJ', 'TJK', '', 0, 1),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', '', 0, 1),
(209, 'Thailand', 'TH', 'THA', '', 0, 1),
(210, 'Togo', 'TG', 'TGO', '', 0, 1),
(211, 'Tokelau', 'TK', 'TKL', '', 0, 1),
(212, 'Tonga', 'TO', 'TON', '', 0, 1),
(213, 'Trinidad and Tobago', 'TT', 'TTO', '', 0, 1),
(214, 'Tunisia', 'TN', 'TUN', '', 0, 1),
(215, 'Turkey', 'TR', 'TUR', '', 0, 1),
(216, 'Turkmenistan', 'TM', 'TKM', '', 0, 1),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', '', 0, 1),
(218, 'Tuvalu', 'TV', 'TUV', '', 0, 1),
(219, 'Uganda', 'UG', 'UGA', '', 0, 1),
(220, 'Ukraine', 'UA', 'UKR', '', 0, 1),
(221, 'United Arab Emirates', 'AE', 'ARE', '', 0, 1),
(222, 'United Kingdom', 'GB', 'GBR', '', 1, 1),
(223, 'United States', 'US', 'USA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}', 0, 1),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', '', 0, 1),
(225, 'Uruguay', 'UY', 'URY', '', 0, 1),
(226, 'Uzbekistan', 'UZ', 'UZB', '', 0, 1),
(227, 'Vanuatu', 'VU', 'VUT', '', 0, 1),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', '', 0, 1),
(229, 'Venezuela', 'VE', 'VEN', '', 0, 1),
(230, 'Viet Nam', 'VN', 'VNM', '', 0, 1),
(231, 'Virgin Islands (British)', 'VG', 'VGB', '', 0, 1),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', '', 0, 1),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', '', 0, 1),
(234, 'Western Sahara', 'EH', 'ESH', '', 0, 1),
(235, 'Yemen', 'YE', 'YEM', '', 0, 1),
(236, 'Yugoslavia', 'YU', 'YUG', '', 0, 1),
(237, 'Democratic Republic of Congo', 'CD', 'COD', '', 0, 1),
(238, 'Zambia', 'ZM', 'ZMB', '', 0, 1),
(239, 'Zimbabwe', 'ZW', 'ZWE', '', 0, 1);

-- --------------------------------------------------------

alter table `engine4_event_events` 
   add column `country` varchar(64) NULL after `rating`, 
   add column `city` varchar(256) NULL after `country`, 
   add column `address` varchar(500) NULL after `city`,
   change `location` `location` varchar(115) character set utf8 collate utf8_unicode_ci NOT NULL;

alter table `engine4_event_events` 
   add column `brief_description` text NULL after `title`, 
   add column `capacity` int(11) DEFAULT '0' NOT NULL after `address`, 
   add column `email` varchar(115) NULL after `capacity`, 
   add column `url` varchar(256) NULL after `email`, 
   add column `phone` varchar(64) NULL after `url`, 
   add column `contact_info` varchar(256) NULL after `phone`;
   
alter table `engine4_event_events` 
   add column `zip_code` int(11) NULL after `address`, 
   add column `latitude` varchar(64) NULL after `zip_code`, 
   add column `longitude` varchar(64) NULL after `latitude`;
   
alter table `engine4_event_events` 
   add column `repeat_type` int(11) default 0 , 
   add column `repeat_group` varchar(64) NULL after `repeat_type`, 
   add column `end_repeat` datetime NULL after `repeat_type`,
   add column `repeat_order` int DEFAULT '0' after `end_repeat`,
   add column `group_invite` int NULL after `repeat_order`;   


alter table `engine4_event_events` 
   add column `price` float DEFAULT '0' NULL after `capacity`;
   
alter table `engine4_event_events`    
  add column `click_count` int(11) unsigned default 0;
     
alter table `engine4_event_events`      
  add column `share_count` int(11) unsigned default 0; 
   
alter table `engine4_event_events` 
   add column `featured` int DEFAULT '0' NULL after `price`;   

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('ynevent_delete', 'ynevent', 'Event {var:$ynevent_title} that you have been joined is deleted by {item:$subject}.', 0, '', 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES 
('ynevent_edit_delete', 'ynevent', 'Event {var:$ynevent_title} that you have been joined is moved to {item:$object} by {item:$subject}.', 0, '', 1);
   
CREATE TABLE IF NOT EXISTS `engine4_event_sponsors` (
  `sponsor_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(256) NULL,
  `photo_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`sponsor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `engine4_event_agents` (
  `agent_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(128) collate utf8_unicode_ci NOT NULL,
  `starttime` datetime default NULL,
  `endtime` datetime default NULL,
  `category_id` int(11) unsigned default NULL,
  `view` int(11) unsigned default NULL,
  `order` varchar(50) collate utf8_unicode_ci default NULL,
  `state` varchar(128) collate utf8_unicode_ci default NULL,
  `city` varchar(128) collate utf8_unicode_ci default NULL,
  `keyword` varchar(128) collate utf8_unicode_ci default NULL,
  `address` varchar(250) collate utf8_unicode_ci default NULL,
  `country` varchar(64) collate utf8_unicode_ci default NULL,
  `mile_of` double unsigned default '0',
  `zipcode` varchar(32) collate utf8_unicode_ci default NULL,
  `creation_date` datetime NOT NULL,
  `keyword_pattern` varchar(255) collate utf8_unicode_ci default NULL,
  `address_pattern` varchar(255) collate utf8_unicode_ci default NULL,
  `lat` double default '0',
  `lon` double default '0',
  PRIMARY KEY  (`agent_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

update engine4_event_events set repeat_group = event_id
where repeat_group is null;

-- Add transfer
INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
('ynevent_profile_transfer', 'ynevent', 'Transfer Owner', 'Ynevent_Plugin_Menus', '', 'ynevent_profile', '',1,0,7);
INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynevent_transfer', 'ynevent', '{item:$subject} has became the owner of the event {item:$object}', 1, 3, 1, 1, 1, 1);
INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynevent_transfer', 'ynevent', 'You were set to become the owner of the event {item:$object}.', 0, '');

-- event 4.05
UPDATE `engine4_core_modules` SET `version` = '4.05' WHERE `name` = 'ynevent';

-- Update `engine4_activity_actiontypes`
UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} posted a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_create' AND `module` = 'ynevent';

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} replied to a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_reply' AND `module` = 'ynevent';


INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES 
('ynevent_admin_main_reviews', 'ynevent', 'Reviews', '', '{"route":"admin_default","module":"ynevent","controller":"reviews"}', 'ynevent_admin_main', '', 1, 0, 6),
('ynevent_admin_main_fields', 'ynevent', 'Custom Fields', '', '{"route":"admin_default","module":"ynevent","controller":"fields"}', 'ynevent_admin_main', '', 1, 0, 5);

CREATE TABLE IF NOT EXISTS `engine4_event_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY (`field_id`,`option_id`,`child_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_meta` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(32) NOT NULL DEFAULT '',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `display` tinyint(1) unsigned DEFAULT '1',
  `search` tinyint(1) unsigned DEFAULT '0',
  `show` tinyint(1) unsigned DEFAULT '1',
  `order` smallint(3) unsigned NOT NULL DEFAULT '999',
  `config` text NOT NULL,
  `validators` text COLLATE utf8_unicode_ci,
  `filters` text COLLATE utf8_unicode_ci,
  `style` text COLLATE utf8_unicode_ci,
  `error` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_options` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL DEFAULT '999',
  PRIMARY KEY (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine4_event_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `engine4_event_events` 
ADD `event_of_date` date DEFAULT NULL,
ADD `metadata` varchar(255) collate utf8_unicode_ci default NULL,
ADD `cover_photo` int(11) ,
ADD `online` tinyint(1) DEFAULT 0;

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('friend_host', 'ynevent', 'You have been added as host of event {item:$object}', 0, ''),
('event_import_blog', 'ynevent', 'Your blog {item:$subject} has been added to event {item:$object}', 0, '');

ALTER TABLE `engine4_event_photos` 
ADD `is_featured` tinyint(1) DEFAULT 1;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_highlights` (
  `highlight_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`highlight_id`, `event_id`, `item_id`),
  KEY `user_id` (`event_id`, `item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_announcements` (
  `announcement_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `highlight` TINYINT( 1 ) NOT NULL DEFAULT  '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `report_count` int(11) DEFAULT 0,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `engine4_ynevent_reviewreports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `type` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, 
`started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, 
`failure_count`, `success_last`, `success_count`) VALUES 
('Advanced Event Notification', 'ynevent', 'Ynevent_Plugin_Task_Notification', 600, 1, 0, 0, 0, 0, 0, 
0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `engine4_ynevent_mappings`
--

CREATE TABLE IF NOT EXISTS `engine4_ynevent_mappings` (
  `mapping_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime DEFAULT NULL,
  PRIMARY KEY (`mapping_id`,`event_id`,`item_id`),
  KEY `user_id` (`event_id`,`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
