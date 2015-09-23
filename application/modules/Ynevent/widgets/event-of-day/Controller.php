<?php
/**
 * YouNet Company
 *
 * @category   	Application_Extensions
 * @package    	Adv.Event
 * @company     YouNet Company
 * @author		LuanND
 */

class Ynevent_Widget_EventOfDayController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    	$table = Engine_Api::_()->getItemTable('event');
		$select = $table->select()->where('event_of_date = ?',date('Y-m-d'))->limit(1);
		
        $this->view->event = $table->fetchRow($select);
		if(!count($this->view->event))
		{
			return $this->setNoRender();
		}		
		
  }
}
?>
