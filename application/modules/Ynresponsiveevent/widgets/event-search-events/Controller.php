<?php
class Ynresponsiveevent_Widget_EventSearchEventsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event' || !Engine_Api::_() -> hasItemType('event'))
	{
		return $this -> setNoRender(true);
	}
	 // Create form
     $formFilter = new Ynresponsiveevent_Form_Event_Browse();
	 $formFilter->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'ynresponsive_event_listtng', true));
	 $this->view->form = $formFilter;
	 $request = Zend_Controller_Front::getInstance()->getRequest();
     $values = $request->getParams();
	 $formFilter -> populate($values);
	 $view = $this->view;
     $view->addHelperPath(APPLICATION_PATH . '/application/modules/Ynresponsive1/View/Helper', 'Ynresponsive1_View_Helper');
  }
}