<?php
class Advgroup_VideoController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
			{
				Engine_Api::_() -> core() -> setSubject($group);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		
	}

	public function listAction()
	{
		//Checking Ynvideo Plugin - View privacy
		$ynvideo_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('ynvideo');
		$video_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('video');
		if (!$video_enable && !$ynvideo_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Video_Search;

		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}
		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed($viewer, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'video');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['parent_type'] = 'group';
		$params['parent_id'] = $group -> getIdentity();
		$params['search'] = 1;
		$params['limit'] = 12;
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		
		//Get data from table Mappings
		$tableMapping = Engine_Api::_()->getItemTable('advgroup_mapping');
		$mapping_ids = $tableMapping -> getVideoIdsMapping($group -> getIdentity());
		
		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('video');
		$select = $tableVideo -> select() 
			-> from($tableVideo -> info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "group"') 
			-> where('parent_id = ?', $group -> getIdentity());
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
		
		//Get data
		$this -> view -> paginator = $paginator = $group -> getVideosPaginator($params);
		
		if (!empty($params['orderby']))
		{
			switch($params['orderby'])
			{
				case 'most_liked' :
					$this -> view -> infoCol = 'like';
					break;
				case 'most_commented' :
					$this -> view -> infoCol = 'comment';
					break;
				default :
					$this -> view -> infoCol = 'view';
					break;
			}
		}
	}

	public function manageAction()
	{
		//Checking Ynvideo Plugin - Viewer required -View privacy
		$video_enable = Engine_Api::_() -> hasItemType('video');
		if (!$video_enable)
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid())
		{
			return;
		}

		//Get viewer, group, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Advgroup_Form_Video_Search;

		// Check create video authorization
		$canCreate = $group -> authorization() -> isAllowed(null, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('group', $viewer, 'video');

		if ($canCreate && $levelCreate)
		{
			$this -> view -> canCreate = true;
		}
		else
		{
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = array();
		$params = $this -> _getAllParams();
		$params['parent_type'] = 'group';
		$params['parent_id'] = $group -> getIdentity();
		$params['user_id'] = $viewer -> getIdentity();
		$params['limit'] = 12;
		$form -> populate($params);
		$this -> view -> formValues = $form -> getValues();
		
		//Get data from table Mappings
		$tableMapping = Engine_Api::_()->getItemTable('advgroup_mapping');
		$mapping_ids = $tableMapping -> getVideoIdsMapping($group -> getIdentity());
		
		//Get data from table video
		$tableVideo = Engine_Api::_()->getItemTable('video');
		$select = $tableVideo -> select() 
			-> from($tableVideo->info('name'), new Zend_Db_Expr("`video_id`"))
			-> where('parent_type = "group"') 
			-> where('parent_id = ?', $group -> getIdentity());
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
		
		//Get data
		$this -> view -> paginator = $paginator = $group -> getVideosPaginator($params);
		if (!empty($params['orderby']))
		{
			switch($params['orderby'])
			{
				case 'most_liked' :
					$this -> view -> infoCol = 'like';
					break;
				case 'most_commented' :
					$this -> view -> infoCol = 'comment';
					break;
				default :
					$this -> view -> infoCol = 'view';
					break;
			}
		}
		
		$this->view->group = $group;
	}
	
	public function highlightAction() {
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}		
		
		$group = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$item_id = $this -> _getParam('video_id', null);
				
		$table = Engine_Api::_() -> getDbTable('highlights', 'advgroup');
		$select = $table->select()
				-> where("group_id = ?", $group -> getIdentity())
				-> where("type = 'video'")
				-> where('item_id = ?', $item_id)
				-> limit(1);
		$items  = $table->fetchAll($select);

		if (!count($items))
		{
			$highlightItem = $table->createRow();
			$highlightItem->setFromArray(array(
				'user_id' => $viewer->getIdentity(),
				'group_id' => $group -> getIdentity(),
				'item_id' => $item_id,
				'type' => 'video',
				'highlight' => 1
			));
		}
		else 
		{
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			try {
				$select2 = $table -> select() -> where("group_id = ?", $group -> getIdentity()) -> where("type = 'video'") -> where("highlight = 1") -> limit(1);
				$row2 = $table -> fetchRow($select2);
				if($row2)
				{
					
					$row2 -> highlight = !$row2 -> highlight;
					$row2->save();
				}
			
				$select = $table -> select() -> where("group_id = ?", $group -> getIdentity()) -> where('item_id = ?', $item_id) -> where("type = 'video'") -> limit(1);
				$row = $table -> fetchRow($select);
				//$flag = $row -> hightlight;
				if ($row->item_id != $row2->item_id) {				
					$row -> highlight = !$row -> highlight;
					$row -> save();
				}
					
				$db -> commit();
					
			} catch (Exception $e) {
				$db -> rollback();
				$this -> view -> success = false;
			}
		}
			$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format'=> 'smoothbox',
					'messages' => array($this->view->translate('This video is highlighted/unhighlighted  successfully.'))
			));
		
	}

}
?>
