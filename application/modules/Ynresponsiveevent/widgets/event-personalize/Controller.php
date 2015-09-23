<?php
class Ynresponsiveevent_Widget_EventPersonalizeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event' || !Engine_Api::_() -> hasItemType('event') || !Engine_Api::_() -> user() -> getViewer() -> getIdentity())
	{
		return $this -> setNoRender(true);
	}
	$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
	$request = Zend_Controller_Front::getInstance()->getRequest();
    $values = $request->getParams();
	if(!empty($values['owner']))
		$this -> view -> owner = true;
	if(!empty($values['type']))
		$this -> view -> type = $values['type'];
  }
}