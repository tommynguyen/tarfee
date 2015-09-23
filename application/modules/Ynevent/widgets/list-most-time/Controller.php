<?php

class Ynevent_Widget_ListMostTimeController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynevent/externals/scripts/YneventTabContent.js');
		$params = $this -> _getAllParams();
		$tab_upcoming = $tab_today = $tab_week = $tab_month = $mode_list = $mode_grid = $mode_map = 1;
		$tab_enabled = $mode_enabled = array();
		$view_mode = 'list';
		if(isset($params['tab_upcoming']))
		{
			$tab_upcoming = $params['tab_upcoming'];
		}
		if($tab_upcoming)
		{
			$tab_enabled[] = 'upcoming';
		}
		if(isset($params['tab_today']))
		{
			$tab_today = $params['tab_today'];
		}
		if($tab_today)
		{
			$tab_enabled[] = 'today';
		}
		if(isset($params['tab_week']))
		{
			$tab_week = $params['tab_week'];
		}
		if($tab_week)
		{
			$tab_enabled[] = 'week';
		}
		if(isset($params['tab_month']))
		{
			$tab_month = $params['tab_month'];
		}
		if($tab_month)
		{
			$tab_enabled[] = 'month';
		}
		
		if(isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		if(isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if(isset($params['mode_map']))
		{
			$mode_map = $params['mode_map'];
		}
		if($mode_map)
		{
			$mode_enabled[] = 'map';
		}
		if(isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}
		$this -> view -> tab_enabled = $tab_enabled;
		$this -> view -> mode_enabled = $mode_enabled;
		
		$class_mode = "ynevent_list-view";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "ynevent_grid-view";
				break;
			case 'map':
				$class_mode = "ynevent_map-view";
				break;
			default:
				$class_mode = "ynevent_list-view";
				break;
		}
		$this -> view -> class_mode = $class_mode;
		$this -> view -> view_mode = $view_mode;
		if(!$tab_enabled)
		{
			$this -> setNoRender();
		}
		
		$itemCount = $this->_getParam('itemCountPerPage', 6);
        $table = Engine_Api::_()->getItemTable('event');
		$tableName = $table -> info('name');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$oldTz = date_default_timezone_get();
		if($viewer -> getIdentity())
			date_default_timezone_set($viewer -> timezone);
		$date = date('Y-m-d H:i:s');
		date_default_timezone_set($oldTz);
		
		$time = strtotime($date);
		$sub = $time - time();
		
		if(!$itemCount)
		{
			$itemCount = 6;
		}
		$this->view->itemCount = $itemCount;
		
		//upcoming event
		// membership
		$membershipTb = Engine_Api::_() -> getDbtable('membership', 'ynevent');
		$membershipName = $membershipTb -> info('name');
		$select = $membershipTb -> getMembershipsOfSelect($viewer);
		$select -> where("$tableName.endtime > FROM_UNIXTIME(?)", $time)
				-> order("$membershipName.rsvp DESC")
				-> order("$tableName.starttime ASC")
				-> limit($itemCount);
		$events_upcoming = $table -> fetchAll($select);
		if(count($events_upcoming) < $itemCount)
		{
			$select = $table -> select();
			$select -> where("$tableName.endtime > FROM_UNIXTIME(?)", $time)
					-> order("$tableName.starttime ASC")
					-> limit($itemCount - count($events_upcoming));
			$otherEvents = $table -> fetchAll($select);
			foreach($otherEvents as $otherEvent)
			{
				$events_upcoming[] = $otherEvent;
			}
		}
		
        $this->view->events_upcoming = $events_upcoming;
		
		// today
       	$select = $table->select()     
			->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = YEAR('{$date}')")
	       	->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = MONTH('{$date}')")
	       	->where("DAY(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = DAY('{$date}')")
       		->order("starttime ASC")
			->limit($itemCount);
        $this->view->events_today = $events_today = $table->fetchAll($select);
		 
		// this week
        $select = $table->select()  
			->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = YEAR('{$date}')")   
	       	->where("WEEK(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = WEEK('{$date}')")
       		->order("starttime ASC")
			->limit($itemCount);
        $this->view->events_week = $events_week = $table->fetchAll($select);
		
		//this month
        	$select = $table->select()     
			->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = YEAR('{$date}')")
	       	->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = MONTH('{$date}')")
       		->order("starttime ASC")
			->limit($itemCount);
        $this->view->events_month = $events_month = $table->fetchAll($select);
        
        $eventIdsUpcoming = $eventIdsToday = $eventIdsWeek = $eventIdsMonth = array();
		foreach ($events_upcoming as $e){
			$eventIdsUpcoming[] = $e -> getIdentity();
		}
		foreach ($events_today as $e){
			$eventIdsToday[] = $e -> getIdentity();
		}
		foreach ($events_week as $e){
			$eventIdsWeek[] = $e -> getIdentity();
		}
		foreach ($events_month as $e){
			$eventIdsMonth[] = $e -> getIdentity();
		}
		$this->view->eventIdsUpcoming = implode("_", $eventIdsUpcoming);
		$this->view->eventIdsToday = implode("_", $eventIdsToday);
		$this->view->eventIdsWeek = implode("_", $eventIdsWeek);
		$this->view->eventIdsMonth = implode("_", $eventIdsMonth);
    }
}
