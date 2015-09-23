<?php

class Ynevent_SponsorController extends Core_Controller_Action_Standard
{
	public function editAction()
  	{
  		if( !$this->_helper->requireUser()->isValid() ) return;
		$viewer = Engine_Api::_()->user()->getViewer();
    	$sponsor = Engine_Api::_()->getItem('event_sponsor', $this->_getParam('id'));
    	$widgetId = $this->_getParam('tab', null);
    	$eventId = $sponsor->event_id;
		$this->view->form = $form = new Ynevent_Form_Sponsor_Edit();
	    $form->populate($sponsor->toArray());
	    
		// Check post/form
	    if( !$this->getRequest()->isPost() ) { return; }
	    if( !$form->isValid($this->getRequest()->getPost()) ) { return; }

	    // Process
	    $db = Engine_Api::_()->getItemTable('event_sponsor')->getAdapter();
	    $db->beginTransaction();

	    try {
	    	$values = $form->getValues(); 
	      	$sponsor->setFromArray($values)->save();
	    	// Add photo
			if (!empty($values['photo'])) {
				$sponsor->setPhoto($form->photo);
			}
	      	$db->commit();
	    }
	    catch( Exception $e ) {
	      $db->rollBack();
	      throw $e;
	    }

	    return $this->_forward('success', 'utility', 'core', array(
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
	      'layout' => 'default-simple',
	      'parentRedirect' => $this->view->url(array('id' => $eventId, 'tab' => $widgetId), 'event_profile') . ( ($tabId && is_numeric($tabId)) ? "?tab={$tabId}" : "" ),
	      'closeSmoothbox' => true,
	    ));
	    
  }

  	public function deleteAction()
  	{
	  	// Check permissions
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $this->view->sponsor = $sponsor = Engine_Api::_()->getItem('event_sponsor', $this->_getParam('id'));
	    $widgetId = $this->_getParam('tab', null);
		$eventId = $sponsor->event_id;
	    if( !$this->_helper->requireUser()->isValid() ) return;
	    if( !$this->getRequest()->isPost() ) { return; }
	    
	    $db = Engine_Api::_()->getItemTable('event_sponsor')->getAdapter();
		$db->beginTransaction();
	
	    try {
	      $sponsor->delete();
	      $db->commit();
	    } catch( Exception $e ) {
	      $db->rollBack();
	      throw $e;
	    }
		return $this->_forward('success', 'utility', 'core', array(
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully')),
		      'layout' => 'default-simple',
		      //'parentRefresh' => true,
		      'parentRedirect' => $this->view->url(array('id' => $eventId, 'tab' => $widgetId), 'event_profile') . ( ($tabId && is_numeric($tabId)) ? "?tab={$tabId}" : "" ),
		      'closeSmoothbox' => true,
		));
	}
	
	public function createAction()
  	{
		$this->view->form = $form = new Ynevent_Form_Sponsor_Create();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		// Not post/invalid
		if ( !$request->isPost( )) { return; }
		if ( !$form->isValid($request->getPost()) ) { return; }
		
		// Process
		$eventId = $this->_getParam('event_id');
		$tabId = $this->_getParam('tab');
		if ( !( $eventId && is_numeric($eventId) ) ) { return; }
		
		$this->view->event = $event = Engine_Api::_()->getItem('event', $this->_getParam('event_id'));
		$values = $form->getValues();
		$values['event_id'] = $event->getIdentity();
		$db = Engine_Api::_()->getDbtable('sponsors', 'ynevent')->getAdapter();
		$db->beginTransaction();
		try {
			$table = Engine_Api::_()->getDbtable('sponsors', 'ynevent');
			$sponsor = $table->createRow();
			$sponsor->setFromArray($values);
			$sponsor->save();
			// Add photo
			if (!empty($values['photo'])) {
				$sponsor->setPhoto($form->photo);
			}
			// Commit
			$db->commit();
			return $this->_forward('success', 'utility', 'core', array(
		      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added Successfully')),
		      'layout' => 'default-simple',
		      //'parentRefresh' => true,
		      'parentRedirect' => $this->view->url(array('id' => $eventId), 'event_profile') . ( ($tabId && is_numeric($tabId)) ? "?tab={$tabId}" : "" ),
		      'closeSmoothbox' => true,
			));
			
		} catch (Engine_Image_Exception $e) {
			$db->rollBack();
			$form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}
}