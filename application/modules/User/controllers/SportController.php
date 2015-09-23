<?php 
class User_SportController extends Core_Controller_Action_Standard {
  	public function indexAction(){

  	}
  	
	public function manageAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $this->_getParam('user_id', 0);
		$user = Engine_Api::_()->user()->getUser($user_id);
		if (!$user_id || !$user) {
			return $this->_helper->requireSubject()->forward();
		}
		if ($user_id != $viewer->getIdentity()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->sports = $sports = $user->getSports();
		$this->view->form = $form = new User_Form_Sport();
	}
	
	public function addAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $this->_getParam('user_id', 0);
		$user = Engine_Api::_()->user()->getUser($user_id);
		if (!$user_id || !$user) {
			return $this->_helper->requireSubject()->forward();
		}
		if ($user_id != $viewer->getIdentity()) {
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->sports = $sports = $user->getSports();
		if (count($sports) >= 2) {
			return $this->_helper->requireAuth()->forward();
		}
		$this->view->form = $form = new User_Form_Sport(array('count' => count($sports)));
		
		if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
		
		$values = $form->getValues();
        
        if (!isset($values['toValues']) || empty($values['toValues'])) {
            $form->addError('Can not find the sports.');
            return;
        } 
        $db = Engine_Api::_()->getDbtable('sportmaps', 'user')->getAdapter();
        $db->beginTransaction();
        
        $ids = explode(',', $values['toValues']);
        try {
            $table = Engine_Api::_()->getDbtable('sportmaps', 'user');
            foreach ($ids as $id) {
                $map = $table->createRow();
                $map->user_id = $user_id;
				$map->sport_id = $id;
                $map->save();
            }
			
			$db->commit();
			
			return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => true, 
                'messages' => 'Add Sport sucessful.'));
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
	}
	
	public function suggestAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        
        $text = $this->_getParam('text', $this->_getParam('search', $this->_getParam('value')));
        
        $params = array();
        if( null !== $text ) {
            $params['title'] = $text;
        }
        $params['limit'] = 10;
		
		$viewer = Engine_Api::_()->user()->getViewer();
        $sports = Engine_Api::_()->getDbTable('sportcategories', 'user')->getCategoriesLevel1($params);
    
        $data = array();
        foreach( $sports as $sport ){
            $data[] = array(
                'id' => $sport->getIdentity(),
                'label' => $sport->getTitle(), // We should recode this to use title instead of label
                'title' => $sport->getTitle(),
                'photo' => $this->view->itemPhoto($sport, 'thumb.icon'),
                'type' => 'sport'
            );
        }
    	
        // send data
        $data = Zend_Json::encode($data);
        $this->getResponse()->setBody($data);
    }

	public function savePreferredAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		$table = Engine_Api::_() -> getDbTable('sportmaps', 'user');

		$sport_ids = $this -> _getParam('ids');
		$user_id = $this -> _getParam('user_id');

		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$sportIds = explode(",", $sport_ids);
			//delete all before insert
			$table -> deleteAllRows($user_id);
			foreach ($sportIds as $sportId)
			{
				$row = $table -> getRow($user_id, $sportId);
				if (!isset($row) && empty($row))
				{
					$row = $table -> createRow();
				}
				$row -> user_id = $user_id;
				$row -> sport_id = $sportId;
				$row -> save();
			}
			Engine_Api::_()->getDbTable('sportmaps', 'ynadvsearch')->updateItem(Engine_Api::_()->getItem('user', $user_id));
			$status = 'true';
			$db -> commit();

		}
		catch (Exception $e)
		{
			$db -> rollBack();
			$status = 'false';
		}

		$data = array();
		$data[] = array('status' => $status, );

		return $this -> _helper -> json($data);
	}
}