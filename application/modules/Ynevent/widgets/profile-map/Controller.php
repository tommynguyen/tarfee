<?php

class Ynevent_Widget_ProfileMapController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Prepare data
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> fullAddress = $event -> getFullAddress();
		
		// Convert the dates for the viewer
		$tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$startDateObject = new Zend_Date(strtotime($event->starttime));
		if( $viewer->getIdentity() ) {
			$tz = $viewer->timezone;
		}
		$startDateObject->setTimezone($tz);
		$this->view->startDateObject = $startDateObject;
	}

}
