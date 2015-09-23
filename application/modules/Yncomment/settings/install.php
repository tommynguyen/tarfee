<?php
class Yncomment_Installer extends Engine_Package_Installer_Module {
    function onInstall() {
        $db = $this->getDb();
        $tableObj = $db->query("SHOW TABLES LIKE 'engine4_core_comments'")->fetch();
        if (!empty($tableObj)) 
        {
            $parent_comment_id = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'parent_comment_id' ")->fetch();
            if (empty($parent_comment_id)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `parent_comment_id` int( 11 ) NOT NULL DEFAULT  '0';");
            }

            $params = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'params' ")->fetch();
            if (empty($params)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_type = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'attachment_type' ")->fetch();
            if (empty($attachment_type)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `attachment_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_id = $db->query("SHOW COLUMNS FROM engine4_core_comments LIKE 'attachment_id' ")->fetch();
            if (empty($attachment_id)) {
                $db->query("ALTER TABLE  `engine4_core_comments` ADD  `attachment_id` INT( 11 ) NULL DEFAULT  '0';");
            }
        }

        $tableObj = $db->query("SHOW TABLES LIKE 'engine4_activity_comments'")->fetch();
        if (!empty($tableObj)) 
        {
            $parent_comment_id = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'parent_comment_id' ")->fetch();
            if (empty($parent_comment_id)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `parent_comment_id` int( 11 ) NOT NULL DEFAULT  '0';");
            }

            $attachment_type = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'attachment_type' ")->fetch();
            if (empty($attachment_type)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `attachment_type` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }

            $attachment_id = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'attachment_id' ")->fetch();
            if (empty($attachment_id)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `attachment_id` INT( 11 ) NULL DEFAULT  '0';");
            }

            $params = $db->query("SHOW COLUMNS FROM engine4_activity_comments LIKE 'params' ")->fetch();
            if (empty($params)) {
                $db->query("ALTER TABLE  `engine4_activity_comments` ADD  `params` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            }
        }

        $table_engine4_album_albums_exist = $db->query("SHOW TABLES LIKE 'engine4_album_albums'")->fetch();
        if ($table_engine4_album_albums_exist) {
            $column = $db->query("SHOW COLUMNS FROM `engine4_album_albums` LIKE 'type'")->fetch();
            if (!empty($column)) {
                $type = $column['Type'];
                if (!strpos($type, "'profile','message','comment',")) {
                    $type = str_replace("'profile','message',", "'profile','message','comment', ", $type);
                    $db->query("ALTER TABLE `engine4_album_albums` CHANGE `type` `type` $type CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
                }
            }
        }
        $this->setActivityFeeds();
        parent::onInstall();
    }

    public function setActivityFeeds() {
        $db = $this->getDb();
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("replied", "yncomment", \'{item:$subject} has replied on your {item:$object:$label}.\', "0", ""), ("replied_replied", "yncomment", \'{item:$subject} has replied on a {item:$object:$label} you replied on.\', "0", "")');
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES ("liked_replied", "yncomment", \'{item:$subject} has replied on a {item:$object:$label} you liked.\', "0", "")');
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES ( 'notify_liked_replied', 'yncomment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]')");
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES ('notify_replied', 'yncomment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]'), ('notify_replied_replied', 'yncomment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]');");
        // Add notification tag
        $db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) VALUES(\'yncomment_tagged\', \'yncomment\', \'{item:$subject} tagged your {var:$item_type} in a {item:$object:$label}.\', 0, \'\', 1);');
        $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES ( 'notify_yncomment_tagged', 'yncomment', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[item_type]')");
        // Add some action type
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_blog", "blog", \'{item:$subject} replied to a comment on {item:$owner}\'\'s blog {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_album", "album", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_album_photo", "album", \'{item:$subject} replied to a comment on {item:$owner}\'\'s album photo {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_video", "video", \'{item:$subject} replied to a comment on {item:$owner}\'\'s video {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_poll", "poll", \'{item:$subject} replied to a comment on {item:$owner}\'\'s poll {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_group", "group", \'{item:$subject} replied to a comment on {item:$owner}\'\'s group {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_event", "event", \'{item:$subject} replied to a comment on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("yncomment_classified", "classified", \'{item:$subject} replied to a comment on {item:$owner}\'\'s classified {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("comment_group", "group", \'{item:$subject} commented on {item:$owner}\'\'s group {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
        $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("comment_event", "event", \'{item:$subject} commented on {item:$owner}\'\'s event {item:$object:$title}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
    }
}