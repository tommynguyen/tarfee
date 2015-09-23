<?php

class Ynevent_Widget_ListMostItemsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Ynevent/externals/scripts/YneventTabContent.js');
		$params = $this -> _getAllParams();
		$tab_popular = $tab_attending = $tab_liked = $tab_rated = $mode_list = $mode_grid = $mode_map = 1;
		$tab_enabled = $mode_enabled = array();
		$view_mode = 'list';
		if(isset($params['tab_popular']))
		{
			$tab_popular = $params['tab_popular'];
		}
		if($tab_popular)
		{
			$tab_enabled[] = 'popular';
		}
		if(isset($params['tab_attending']))
		{
			$tab_attending = $params['tab_attending'];
		}
		if($tab_attending)
		{
			$tab_enabled[] = 'attending';
		}
		if(isset($params['tab_liked']))
		{
			$tab_liked = $params['tab_liked'];
		}
		if($tab_liked)
		{
			$tab_enabled[] = 'liked';
		}
		if(isset($params['tab_rated']))
		{
			$tab_rated = $params['tab_rated'];
		}
		if($tab_rated)
		{
			$tab_enabled[] = 'rated';
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
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$filter = $request -> getParam('filter', 'future');
		if($filter == 'future')
		{
			$filter = ">";
		}
		else
		{
			$filter = "<";
		}
		//popular event
		$select = $table->select() 
			-> where('search = ?', 1) 
			-> where("endtime $filter FROM_UNIXTIME(?)", time())
			-> order('view_count')
			->limit($itemCount);
        $this->view->events_popular = $events_popular = $table->fetchAll($select);
		
		// most attending
		$tableEventName = $table->info('name');
		$select = $table->select() ->limit($itemCount);
 
      	$tableMembership = Engine_Api::_()->getDbTable('membership','ynevent');
     	$tableMembershipName = $tableMembership->info('name');
      	$select->setIntegrityCheck(false)
              -> from($tableEventName,array("$tableEventName.*","count($tableMembershipName.resource_id)as member_attending"))
              -> join($tableMembershipName, "$tableMembershipName.resource_id=$tableEventName.event_id", '')
              -> where("$tableMembershipName.rsvp = ?", 2)
              -> group("$tableMembershipName.resource_id")
			  -> where("endtime $filter FROM_UNIXTIME(?)", time())
              -> order("count($tableMembershipName.resource_id) DESC")
			  -> limit($itemCount);
		 $this->view->events_attending = $events_attending = $table->fetchAll($select);
		 
		//most liked
        $ltable  = Engine_Api::_()->getDbtable('likes', 'core');
        $Name = $table->info('name');
       	$lName = $ltable->info('name');
       	$select = $table->select()->from($Name,"$Name.*,count($lName.like_id)as liked_count");
       	$select
       			-> joinLeft($lName, "resource_id = $Name.event_id AND resource_type LIKE 'event'",'')
        		-> group("$Name.event_id")  
        		-> order("Count($lName.like_id) DESC");
        $select -> where("search = ?","1")
        		-> where("endtime $filter FROM_UNIXTIME(?)", time());
        $events = $table->fetchAll($select);
        $this->view->events_liked = $events_liked = Ynevent_Plugin_Utilities::getListOfEvents($events, $itemCount);
		
		//most rated
        $select = $table->select();
        $tableEventName = $table->info('name');
        $tableRating = Engine_Api::_()->getDbTable('ratings', 'ynevent');
        $tableRatingName = $tableRating->info('name');
        $select->setIntegrityCheck(false)
                -> from($tableEventName, array("$tableEventName.*", "count($tableRatingName.event_id)as rating_count"))
                -> join($tableRatingName, "$tableRatingName.event_id=$tableEventName.event_id", '')
                -> where("$tableEventName.search = ?", 1)
                -> group("$tableRatingName.event_id")
                -> where("endtime $filter FROM_UNIXTIME(?)", time())
                -> order("$tableEventName.rating DESC");
        $this->view->events_rated = $events_rated = Zend_Paginator::factory($select);

		$eventIdsPopular = $eventIdsAttending = $eventIdsLiked = $eventIdsRated = array();
		foreach ($events_popular as $e){
			$eventIdsPopular[] = $e -> getIdentity();
		}
		foreach ($events_attending as $e){
			$eventIdsAttending[] = $e -> getIdentity();
		}
		foreach ($events_liked as $e){
			$eventIdsLiked[] = $e -> getIdentity();
		}
		foreach ($events_rated as $e){
			$eventIdsRated[] = $e -> getIdentity();
		}
		$this->view->eventIdsPopular = implode("_", $eventIdsPopular);
		$this->view->eventIdsAttending = implode("_", $eventIdsAttending);
		$this->view->eventIdsLiked = implode("_", $eventIdsLiked);
		$this->view->eventIdsRated = implode("_", $eventIdsRated);
		
    }

}
