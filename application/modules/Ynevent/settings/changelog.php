<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynevent
 * @author     DangTH
 */
return array(    
    '4.01p4' => array(
		'/application/languages/en/ynevent.csv' => 'Add the language pharse _EMAIL_NOTIFY_YNEVENT_DISCUSSION_REPLY_TITLE',    		
    	'controllers/IndexController.php' => 'When browsing the upcoming events, the most upcoming events will be displayed first',    		
        'Form/Invite.php' => 'Fix the issue about inviting members',
    	'Model/DbTable/Events.php' => 'Fix the issue about search events, event owner can always search their events, and the most upcoming events will be displayed first when browsing upcoming events, fix the issue searching events users invited',
        'Plugin/Utilities.php' => 'Fix the issue about privacy',
    	'settings/changelog.php' => 'Incremented version',
    	'settings/manifest.php' => 'Incremented version'    	
    ),       
)
?>