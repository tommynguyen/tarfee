<?php
class Ynresponsiveevent_AdminManageEventsController extends Core_Controller_Action_Admin 
{
	public function init() {
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsiveevent_admin_main', array(), 'ynresponsiveevent_admin_main_manage_events');
	}

	public function indexAction() 
	{
		$this -> view -> headLink() -> appendStylesheet($this -> view -> baseUrl() . '/application/modules/Ynresponsiveevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
		$this -> view -> form = $form = new Ynresponsiveevent_Form_Admin_SearchEvents();
		$values = array();
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		if ($this -> getRequest() -> isPost()) {
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value) {
				if ($key == 'delete_' . $value) {
					$event = Engine_Api::_() -> getItem('ynresponsiveevent_event', $value);
					$event -> delete();
				}
			}
		}

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> getDbtable('events', 'ynresponsiveevent') -> getEventPaginator($values);
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
		if (!isset($values['order'])) {
			$values['order'] = "starttime";
		}

		if (!isset($values['direction'])) {
			$values['direction'] = "asc";
		}
		$this -> view -> formValues = $values;
	}

	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> event_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$event = Engine_Api::_() -> getItem('ynresponsiveevent_event', $id);
				$event -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-manage-events/delete.tpl');
	}

	public function createAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsiveevent_Form_Event_Create();

		// Not post/invalid
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		// Process
		$values = $form -> getValues();

		$db = Engine_Api::_() -> getDbtable('events', 'ynresponsiveevent') -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create event
			$table = Engine_Api::_() -> getDbtable('events', 'ynresponsiveevent');
			$event = $table -> createRow();
			$values['event_id'] = $values['toValues'];
			$event -> setFromArray($values);
			$event -> save();

			// Add photo
			if (!empty($values['photo'])) {
				$event -> setPhoto($form -> photo);
			}

			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The event was added successfully.'))));
		} catch( Engine_Image_Exception $e ) {
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction() 
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$event_id = $this -> getRequest() -> getParam('id');
		$event = Engine_Api::_() -> getItem('ynresponsiveevent_event', $event_id);
		// Create form
		$this -> view -> form = $form = new Ynresponsiveevent_Form_Event_Edit();
		if (!$this -> getRequest() -> isPost()) 
		{
			$form -> populate($event -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();

		// Process
		$db = Engine_Api::_() -> getDbtable('events', 'ynresponsiveevent') -> getAdapter();
		$db -> beginTransaction();

		try {
			// Set event info
			$event -> setFromArray($values);
			$event -> save();

			if (!empty($values['photo'])) {
				$event -> setPhoto($form -> photo);
			}
			// Commit
			$db -> commit();
		} catch( Engine_Image_Exception $e ) {
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The event was edited successfully.'))));
	}

	public function suggestAction() {
		$table = Engine_Api::_() -> getItemTable('event');
		;
		$table_name = $table -> info('name');
		$data = array();
		$select = $table -> select();
		if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value')))) {
			$select -> where("$table_name.title LIKE ?", '%' . $text . '%');
		}
		foreach ($select->getTable()->fetchAll($select) as $event) {
			if (!Engine_Api::_() -> getDbtable('events', 'ynresponsiveevent') -> getEventById($event -> getIdentity())) {
				$data[] = array('type' => 'event', 'id' => $event -> getIdentity(), 'guid' => $event -> getGuid(), 'label' => $event -> getTitle(), 'photo' => $this -> view -> itemPhoto($event, 'thumb.normal'), 'url' => $event -> getHref(), );
			}
		}
		return $this -> _helper -> json($data);
	}

}
