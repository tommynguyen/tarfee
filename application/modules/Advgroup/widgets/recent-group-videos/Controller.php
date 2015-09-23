<?php
class Advgroup_Widget_RecentGroupVideosController extends Engine_Content_Widget_Abstract{
  public function indexAction(){
     // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
	
    if(!Engine_Api::_()->hasItemType('video'))
    {
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
    $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }
        
    $params = array();
    $params['parent_type'] = 'group';
    $params['parent_id'] = $subject->getIdentity();
    $params['orderby'] = 'creation_date';
	
	//Get data from table Mappings
	$tableMapping = Engine_Api::_()->getItemTable('advgroup_mapping');
	$mapping_ids = $tableMapping -> getVideoIdsMapping($subject -> getIdentity());
	
	//Get data from table video
	$tableVideo = Engine_Api::_()->getItemTable('video');
	$select = $tableVideo -> select() 
		-> from($tableVideo->info('name'), new Zend_Db_Expr("`video_id`"))
		-> where('parent_type = "group"') 
		-> where('parent_id = ?', $subject -> getIdentity());
	$video_ids = $tableVideo -> fetchAll($select);
	
	//Merge ids
	foreach($mapping_ids as $mapping_id)
	{
		$params['ids'][] = $mapping_id -> item_id;
	}
	foreach($video_ids as $video_id)
	{
		if(!in_array($video_id -> video_id, $params['ids']))
		{
			$params['ids'][] = $video_id -> video_id;
		}
	}
	
    $this->view->paginator = $paginator = $subject -> getVideosPaginator($params);
	$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 3));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	$this -> view -> itemCountPerPage = $this->_getParam('itemCountPerPage', 3);
	
	$canCreate = $subject -> authorization() -> isAllowed($viewer, 'video');
    $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'video');
   
      if ($canCreate && $levelCreate) {
        $this -> view -> canCreate = true;
      } else {
        $this -> view -> canCreate = false;
      }
  }
	 
}
?>
