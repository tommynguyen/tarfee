<?php
class User_Widget_ClubProfilePlayersController extends Engine_Content_Widget_Abstract {
  	public function indexAction() {
    	// Don't render this if not authorized
    	$viewer = Engine_Api::_()->user()->getViewer();
    	if( !Engine_Api::_()->core()->hasSubject() ) {
      		return $this->setNoRender();
    	}

    	// Get subject and check auth
    	$subject = Engine_Api::_()->core()->getSubject('group');
		
		if (!$subject) {
			return $this->setNoRender();
		}
    	if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      		return $this->setNoRender();
    	}
    
    	// Get paginator
    	$this->view->paginator = $paginator = Engine_Api::_() -> getDbTable('playercards', 'user') -> getClubPlayersPaginator($subject);

    	// Set item count per page and current page number
    	$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 3));
    	$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$this -> view -> itemCountPerPage = $this->_getParam('itemCountPerPage', 3);
  	}
}