<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynevent
 * @author     LuanND
 */
class Ynevent_AnnouncementController extends Core_Controller_Action_Standard {

	public function init() 
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> tab = $this->_getParam('tab', null);
		if (0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('event', $event_id)))
		{
			if (!Engine_Api::_() -> core() -> hasSubject($event -> getType()))
			{
				Engine_Api::_() -> core() -> setSubject($event);
			}	
			
			if (!$this -> _helper -> requireAuth -> setAuthParams($event, null, 'view') -> isValid())
			{
				return $this -> _helper -> requireSubject -> forward();
			}
			
			$viewer = Engine_Api::_()->user()->getViewer();
			if(!$event -> isOwner($viewer))
			{
				return $this -> _helper -> requireSubject -> forward();
			}		
		}
		else {
			return $this -> _helper -> requireSubject -> forward();
		}
	}
	public function manageAction()
	{
		if (!$this -> _helper -> requireSubject('event') -> isValid())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
		 
		// get announcement
		$page = $this->_getParam('page', 1);
		$table = Engine_Api::_() -> getItemTable('ynevent_announcement');
		$select = $table -> select() -> where("event_id = ?", $event -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) ->order("highlight DESC");
		
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
	}
	public function createAction()
  	{
  		if (!$this -> _helper -> requireSubject('event') -> isValid())
		{
			return $this -> _helper -> requireSubject -> forward();
		}
    	$this->view->form = $form = new Ynevent_Form_Announcement_Create();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();		
		$viewer = Engine_Api::_()->user()->getViewer();
		
	    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) 
	    {
	      $params = $form->getValues();
	      $params['user_id'] = Engine_Api::_()->user()->getViewer()->getIdentity();
		  $params['event_id'] = $event -> getIdentity();
		  
	      $announcement = Engine_Api::_()->getDbtable('announcements', 'ynevent')->createRow();
	      $announcement->setFromArray($params);
	      $announcement->save();
		  
		  //un-highlight another announcement
		  $announcement->setUnHighlight();
		  
	      return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $event -> getIdentity()),'event_extended', true);
	    }
  	}
	
	public function editAction() 
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireSubject('event') -> isValid()) 
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this->view->event = $event = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$id = $this->_getParam('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynevent_announcement', $id);
		if(!$announcement->isOwner($viewer))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this->view->form = $form = new Ynevent_Form_Announcement_Edit();
		$form -> populate($announcement -> toArray());

		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		// Process
		$table = Engine_Api::_() -> getItemTable('ynevent_announcement');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try 
		{
			$values = $form -> getValues();
			$announcement -> body = $values['body'];
			$announcement -> title = $values['title'];
			$announcement -> highlight = $values['highlight'];
			$announcement -> user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			$announcement -> save();
			
			//un-highlight another announcement
		  	$announcement->setUnHighlight();
			
			$db -> commit();
			if($this->_getParam('back', null))
			{
				return $this->_helper->redirector->gotoRoute(array('id' => $event -> getIdentity()), 'event_profile', true);
			}
			else
				return $this->_helper->redirector->gotoRoute(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $event -> getIdentity()), 'event_extended', true);
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}
	
	public function deleteAction() 
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		if (!$this -> _helper -> requireSubject('event') -> isValid()) 
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$event = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$id = $this->_getParam('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynevent_announcement', $id);
		if(!$announcement->isOwner($viewer))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> form = $form = new Ynevent_Form_Announcement_Delete();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynevent_announcement');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try 
		{
			$announcement -> delete();
			$db -> commit();
			$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array('Delete announcement successfully.')
				));
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function highlightAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid()) 
		{
			return;
		}
		if (!$this -> _helper -> requireSubject('event') -> isValid()) 
		{
			return;
		}
		$event = Engine_Api::_() -> core() -> getSubject();
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$id = $this -> _getParam('announcement_id', null);
    	$announcement = Engine_Api::_()->getItem('ynevent_announcement', $id);
		
		$db = Engine_Api::_() -> getDbTable('announcements', 'ynevent') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$announcement -> setProfile();
			
			//un-highlight another announcement
		    $announcement->setUnHighlight();
			
			$db -> commit();
			$this -> view -> success = true;
			$this -> view -> enabled = $announcement -> highlight;
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
		}
		
		$this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format'=> 'smoothbox',
					'messages' => array($this->view->translate('Success.'))
			));
	}
}
