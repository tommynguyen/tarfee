<?php

class Ynevent_Widget_ProfileGoogleCalendarController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$oauth2_client_id = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.google.oauth.client.id', '');
  	$oauth2_client_secret = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.google.oauth.client.secret', '');
  	$developer_key = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.google.api', '');
  	 
  	if ( ($oauth2_client_id == '') || ($oauth2_client_secret == '') || ($developer_key == '') )
  	{
  		return $this->setNoRender();
  	}
  	
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
    $this->view->event = $subject;
    $message = "";
    if (isset($_SESSION['google_calendar_message']))
    {
    	$message = $_SESSION['google_calendar_message'];
    	unset($_SESSION['google_calendar_message']);
    }
    
    $this->view->message = $message; 
  }
}