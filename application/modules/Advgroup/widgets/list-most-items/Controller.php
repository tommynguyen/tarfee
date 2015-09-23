<?php

class Advgroup_Widget_ListMostItemsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	$headScript = new Zend_View_Helper_HeadScript();
   		$headScript -> appendFile('application/modules/Advgroup/externals/scripts/AdvgroupTabContent.js');
		$params = $this -> _getAllParams();
		
		$tab_recent = $tab_popular = $tab_active = $tab_directory = $mode_list = $mode_grid = $mode_map = 1;
		$tab_enabled = $mode_enabled = array();
		$view_mode = 'list';
		
		if(isset($params['tab_popular']))
		{
			$tab_popular = $params['tab_popular'];
		}
		if($tab_popular)
		{
			$tab_enabled[] = 'popular';
		}
		if(isset($params['tab_recent']))
		{
			$tab_recent = $params['tab_recent'];
		}
		if($tab_recent)
		{
			$tab_enabled[] = 'recent';
		}
		if(isset($params['tab_active']))
		{
			$tab_active = $params['tab_active'];
		}
		if($tab_active)
		{
			$tab_enabled[] = 'active';
		}
		if(isset($params['tab_directory']))
		{
			$tab_directory = $params['tab_directory'];
		}
		if($tab_directory)
		{
			$tab_enabled[] = 'directory';
		}		
		if(isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		if(isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if(isset($params['mode_map']))
		{
			$mode_map = $params['mode_map'];
		}
		if($mode_map)
		{
			$mode_enabled[] = 'map';
		}
		if(isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		
		if($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}
		
		$this -> view -> tab_enabled = $tab_enabled;	
		$this -> view -> mode_enabled = $mode_enabled;
		
		$class_mode = "advgroup_list-view";
		switch ($view_mode) 
		{
			case 'grid':
				$class_mode = "advgroup_grid-view";
				break;
			case 'map':
				$class_mode = "advgroup_map-view";
				break;
			default:
				$class_mode = "advgroup_list-view";
				break;
		}
		$this -> view -> class_mode = $class_mode;
		$this -> view -> view_mode = $view_mode;
		if(!$tab_enabled)
		{
			$this -> setNoRender();
		}
		$itemCount = $this->_getParam('itemCountPerPage', 6);
		
		if(!$itemCount)
		{
			$itemCount = 6;
		}
		$this->view->itemCount = $itemCount;
        $table = Engine_Api::_()->getItemTable('group');
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		
		// recent groups		
		    $count = $this->_getParam('itemCountPerPage');
		    if(!is_numeric($count) | $count <=0) $count = 6;
		    
		    $recentType = $this->_getParam('recentType', 'creation');
		    if( !in_array($recentType, array('creation', 'modified')) ) {
		      $recentType = 'creation';
		    }
		    $this->view->recentType = $recentType;
		    $this->view->recentCol = $recentCol = $recentType . '_date';
		    
		    // Get paginator
		    $table = Engine_Api::_()->getItemTable('group');
		    $select = $table->select()
		      ->where('search = ?', 1)
		      ->where("is_subgroup = ?",0) 
			 // ->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$time} {$type})"))
		      ->limit($count);
		    if( $recentType == 'creation' ) {
		      // using primary should be much faster, so use that for creation
		      $select->order('group_id DESC');
		    } else {
		      $select->order($recentCol . ' DESC');
		    }
		    $this->view->recentgroups = $groups = $table->fetchAll($select);
			
		    $this->view->limit = $count;
		    // Hide if nothing to show
		    if( count($groups) <= 0 ) {
		      //return $this->setNoRender();
		    }
		 
		//most popular groups
       
			$count = $this->_getParam('itemCountPerPage');
		    if(!is_numeric($count) | $count <=0) $count = 6;
		    
		    $popularType = $this->_getParam('popularType', 'member');
		    if( !in_array($popularType, array('view', 'member')) ) {
		      $popularType = 'member';
		    }
		    $this->view->popularType = $popularType;
		    $this->view->popularCol = $popularCol = $popularType . '_count';
		    
		    // Get paginator
		    $table = Engine_Api::_()->getItemTable('group');
		    $select = $table->select()
		      ->where('search = ?', 1)
		      ->where("is_subgroup = ?",0)
		      ->order($popularCol . ' DESC')
		      ->limit($count);
		    $this->view->populargroups = $groups = $table->fetchAll($select);
		    $this->view->limit = $count;
		    // Hide if nothing to show
		    if( count($groups) <= 0 ) {
		      //return $this->setNoRender();
		    }
		
		//most active group
			
			$count = $this->_getParam('itemCountPerPage');
		    if(!is_numeric($count) | $count <=0) $count = 6;
			
		    $time = $this->_getParam('time',1);
		    if( !in_array($time, array(1, 2, 3)) ) {
		    	$time = 1;
		    }
		
		    $date = date('Y-m-d H:i:s');
		    switch($time){
		    	case 1:
		    		$newdate = strtotime ( '-30 day' , strtotime ($date)) ;
		    		break;
		    	case 2:
		    		$newdate = strtotime ( '-60 day' , strtotime ($date)) ;
		    		break;
		    	case 3:
		    		$newdate = strtotime ( '-90 day' , strtotime ($date)) ;
		    }
		    $newdate = date ( 'Y-m-d H:i:s' , $newdate );
		    
		    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
		    $topicName =$topicTable ->info('name');
		    $groupTable = Engine_Api::_()->getItemTable('group');
		    $groupName = $groupTable->info('name');
		    
		    $select = $groupTable->select()->from($groupName,array("$groupName.*","COUNT('topic_id') AS topic_count"))
		                ->setIntegrityCheck(false)
		                ->joinRight($topicName, "$topicName.group_id = $groupName.group_id","$topicName.topic_id")
		                ->where("$groupName.search = ?", 1)
		                ->where("$groupName.is_subgroup = ?",0)
		                ->where("$topicName.creation_date > ?",$newdate)
		                ->group("$groupName.group_id")
		                ->order("COUNT('topic_id') DESC")
		                ->limit($count);
		    $this->view->activegroups = $groups = $groupTable->fetchAll($select);
		    $this->view->limit = $count;
		    if( count($groups) <= 0 ) {
		      //return $this->setNoRender();
		    }
		
		//group directory
		$request = Zend_Controller_Front::getInstance()->getRequest();
	      // clear title of widget
	      if ($request->isPost()) {
	      	$this->getElement()->setTitle('');
	      	$element = $this->getElement();
	      }
	      
	      $table = Engine_Api::_()->getItemTable('group');
	      $select = $table->select()
	      ->where('search = ?', 1)
	      ->where('is_subgroup = ?', 0)
	      ->order('title ASC');
	  		     
	      $this->view->directory = $paginator = Zend_Paginator::factory($select);
			
	      // Set item count per page and current page number
	      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
	      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	      if( count($paginator) <= 0 ) {
	      	//return $this->setNoRender();
	      }
    }

}
