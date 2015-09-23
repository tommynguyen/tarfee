<?php
class Ynresponsiveevent_Widget_EventSlideEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event' || !Engine_Api::_() -> hasItemType('event'))
	{
		return $this -> setNoRender(true);
	}
	$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
	$paginator = Engine_Api::_() -> getDbTable('events', 'ynresponsiveevent') -> getEventPaginator();
	$paginator -> setItemCountPerPage($itemCountPerPage);
	$this -> view -> events = $paginator;
	$event_active = 'event';
	if (Engine_Api::_()->hasModuleBootstrap('ynevent'))
	{
		$event_active = 'ynevent';
	}
	$this -> view -> event_active = $event_active;
  }
}