<?php

class Ynevent_Widget_ProfileSlideshowPhotosController extends Engine_Content_Widget_Abstract
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

    $this->view->allowLoop =  $allowLoop = ($this->_getParam('allowLoop') == '1') ? true : false;
    $this->view->effect = $effect = $this->_getParam('effect', 'fade');
    
    // Get paginator
    $album = $subject->getSingletonAlbum();
	$params = array('is_featured' => 1);
	
    $this->view->paginator = $paginator = $album->getFeaturedPaginator($params);

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    
    // Do not render if nothing to show and cannot upload
    if( $paginator->getTotalItemCount() <= 0) 
    {
      return $this->setNoRender();
    }

  }
}