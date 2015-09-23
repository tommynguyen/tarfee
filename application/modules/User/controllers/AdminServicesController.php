<?php
class User_AdminServicesController extends Core_Controller_Action_Admin {

	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('profilesection_admin_main', array(), 'profilesection_admin_settings_service');
		$this -> view -> services = Engine_Api::_() -> getDbtable('services', 'user') -> getAllServices();
	}

	public function addAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new User_Form_Admin_Service_Add();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// add category to the database
				// Transaction
				$table = Engine_Api::_() -> getDbtable('services', 'user');

				// insert the category into the database
				$row = $table -> createRow();
				$row -> title = $values["title"];
				$row -> save();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Add service successfully!')));
		}
	}

	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> service_id = $id;

		$serviceTable = Engine_Api::_() -> getDbtable('services', 'user');
		$service = $serviceTable -> find($id) -> current();

		$this -> view -> canDelete = true;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = $serviceTable -> getAdapter();
			$db -> beginTransaction();

			try {

				$service -> delete();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Delete Service successfully!')));
		}

		// Output
	}

	public function editAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			die('No identifier specified');
		}
		$serviceTable = Engine_Api::_() -> getDbtable('services', 'user');
		$service = $serviceTable -> find($id) -> current();
		$form = $this -> view -> form = new User_Form_Admin_Service_Add();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$form -> setField($service);

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$service -> title = $values["title"];
				$service -> save();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Edit Service successfully!')));
		}

	}

}
