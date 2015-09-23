<?php
class Advgroup_TopicController extends Core_Controller_Action_Standard {
	public function init() {
		//Subject checking
		if (Engine_Api::_() -> core() -> hasSubject())
			return;

		//Set subject if there is no subject
		if (0 !== ($topic_id = (int)$this -> _getParam('topic_id')) && null !== ($topic = Engine_Api::_() -> getItem('advgroup_topic', $topic_id))) {
			Engine_Api::_() -> core() -> setSubject($topic);
		} else if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id))) {
			Engine_Api::_() -> core() -> setSubject($group);
		}
	}

	public function indexAction() {
		//Subject and Auth view Checking
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;

		//Get Group and Search Form
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> form = $form = new Advgroup_Form_Topic_Search;

		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		if ($viewer -> getIdentity() == 0)
			$form -> removeElement('view');
		//Get Search Condition
		$params = array();
		$params['group_id'] = $group -> getIdentity();
		$params['search'] = $this -> _getParam('search', '');
		$params['closed'] = $this -> _getParam('closed', '');
		$params['view'] = $this -> _getParam('view', 0);
		$params['order'] = $this -> _getParam('order', 'recent');
		$params['user_id'] = null;
		if ($params['view'] == 1) {
			$params['user_id'] = $viewer -> getIdentity();
		}

		//Populate Search Form
		$form -> populate(array('search' => $params['search'], 'closed' => $params['closed'], 'view' => $params['view'], 'order' => $params['order'], 'page' => $this -> _getParam('page', 1)));

		$this -> view -> formValues = $form -> getValues();

		//Get Topic Paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('advgroup_topic') -> getTopicsPaginator($params);
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));
		//Other Stuffs
		$this -> view -> can_post = $can_post = $this -> _helper -> requireAuth -> setAuthParams(null, null, 'comment') -> checkRequire();
	}

	public function viewAction() {
		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;

		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> topic = $topic = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> group = $group = $topic -> getParentGroup();
		$this -> view -> canEdit = $topic -> canEdit(Engine_Api::_() -> user() -> getViewer());
		$this -> view -> officerList = $group -> getOfficerList();

		//Check view & comment authorization
		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if ($parent_group -> authorization() -> isAllowed($viewer, 'comment')) {
//				$canPost = $group -> authorization() -> isAllowed($viewer, 'comment');
//			} else {
//				$canPost = $parent_group -> authorization() -> isAllowed($viewer, 'comment');
//			}
//		} else {
			$canPost = $group -> authorization() -> isAllowed($viewer, 'comment');
//		}

		$this -> view -> canPost;

		if (!$viewer || !$viewer -> getIdentity() || $viewer -> getIdentity() != $topic -> user_id) {
			$topic -> view_count = new Zend_Db_Expr('view_count + 1');
			$topic -> save();
		}

		// Check watching
		$isWatching = null;
		if ($viewer -> getIdentity()) {
			$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'advgroup');
			$isWatching = $topicWatchesTable -> select() -> from($topicWatchesTable -> info('name'), 'watch') -> where('resource_id = ?', $group -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1) -> query() -> fetchColumn(0);
			if (false === $isWatching) {
				$isWatching = null;
			} else {
				$isWatching = (bool)$isWatching;
			}
		}
		$this -> view -> isWatching = $isWatching;

		// @todo implement scan to post
		$this -> view -> post_id = $post_id = (int)$this -> _getParam('post');

		$table = Engine_Api::_() -> getDbtable('posts', 'advgroup');
		$select = $table -> select() -> where('group_id = ?', $group -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> order('creation_date ASC');

		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);

		// Skip to page of specified post
		if (0 !== ($post_id = (int)$this -> _getParam('post_id')) && null !== ($post = Engine_Api::_() -> getItem('advgroup_post', $post_id))) {
			$icpp = $paginator -> getItemCountPerPage();
			$page = ceil(($post -> getPostIndex() + 1) / $icpp);
			$paginator -> setCurrentPageNumber($page);
		}

		// Use specified page
		else if (0 !== ($page = (int)$this -> _getParam('page'))) {
			$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		}

		if ($canPost && !$topic -> closed) {
			$this -> view -> form = $form = new Advgroup_Form_Post_Create();
			$form -> populate(array('topic_id' => $topic -> getIdentity(), 'ref' => $topic -> getHref(), 'watch' => (false === $isWatching ? '0' : '1'), ));
		}
	}

	public function createAction() {
		//Require user and subject
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('group') -> isValid())
			return;
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject('group');
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		//Comment authorization
//		if ($group -> is_subgroup) {
//			$parent_group = $group -> getParentGroup();
//			if (!$parent_group -> authorization() -> isAllowed($viewer, 'comment')) {
//				return $this -> _helper -> requireAuth -> forward();
//			} elseif (!$group -> authorization() -> isAllowed($viewer, 'comment')) {
//				return $this -> _helper -> requireAuth -> forward();
//			}
//		} else
      if (!$group -> authorization() -> isAllowed($viewer, 'comment')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Topic_Create();

		// Check method/data
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		$values['group_id'] = $group -> getIdentity();

		$topicTable = Engine_Api::_() -> getDbtable('topics', 'advgroup');
		$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'advgroup');
		$postTable = Engine_Api::_() -> getDbtable('posts', 'advgroup');

		$db = $group -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create topic
			$topic = $topicTable -> createRow();
			$topic -> setFromArray($values);
			$topic -> save();

			// Create post
			$values['topic_id'] = $topic -> topic_id;

			$post = $postTable -> createRow();
			$post -> setFromArray($values);
			$post -> save();

			// Create topic watch
			$topicWatchesTable -> insert(array('resource_id' => $group -> getIdentity(), 'topic_id' => $topic -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'watch' => (bool)$values['watch'], ));

			// Add activity
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($viewer, $group, 'advgroup_topic_create');
			if ($action) {
				$action -> attach($topic);
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		// Redirect to the post
		$this -> _redirectCustom($post);
	}

	public function postAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;
		$this -> view -> topic = $topic = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> group = $group = $topic -> getParentGroup();

		$viewer = Engine_Api::_() -> user() -> getViewer();
      if (!$group -> authorization() -> isAllowed($viewer, 'comment')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		if ($topic -> closed) {
			$this -> view -> status = false;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This has been closed for posting.');
			return;
		}

		// Make form
		$this -> view -> form = $form = new Advgroup_Form_Post_Create();

		// Check method/data
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$topicOwner = $topic -> getOwner();
		$isOwnTopic = $viewer -> isSelf($topicOwner);

		$postTable = Engine_Api::_() -> getDbtable('posts', 'advgroup');
		$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'advgroup');
		$userTable = Engine_Api::_() -> getItemTable('user');
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');

		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		$values['group_id'] = $group -> getIdentity();
		$values['topic_id'] = $topic -> getIdentity();

		$watch = (bool)$values['watch'];
		$isWatching = $topicWatchesTable -> select() -> from($topicWatchesTable -> info('name'), 'watch') -> where('resource_id = ?', $group -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1) -> query() -> fetchColumn(0);

		$db = $group -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create post
			$post = $postTable -> createRow();
			$post -> setFromArray($values);
			$post -> save();

			// Watch
			if (false === $isWatching) {
				$topicWatchesTable -> insert(array('resource_id' => $group -> getIdentity(), 'topic_id' => $topic -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'watch' => (bool)$watch, ));
			} else if ($watch != $isWatching) {
				$topicWatchesTable -> update(array('watch' => (bool)$watch, ), array('resource_id = ?' => $group -> getIdentity(), 'topic_id = ?' => $topic -> getIdentity(), 'user_id = ?' => $viewer -> getIdentity(), ));
			}

			// Activity
			$action = $activityApi -> addActivity($viewer, $group, 'advgroup_topic_reply',$topic->toString());
			if ($action) {
			   $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}

			// Notifications
			$notifyUserIds = $topicWatchesTable -> select() -> from($topicWatchesTable -> info('name'), 'user_id') -> where('resource_id = ?', $group -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> where('watch = ?', 1) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);

			foreach ($userTable->find($notifyUserIds) as $notifyUser) {
				// Don't notify self
				if ($notifyUser -> isSelf($viewer)) {
					continue;
				}
				if ($notifyUser -> isSelf($topicOwner)) {
					$type = 'advgroup_discussion_response';
				} else {
					$type = 'advgroup_discussion_reply';
				}
				$notifyApi -> addNotification($notifyUser, $viewer, $topic, $type, array('message' => $this -> view -> BBCode($post -> body), ));
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		// Redirect to the post
		$this -> _redirectCustom($post);
	}

	public function stickyAction() {
		$topic = Engine_Api::_() -> core() -> getSubject('advgroup_topic');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> getItem('group', $topic -> group_id);

		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;
		if ($viewer -> getIdentity() != $topic -> user_id) {
			if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'topic.edit') -> isValid() && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
				return;
		}

		$table = $topic -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$topic = Engine_Api::_() -> core() -> getSubject();
			$topic -> sticky = (null === $this -> _getParam('sticky') ? !$topic -> sticky : (bool)$this -> _getParam('sticky'));
			$topic -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> _redirectCustom($topic);
	}

	public function closeAction() {
		$topic = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> getItem("group", $topic -> group_id);

		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;
		if ($viewer -> getIdentity() != $topic -> user_id) {
			if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'topic.edit') -> isValid() && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
				return;
		}

		$table = $topic -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$topic = Engine_Api::_() -> core() -> getSubject();
			$topic -> closed = (null === $this -> _getParam('closed') ? !$topic -> closed : (bool)$this -> _getParam('closed'));
			$topic -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> _redirectCustom($topic);
	}

	public function renameAction() {
		$topic = Engine_Api::_() -> core() -> getSubject('advgroup_topic');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> getItem("group", $topic -> group_id);
		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;
		if ($viewer -> getIdentity() != $topic -> user_id) {
			if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'topic.edit') -> isValid() && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
				return;
		}

		$this -> view -> form = $form = new Advgroup_Form_Topic_Rename();

		if (!$this -> getRequest() -> isPost()) {
			$form -> title -> setValue(htmlspecialchars_decode($topic -> title));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = $topic -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$title = htmlspecialchars($form -> getValue('title'));

			$topic = Engine_Api::_() -> core() -> getSubject();
			$topic -> title = $title;
			$topic -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Topic renamed.')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function deleteAction() {
		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;

		$topic = Engine_Api::_() -> core() -> getSubject('advgroup_topic');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> getItem('group', $topic -> group_id);
		if ($viewer -> getIdentity() != $topic -> user_id) {
			if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'topic.edit') -> isValid() && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
				return;
		}

		$this -> view -> form = $form = new Advgroup_Form_Topic_Delete();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$table = $topic -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$topic = Engine_Api::_() -> core() -> getSubject();
			$group = $topic -> getParent('group');
			$topic -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Topic deleted.')), 'layout' => 'default-simple', 'parentRedirect' => $group -> getHref(), ));
	}

	public function watchAction() {
		$topic = Engine_Api::_() -> core() -> getSubject();
		$group = Engine_Api::_() -> getItem('group', $topic -> group_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		//Check view & comment authorization
		if ($group -> is_subgroup) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
				return $this -> _helper -> requireAuth -> forward();
			}
		} elseif (!$group -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> _helper -> requireAuth -> forward();
		}

		$watch = $this -> _getParam('watch', true);

		$topicWatchesTable = Engine_Api::_() -> getDbtable('topicWatches', 'advgroup');
		$db = $topicWatchesTable -> getAdapter();
		$db -> beginTransaction();

		try {
			$isWatching = $topicWatchesTable -> select() -> from($topicWatchesTable -> info('name'), 'watch') -> where('resource_id = ?', $group -> getIdentity()) -> where('topic_id = ?', $topic -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1) -> query() -> fetchColumn(0);

			if (false === $isWatching) {
				$topicWatchesTable -> insert(array('resource_id' => $group -> getIdentity(), 'topic_id' => $topic -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'watch' => (bool)$watch, ));
			} else if ($watch != $isWatching) {
				$topicWatchesTable -> update(array('watch' => (bool)$watch, ), array('resource_id = ?' => $group -> getIdentity(), 'topic_id = ?' => $topic -> getIdentity(), 'user_id = ?' => $viewer -> getIdentity(), ));
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> _redirectCustom($topic);
	}
	public function reportAction()
	{
		$topic = Engine_Api::_() -> core() -> getSubject('advgroup_topic');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$group = Engine_Api::_() -> getItem("group", $topic -> group_id);
		if (!$this -> _helper -> requireSubject('advgroup_topic') -> isValid())
			return;
		$this -> view -> form = $form = new Advgroup_Form_Topic_Report();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$table = Engine_Api::_()->getItemTable('advgroup_report');
		$db = $table->getAdapter();
		$db->beginTransaction();
		try 
		{
			$values = array('user_id'=>$viewer->getIdentity(), 'group_id' =>$this->_getParam('group_id',0),
					'topic_id'=>$this->_getParam('topic_id',0),'post_id'=>$this->_getParam('post_id',0),
					'content'=>$form->getValue('body'));
			
			$report = $table->createRow();
      		$report->setFromArray($values);
      		$report->save();
      		$db->commit();
		} 
		catch( Exception $e ) {
			$db->rollBack();
      		throw $e; // This should be caught by error handler
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('The report will be sent to admin')), 'layout' => 'default-simple','smoothboxClose' => true, 'parentRefresh' => false, ));
		
		
	}

}
