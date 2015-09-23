<?php
class Ynresponsiveevent_Widget_EventTopSponsorsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event' || !Engine_Api::_() -> hasItemType('event'))
	{
		return $this -> setNoRender(true);
	}
	$itemCountPerPage = $this -> _getParam('itemCountPerPage', 10);
	$paginator = Engine_Api::_() -> getDbTable('sponsors', 'ynresponsiveevent') -> getSponsorPaginator();
	$paginator -> setItemCountPerPage($itemCountPerPage);
	$this -> view -> sponsors = $paginator;
  }
}