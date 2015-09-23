<?php
class Ynevent_Widget_ListMostLikedEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
       $table = Engine_Api::_()->getDbtable('events', 'ynevent');
       $ltable  = Engine_Api::_()->getDbtable('likes', 'core');
       $Name = $table->info('name');
       $lName = $ltable->info('name');
       $select = $table->select()->from($Name,"$Name.*,count($lName.like_id)as liked_count");
       $select
       ->joinLeft($lName, "resource_id = $Name.event_id AND resource_type LIKE 'event'",'')
        ->group("$Name.event_id")  
        ->order("Count($lName.like_id) DESC");
        $select ->where("search = ?","1");
        $events = $table->fetchAll($select);
        $itemCount = $this->_getParam('itemCountPerPage', 5);
        $this->view->showedEvents = $showedEvents 
        	= Ynevent_Plugin_Utilities::getListOfEvents($events, empty($itemCount)?5:$itemCount);
        

    // Hide if nothing to show
    if( count($showedEvents) <= 0 ) {
      return $this->setNoRender();
    }
  }
}
