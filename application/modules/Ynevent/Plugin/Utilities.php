<?php
class Ynevent_Plugin_Utilities {
	public static function getListOfEvents($events, $maxItems = 0) {
		$showedEvents = array();
		$viewer = Engine_Api::_()->user()->getViewer();
	 	$auth = Engine_Api::_()->authorization()->context;
	 	$count = 0;
        foreach($events as $event) {
        	if ($auth->isAllowed($event, $viewer, 'view')) {
        		array_push($showedEvents, $event);
        	}
        	$count++;
        	if ($count == $maxItems) {
        		break;
        	}
        }
        return $showedEvents;
	}
	public static function getNumberAgentAllow($user)
	{
		$arr = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'search_agent');
		if (is_array($arr) || is_object($arr)) {
			return current($arr);
		}
	}
}