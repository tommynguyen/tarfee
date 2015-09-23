<?php
class Ynresponsiveevent_Widget_EventCategoriesController extends Engine_Content_Widget_Abstract
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
	$this -> view -> categories = Engine_Api::_() -> ynresponsiveevent() -> getCategories($event_active);
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $values = $request->getParams();
	if(!empty($values['category_id']))
		$this -> view -> category_id = $values['category_id'];
  }
}