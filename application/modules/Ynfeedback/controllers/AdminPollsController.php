<?php

class Ynfeedback_AdminPollsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_main_polls');

    if ($this->getRequest()->isPost())
    {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'delete_' . $value)
        {
          $poll = Engine_Api::_()->getItem('ynfeedback_poll', $value);
		  if($poll)
          	$poll->delete();
        }
      }
    }

    $page = $this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->getItemTable('ynfeedback_poll')->getPollsPaginator(array(
      'orderby' => 'admin_id',
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);

  }
  
  
  public function setShowAction()
  {
    	$id = $this->_getParam('id');
		$value = $this->_getParam('value');
    	if ($id)
    	{
    		$poll = Engine_Api::_() -> getItem('ynfeedback_poll', $id);
    		$pollTbl = Engine_Api::_()->getItemTable('ynfeedback_poll');
    		if ($poll)
    		{
    			if(!$poll -> show)
				{
	    			$poll->show = 1;
	    			$poll->save();
	    			$pollTbl->update(array(
	    				'show' => '0'
	    			), array(
	    				'poll_id <> ? ' => $id
	    			));
	    			
	    			echo Zend_Json::encode(array(
		    			'error_code' => 0,
		    			'type' => 'show',
		    			'message' => Zend_Registry::get("Zend_Translate")->_("Success!")
		    		));
		    		exit;
				}
				else
				{
					$poll->show = 0;
	    			$poll->save();
	    			echo Zend_Json::encode(array(
		    			'error_code' => 0,
		    			'type' => 'unshow',
		    			'message' => Zend_Registry::get("Zend_Translate")->_("Success!")
		    		));
		    		exit;
				}	
    		}
    	}
    	else
    	{
    		echo Zend_Json::encode(array(
    			'error_code' => 1,
    			'error_message' => Zend_Registry::get("Zend_Translate")->_("Failure!")
    		));
    		exit;
    	}
    	
  }
  
  public function editAction()
  {
	//get poll
	$id = $this->_getParam('id');
	$poll = Engine_Api::_() -> getItem('ynfeedback_poll', $id);
	
	if(empty($poll))
	{
		return $this->_helper->requireSubject()->forward();
	}
	
    // Get form
    $this->view->form = $form = new Ynfeedback_Form_Admin_Polls_Edit();
	
	//populate 
	$form -> populate($poll -> toArray());
	
	//get options of poll
	$tableOptions = Engine_Api::_()->getDbtable('options', 'ynfeedback');
	$this -> view -> options = $options = $tableOptions -> getOptionsOfPoll($poll);
	
    // Check method/valid
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $poll->setFromArray($values);
      $poll->save();
	  
	  foreach($options as $option)
	  {
	  	 $poll_option_id = $option -> poll_option_id;
		 $nameForm = 'option_'.$poll_option_id;
		 $valueNameFrom = $this ->_getParam($nameForm);
		 if(empty($valueNameFrom))
		 {
		 	$pollVoteTable = Engine_Api::_() -> getDbTable('pollVotes', 'ynfeedback');
		 	$select = $pollVoteTable -> select() -> where('poll_option_id = ?', $option -> poll_option_id);
		 	$pollvotes = $pollVoteTable -> fetchAll($select);
			foreach ($pollvotes as $pollvote) {
				$pollvote -> delete();
			}
		 	$option -> delete();
		 }
		 else
		 {
		 	$option -> poll_option = $valueNameFrom;
			$option -> save();
		 }
	  }
	  
		
	  // Check options
	    $optionsArray = (array) $this->_getParam('optionsArray');
	    $optionsArray = array_filter(array_map('trim', $optionsArray));
	    $optionsArray = array_slice($optionsArray, 0, $max_options);
	   /*
		if( empty($options) || !is_array($options) || count($options) < 2 ) {
		 return $form->addError('You must provide at least two possible answers.');
	   }*/
	   
	    foreach( $optionsArray as $index => $option ) {
	      if( strlen($option) > 80 ) {
	        $optionsArray[$index] = Engine_String::substr($option, 0, 80);
	      }
	    }
	  	  
	  //add new options
	  $pollOptionsTable = Engine_Api::_()->getDbtable('options', 'ynfeedback');
      $censor = new Engine_Filter_Censor();
      $html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
      foreach( $optionsArray as $option ) {
        $option = $censor->filter($html->filter($option));
        $pollOptionsTable->insert(array(
          'poll_id' => $poll->getIdentity(),
          'poll_option' => $option,
        ));
      }
	  
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($poll) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
  }
  
  public function createAction()
  {
    
    $this->view->options = array();
    $this->view->form = $form = new Ynfeedback_Form_Admin_Polls_Create();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Check options
    $options = (array) $this->_getParam('optionsArray');
    $options = array_filter(array_map('trim', $options));
    $options = array_slice($options, 0, $max_options);
    $this->view->options = $options;
    if( empty($options) || !is_array($options) || count($options) < 2 ) {
      return $form->addError('You must provide at least two possible answers.');
    }
    foreach( $options as $index => $option ) {
      if( strlen($option) > 80 ) {
        $options[$index] = Engine_String::substr($option, 0, 80);
      }
    }

    // Process
    $pollTable = Engine_Api::_()->getItemTable('ynfeedback_poll');
    $pollOptionsTable = Engine_Api::_()->getDbtable('options', 'ynfeedback');
    $db = $pollTable->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();
      // Create poll
      $poll = $pollTable->createRow();
      $poll->setFromArray($values);
      $poll->save();

      // Create options
      $censor = new Engine_Filter_Censor();
      $html = new Engine_Filter_Html(array('AllowedTags'=> array('a')));
      foreach( $options as $option ) {
        $option = $censor->filter($html->filter($option));
        $pollOptionsTable->insert(array(
          'poll_id' => $poll->getIdentity(),
          'poll_option' => $option,
        ));
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
  }
  
  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->poll_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $poll = Engine_Api::_()->getItem('ynfeedback_poll', $id);
        $poll->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-polls/delete.tpl');
  }

}