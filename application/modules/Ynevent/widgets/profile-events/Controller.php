<?php

class Ynevent_Widget_ProfileEventsController extends Engine_Content_Widget_Abstract
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
    if( !($subject instanceof User_Model_User) ) {
      return $this->setNoRender();
    }
	
	$type = $this -> _getParam('type', 0);

    // Get paginator
    $membership = Engine_Api::_()->getDbtable('membership', 'ynevent');
    $this->view->paginator = $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject) -> where('parent_type = ?', 'user') -> where('type_id = ?', $type) ->order('starttime DESC'));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	$this -> view -> itemCountPerPage = $this->_getParam('itemCountPerPage', 5);
	$this -> view -> type = $type;
	if($paginator -> getTotalItemCount() <= 0)
	{
		return $this -> setNoRender();
	}
  }
}