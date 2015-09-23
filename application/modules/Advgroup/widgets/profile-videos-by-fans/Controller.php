<?php
class Advgroup_Widget_ProfileVideosByFansController extends Engine_Content_Widget_Abstract{
  	public function indexAction(){
     	// Don't render this if not authorized
    	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    	if( !Engine_Api::_()->core()->hasSubject() ) {
      		return $this->setNoRender();
    	}
	
    	if(!Engine_Api::_()->hasItemType('video')) {
      		return $this->setNorender();
    	}
    	
    	// Get subject and check auth
    	$this->view->group = $subject = Engine_Api::_()->core()->getSubject('group');
    	if($subject->is_subgroup && !$subject->isParentGroupOwner($viewer)){
       		$parent_group = $subject->getParentGroup();
    		if(!$parent_group->authorization()->isAllowed($viewer , "view")){
          		return $this->setNoRender();
        	}
        	else if(!$subject->authorization()->isAllowed($viewer , "view")){
          		return $this->setNoRender();
        	}
    	}
    	else if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      		return $this->setNoRender();
    	}
    
		$ids = array();
	    $members = $subject->membership()->getMembersInfo();
	    foreach( $members as $member ) {
	    	if ($member->user_id != $subject->user_id)
	      		$ids[] = $member->user_id;
	    }
		
		if (empty($ids)) $ids = array(0);
		
		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('video');
		$video_ids = array();
		$clubMapping = Engine_Api::_() -> getDbTable('mappingvideos', 'advgroup');
		$select = $clubMapping -> select() -> where("club_id = ?", $subject -> getIdentity());
		foreach ($clubMapping -> fetchAll($select) as $videoMap) 
		{
			$video_ids[] = $videoMap -> video_id;
		}
		if (empty($video_ids)) $video_ids = array(0);
		$video_ids = array_unique($video_ids);
		
		$select = $tableVideo -> select()
			-> where('video_id IN (?)', $video_ids)
			-> where('owner_id IN (?)', $ids)
			-> order('creation_date DESC');
    	
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select-> where("owner_id NOT IN (?)", $deactiveIds);
		}	
    	$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 3));
    	$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		$this -> view -> itemCountPerPage = $this->_getParam('itemCountPerPage', 5);
		
  		if (!$paginator->getTotalItemCount()) {
  			return $this->setNoRender();
  		}
  	}
}
?>
