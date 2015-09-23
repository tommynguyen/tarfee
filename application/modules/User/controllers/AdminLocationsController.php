<?php
class User_AdminLocationsController extends Core_Controller_Action_Admin {

	public function indexAction() {
		$this->view->id = $id = $this->_getParam('id', 0);
		$this->view->location = $location = Engine_Api::_()->getItem('user_location', $id);
		$this->view->locations = $locations = Engine_Api::_()->getItemTable('user_location')->getLocations($id);
	}

	public function addAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$parent_id = $this->_getParam('id', 0);
		$parent = Engine_Api::_()->getItem('user_location', $parent_id);
		$form = $this -> view -> form = new User_Form_Admin_Location_Add(array('id' => $parent_id));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		if ($parent) {
			$form->removeElement('continent');
		}

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// add category to the database
				// Transaction
				$table = Engine_Api::_() -> getDbtable('locations', 'user');

				// insert the category into the database
				$row = $table -> createRow();
				$row -> title = $values["title"];
				if ($parent) {
					$row->parent_id = $parent_id;
					$row->level = intval($parent->level) + 1;
					$row->continent = $parent->continent;
				}
				else {
					$row->parent_id = 0;
					$row->level = 0;
					$row->continent = $values['continent'];
				}
				$row -> save();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Add successfully!')));
		}
	}

	public function editAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		$id = $this->_getParam('id', 0);
		$location = Engine_Api::_()->getItem('user_location', $id);
		if (!$id || !$location) {
			return $this->_helper->requireSubject()->forward();
		}
		
		$form = $this -> view -> form = new User_Form_Admin_Location_Add();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$form -> setField($location);
		
		if ($location->level) {
			$form->removeElement('continent');
		}
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				$location -> title = $values["title"];
				if ($location->level == 0) $location->continent = $values['continent'];
				$location -> save();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Edit successfully!')));
		}

	}

	public function deleteAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> location_id = $id;

		$table = Engine_Api::_() -> getDbtable('locations', 'user');
		$location = Engine_Api::_()->getItem('user_location', $id);

		$this -> view -> canDelete = true;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = $table -> getAdapter();
			$db -> beginTransaction();

			try {

				$location -> delete();
				$table->delete($table->getAdapter()->quoteInto('parent_id = ?', $id));
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => true, 'messages' => array('Delete successfully!')));
		}

		// Output
	}
}
