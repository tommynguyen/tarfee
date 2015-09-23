<?php

class Ynevent_Widget_ProfileFollowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
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
	if ($viewer->getIdentity() == 0) {
		return $this->setNoRender();
	}
    
    // Build form
    $followTable = Engine_Api::_()->getDbTable('follow','ynevent');
    $row = $followTable->getFollowEvent($subject->getIdentity(),$viewer->getIdentity());
    $this->view->viewer_id = $viewer->getIdentity();
	$follow = 0;
	if($row)
	{
		$follow =  $row->follow;
	}
	
    $this->view->follow = $follow;
  }
}