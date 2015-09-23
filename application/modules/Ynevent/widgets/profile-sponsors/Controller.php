<?php

class Ynevent_Widget_ProfileSponsorsController extends Engine_Content_Widget_Abstract {
	
	protected $_childCount;
	
	public function indexAction() {
		// Don't render this if not authorized
		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		// Get params
		$this->view->page = $page = $this->_getParam('page', 1);
		$this->view->search = $search = $this->_getParam('search');

		// Prepare data
		$this->view->event = $event = $subject;
		$this->view->canAdd = ($event->user_id == $viewer->getIdentity()) ? true : false;

		$this->view->form = $form = new Ynevent_Form_Sponsor_Create();
		// Get paginator
	    $table = Engine_Api::_()->getItemTable('event_sponsor');
	    $select = $table->select()
	      ->where('event_id = ?', $subject->getIdentity())
	      ->order('sponsor_id DESC');
	      ;

	    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
	
	    // Set item count per page and current page number
	    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
	    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
	
	    // Do not render if nothing to show and not viewer
	    /*
	    if( $paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity() ) {
	      return $this->setNoRender();
	    }
		*/
	    
	    // Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
				
		$request = Zend_Controller_Front::getInstance()->getRequest();
		// Not post/invalid
		if ( !$request->isPost( )) { return; }

		if ( !$form->isValid($request->getPost()) ) { return; }
		
		// Process
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
		} catch (Engine_Image_Exception $e) {
			$db->rollBack();
			$form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
     }
     
	public function getChildCount()
	{
	    return $this->_childCount;
	}
}
?>