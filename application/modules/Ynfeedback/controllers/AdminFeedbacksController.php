<?php
class Ynfeedback_AdminFeedbacksController extends Core_Controller_Action_Admin
{
	public function init()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_main_feedbacks');
	}
	
	public function decisionAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this -> getRequest() -> getParam('id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynfeedback_Form_Admin_Feedbacks_Decision(array('item' => $idea));
		if (!$idea)
		{
			return $this->_helper->requireSubject()->forward();
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$values = $form -> getValues();
		$idea -> decision = $values['decision'];
		$idea -> status_id = $values['status'];
		$idea -> decision_owner_id = $viewer -> getIdentity();
		$idea -> save();
		
		$message = Zend_Registry::get('Zend_Translate') -> _('Save decision successfully.');
		
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}
	
	public function emailAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this -> getRequest() -> getParam('id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynfeedback_Form_Admin_Feedbacks_Message();
		if (!$idea)
		{
			return $this->_helper->requireSubject()->forward();
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		
		$tableFollowers = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
		$follows = $tableFollowers -> getAllFollow($idea -> getIdentity());
		$values = $form -> getValues();
		//send email
		$params['website_name'] = Engine_Api::_()->getApi('settings','core')->getSetting('core.site.title','');
		$params['website_link'] =  'http://'.@$_SERVER['HTTP_HOST']; 
		$href =  				 
			'http://'. @$_SERVER['HTTP_HOST'].
			Zend_Controller_Front::getInstance()->getRouter()->assemble(array('idea_id' => $idea -> getIdentity(), 'slug' => $idea -> getSlug()),'ynfeedback_specific',true);
		$params['feedback_link'] = $href;	
		$params['feedback_name'] = $idea -> getTitle();
		$params['message'] = $values['messages'];
		//send mail to follower
		foreach($follows as $follow)
		{
			$user = Engine_Api::_() -> getItem('user', $follow -> user_id);
			if($user -> getIdentity())
			{
				try{
					Engine_Api::_()->getApi('mail','ynfeedback')->send($user -> email, 'ynfeedback_email_followers',$params);
				}
				catch(exception $e){
					//keep silent
				}
			}
		}
		$message = Zend_Registry::get('Zend_Translate') -> _('Send messages successfully.');

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
		
	}
	
	public function multiselectedAction() {
		$action = $this -> _getParam('select_action', 'Delete');
		$this -> view -> action = $action;
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true) {
			$ids_array = explode(",", $ids);
			switch ($action) {
				case 'Delete' :
					foreach ($ids_array as $id) {
						$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $id);
						if (isset($idea)) {
							$idea -> delete();
						}
					}
					break;
			}
			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}
	
	public function changestatusAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$ideaID = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $idea = Engine_Api::_()->getItem('ynfeedback_idea', $ideaID);
        if (!$idea) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the feedback.")));
            exit ;
        }
		if ($ideaID) {
			$idea -> status_id = $value;
			$idea -> decision_owner_id = $viewer -> getIdentity();
			$idea -> save();
			
			//send notification to followers foreach idea
			Engine_Api::_() -> ynfeedback() -> sendNotificationToFollower($idea, 'ynfeedback_idea_change_status', $idea, $idea);
			
			echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set status successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset status successfully!")));
			exit ;
		} else {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not set status this feedback")));
			exit ;
		}
	}
	
	public function highlightAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		$ideaID = $this -> _getParam('id');
		$value = $this -> _getParam('value');
        $idea = Engine_Api::_()->getItem('ynfeedback_idea', $ideaID);
        if (!$idea) {
            echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not find the feedback.")));
            exit ;
        }
		if ($ideaID) {
			$idea -> highlighted = $value;
			$idea -> save();
			
			echo Zend_Json::encode(array('error_code' => 0, 'error_message' => '', 'message' => ($value) ? Zend_Registry::get("Zend_Translate") -> _("Set highlight successfully!") : Zend_Registry::get("Zend_Translate") -> _("Unset highlight successfully!")));
			exit ;
		} else {
			echo Zend_Json::encode(array('error_code' => 1, 'error_message' => Zend_Registry::get("Zend_Translate") -> _("Can not set highlight this feedback")));
			exit ;
		}
	}
	
	public function deleteAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this -> getRequest() -> getParam('id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynfeedback_Form_Feedback_Delete();
		if (!$idea)
		{
			return $this->_helper->requireSubject()->forward();
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$db = $idea -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$idea -> delete();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$message = Zend_Registry::get('Zend_Translate') -> _('The selected feedback has been deleted.');

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array($message),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
		
	}
	
	public function getOwnerAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$ids = $this -> _getParam('ids', null);
		$ids_array = explode(",", $ids);
		$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
		$arr_authors = array();
		
		foreach( $ids_array as $id )
		{
			$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $id);
			if(!empty($idea))
			{
				$authors = $authorTable -> getAuthorsByIdeaId($idea -> getIdentity());
				foreach($authors as $author)
				{
					$user = Engine_Api::_() -> getItem('user', $author -> user_id);
					if($user -> getIdentity() && !in_array($author -> user_id, $arr_authors))
					{
						$arr_authors[$author -> user_id] = $user -> getTitle();
					}
				}
	        	if($idea -> user_id != 0 && !in_array($idea -> user_id, $arr_authors))
				{
					$arr_authors[$idea -> getOwner() -> getIdentity()] = $idea -> getOwner() -> getTitle();
				}
			}
        }
		if(!empty($arr_authors))
		{
			echo Zend_Json::encode(array('error' => 0, 'author' => json_encode($arr_authors)));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 1));
		}
	}
	public function getIdeaAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
        $idea = Engine_Api::_()->getItem('ynfeedback_idea', $this ->_getParam('id'));
		if(!$idea)
		{
			echo Zend_Json::encode(array('error' => 1));
		}
		else
		{
			echo Zend_Json::encode(array('error' => 0, 'title' => $idea -> getTitle(), 'description' => $idea -> getDescription()));
		}
	}
	
	public function suggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $table = Engine_Api::_()->getItemTable('ynfeedback_idea');
    
        // Get params
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        $limit = (int) $this->_getParam('limit', 10);
    
        // Generate query
        $select = $table->select();
    
        if( null !== $text ) {
            $select->where('`'.$table->info('name').'`.`title` LIKE ?', '%'. $text .'%');
			$select->where('deleted = ?', 0);
        }
        $select->limit($limit);
    
        // Retv data
        $data = array();
        foreach( $select->getTable()->fetchAll($select) as $idea ){
        	
			$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
			$authors = $authorTable -> getAuthorsByIdeaId($idea -> getIdentity());
			$arr_authors = array();
			foreach($authors as $author)
			{
				$user = Engine_Api::_() -> getItem('user', $author -> user_id);
				if($user -> getIdentity())
				{
					$arr_authors[$author -> idea_id .'_'. $author -> user_id] = $user -> getTitle();
				}
			}
        	if($idea -> user_id != 0)
			{
				$arr_authors[$idea->getIdentity() .'_'. $idea -> getOwner() -> getIdentity()] = $idea -> getOwner() -> getTitle();
	            $data[] = array(
	                'id' => $idea->getIdentity(),
	                'label' => $idea->getTitle(), // We should recode this to use title instead of label
	                'title' => $idea->getTitle(),
	                'url' => $idea->getHref(),
	                'authors' => $arr_authors,
	                'owner_name' => $idea -> getOwner() -> getTitle(),
	                'owner_url' => $idea -> getOwner() -> getHref(),
	            );
			}
			else
			{
				$data[] = array(
	                'id' => $idea->getIdentity(),
	                'label' => $idea->getTitle(), // We should recode this to use title instead of label
	                'title' => $idea->getTitle(),
	                'url' => $idea->getHref(),
	                'authors' => $arr_authors,
	                'owner_name' => $idea -> guest_name,
	            );
			}
        }
    
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }
	
	public function mergeselectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		if(!empty($ids))
		{
			$this -> view -> ids = $ids_array = explode(",", $ids);
		}
		$this->view->form = $form = new Ynfeedback_Form_Admin_Feedbacks_Merge();
		
		$posts = $this->getRequest()->getPost();
		if(!empty($posts['toValues']))
		{
			$this -> view -> ids  = $ids_array =  explode(",", $posts['toValues']);
		}
		
		//return if not click submit or save draft
		$submit_button = $this -> _getParam('submit_button');
		if (!isset($submit_button))
		{
			return;
		}
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
		
		$values = $form -> getValues();
		$toValues = $values['toValues'];
		$toValues_array = explode(",", $toValues);
		//check if merge at least 2 ideas
		if(count($toValues_array) < 2)
		{
			$form -> addError('Merge requires at least 2 feedbacks');
			return;
		}
		$mergeIdeaId = $values['listFeedback'];
		$mergeIdea = Engine_Api::_() -> getItem('ynfeedback_idea', $mergeIdeaId);
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try {
			//update mergeIdea
			$now =  date("Y-m-d H:i:s");
			$mergeIdea -> description = $values['description'];
			$mergeIdea -> title = $values['title'];
			$mergeIdea -> modified_date = $now;
			$mergeIdea -> user_id = $values['listOwner'];
			$mergeIdea -> save();
			
			//action for other ideas
			if(($key = array_search($mergeIdeaId, $toValues_array)) !== false) {
			    unset($toValues_array[$key]);
			}
			foreach($toValues_array as $ideaID)
			{
				$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $ideaID);
				if(!empty($idea))
				{
					//set new co-authors
					$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
					if(!empty($idea))
					{
						$authors = $authorTable -> getAuthorsByIdeaId($idea -> getIdentity());
						foreach($authors as $author)
						{
							$user = Engine_Api::_() -> getItem('user', $author -> user_id);
							if($user -> getIdentity() && !in_array($author -> user_id, $arr_authors))
							{
								//add new to co-authors
								$isAuthor = $authorTable -> isAuthor($mergeIdeaId, $user -> getIdentity());
								if(!$isAuthor)
								{
									$newAuthor = $authorTable -> createRow();
									$newAuthor -> idea_id = $mergeIdeaId;
									$newAuthor -> user_id = $user -> getIdentity();
									$newAuthor -> save();
								}
							}
						}
			        	if($idea -> user_id != 0 && !in_array($idea -> user_id, $arr_authors))
						{
							//add new to co-authors
							$isAuthor = $authorTable -> isAuthor($mergeIdeaId, $idea -> user_id);
							if(!$isAuthor)
							{
								$newAuthor = $authorTable -> createRow();
								$newAuthor -> idea_id = $mergeIdeaId;
								$newAuthor -> user_id = $idea -> user_id;
								$newAuthor -> save();
							}
						}
					}
					
					//if select move_material
					if($values['move_material'])
					{
						//move screenshots
						$screenShots = $idea -> getScreenshots();
						foreach($screenShots as $screenShot)
						{
							$screenShot -> idea_id = $mergeIdeaId;
							$screenShot -> save();
						}
						
						//move files
						$files = $idea -> getFiles();
						foreach($files as $file)
						{
							$file -> idea_id = $mergeIdeaId;
							$file -> save();
						}
					}
					
					//if select move_activity
					if($values['move_activity'])
					{
						//move follow
						$tableFollow = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
						$follows = $tableFollow -> getAllFollow($ideaID);
						foreach($follows as $follow)
						{
							$follow -> idea_id = $mergeIdeaId;
							$follow -> save();
						}
						
						//move likes
						$likes = $idea -> likes() -> getAllLikes();
						$countLike = 0;
						foreach($likes as $like)
						{
							$user = Engine_Api::_() -> getItem('user', $like -> poster_id);
							if($user -> getIdentity())
							{
								//check if users already like feedback
								$isLike = $mergeIdea -> likes() -> isLike($user);
								if(!$isLike)
								{
									$like -> resource_id = $mergeIdeaId;
									$like -> save();
									$countLike++;
								}
								else
								{
									$like -> delete();
								}
							}
						}
						$mergeIdea -> like_count += $countLike;
						
						//move comments
						$comments = $idea -> comments() -> getAllComments();
						$countComment =  count($comments);
						foreach($comments as $comment)
						{
							$comment -> resource_id = $mergeIdeaId;
							$comment -> save();
						}
						$mergeIdea -> comment_count += $countComment;
						
						
						//move votes
						$votes = $idea -> votes() -> getAllVotes($ideaID);
						$voteCount = 0;
						foreach($votes as $vote)
						{
							$user = Engine_Api::_() -> getItem('user', $vote -> user_id);
							if($user -> getIdentity())
							{
								//check if users already like feedback
								$isVote = $mergeIdea -> votes() -> isVoted($user);
								if(!$isVote)
								{
									$vote -> idea_id = $mergeIdeaId;
									$vote -> save();
									$voteCount++;
								}
								else
								{
									$vote -> delete();
								}
							}
						}
						$mergeIdea -> vote_count += $voteCount;
						$mergeIdea -> save();
					}
				}
			}
			
			//if select send_notification
			if($values['send_notification'])
			{
				//send notification
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				//get owner & authors
				$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
				
				//for delete item
				foreach( $toValues_array as $ideaID )
				{
					$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $ideaID);
					if(!empty($idea))
					{
						$authors = $authorTable -> getAuthorsByIdeaId($idea -> getIdentity());
						foreach($authors as $author)
						{
							$user = Engine_Api::_() -> getItem('user', $author -> user_id);
							if($user -> getIdentity() && !in_array($author -> user_id, $arr_authors))
							{
								//send to co-authors
								$notifyApi -> addNotification($user, $idea, $mergeIdea, 'ynfeedback_idea_merge');
							}
						}
			        	if($idea -> user_id != 0 && !in_array($idea -> user_id, $arr_authors))
						{
							//send to owner
							$notifyApi -> addNotification($idea -> getOwner(), $idea, $mergeIdea, 'ynfeedback_idea_merge');
						}
						//send notification to followers foreach idea
						Engine_Api::_() -> ynfeedback() -> sendNotificationToFollower($idea, 'ynfeedback_idea_merge_follow', $idea, $mergeIdea);
					}
		        }
				//for owner of merge feedback
				$notifyApi -> addNotification($mergeIdea -> getOwner(), $mergeIdea, $mergeIdea, 'ynfeedback_idea_merge_owner');
			}
			
			//delete merging ideas 
			foreach($toValues_array as $ideaID)
			{
				$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $ideaID);
				if(!empty($idea))
				{
					$idea -> delete();
				}
			}
			$db -> commit();
			
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		$this->_helper->redirector->gotoRoute(array('module'=>'ynfeedback','controller'=>'feedbacks', 'action' => 'index'), 'admin_default', true);
	}
	
	
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynfeedback_Form_Admin_Search;
		$form -> isValid ($this -> _getAllParams());
		$params = $form -> getValues();
		$tableStatus = Engine_Api::_() -> getDbTable('status', 'ynfeedback');
		$statusLists = $tableStatus -> getStatusList();
		unset($statusLists[0]);
		$this -> view -> statusLists = $statusLists;
		if(empty($params['orderby']))
		{
			$params['orderby'] = 'idea_id';
		}
		if(empty($params['direction'])) 
		{
			$params['direction'] = 'DESC';
		}
		
		if ($params['from_date']) {
            $from_date = new Zend_Date(strtotime($params['from_date']));
			$from_date->setTimezone($sysTimezone);
			$params['from_date'] = $from_date;
        }
		
	    if ($params['to_date']) {
	    	$to_date = new Zend_Date(strtotime($params['to_date']));
			$to_date->setTimezone($sysTimezone);
			$params['to_date'] = $to_date;
	    }
		
		// Get Ideas Paginator
		$ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
		$this -> view -> paginator = $ideaTbl -> getIdeasPaginator($params);
		$items_per_page = 10;
		$this -> view -> paginator->setItemCountPerPage($items_per_page);
		$page = $this ->_getParam('page', 1);
		$this->view->paginator->setCurrentPageNumber($page);
		$params['page'] = $page;
		$this -> view -> formValues = $params;

	}
	
	public function statisticAction()
	{
		$feedbackId = $this ->_getParam('id', 0);
		if ($feedbackId == 0)
		{
			return $this->_helper->requireSubject()->forward(); 
		}
		$this -> view -> feedback = $feedback = Engine_Api::_()->getItem('ynfeedback_idea', $feedbackId);
		if (is_null($feedback))
		{
			return $this->_helper->requireSubject()->forward();
		}
		$view = $this->view;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($feedback);
	}
	
}