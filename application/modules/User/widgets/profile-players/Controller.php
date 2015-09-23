<?php
class User_Widget_ProfilePlayersController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
	$this -> getElement() -> removeDecorator('Title');
    // Get paginator
    $profile_owner_id = $subject->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_() -> getDbTable('playercards', 'user') -> getPlayersPaginator($profile_owner_id, true);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 6));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	
  }
}