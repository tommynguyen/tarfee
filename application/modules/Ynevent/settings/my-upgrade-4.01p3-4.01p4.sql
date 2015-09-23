INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`) 
VALUES ('ynevent_invite_message', 'ynevent', '{item:$subject} has invited you to the event {item:$object}.', '1', 'ynevent.widget.request-event', '1');

UPDATE `engine4_core_mailtemplates` 
SET  `vars` = '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]' 
WHERE `engine4_core_mailtemplates`.`type` = 'notify_ynevent_invite';

INSERT IGNORE INTO `engine4_core_mailtemplates` (`mailtemplate_id`, `type`, `module`, `vars`) 
VALUES (NULL, 'notify_ynevent_invite_message', 'ynevent', '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]');