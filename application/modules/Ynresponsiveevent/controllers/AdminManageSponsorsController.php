<?php
class Ynresponsiveevent_AdminManageSponsorsController extends Core_Controller_Action_Admin
{
	public function init()
	{
	    $this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynresponsiveevent_admin_main', array(), 'ynresponsiveevent_admin_main_manage_sponsors');
	}

	public function indexAction() 
	{
		if ($this -> getRequest() -> isPost()) 
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value) 
			{
				if ($key == 'delete_' . $value) 
				{
					$event = Engine_Api::_() -> getItem('ynresponsiveevent_sponsor', $value);
					$event -> delete();
				}
			}
		}
		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> getDbtable('sponsors', 'ynresponsiveevent') -> getSponsorPaginator();
		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> sponsor_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$sponsor = Engine_Api::_() -> getItem('ynresponsiveevent_sponsor', $id);
				$sponsor -> delete();
				$db -> commit();
			} catch (Exception $e) {
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
		// Output
		$this -> renderScript('admin-manage-sponsors/delete.tpl');
	}

	public function createAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Create form
		$this -> view -> form = $form = new Ynresponsiveevent_Form_Sponsor_Create();

		// Not post/invalid
		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$values = $form -> getValues();
		$db = Engine_Api::_() -> getDbtable('sponsors', 'ynresponsiveevent') -> getAdapter();
		$db -> beginTransaction();

		try {
			// Create sponsor
			$table = Engine_Api::_() -> getDbtable('sponsors', 'ynresponsiveevent');
			$sponsor = $table -> createRow();
			$values['event_id'] = $values['toValues'];
			$sponsor -> setFromArray($values);
			$sponsor -> save();

			// Add photo
			if (!empty($values['photo'])) {
				$sponsor -> setPhoto($form -> photo);
			}
			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The sponsor was added successfully.'))));
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
		$sponsor_id = $this -> getRequest() -> getParam('id');
		$sponsor = Engine_Api::_() -> getItem('ynresponsiveevent_sponsor', $sponsor_id);
		// Create form
		$this -> view -> form = $form = new Ynresponsiveevent_Form_Sponsor_Edit();
		if (!$this -> getRequest() -> isPost()) 
		{
			$form -> populate($sponsor -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();

		// Process
		$db = Engine_Api::_() -> getDbtable('sponsors', 'ynresponsiveevent') -> getAdapter();
		$db -> beginTransaction();

		try {
			if (!empty($values['photo'])) {
				$sponsor -> setPhoto($form -> photo);
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
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The sponsor was edited successfully.'))));
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
			if (!Engine_Api::_() -> getDbtable('sponsors', 'ynresponsiveevent') -> checkEventById($event -> getIdentity())) {
				$data[] = array('type' => 'event', 'id' => $event -> getIdentity(), 'guid' => $event -> getGuid(), 'label' => $event -> getTitle(), 'photo' => $this -> view -> itemPhoto($event, 'thumb.normal'), 'url' => $event -> getHref(), );
			}
		}
		return $this -> _helper -> json($data);
	}
}
