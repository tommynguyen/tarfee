<?php

class Ynevent_Widget_ListMostTimePastController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynevent/externals/scripts/YneventTabContent.js');
		$params = $this -> _getAllParams();
		$tab_past = $tab_week = $tab_month = $mode_list = $mode_grid = $mode_map = 1;
		$tab_enabled = $mode_enabled = array();
		$view_mode = 'list';
		if(isset($params['tab_past']))
		{
			$tab_past = $params['tab_past'];
		}
		if($tab_past)
		{
			$tab_enabled[] = 'past';
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
		if(!$itemCount)
		{
			$itemCount = 6;
		}
		$this->view->itemCount = $itemCount;
		
        $table = Engine_Api::_()->getItemTable('event');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$oldTz = date_default_timezone_get();
		if($viewer -> getIdentity())
			date_default_timezone_set($viewer -> timezone);
		$date = date('Y-m-d H:i:s');
		date_default_timezone_set($oldTz);
		
		$time = strtotime($date);
		$sub = $time - time();
		
		//past event
		$select = $table->select() 
			-> where("endtime < FROM_UNIXTIME(?)", time())
       		-> order("starttime ASC")
			-> limit($itemCount);
        $this->view->events_pastevent = $events_pastevent = $table->fetchAll($select);
		
		$week = (int) date('W', strtotime(date('Y-m-d H:i:s')));
		$subyear = 0;
		if($week == 1)
		{
			$subyear = 1;
		}
		// last week
        $select = $table->select()  
			->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = YEAR('{$date}') - ?",$subyear)      
	       	->where("WEEK(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = WEEK(date_sub('{$date}', INTERVAL 7 day))")
       		->order("starttime ASC")
			->limit($itemCount);
        $this->view->events_preweek = $events_preweek = $table->fetchAll($select);
		
		//last month
        	$select = $table->select()    
			->where("YEAR(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = YEAR('{$date}') - ?",$subyear)      
	       	->where("MONTH(FROM_UNIXTIME(UNIX_TIMESTAMP(starttime) + {$sub})) = MONTH(date_sub('{$date}', INTERVAL 1 MONTH))")
       		->order("starttime ASC")
			->limit($itemCount);
        $this->view->events_premonth = $events_premonth = $table->fetchAll($select);
		
		$eventIds = array();
		foreach ($events_pastevent as $e){
			$eventIdsPast[] = $e -> getIdentity();
		}
		foreach ($events_preweek as $e){
			$eventIdsPreweek[] = $e -> getIdentity();
		}
		foreach ($events_premonth as $e){
			$eventIdsPremonth[] = $e -> getIdentity();
		}
		$this->view->eventIdsPast = implode("_", $eventIdsPast);
		$this->view->eventIdsPreweek = implode("_", $eventIdsPreweek);
		$this->view->eventIdsPremonth = implode("_", $eventIdsPremonth);
    }

}
