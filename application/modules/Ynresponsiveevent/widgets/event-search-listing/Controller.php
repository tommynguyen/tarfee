<?php
class Ynresponsiveevent_Widget_EventSearchListingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event' || !Engine_Api::_() -> hasItemType('event'))
	{
		return $this -> setNoRender(true);
	}
	$event_active = 'event';
	if (Engine_Api::_()->hasModuleBootstrap('ynevent'))
	{
		$event_active = 'ynevent';
	}
	$this -> view -> event_active = $event_active;
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $values = $request->getParams();
	//search in sub categories
	if (!empty($values['category_id']) && $values['category_id'] > 0)
	{
		if($event_active == 'ynevent')
		{
			$parentCat = $values['category_id'];
			$parentNode = Engine_Api::_() -> getDbtable('categories', 'ynevent') -> getNode($parentCat);
			if ($parentNode)
			{
				$childsNode = $parentNode -> getAllChildrenIds();
				$values['arrayCat'] = $childsNode;
			}
		}
	}

	if (!empty($values['start_date']))
	{
		$temp = explode("-", $values['start_date']);
		//Date format is Y-m-d;
		if (count($temp) == 3)
			$values['start_date'] = $temp[0] . "-" . $temp[1] . "-" . $temp[2];
	}
	if (!empty($values['end_date']))
	{
		$temp = explode("-", $values['end_date']);
		//Date format is Y-m-d;
		if (count($temp) == 3)
			$values['end_date'] = $temp[0] . "-" . $temp[1] . "-" . $temp[2];
	}
	if (!empty($values['tag']))
	{
		$tag = Engine_Api::_() -> getItem('core_tag', $values['tag']);
		if($tag)
			$this -> view -> tagName = $tag -> text;
	}
	// Get paginator
	$this -> view -> paginator = $paginator = Engine_Api::_() -> ynresponsiveevent() -> getEventPaginator($values);
	$paginator -> setCurrentPageNumber($this -> _getParam('page'));
	$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create');
	if(isset($values['keyword']) || isset($values['category_id']))
		$this -> view -> is_search = true;
	if(isset($values['view_more']) || $values['view_more'])
		$this -> view -> view_more = true;
	unset($values['module']);
	unset($values['controller']);
	unset($values['action']);
	unset($values['rewrite']);
	unset($values['format']);
	unset($values['page']);
	unset($values['type']);
    $view_mode = $this->_getParam('view_mode', 'list');
	$this -> view -> view_mode = $view_mode;
	$this -> view -> formValues = $values;
  }
}