<?php
class Ynfeed_CustomListController extends Core_Controller_Action_Standard 
{
	protected $_viewer;
	protected $_viewer_id;
	protected $_isMobile;

	public function init() 
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> viewer_id = $this -> _viewer_id = $this -> _viewer -> getIdentity();
		$session = new Zend_Session_Namespace('mobile');
		$this -> _isMobile = $session -> mobile;
	}

	public function createAction() 
	{
		$this -> view -> customTypeLists = $customTypeLists = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getCustomTypeList(array('enabled' => 1));
		$count = count($customTypeLists);
		if (empty($count))
			return $this -> _forward('notfound', 'error', 'core');
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$listTitle = $_POST['title'];
		if (!empty($listTitle)) {
			// create new list
			$table = Engine_Api::_() -> getDbtable('lists', 'ynfeed');
			$list = $table -> createRow();
			$list -> setFromArray(array('title' => $listTitle, 'owner_id' => $this -> _viewer_id));
			$list -> save();

			$selected_resources = $_POST['selected_resources'];
			$list -> setListItems($selected_resources);
		}
		if($this -> _isMobile)
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		else
			return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your List has been created successfully.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function editAction() {
		$list_id = $this -> _getParam('list_id', null);
		$this -> view -> list = $list = Engine_Api::_() -> getItem('ynfeed_list', $list_id);
		$this -> view -> customTypeLists = $customTypeLists = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getCustomTypeList(array('enabled' => 1));
		$count = count($customTypeLists);
		if (empty($count))
			return $this -> _forward('notfound', 'error', 'core');

		$this -> view -> listCount = $list -> count();
		$this -> view -> customList = $list -> getListItems();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$listTitle = $_POST['title'];
		if (!empty($listTitle)) {
			$list -> setFromArray(array('title' => $listTitle));
			$list -> save();

			$selected_resources = $_POST['selected_resources'];
			$list -> setListItems($selected_resources);
		}
		if($this -> _isMobile)
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		else
			return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function deleteAction() {
		$list_id = $this -> _getParam('list_id', null);

		$this -> view -> list = $list = Engine_Api::_() -> getItem('ynfeed_list', $list_id);
		if (!empty($list)) {
			$list -> getListItemTable() -> delete(array("list_id = ? " => $list -> list_id));
			$list -> delete();
		}
		if($this -> _isMobile)
			return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		else
			return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your List has been deleted.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function getContentItemsAction() 
	{
		$resource_type = $this -> _getParam('resource_type', null);
		if(in_array($resource_type, array('blog_link', 'blog_feature', 'ynvideo_signature', 'ynvideo_playlistassoc', 'album_rating', 'mp3music_param', 'mp3music_song_rating', 'forum_container', 'ynsocialads_statistic')) || !$resource_type)
		{
			$this -> view -> count = 0;
			return;
		}
		$membership_type = null;
		switch ($resource_type) {
			case 'ynvideo':
			case 'ynvideo_video':
				$resource_type = 'video';
				break;
			case 'group':
			case 'advgroup':
				$resource_type = $membership_type = 'group';
				if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
				{
					$membership_type = 'ynevent';
				}
				break;
			case 'event':
			case 'ynevent_event':
				$resource_type = $membership_type = 'event';
				if(Engine_Api::_() -> hasModuleBootstrap('ynevent'))
				{
					$membership_type = 'ynevent';
				}
				break;
		}
		if ($resource_type && strpos($resource_type, '_listtype_') !== false) {
			$explode_resource_type = explode('_listtype_', $resource_type);
			$resource_type = $explode_resource_type[0];
			$listingtype_id = $explode_resource_type[1];
		}
		if (empty($resource_type) || !Engine_Api::_() -> hasItemType($resource_type))
			return;
		$this -> _helper -> layout -> disableLayout();
		$likeTable = Engine_Api::_()->getDbtable('likes', 'core');
      	$likeTableName = $likeTable->info('name');
		
		$table = Engine_Api::_() -> getItemTable($resource_type);
		$tableName = $table -> info('name');
		$primary_id = current($table -> info('primary'));

		$metaDataInfo = $table -> info('metadata');

		if(isset($metaDataInfo['user_id']) || isset($metaDataInfo['owner_id']))
		{
			if (isset($metaDataInfo['user_id'])) 
			{
				$owner_id = 'user_id';
			} 
			else 
			{
				$owner_id = 'owner_id';
			}
			$search = $this -> _getParam('search', null);
			$ids = array();
	
			// For User Friends
			if ($resource_type == 'user') 
			{
				$ids = $this -> _viewer -> membership() -> getMembershipsOfIds();
			}
			
			// For Group which member have join || For Event which member have attend
			if (($resource_type == 'group' || $resource_type == 'event') && $membership_type) 
			{
				$membershipTable = Engine_Api::_() -> getDbtable('membership', $membership_type);
				$mtName = $membershipTable -> info('name');
	
				$select = $membershipTable -> select() -> setIntegrityCheck(false) -> from($tableName, "$tableName.$primary_id") -> join($mtName, "`{$mtName}`.`resource_id` = `{$tableName}`.`{$primary_id}`", null) -> where("`{$mtName}`.`active` = ?", (bool)true) -> where("`{$mtName}`.`user_id` = ?", $this -> _viewer_id);
	
				if ($resource_type == 'event') {
					$select -> where("`{$mtName}`.`rsvp` = ?", 2);
				}
				$ids = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
			}
	
			if (!empty($primary_id)) 
			{
				$select = $likeTable -> select() -> setIntegrityCheck(false) -> from($likeTableName, "$likeTableName.resource_id") -> join($tableName, "$likeTableName.resource_id = $tableName.$primary_id", null) -> where($likeTableName . '.resource_type = ?', $resource_type) -> where($likeTableName . '.poster_type = ?', 'user') -> where($likeTableName . '.poster_id = ?', $this -> _viewer_id);
				$likeIds = $select -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
				$ids = array_merge($ids, $likeIds);
			}
	
			$orBaseSql = "$owner_id = $this->_viewer_id";
	
			$select = $table -> select();
	
			if (!empty($ids)) 
			{
				$ids = array_unique($ids);
				if ($resource_type != 'user') 
				{
					$orBaseSql = "$owner_id = $this->_viewer_id";
					$select -> where("( $orBaseSql or $primary_id  IN (?))", (array)$ids);
				} 
				else 
				{
					$select -> where("$primary_id  IN (?)", (array)$ids);
				}
			} else {
				$select -> where("$owner_id = ?", $this -> _viewer_id);
			}
	
			if (!empty($search) && isset($metaDataInfo['title'])) {
				$select -> where("title like ? ", "%" . $search . "%");
			} elseif (($resource_type == 'user') && !empty($search) && isset($metaDataInfo['displayname'])) {
				$select -> where("displayname like ? ", "%" . $search . "%");
			}
			$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
			$paginator -> setItemCountPerPage(40);
			$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
			$this -> view -> count = $paginator -> getTotalItemCount();
		}
		else
		{
			$this -> view -> count = 0;
		}
	}

}
