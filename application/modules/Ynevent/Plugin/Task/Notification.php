<?php

class Ynevent_Plugin_Task_Notification extends Core_Plugin_Task_Abstract {

     public function execute() 
     {
        $eventTable = Engine_Api::_() -> getDbTable("events", "ynevent");
		$toTime = time() + (24 * 60 * 60);
		$fromTime = $toTime - 600;
		$fromDay = date('Y-m-d H:i:s', $fromTime);
		$toDay = date('Y-m-d H:i:s', $toTime);
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $start_events = $eventTable -> getAllEventsStartInDay($fromDay, $toDay);
		//$end_events = $eventTable -> getAllEventsEndInDay($fromDay, $toDay);
		foreach ($start_events as $event) 
		{
			$member = Engine_Api::_() -> user() -> getUser($event -> member_id);
			$notifyApi->addNotification($member, $member, $event, 'ynevent_remind');
		}
		/*
		foreach ($end_events as $event) 
		{
			$member = Engine_Api::_() -> user() -> getUser($event -> member_id);
			$notifyApi->addNotification($member, $member, $event, 'ynevent_notify_end');
		}
		 */
		
     }

}

