<?php
class User_RecommendationController extends Core_Controller_Action_Standard {
    public function askAction() {
    	$this -> _helper -> layout -> setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$enable = Engine_Api::_()->user()->checkSectionEnable($viewer, 'recommendation');
		$canAsk = $viewer->canAskRecommendation();
		if (!$enable) {
			return $this->_helper->requireAuth()->forward();
		}
		if (!$canAsk) {
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => false, 
                'messages' => $this->view->translate('You can not add for recommendation.')));
		}
		$this->view->form = $form = new User_Form_Recommendation_Ask();
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        
        if (!isset($values['toValues']) || empty($values['toValues'])) {
            $form->addError('Can not find the user.');
            return;
        } 
        $db = Engine_Api::_()->getDbtable('recommendations', 'user')->getAdapter();
        $db->beginTransaction();
        
        $ids = explode(',', $values['toValues']);
        try {
            $table = Engine_Api::_()->getDbtable('recommendations', 'user');
            foreach ($ids as $id) {
                $recommendation = $table->createRow();
                $recommendation->giver_id = $id;
				$recommendation->receiver_id = $viewer->getIdentity();
				$recommendation->creation_date = date('Y-m-d H:i:s');
				$recommendation->modified_date = date('Y-m-d H:i:s');
                $recommendation->save();
            }
			
			$db->commit();
			
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => false, 
                'messages' => $this->view->translate('Ask for recommendations sucessful.')));
    	}
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }
	
	public function giveAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$receiver_id = $this->_getParam('receiver_id', 0);
		$receiver = Engine_Api::_()->getItem('user', $receiver_id);
		if (!$receiver_id || !$receiver_id) {
			return $this->_helper->requireSubject()->forward();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$recommendation = $receiver->getRecommendation($viewer->getIdentity());
		if ($viewer->getIdentity() == $receiver->getIdentity() || !$viewer->isFriend($receiver_id)) {
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => false, 
                'messages' => $this->view->translate('You can not write recommendation for this user.')));
		}

		if ($recommendation && $recommendation->approved) {
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => false, 
                'messages' => $this->view->translate('You have written recommendation for this user already.')));
		}
		
		$this->view->form = $form = new User_Form_Recommendation_Write(array('user' => $receiver));
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
		
		if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
		
		$db = Engine_Api::_()->getDbtable('recommendations', 'user')->getAdapter();
        $db->beginTransaction();
        
        try {
            $table = Engine_Api::_()->getDbtable('recommendations', 'user');
			if (!$recommendation) {
				$recommendation = $table->createRow();
				$recommendation->receiver_id = $receiver_id;
				$recommendation->giver_id = $viewer->getIdentity();
				$recommendation->creation_date = date('Y-m-d H:i:s');
				$recommendation->modified_date = date('Y-m-d H:i:s');
			}
			$recommendation->content = $values['content'];
			$recommendation->request = 0;
			$recommendation->given_date = date('Y-m-d H:i:s');
			$recommendation->save();			
			$db->commit();
			
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => false, 
                'messages' => $this->view->translate('Write recommendation successfully.')));
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }
	}
	
	public function pendingAction() {
    	$this -> _helper -> layout -> setLayout('default-simple');
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$enable = Engine_Api::_()->user()->checkSectionEnable($viewer, 'recommendation');
		if (!$enable) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->recommendations = $recommendations = $viewer->getPendingRecommendations();
		
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        $values = $this->getRequest()->getPost();
        
		$approved_ids = $values['approve_checkbox'];
		$deleted_ids = $values['delete_checkbox'];
        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
        if (count($approved_ids)) {
        	$table->approveRecommendations($viewer->getIdentity(), $approved_ids);
        }
		
		if (count($deleted_ids)) {
        	$table->deleteRecommendations($viewer->getIdentity(), $deleted_ids);
        }
			
		return $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true, 
            'parentRefresh' => true, 
            'messages' => $this->view->translate('Save change successfully.')));
    }
	
	public function receivedAction() {
    	$this -> _helper -> layout -> setLayout('default-simple');
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$enable = Engine_Api::_()->user()->checkSectionEnable($viewer, 'recommendation');
		if (!$enable) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->recommendations = $recommendations = $viewer->getReceivedRecommendations();
		
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        $values = $this->getRequest()->getPost();
        
		$show_ids = $values['show_checkbox'];
		$deleted_ids = $values['delete_checkbox'];
        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
        $table->showRecommendations($viewer->getIdentity(), $show_ids);
		
		if (count($deleted_ids)) {
        	$table->deleteRecommendations($viewer->getIdentity(), $deleted_ids);
        }
			
		return $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true, 
            'parentRefresh' => true, 
            'messages' => $this->view->translate('Save change successfully.')));
    }
	
	public function requestAction() {
    	$this -> _helper -> layout -> setLayout('default-simple');
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$this->view->recommendations = $recommendations = $viewer->getRequestRecommendations();
		
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        $values = $this->getRequest()->getPost();
        
		$ignore_ids = $values['ignore_checkbox'];
        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
		
		if (count($ignore_ids)) {
        	$table->ignoreRecommendations($viewer->getIdentity(), $ignore_ids);
        }
			
		return $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true, 
            'parentRefresh' => true, 
            'messages' => $this->view->translate('Save change successfully.')));
    }

	public function suggestFriendsAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) {
            $data = null;
        } else {
            $data = array();
            $table = Engine_Api::_()->getItemTable('user');
      
            $usersAllowed = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $viewer->level_id, 'auth');

            if( (bool)$this->_getParam('message') && $usersAllowed == "everyone" ) {
                $select = Engine_Api::_()->getDbtable('users', 'user')->select();
                $select->where('username <> ?',$viewer->username);
            }
            else 
            {
            	 $table = Engine_Api::_()->getDbtable('users', 'user');
			     $subtable = Engine_Api::_()->getDbtable('membership', 'user');
			     $tableName = $table->info('name');
			     $subtableName = $subtable->info('name');
			
			     $select = $table->select()
				      ->from($tableName)
				      ->joinLeft($subtableName, '`'.$subtableName.'`.`user_id` = `'.$tableName.'`.`user_id`', null)
				      ->where('`'.$subtableName.'`.`resource_id` = ?', $viewer->getIdentity());
			
			      $select->where('`'.$subtableName.'`.`active` = ?', 1);
            }
			
            if( $this->_getParam('includeSelf', false) ) {
                $data[] = array(
                    'type' => 'user',
                    'id' => $viewer->getIdentity(),
                    'guid' => $viewer->getGuid(),
                    'label' => $viewer->getTitle() . ' (you)',
                    'photo' => $this->view->itemPhoto($viewer, 'thumb.profile'),
                    'url' => $viewer->getHref(),
                );
            }

            if( 0 < ($limit = (int) $this->_getParam('limit', 10)) ) {
                $select->limit($limit);
            }

            if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))) ) {
                $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
            }
      		
            $ids = array();
            foreach( $select->getTable()->fetchAll($select) as $friend ) {
            	$recommendation = Engine_Api::_()->getDbTable('recommendations', 'user')->getRecommendation($viewer->getIdentity(), $friend->getIdentity());
				if (!$recommendation) {
	                $data[] = array(
	                    'type'  => 'user',
	                    'id'    => $friend->getIdentity(),
	                    'guid'  => $friend->getGuid(),
	                    'label' => $friend->getTitle(),
	                    'photo' => $this->view->itemPhoto($friend, 'thumb.profile'),
	                    'url'   => $friend->getHref(),
	                );
	                $ids[] = $friend->getIdentity();
	                $friend_data[$friend->getIdentity()] = $friend->getTitle();
				}
            }
        }

        if( $this->_getParam('sendNow', true) ) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }
}
