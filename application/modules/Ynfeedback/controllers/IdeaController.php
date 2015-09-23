<?php

class Ynfeedback_IdeaController extends Core_Controller_Action_Standard {
	
    public function init() {
		
		if (0 !== ($idea_id = (int)$this -> _getParam('idea_id')) && null !== ($idea = Engine_Api::_() -> getItem('ynfeedback_idea', $idea_id)))
		{
			Engine_Api::_() -> core() -> setSubject($idea);
		}
		$this -> _helper -> requireSubject('ynfeedback_idea');
	}
		
    public function indexAction() {
        $this->view->someVar = 'someVal';
    }
    
	public function profileFollowAction() 
    {
    	$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
        $idea = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbTable('follows', 'ynfeedback');
        $row = $followTable->getFollowIdea($idea->getIdentity(), $viewer->getIdentity());
		$option_id = $this->getRequest()->getParam('option_id', 1);
        if ($option_id) 
        {
           if(!$row)
		   {
		   		$row = $followTable->createRow();
			    $row->idea_id = $idea->getIdentity();
			    $row->user_id = $viewer->getIdentity();
				$row->creation_date = date('Y-m-d H:i:s');
				$row -> save();
				
				$idea -> follow_count = $idea -> follow_count + 1;
				$idea -> save();
		   }
        } 
		else if($row)
		{
			$row -> delete();
			$idea -> follow_count = $idea -> follow_count - 1;
			$idea -> save();
		}
    }
	
	public function unFollowAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_()->core()->getSubject();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($idea, $viewer, 'view') -> isValid())
		{
			return;
		}
		$db = Engine_Api::_() -> getDbtable('ideas', 'ynfeedback') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$viewer = Engine_Api::_()->user()->getViewer();
        	$followTable = Engine_Api::_()->getDbTable('follows', 'ynfeedback');
        	$row = $followTable->getFollowIdea($idea->getIdentity(), $viewer->getIdentity());
			if ($row)
			{
				$row -> delete();
				$idea -> follow_count = $idea -> follow_count - 1;
				$idea -> save();
				$db -> commit();
			}
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Unfollow successfully.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			throw $e;
		}
	}
	
	public function viewAction()
	{
		$this -> _helper -> content -> setEnabled();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> idea = $idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this ->_getParam('idea_id'));
		if(empty($idea))
		{
			return $this->_helper->requireSubject()->forward();
		}
		if($idea -> deleted)
		{
			return $this->_helper->requireSubject()->forward();
		}
		if(!$idea -> isViewable())
		{
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		//view count
		if(!$viewer -> isSelf($idea -> getOwner()))
		{
			$idea -> view_count += 1;
			$idea -> save();
		}
		
		//follow
		$followTable = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
		$row = $followTable -> getFollowIdea($idea -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> follow = $row ? 1 : 0;
        
        $this->view->screenshots = $idea->getScreenshots();
        $this->view->files = $idea->getFiles();
		
	}
	
	public function deleteAction()
	{
		$this -> _helper -> requireUser();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this -> getRequest() -> getParam('idea_id'));
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynfeedback_Form_Feedback_Delete();
		if (!$idea)
		{
			return $this->_helper->requireSubject()->forward();
		}
        
        if (!$idea->isDeletable()) {
            return $this->_helper->requireAuth()->forward();
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
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'ynfeedback_general', true),
			'messages' => Array($message)
		));
	}
	
	public function editAction() {
		
		// Return if guest try to access to create link.
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$view = Zend_Registry::get('Zend_View');
		
		$idea = Engine_Api::_() -> getItem('ynfeedback_idea', $this ->_getParam('idea_id'));
		if(empty($idea))
		{
			return $this->_helper->requireSubject()->forward();
		}
		
        if (!$idea->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        
		//get category
		$tableCategory = Engine_Api::_() -> getItemTable('ynfeedback_category');
		$categories = $tableCategory -> getCategories();
		$category_id = $this -> _getParam('category_id', $idea -> category_id);

		// Create Form
		//get current category
		$category = Engine_Api::_() -> getItem('ynfeedback_category', $category_id);
		
		//get profile question
		$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynfeedback_idea');
		if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
			$profileTypeField = $topStructure[0] -> getChild();
			$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $category -> option_id);
		}
			
		$this -> view -> form = $form = new Ynfeedback_Form_Feedback_Edit( array('formArgs' => $formArgs, 'item' => $idea));
		
		//populate all data
		
		$idea -> description = htmlspecialchars_decode($idea -> description);
		$idea -> description = strip_tags($idea -> description);
		$form -> populate($idea -> toArray());
		
		// Populate auth
		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'everyone',
		);
		foreach ($roles as $role) 
		{
			if (isset($form -> auth_view -> options[$role]) && $auth -> isAllowed($idea, $role, 'view')) {
				$form -> auth_view -> setValue($role);
			}
			if (isset($form -> auth_comment -> options[$role]) && $auth -> isAllowed($idea, $role, 'comment')) {
				$form -> auth_comment -> setValue($role);
			}
		}

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
		$form -> populate($posts);
		
		
		//populate co-authors
		if(!$posts)
		{
			$authorTable = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
			$this -> view -> authors = $authors = $authorTable -> getAuthorsByIdeaId($idea -> getIdentity());
		}
		
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
      		$idea->setFromArray($values);
			$idea -> save();
			
			//Set Co-authors
			$tableAuthor = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
			$tableAuthor -> deleteAllAuthorsByIdeaId($idea -> getIdentity());
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
			
			$db -> commit();

		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		
		//send to follower
		Engine_Api::_() -> ynfeedback() -> sendNotificationToFollower($idea, 'ynfeedback_idea_edit', $idea, $idea);
		
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'action' => 'view',
				'idea_id' => $idea -> getIdentity(),
			), 'ynfeedback_specific', true),
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}

    public function manageScreenshotsAction() {
        $this -> _helper -> content -> setEnabled();
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$idea->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $this->view->screenshots = $screenshots = Engine_Api::_()->getItemTable('ynfeedback_screenshot')->getScreenshotsOfIdea($idea->getIdentity());
        $maxSize = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_screenshotsize');
        if ($maxSize == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_screenshotsize'));
            if ($row) {
                $maxSize = $row->value;
            }
        }
        $this->view->maxSize = $maxSize;
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_screenshot');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_screenshot'));
            if ($row) {
                $max = $row->value;
            }
        }
        $this->view->max = $max;
    }

    public function addScreenshotAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        if (!$idea->isEditable()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to do this.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        
        if (!$this -> getRequest() -> isPost()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }

        if (empty($_FILES['files'])) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('No file');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
        }
        $name = $_FILES['files']['name'][0];
        $type = explode('/', $_FILES['files']['type'][0]);
        if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload File');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_screenshot');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_screenshot'));
            if ($row) {
                $max = $row->value;
            }
        }
        $screenshots = Engine_Api::_()->getItemTable('ynfeedback_screenshot')->getScreenshotsOfIdea($idea->getIdentity());
        if (count($screenshots) >= $max) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Numbers of screenshots is reach limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }   
             
        $maxSize = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_screenshotsize');
        if ($maxSize == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_screenshotsize'));
            if ($row) {
                $maxSize = $row->value;
            }
        }
        if($_FILES['files']['size'][0] > $maxSize*1024) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Exceeded filesize limit.');
            //TODO remove storage file
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        $temp_file = array(
            'type' => $_FILES['files']['type'][0],
            'tmp_name' => $_FILES['files']['tmp_name'][0],
            'name' => $_FILES['files']['name'][0]
        );
        $photo_id = Engine_Api::_() -> ynfeedback() -> setPhoto($temp_file, array(
            'parent_type' => 'ynfeedback_idea',
            'parent_id' => $idea->getIdentity(),
        ));
        
        $screenshots = Engine_Api::_()->getItemTable('ynfeedback_screenshot')->getScreenshotsOfIdea($idea->getIdentity());
        if (count($screenshots) >= $max) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Numbers of screenshots is reach limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        $table = Engine_Api::_()->getItemTable('ynfeedback_screenshot');
        $photo = $table->createRow();
        $photo->idea_id = $idea->getIdentity();
        $photo->photo_id = $photo_id;
        $photo->title = $_FILES['files']['name'][0];
        $photo->save();

        $status = true;
        $name = $_FILES['files']['name'][0];
        $photo_url = Engine_Api::_()->ynfeedback()->getPhotoLink($photo_id, 'thumb.normal');
		
		//send to follower
		Engine_Api::_() -> ynfeedback() -> sendNotificationToFollower($idea, 'ynfeedback_idea_new_screenshot', $idea, $idea);
		
        return $this -> getResponse() -> setBody(Zend_Json::encode(array(
        	'files' => array(
        		0 => array(
        			'status' => $status, 
        			'name'=> $name, 
        			'screenshot_id' => $photo->getIdentity(), 
        			'photo_url' => $photo_url
        		)
        	),
        	'max' => $max,
        	'current' => count($screenshots) + 1
        )));
    }

    public function removeScreenshotsAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            echo json_encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('Invalid request.')));
            return;
        }
        if (!$idea->isEditable()) {
            echo json_encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to do this.')));
            return;
        }
        
        $photo_ids = $this->_getParam('photo_ids', '');
        $photo_arr = explode(',', $photo_ids);
        if (!empty($photo_arr)) {
            $table = Engine_Api::_()->getDbTable('screenshots', 'ynfeedback');
            $table->delete(array('idea_id = ?' => $idea->getIdentity(), 'screenshot_id IN (?)' => $photo_arr));
        }
        
        //TODO
        //remove storage file
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $screenshots = Engine_Api::_()->getItemTable('ynfeedback_screenshot')->getScreenshotsOfIdea($idea->getIdentity());
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_screenshot');
    	if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_screenshot'));
            if ($row) {
                $max = $row->value;
            }
        }
        echo json_encode(array(
        	'status' => true,
        	'max' => $max,
        	'current' => count($screenshots),
        ));
        return;
    }

    public function manageFilesAction() {
        $this -> _helper -> content -> setEnabled();
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            return $this->_helper->requireSubject()->forward();
        }
        if (!$idea->isEditable()) {
            return $this->_helper->requireAuth()->forward();
        }
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $this->view->files = $files = Engine_Api::_()->getItemTable('ynfeedback_file')->getFilesOfIdea($idea->getIdentity());
        $maxSize = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_filesize');
        if ($maxSize == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_filesize'));
            if ($row) {
                $maxSize = $row->value;
            }
        }
        $this->view->maxSize = $maxSize;
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_file');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_file'));
            if ($row) {
                $max = $row->value;
            }
        }
        $this->view->max = $max;
        $extString = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'file_ext');
        $extArr = array();
        if ($extString)
            $extArr = array_map('trim', explode(',', $extString));
        $this->view->extArr = $extArr;
    }

    public function addFileAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        if (!$idea->isEditable()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to do this.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }
        
        if (!$this -> getRequest() -> isPost()) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
        }

        if (empty($_FILES['files'])) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('No file');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
        }
        $name = $_FILES['files']['name'][0];
        $type = explode('/', $_FILES['files']['type'][0]);
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $extString = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'file_ext');
        $extArr = array();
        if ($extString)
            $extArr = array_map('trim', explode(',', $extString));
        if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || (!empty($extArr) && !in_array($type[1], $extArr))) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload File');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_file');
        if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_file'));
            if ($row) {
                $max = $row->value;
            }
        }
        $files = Engine_Api::_()->getItemTable('ynfeedback_file')->getFilesOfIdea($idea->getIdentity());
        if (count($files) >= $max) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Numbers of files is reach limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }   
             
        $maxSize = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_filesize');
        if ($maxSize == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_filesize'));
            if ($row) {
                $maxSize = $row->value;
            }
        }
        if($_FILES['files']['size'][0] > $maxSize*1024) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Exceeded filesize limit.');
            //TODO remove storage file
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        $temp_file = array(
            'type' => $_FILES['files']['type'][0],
            'tmp_name' => $_FILES['files']['tmp_name'][0],
            'name' => $_FILES['files']['name'][0]
        );
        $file_id = Engine_Api::_() -> ynfeedback() -> uploadFile($temp_file, array(
            'parent_type' => 'ynfeedback_idea',
            'parent_id' => $idea->getIdentity(),
        ));
        
        $files = Engine_Api::_()->getItemTable('ynfeedback_file')->getFilesOfIdea($idea->getIdentity());
        if (count($files) >= $max) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Numbers of files is reach limit.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
        }
        
        $table = Engine_Api::_()->getItemTable('ynfeedback_file');
        $file = $table->createRow();
        $file->idea_id = $idea->getIdentity();
        $file->storagefile_id = $file_id;
        $file->title = $_FILES['files']['name'][0];
        $file->save();

        $status = true;
        $name = $_FILES['files']['name'][0];
		
		//send to follower
		Engine_Api::_() -> ynfeedback() -> sendNotificationToFollower($idea, 'ynfeedback_idea_new_file', $idea, $idea);
		
        return $this -> getResponse() -> setBody(Zend_Json::encode(array(
        	'files' => array(
        		0 => array(
        			'status' => $status, 
        			'name'=> $name, 
        			'file_id' => $file->getIdentity()
        		)
        	),
        	'max' => $max,
        	'current' => count($files) + 1
        )));
    }

    public function removeFilesAction() {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $this->view->idea = $idea = Engine_Api::_() -> core() -> getSubject();
        if (!$idea) {
            echo json_encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('Invalid request.')));
            return;
        }
        if (!$idea->isEditable()) {
            echo json_encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You don\'t have permission to do this.')));
            return;
        }
        
        $file_ids = $this->_getParam('file_ids', '');
        $file_arr = explode(',', $file_ids);
        if (!empty($file_arr)) {
            $table = Engine_Api::_()->getDbTable('files', 'ynfeedback');
            $table->delete(array('idea_id = ?' => $idea->getIdentity(), 'file_id IN (?)' => $file_arr));
        }
        
        //TODO
        //remove storage file
        $viewer = Engine_Api::_()->user()->getViewer();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $files = Engine_Api::_()->getItemTable('ynfeedback_file')->getFilesOfIdea($idea->getIdentity());
        $max = $permissionsTable->getAllowed('ynfeedback_idea', $viewer->level_id, 'max_file');
    	if ($max == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $viewer->level_id)
                ->where('type = ?', 'ynfeedback_idea')
                ->where('name = ?', 'max_file'));
            if ($row) {
                $max = $row->value;
            }
        }
        echo json_encode(array(
        	'status' => true,
        	'max' => $max,
        	'current' => count($files),
        ));
        return;
    }
}
