<?php
class AdvGroup_Widget_GroupCreateLinkController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$isOwner = false;
	    // Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $subject = Engine_Api::_()->core()->getSubject();
		$this -> view -> group = $group = Engine_Api::_() -> advgroup() -> getGroupUser($subject);
		if($subject -> isSelf($viewer)) 
		{
			$isOwner = true;
			if( Engine_Api::_()->authorization()->isAllowed('group', $viewer, 'create') ) {
		        $checkGroupUser = Engine_Api::_() -> advgroup() -> checkGroupUser();
				if($checkGroupUser) {
					$this -> view -> canCreate = false;
				} else {
					$this -> view -> canCreate = true;
				}
		    } 	
		}
		$this -> view -> isOwner = $isOwner;
    }
}
	