<?php
class Ynsocialads_Widget_ManageLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$isOwner = false;
	    // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject();
		if(!$subject -> isOwner($viewer)) 
		{
			return $this->setNoRender();
		}
    }
}
	