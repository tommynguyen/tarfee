<?php

class Ynevent_Plugin_Core
{

	public function onBeforeActivityNotificationsUpdate($event)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable("remind", "ynevent");
		$reminds = $table -> getRemindEvents($viewer -> getIdentity());
		$view = Zend_Registry::get('Zend_View');
		if (count($reminds))
		{
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

			foreach ($reminds as $event)
			{

				$date = $view -> locale() -> toDateTime($event -> starttime);
				$params = array("label" => $date);
				$notifyApi -> addNotification($viewer, $viewer, $event, 'ynevent_remind', $params);
				//set remind is read
				$remind = $table -> getRemindRow($event -> event_id, $viewer -> getIdentity());
				$remind -> is_read = 1;
				$remind -> save();
			}
		}
	}

	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'event');
	}

	public function onUserDeleteBefore($event)
	{
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User)
		{
			// Delete posts
			$postTable = Engine_Api::_() -> getDbtable('posts', 'ynevent');
			$postSelect = $postTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($postTable->fetchAll($postSelect) as $post)
			{
				//$post->delete();
			}

			// Delete topics
			$topicTable = Engine_Api::_() -> getDbtable('topics', 'ynevent');
			$topicSelect = $topicTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($topicTable->fetchAll($topicSelect) as $topic)
			{
				//$topic->delete();
			}

			// Delete photos
			$photoTable = Engine_Api::_() -> getDbtable('photos', 'ynevent');
			$photoSelect = $photoTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($photoTable->fetchAll($photoSelect) as $photo)
			{
				$photo -> delete();
			}

			// Delete memberships
			$membershipApi = Engine_Api::_() -> getDbtable('membership', 'ynevent');
			foreach ($membershipApi->getMembershipsOf($payload) as $event)
			{
				$membershipApi -> removeMember($event, $payload);
			}

			// Delete events
			$eventTable = Engine_Api::_() -> getDbtable('events', 'ynevent');
			$eventSelect = $eventTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($eventTable->fetchAll($eventSelect) as $event)
			{
				$event -> delete();
			}
		}
	}

	public function addActivity($event)
	{
		$payload = $event -> getPayload();
		$subject = $payload['subject'];
		$object = $payload['object'];

		// Only for object=event
		if ($object instanceof Event_Model_Event && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
		{
			$event -> addResponse(array(
				'type' => 'event',
				'identity' => $object -> getIdentity()
			));
		}
	}

	public function getActivity($event)
	{
		// Detect viewer and subject
		$payload = $event -> getPayload();
		$user = null;
		$subject = null;
		if ($payload instanceof User_Model_User)
		{
			$user = $payload;
		}
		else
		if (is_array($payload))
		{
			if (isset($payload['for']) && $payload['for'] instanceof User_Model_User)
			{
				$user = $payload['for'];
			}
			if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract)
			{
				$subject = $payload['about'];
			}
		}
		if (null === $user)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if ($viewer -> getIdentity())
			{
				$user = $viewer;
			}
		}
		if (null === $subject && Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
		}

		// Get feed settings
		$content = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.content', 'everyone');

		// Get event memberships
		if ($user)
		{
			$data = Engine_Api::_() -> getDbtable('membership', 'ynevent') -> getMembershipsOfIds($user);
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'event',
					'data' => $data,
				));
			}
		}
	}

	public function onEventUpdateAfter($event)
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = $event -> getPayload();
		if (!($event instanceof Ynevent_Model_Event))
		{
			return;
		}

		//Update remind_time in event_remind if exist
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$remindTable = Engine_Api::_() -> getDbtable('remind', 'ynevent');
		$remind = $remindTable -> getRemindRow($event -> getIdentity(), $viewer -> getIdentity());
		if (count($remind) > 0)
		{
			if ($remind -> is_read == 0)
			{
				$remain = $remind -> remain_time;
				$dayRemind = strtotime("-$remain minutes", strtotime($event -> starttime));
				$dayRemind = date('Y-m-d H:i:s', $dayRemind);
				$remind -> remind_time = $dayRemind;
				$remind -> save();
			}
		}
	}

	public function onActivityActionCreateAfter($event)
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$action = $event -> getPayload();
		if (!($action instanceof Activity_Model_Action))
		{
			return;
		}
		if ($action -> type == "ynevent_join")
		{

			$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
			$row = $table -> getFollowEvent($action -> object_id, $action -> subject_id);
			if (!$row)
			{
				$values = array(
					'resource_id' => $action -> object_id,
					'user_id' => $action -> subject_id,
					'follow' => 0
				);
				$row = $table -> createRow();
				$row -> setFromArray($values);

				$row -> save();

			}
		}
	}

	public function onItemCreateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$event_id = $request -> getParam("subject_id", null);
			$type = $request -> getParam("parent_type", null);
			
			if ($type == 'event')
			{
				$widget_id = $request -> getParam("tab", null);
				if ($event_id)
				{
					$type = $payload -> getType();
					
					switch ($type) {
						case 'video':
							
							$ynvideo_enabled = Engine_Api::_()->ynevent()->checkYouNetPlugin('ynvideo');
							if(!$ynvideo_enabled)
							{
								$table = Engine_Api::_() -> getDbTable('mappings', 'ynevent');
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'event_id' => $event_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> owner_id,				       
							       'type' => 'video',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
								$row -> save();
								
								//ynvideo already send feed
								$video = Engine_Api::_()->getItem('video', $payload -> getIdentity());
								$video -> parent_type = 'event';
								$video -> parent_id = $event_id;
								$video -> save();
								$viewer = Engine_Api::_() -> user() -> getViewer();
								$item = Engine_Api::_() -> getItem($video -> parent_type, $video -> parent_id );
								$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
								$action = $activityApi->addActivity($viewer, $item, 'ynevent_video_create');
								if($action) {
									$activityApi->attachActivity($action, $video);
								}
								// Rebuild privacy
								$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
								foreach ($actionTable->getActionsByObject($video) as $action)
								{
									$actionTable -> resetActivityBindings($action);
								}
							}
							
							$table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
							try {
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'event_id' => $event_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> owner_id,				       
							       'type' => 'video',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
							}
							catch (Exception $e) {
							}
							
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$event = Engine_Api::_() -> getItem('event', $event_id);
							$key = 'ynevent_predispatch_url:' . $module_video . '.index.view';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'subject' => $event->getGuid(),
							), 'event_extended', true);
							$_SESSION[$key] = $value;
							break;
						
						case 'blog':
							$table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
							try {
								
								$row = $table -> createRow();
							    $row -> setFromArray(array(
							       'event_id' => $event_id,
							       'item_id' => $payload -> getIdentity(),
							       'user_id' => $payload -> owner_id,				       
							       'type' => 'blog',
							       'creation_date' => date('Y-m-d H:i:s'),
							       'modified_date' => date('Y-m-d H:i:s'),
							       ));
							    $row -> save();
							}
							catch (Exception $e) {
							}
							
							$key = 'ynevent_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'id' => $event_id,
								'tab' => $widget_id
							), 'event_profile', true);
							$_SESSION[$key] = $value;
							break;
					}
				}
			}
		}
	}

	public function onItemUpdateAfter($event)
	{
		$payload = $event -> getPayload();
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$event_id = $request -> getParam("subject_id", null);
			$type = $request -> getParam("parent_type", null);
			
			if ($type == 'event')
			{
				$widget_id = $request -> getParam("tab", null);
				if ($event_id)
				{
					$type = $payload -> getType();
					switch ($type) {
						case 'video':
							$ynvideo_enabled = Engine_Api::_()->ynevent()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$event = Engine_Api::_()->getItem('event', $event_id);
							$key = 'ynevent_predispatch_url:' . $module_video . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'subject' => $event->getGuid(),
							), 'event_extended', true);
							$_SESSION[$key] = $value;
							break;
							
						case 'blog':
							
							$key = 'ynevent_predispatch_url:' . $request -> getParam('module') . '.index.manage';
							$value = $view -> url(array(
								'id' => $event_id,
								'tab' => $widget_id
							), 'event_profile', true);
							$_SESSION[$key] = $value;
							break;
					}
				}
			}
		}
	}
	
	public function onItemDeleteAfter($event)
	{
		$payload = $event -> getPayload();
	
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		if (is_object($request))
		{
			$view = Zend_Registry::get('Zend_View');
			$event_id = $request -> getParam("event_id", null);
			$case = $request -> getParam("case", null);
			$type = $request -> getParam("parent_type", null);
			if ($type == 'event')
			{
				if ($event_id)
				{
					switch ($case) {								
						case 'video':	
							$ynvideo_enabled = Engine_Api::_()->ynevent()->checkYouNetPlugin('ynvideo');
							if($ynvideo_enabled)
							{
								$module_video = "ynvideo";
							}
							else {
								$module_video = "video";
							}
							$event = Engine_Api::_()->getItem('event', $event_id);
							$key = 'ynevent_predispatch_url:' . $module_video . '.index.manage';
							$value = $view -> url(array(
								'controller' => 'video',
								'action' => 'manage',
								'subject' => $event->getGuid(),
							), 'event_extended', true);
							$_SESSION[$key] = $value;
							break;
					}
				}
			}
		}
	}	
}
