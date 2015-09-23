<?php

class Ynfeedback_IndexController extends Core_Controller_Action_Standard 
{
    public function indexAction() 
    {
    	$this -> _helper -> content -> setNoRender () -> setEnabled ();
    }
    
    public function listingAction() {
        $this -> _helper -> content -> setNoRender() -> setEnabled();
    }
    
	public function simpleHelpfulAction()
	{
		$this -> view -> text = $text = $this ->_getParam('text');
		$isBack = $this ->_getParam('back');
		$ideaTable = Engine_Api::_() -> getItemTable('ynfeedback_idea');
		$select = $ideaTable -> select();
		$whereStr = "title LIKE ? or description LIKE ?";
		
		$select -> where($whereStr, "%".$text."%")
				-> where('deleted = ?', '0')
				-> order('vote_count DESC')
				;
		$this -> view -> ideas = $ideas = $ideaTable -> fetchAll($select);
		if(!count($ideas))
		{
			if(isset($isBack))
			{
				$this -> view -> isBack = true;
			}
			else
			{
				$this -> view -> isSkip = true;
			}
		}
	}
	
	public function detailPopupAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$view = new Zend_View();
        $idea = Engine_Api::_()->getItem('ynfeedback_idea', $this ->_getParam('id'));
		if(empty($idea))
		{
			echo Zend_Json::encode(array('error' => 1));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 0, 'popup_helpful_view_detail' => $view -> partial('_popup_helpful_detail.tpl', 'ynfeedback', array('idea' => $idea))));
		}
	}
	
    public function manageAction() {
        $this -> _helper -> content -> setEnabled();
        // Return if guest try to access to create link.
        if (!$this -> _helper -> requireUser -> isValid())
            return;
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        
        //Setup params
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $originalOptions = $params;
        if (!isset($params['page']) || $params['page'] == '0') {
            $page = 1;
        }
        else {
            $page = (int)$params['page'];
        }
        
        $params['user_id'] = $viewer -> getIdentity();
        
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynfeedback_idea');
        $this -> view -> paginator = $paginator = $table -> getIdeasPaginator($params);
        
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page );
        
        $this->view->total = $paginator->getTotalItemCount();
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
    }
    
	public function helpfulAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$view = new Zend_View();
        $category = Engine_Api::_()->getItem('ynfeedback_category', $this ->_getParam('id'));
		if(!$category -> checkHasIdea())
		{
			echo Zend_Json::encode(array('error' => 1));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 0, 'popup_helpful_view' => $view -> partial('_popup_helpful.tpl', 'ynfeedback', array('category' => $category))));
		}
	}
	
	public function createPopupAction(){
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
        // Check authorization to create feedback.
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynfeedback_idea', null, 'create') -> isValid())
            return;
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		//get values
		$values = $this ->_getAllParams();
		
		if(empty($values['title']) || $values['title'] == "")
		{
			return;
		}
		
		//user_id & status
		$values['user_id'] = $viewer -> getIdentity();
		$values['status_id'] = 1;
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			
			$class = new Engine_Filter_HtmlSpecialChars;
			$values['description'] = $class -> filter($values['description']);
			$class = new Engine_Filter_Censor;
			$values['description'] = $class -> filter($values['description']);
			$class = new Engine_Filter_EnableLinks;
			$values['description'] = $class -> filter($values['description']);
			
			//save feedback
			$ideaTable = Engine_Api::_() -> getItemTable('ynfeedback_idea');
			$idea = $ideaTable -> createRow();
      		$idea->setFromArray($values);
			$idea -> save();
			
            // Set auth
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone',
			);
			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}
	
			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}
			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
	
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($idea, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($idea, $role, 'comment', ($i <= $commentMax));
			}
			
			if($viewer -> getIdentity())
			{	
				//add activity
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($idea -> getOwner(), $idea, 'ynfeedback_feedback_create');
				if($action) {
					$activityApi->attachActivity($action, $idea);
				}
			}
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
	        {
	        	if($viewer -> getIdentity())
				{
		        	$user = $idea -> getOwner();
					if($user -> getIdentity())
		            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynfeedback_new', $user);
            	}
			}
			
			$db -> commit();
			
			echo Zend_Json::encode(array('message' => 'Feedback successfully!'));

		} catch (Exception $e) {
			$db -> rollBack();
			echo Zend_Json::encode(array('message' => 'Feedback failure!'));
			
		}
	}
	
   public function manageFollowAction() {
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> content -> setEnabled();
		
	    //Setup params
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        $originalOptions = $params;
        if (!isset($params['page']) || $params['page'] == '0') 
        {
            $page = 1;
        }
        else 
        {
            $page = (int)$params['page'];
        }
		
		$params['follower_id'] = $viewer -> getIdentity();
		$params['follow'] = 1;
		
        //Set curent page
        $table = Engine_Api::_() -> getItemTable('ynfeedback_idea');
        $this -> view -> paginator = $paginator = $table -> getIdeasPaginator($params);
        $paginator->setCurrentPageNumber($page );
        
        unset($originalOptions['module']);
        unset($originalOptions['controller']);
        unset($originalOptions['action']);
        unset($originalOptions['rewrite']);
        $this->view->formValues = array_filter($originalOptions);
	}
	
	public function showResultAction()
    {
    	$this -> _helper -> layout -> setLayout('default-simple');
    	$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> isAdmin())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
    	// Disable layout and viewrenderer
		$tablePoll = Engine_Api::_() -> getItemTable('ynfeedback_poll');
		$poll = Engine_Api::_() -> getItem('ynfeedback_poll', $this->_getParam('id'));
		if(empty($poll))
		{
			return $this->_helper->requireSubject()->forward();
		}
	    $this->view->poll = $poll;
	    $this->view->owner = $owner = $poll->getOwner();
	    $this->view->viewer = $viewer;
	    $this->view->pollOptions = $poll->getOptions();
	    $this->view->hasVoted = false;
	    $this->view->showPieChart = true;
	    $this->view->canVote = true;
	    $this->view->canChangeVote = true;
    }
	
	public function voteAction()
    {
    	
		// Get poll
	    $poll = null;
	    if( null !== ($pollIdentity = $this->_getParam('poll_id')) ) {
	      $poll = Engine_Api::_()->getItem('ynfeedback_poll', $pollIdentity);
   		}
		
		if(empty($poll))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
	    // Check auth
	    if( !$this->_helper->requireUser()->isValid() ) {
	      return;
	    }
	
	    // Check method
	    if( !$this->getRequest()->isPost() ) {
	      return;
	    }
	
	    $option_id = $this->_getParam('option_id');
	    $canChangeVote = true;
	
	    $viewer = Engine_Api::_()->user()->getViewer();
	
	    if( !$poll ) {
	      $this->view->success = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll does not seem to exist anymore.');
	      return;
	    }
	
	    if( $poll->hasVoted($viewer) && !$canChangeVote ) {
	      $this->view->success = false;
	      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have already voted on this poll, and are not permitted to change your vote.');
	      return;
	    }
	
	    $db = Engine_Api::_()->getDbtable('polls', 'ynfeedback')->getAdapter();
	    $db->beginTransaction();
	    try {
	      $poll->vote($viewer, $option_id);
	
	      $db->commit();
	    } catch( Exception $e ) {
	      $db->rollback();
	      $this->view->success = false;
	      throw $e;
	    }
	
	    $this->view->success = true;
	    $pollOptions = array();
	    foreach( $poll->getOptions()->toArray() as $option ) {
	      $option['votesTranslated'] = $this->view->translate(array('%s vote', '%s votes', $option['votes']), $this->view->locale()->toNumber($option['votes']));
	      $pollOptions[] = $option;
	    }
	    $this->view->pollOptions = $pollOptions;
	    $this->view->votes_total = $poll->vote_count;
    }
	
	public function authorSuggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$viewer = Engine_Api::_() -> user() -> getViewer();
        $table = Engine_Api::_()->getItemTable('user');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = Engine_Api::_()->getItemTable('user')->select()->where('search = ?', 1);
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
        }
		if($viewer -> getIdentity())
			$select -> where('user_id <> ?', $viewer -> getIdentity());
		
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $friend ){
            $data[] = array(
                'id' => $friend->getIdentity(),
                'label' => $friend->getTitle(), // We should recode this to use title instead of label
                'title' => $friend->getTitle(),
                'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                'type' => 'user', 
                'url' => $friend->getHref(),
            );
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
	
	public function createAction() {
		
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$view = Zend_Registry::get('Zend_View');
		
        // Check authorization to create feedback.
        if (!$this -> _helper -> requireAuth() -> setAuthParams('ynfeedback_idea', null, 'create') -> isValid())
            return;
        
		//get first category
		$tableCategory = Engine_Api::_() -> getItemTable('ynfeedback_category');
		$categories = $tableCategory -> getCategories();
		$firstCategory = $categories[1];
		$category_id = $this -> _getParam('category_id', $firstCategory -> category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynfeedback_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynfeedback_idea');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}
			
		$this -> view -> form = $form = new Ynfeedback_Form_Feedback_Create( array('formArgs' => $formArgs));
		
		// Populate category list.
		$categories = $tableCategory -> getCategories();
		unset($categories[0]);
		foreach ($categories as $item) {
			$form -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $view->translate($item['title']));
		}

		//repopulate category
		if ($category_id) {
			$form -> category_id -> setValue($category_id);
		} else {
			$form -> addError('Create feedback require at least one category. Please contact admin for more details.');
		}

		//populate data
		$posts = $this -> getRequest() -> getPost();
		if(!isset($posts['submit_button']))
		{
			$this -> view -> posts = $posts;
			return;
		}
		
		// Check method and data validity.
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($posts)) {
			$this -> view -> posts = $posts;
			return;
		}
		
		//get values
		$params = $this ->_getAllParams();
		$values = $form -> getValues();
		
		//check email
		if(!empty($values['guest_email']))
		{
			$regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
			if (!preg_match($regexp, $values['guest_email'])) {
				$form -> addError('Please enter valid email!');
				return;
			}
		}
		//user_id & status
		$values['user_id'] = $viewer -> getIdentity();
		$values['status_id'] = 1;
		
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			
			//save feedback
			$ideaTable = Engine_Api::_() -> getItemTable('ynfeedback_idea');
			$idea = $ideaTable -> createRow();
      		$idea->setFromArray($values);
			$idea -> save();
			
			
			//Set Co-authors
			$tableAuthor = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
			$toValues = $this ->_getParam('toValues');
			if(!empty($toValues))
			{
				$authors = explode(",", $toValues);
				foreach($authors as $authorID)
				{
					if(is_numeric($authorID))
					{
						$user = Engine_Api::_() -> getItem('user', $authorID);
						if($user -> getIdentity())
						{
							$authorRow = $tableAuthor -> createRow();
							$authorRow -> idea_id = $idea -> getIdentity();
							$authorRow -> user_id = $authorID;
							$authorRow -> save();
						}
						else
						{
							$authorRow = $tableAuthor -> createRow();
							$authorRow -> idea_id = $idea -> getIdentity();
							$authorRow -> name = $authorID;
							$authorRow -> save();
						}
				    }
					else
					{
						$authorRow = $tableAuthor -> createRow();
						$authorRow -> idea_id = $idea -> getIdentity();
						$authorRow -> name = $authorID;
						$authorRow -> save();
					}
					
				}
			}
			
			//save custom field			
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($idea);
			$customfieldform -> saveValues();
			
            // Set auth
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone',
			);
			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}
	
			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}
			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
	
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($idea, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($idea, $role, 'comment', ($i <= $commentMax));
			}
			if($viewer -> getIdentity() > 0)
			{
				//add activity
				$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
				$action = $activityApi->addActivity($idea -> getOwner(), $idea, 'ynfeedback_feedback_create');
				if($action) {
					$activityApi->attachActivity($action, $idea);
				}
			}
			
			if (Engine_Api::_() -> hasModuleBootstrap("yncredit"))
	        {
	        	if($viewer -> getIdentity())
				{
		        	$user = $idea -> getOwner();
					if($user -> getIdentity())
		            	Engine_Api::_()->yncredit()-> hookCustomEarnCredits($user, $user -> getTitle(), 'ynfeedback_new', $user);
            	}
			}
			
			$db -> commit();

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		if (!$idea->isEditable()) {
			//return to detail page if not have edit auth
            return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'action' => 'view', 'idea_id' => $idea -> getIdentity()), 'ynfeedback_specific', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
        }
		
		if ($viewer -> getIdentity())
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'action' => 'manage-screenshots',
					'idea_id' => $idea -> getIdentity(),
				), 'ynfeedback_specific', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		else 
		{
			//redirect if viewer is guest
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				), 'ynfeedback_general', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
	}
	
	public function voteFeedbackAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$feedbackId = $this ->_getParam('feedback_id', 0);
		if (!$feedbackId)
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$this -> view -> feedback = $feedback = Engine_Api::_()->getItem('ynfeedback_idea', $feedbackId);
		$this -> view -> widget_id = $this ->_getParam('widget_id', 0);
		if (is_null($feedback))
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$feedback -> votes() -> add($viewer);
	}
	
	public function unvoteFeedbackAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$feedbackId = $this ->_getParam('feedback_id', 0);
		if (!$feedbackId)
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$this -> view -> feedback = $feedback = Engine_Api::_()->getItem('ynfeedback_idea', $feedbackId);
		$this -> view -> widget_id = $this ->_getParam('widget_id', 0);
		if (is_null($feedback))
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer -> getIdentity() == 0)
		{
			return $this -> _helper -> viewRenderer -> setNoRender(true);
		}
		$feedback -> votes() -> remove($viewer);
	}
	
	public function suggestFeedbackAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();

		//Search Params
		$keyword = $this -> _getParam('keyword1', '');
		$page = $this -> _getParam('page', 1);
		$params = array(
			'keyword' => $keyword,
			'page' => $page
		);
		// Get Ideas Paginator
		$ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
		$paginator = $ideaTbl -> getIdeasPaginator($params);
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeedback_max_idea', 20);
		$paginator->setItemCountPerPage($items_per_page);
		if(isset($params['page'])){
			$paginator->setCurrentPageNumber($params['page']);
		}
		$this->view->paginator = $paginator;
	    $this->view->formValues = array_filter($params);
	}
}
