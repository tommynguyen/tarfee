-- Update `engine4_activity_actiontypes`
UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} posted a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_create' AND `module` = 'ynevent';

UPDATE `engine4_activity_actiontypes` SET `body` = '{item:$subject} replied to a {itemChild:$object:topic:$child_id} in the event {item:$object}: {body:$body}' 
WHERE `type` = 'ynevent_topic_reply' AND `module` = 'ynevent';
