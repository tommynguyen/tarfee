<?php
class Ynevent_ReviewController extends Core_Controller_Action_Standard
{
	public function reportAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$review = Engine_Api::_()->getItem('ynevent_review', $this->_getParam('review_id'));
		if(!$review || !$viewer->getIdentity())
		{
			return $this->_helper->requireAuth->forward();
		}
		
		$this->view->form = $form = new Ynevent_Form_Report();
		// If not post or form not valid, return
		if( !$this->getRequest()->isPost() ) {
			return;
		}
		$post = $this->getRequest()->getPost();
		if(!$form->isValid($post))
			return;
	
		// Process
		$table = Engine_Api::_()->getDbtable('reviewreports','Ynevent');
		$db = $table->getAdapter();
		$db->beginTransaction();
		$values = $form->getValues();
		try
		{
			// Create report
			$values = array_merge($form->getValues(), array(
					'user_id' => $viewer->getIdentity(),
					'review_id' => $review->review_id
			));
			
			$review->report_count++;
			$review->save();
	
			$report = $table->createRow();
			$report->setFromArray($values);
			$report->creation_date = date('Y-m-d H:i:s');
			$report->modified_date = date('Y-m-d H:i:s');
			$report->save();
	
			//Send message to admin
			$content = $values['content'];
			$type = $values['type'];
	
			// Commit
			$db->commit();
			
			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				if (Engine_Api::_()->core()->hasSubject()) 
				{
					$event = Engine_Api::_() -> core() -> getSubject();
				}
				else
				{
					$event = Engine_Api::_() -> getItem("event", $review->event_id);	
				}
				
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Sent report successfully.')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $event -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Sent report successfully.')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
		catch( Exception $e )
		{
			$db->rollBack();
			throw $e;
		}
	}
}

