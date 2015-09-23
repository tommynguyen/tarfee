<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Plugin_Core
{
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {

      // Remove from online users
      $onlineUsersTable = Engine_Api::_()->getDbtable('online', 'user');
      $onlineUsersTable->delete(array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Remove friends
      $payload->membership()->removeAllUserFriendship();

      // Remove all cases user is in a friend list
      $payload->lists()->removeUserFromLists();

      // Remove all friend list created by the user
      $payload->lists()->removeUserLists();
      
      // Remove facebook/twitter associations
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->delete('engine4_user_facebook', array(
        'user_id = ?' => $payload->getIdentity(),
      ));
      $db->delete('engine4_user_twitter', array(
        'user_id = ?' => $payload->getIdentity(),
      ));
      $db->delete('engine4_user_janrain', array(
        'user_id = ?' => $payload->getIdentity(),
      ));
	  
	  $mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
	  //Remove all players
	  $playerTable = Engine_Api::_() -> getDbtable('playercards', 'user');
	  $playerSelect = $playerTable -> select() -> where('user_id = ?', $payload -> getIdentity());
	  foreach ($playerTable->fetchAll($playerSelect) as $player)
	  {
	  	  //Get all mapping
		  $mappingSelect = $mappingTable -> select() -> where ("owner_type = ?", $player -> getType())->  where ("owner_id = ?", $player -> getIdentity());
		  foreach ($mappingTable -> fetchAll($mappingSelect) as $item)
		  {
		  	 $item -> delete();
		  }
	  	  $player -> delete();
	  }
	  
	  //Remove all libs
	  $library = $payload -> getMainLibrary();
	  $subLibraries = $library -> getSubLibrary(); 
	  foreach($subLibraries as $subLibrary)
	  {
	  	$subLibrary -> delete();
	  }
	  $library -> delete();
	  
	  //Remove all campaigns
	  $campaignTable = Engine_Api::_() -> getItemTable('tfcampaign_campaign');
	  $campaignSelect = $campaignTable -> select() -> where("user_id = ?", $payload -> getIdentity());
	  foreach ($campaignTable->fetchAll($campaignSelect) as $campaign)
	  {
	  	 $campaign -> delete();
	  }
	  
    }
  }

  public function onUserEnable($event)
  {
    $user = $event->getPayload();
    if( !($user instanceof User_Model_User) ) {
      return;
    }

    // update networks
    Engine_Api::_()->network()->recalculate($user);

      // Create activity for them if it doesn't exist
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $signupActionIdentity = $actionTable->select()
      ->from($actionTable, 'action_id')
      ->where('type = ?', 'signup')
      ->where('subject_type = ?', $user->getType())
      ->where('subject_id = ?', $user->getIdentity())
      ->query()
      ->fetchColumn();
    if( !$signupActionIdentity ) {
      $actionTable->addActivity($user, $user, 'signup');
    }

    // Note: this will get sent to users who are re-enabled after being disabled
    // by an admin
    try {
      // Send welcome email?
      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
        $user,
        'core_welcome',
        array(
          'host' => $_SERVER['HTTP_HOST'],
          'email' => $user->email,
          'date' => time(),
          'recipient_title' => $user->getTitle(),
          'recipient_link' => $user->getHref(),
          'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
          'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        )
      );
    } catch( Exception $e ) {}
  }
  
  public function getAdminNotifications($event)
  {
    // Awaiting approval
    $userTable = Engine_Api::_()->getItemTable('user');
    $select = new Zend_Db_Select($userTable->getAdapter());
    $select->from($userTable->info('name'), 'COUNT(user_id) as count')
      ->where('enabled = ?', 0)
      ->where('approved = ?', 0)
      ;

    $data = $select->query()->fetch();
    if( empty($data['count']) ) {
      return;
    }

    $translate = Zend_Registry::get('Zend_Translate');
    $message = vsprintf($translate->translate(array(
      'There is <a href="%s">%d new member</a> awaiting your approval.',
      'There are <a href="%s">%d new members</a> awaiting your approval.',
      $data['count']
    )), array(
      Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'manage'), 'admin_default', true) . '?enabled=0',
      $data['count'],
    ));

    $event->addResponse($message);
  }

  public function onUserCreateAfter($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
//      if( 'none' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ){
//        $facebook = User_Model_DbTable_Facebook::getFBInstance();
//        if ($facebook->getUser()) {
//          try {
//            $facebook->api('/me');
//            $table = Engine_Api::_()->getDbtable('facebook', 'user');
//            $row = $table->fetchRow(array('user_id = ?'=>$payload->getIdentity()));
//            if (!$row) {
//              $row = Engine_Api::_()->getDbtable('facebook', 'user')->createRow();
//              $row->user_id = $payload->getIdentity();
//            }
//            $row->facebook_uid = $facebook->getUser();
//            $row->save();
//          } catch (Exception $e) {}
//        }
//      }
    
      // Set default email notifications
      $notificationTypesTable = Engine_Api::_()->getDbtable('notificationTypes', 'activity');
      
      // For backwards compatiblitiy this block will only execute if the 
      // getDefaultNotifications function exists. If notifications aren't 
      // being added to the engine4_activity_notificationsettings table
      // check to see if the Activity_Model_DbTable_NotificationTypes class
      // is out of date
      if( method_exists($notificationTypesTable, 'getDefaultNotifications') ){
        $defaultNotifications = $notificationTypesTable->getDefaultNotifications();
        
        Engine_Api::_()->getDbtable('notificationSettings', 'activity')
          ->setEnabledNotifications($payload, $defaultNotifications);
      }
    }
  }
  
  public function onItemDeleteAfter($event)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$payload = $event->getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();    
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id =  $request -> getParam("subject_id", null);
			$typeOwner = $request -> getParam("parent_type", null);
			$case = $request -> getParam("case", null);
            if (is_null($case)) {
                $case = $payload['type'];
            }
			if ($typeOwner == 'user_library' || $typeOwner == 'user_playercard')
			{
				if ($subject_id)
				{
					switch ($case) 
					{								
											
						case 'video':
							
							$viewer = Engine_Api::_() -> user() -> getViewer();
							
							Engine_Api::_() -> getDbTable('mappings', 'user') -> deleteItem(array(
								'owner_type' => $typeOwner,
								'owner_id' => $subject_id,
								'item_type' => $payload['type'], 
								'item_id' => $payload['identity']
							));
								
							$key = 'user_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$tab = $request -> getParam("tab", null);
							if(isset($tab) && !empty($tab)) {
								$value = $viewer -> getHref().'/view/tab/'.$tab;
							} else {
								$value = $viewer -> getHref();
							}
							$_SESSION[$key] = $value;
							break;
							
                    }
				}
			}
		}
	}
  
  public function onItemUpdateAfter($event)
	{
	    $view = Zend_Registry::get('Zend_View');
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$subject_id =  $request -> getParam("subject_id", null);
			$typeOwner = $request -> getParam("parent_type", null);
			$availableType = array('user_playercard', 'video', 'event', 'tfcampaign_campaign', 'blog');
			if(!in_array($payload -> getType(), $availableType))
			{
				return;
			}
			$item = Engine_Api::_()->getItem($payload -> getType(), $payload -> getIdentity());
			if($payload -> getType() == 'video')
			{
				$subject_id = $library_id =  $item->parent_id;
				$typeOwner = $item->parent_type;
			}
			
			if ($typeOwner == 'user_library' || $typeOwner == 'user_playercard')
			{
				if ($subject_id)
				{
					$viewer = Engine_Api::_() -> user() -> getViewer();
					$type = $payload -> getType();
					switch ($type) 
					{
						case 'video':
                            $table = Engine_Api::_() -> getDbTable('mappings', 'user');
                            $select = $table -> select() -> where('owner_id = ?', $subject_id) -> where('item_id = ?', $payload -> getIdentity()) -> where('item_type = ?', 'video') -> limit(1);
                            $video_row = $table -> fetchRow($select);
                            if (!$video_row) {
                            	$row = $table -> createRow();
                                $row -> setFromArray(array(
                                   'user_id' => $viewer -> getIdentity(),
                                   'item_id' => $payload -> getIdentity(),
                                   'item_type' => 'video',
                                   'owner_id' => $subject_id,                     
                                   'owner_type' => $typeOwner,      
                                   'creation_date' => date('Y-m-d H:i:s'),
                                   'modified_date' => date('Y-m-d H:i:s'),
                                   ));
                                $row -> save();
                                
                                $video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
                                
                                // Rebuild privacy
                                $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
                                foreach ($actionTable->getActionsByObject($video) as $action)
                                {
                                    $actionTable -> resetActivityBindings($action);
                                }
                            }
                            if(Engine_Api::_() -> hasModuleBootstrap('ynvideo')) {
                                $module_video = "ynvideo";
                            }
                            else {
                                $module_video = "video";
                            }
                            
							$key = 'user_predispatch_url:' . $module_video . '.index.manage';
							$tab = $request -> getParam("tab", null);
							if(isset($tab) && !empty($tab)) {
								$value = $viewer -> getHref().'/view/tab/'.$tab;
							} else {
								$value = $viewer -> getHref();
							}
							$_SESSION[$key] = $value;
							break;		
					}
				}
			}
		}
	}
  
  public function onItemCreateAfter($event)
	{
		$view = Zend_Registry::get('Zend_View');
		$request = Zend_Controller_Front::getInstance() -> getRequest();    
		$payload = $event -> getPayload();
		if (!is_object($payload))
		{
			return;
		}
        if(!$request)
            { return;}
			
		$availableType = array('user_playercard', 'video', 'event', 'tfcampaign_campaign', 'blog');
		if(!in_array($payload -> getType(), $availableType))
		{
			return;
		}
		$item = Engine_Api::_()->getItem($payload -> getType(), $payload -> getIdentity());
		$user = Engine_Api::_()->user()->getViewer();
		$club = $user->getClub();
		if ($club && $payload -> getType() != 'video' && in_array($payload -> getType(), $availableType) && Engine_Api::_()->user()->canTransfer($item) && ($item->parent_type != 'group')) {
			$item->parent_type = 'group';
			$item->parent_id = $club->getIdentity();
			$item->save();
		}
		
		$table = Engine_Api::_() -> getDbTable('mappings', 'user');
			
		$subject_id = $library_id =  $request -> getParam("subject_id", null);
		$typeOwner = $request -> getParam("parent_type", null);
		
		if($payload -> getType() == 'video')
		{
			$subject_id = $library_id =  $item->parent_id;
			$typeOwner = $item->parent_type;
		}
		
		if ($typeOwner == 'user_library' || $typeOwner == 'user_playercard')
		{
			if ($subject_id)
			{
				$type = $payload -> getType();
				$viewer = Engine_Api::_() -> user() -> getViewer();
				switch ($type) 
				{
					case 'video':
						$row = $table -> createRow();
					    $row -> setFromArray(array(
					       'user_id' => $viewer -> getIdentity(),
					       'item_id' => $payload -> getIdentity(),
					       'item_type' => 'video',
					       'owner_id' => $subject_id,				       
		       			   'owner_type' => $typeOwner,		
					       'creation_date' => date('Y-m-d H:i:s'),
					       'modified_date' => date('Y-m-d H:i:s'),
					       ));
						$row -> save();
						
						// Rebuild privacy
						$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
						$video = Engine_Api::_() -> getItem('video', $payload -> getIdentity());
						foreach ($actionTable->getActionsByObject($video) as $action)
						{
							$actionTable -> resetActivityBindings($action);
						}
						
						if(Engine_Api::_() -> hasModuleBootstrap('ynvideo'))
						{
							$module_video = "ynvideo";
						}
						else 
						{
							$module_video = "video";
						}
						
						if($payload -> type == 0)
                            $key = 'user_predispatch_url:' . $module_video . '.index.manage';
                        else
                            $key = 'user_predispatch_url:' . $module_video . '.index.view';
						
						$tab = $request -> getParam("tab", null);
						if(isset($tab) && !empty($tab)) {
							$value = $viewer -> getHref().'/view/tab/'.$tab;
						} else {
							$value = $viewer -> getHref();
						}
						$_SESSION[$key] = $value;
						break;
				}
			}
		}
	}
}