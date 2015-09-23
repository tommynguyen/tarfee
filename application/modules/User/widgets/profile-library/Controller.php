<?php

class User_Widget_ProfileLibraryController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
	$this -> getElement() -> removeDecorator('Title');
	
	// Get subject
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
	
    // get Main Librady
    $this->view->library = $library = $subject -> getMainLibrary();
   
    //get Table Mappings
    
    $mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
    $videoTable = Engine_Api::_()->getItemTable('video');
    
    $params = array();
    $params['owner_type'] = $library -> getType();
	$params['owner_ids'][] = $library -> getIdentity();
	$params['owner_ids'][] = $subject -> getIdentity();
	$params['user_only'] = true;
    $this->view->mainVideos = $mainVideos = $videoTable -> fetchAll($mappingTable -> getVideosSelect($params));
    }
}