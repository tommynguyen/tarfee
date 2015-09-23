<?php

class Ynevent_AdminSettingsController extends Core_Controller_Action_Admin {

	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_settings');

		$this -> view -> form = $form = new Ynevent_Form_Admin_Global();

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();

			foreach ($values as $key => $value) {
				Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
			}
			$form -> addNotice('Your changes have been saved.');
		}
	}

	public function categoriesAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_categories');

		// $this->view->categories = Engine_Api::_()->getDbtable('categories', 'ynevent')->fetchAll();
		$table = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$nodeId = $this -> _getParam('parent_id', 0);
		$this -> view -> categories = $table -> getChildrenCategories($nodeId);

		$this -> view -> category = $table -> find($nodeId) -> current();
	}

	public function levelAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_level');

		// Get level id
		if (null !== ($id = $this -> _getParam('id'))) {
			$level = Engine_Api::_() -> getItem('authorization_level', $id);
		} else {
			$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
		}

		if (!$level instanceof Authorization_Model_Level) {
			throw new Engine_Exception('missing level');
		}

		$level_id = $id = $level -> level_id;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Admin_Settings_Level( array('public' => ( in_array($level -> type, array('public'))), 'moderator' => ( in_array($level -> type, array('admin', 'moderator'))), ));
		$form -> level_id -> setValue($level_id);

		$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
		$form -> populate($permissionsTable -> getAllowed('event', $level_id, array_keys($form -> getValues())));

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		// Check validitiy
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$values = $form -> getValues();
		$db = $permissionsTable -> getAdapter();
		$db -> beginTransaction();

		try {
			if ($level -> type != 'public') {
				// Set permissions
				$values['auth_comment'] = (array)$values['auth_comment'];
				$values['auth_photo'] = (array)$values['auth_photo'];
				$values['auth_view'] = (array)$values['auth_view'];
				$values['auth_video'] = (array)$values['auth_video'];
				$values['video'] = '1';
			}
			$permissionsTable -> setAllowed('event', $level_id, $values);

			// Commit
			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}
		$form -> addNotice('Your changes have been saved.');
	}

	public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new Ynevent_Form_Admin_Category();
		$form -> setAction($this -> view -> url());

		// Check post
		if (!$this -> getRequest() -> isPost()) {
			$this -> renderScript('admin-settings/form.tpl');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			$this -> renderScript('admin-settings/form.tpl');
			return;
		}

		$values = $form -> getValues();
		$table = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$parentId = $this -> _getParam('parent_id', 0);
		$user = Engine_Api::_() -> user() -> getViewer();
		$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
		$table -> addChild($parentId, $data);
		return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	}

	public function deleteCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> event_id = $id;
		$options = Engine_Api::_() -> getDbTable('categories', 'ynevent') -> getDeleteOptions($id);
		$categoryTable = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$eventTable = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$table = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$node = $categoryTable -> find($id) -> current();
		if (!$options) {
			$this -> view -> canNotDelete = true;
		}

		$moveNode = $this -> view -> moveNode = 
			new Zend_Form_Element_Select('node_id', array('label' => 'Category', 'multiOptions' => $options));

		// Check post
		if (!$this -> getRequest() -> isPost()) {
			$this -> renderScript('admin-settings/delete.tpl');
			return;
		}

		$node_id = $this -> getRequest() -> getPost('node_id', 0);
		// go through logs and see which classified used this category and set it to ZERO
		if (is_object($node)) {
			$table -> deleteNode($node, $node_id);
		}
		
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	}

	public function editCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> event_id = $id;
		$categoryTable = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$category = $categoryTable -> find($id) -> current();

		// Generate and assign form
		$form = $this -> view -> form = new Ynevent_Form_Admin_Category();
		$form -> setAction($this -> view -> url());
		$form -> setField($category);

		// Check post
		if (!$this -> getRequest() -> isPost()) {
			$this -> renderScript('admin-settings/form.tpl');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			$this -> renderScript('admin-settings/form.tpl');
			return;
		}

		// Ok, we're good to add field
		$values = $form -> getValues();

		$db = $categoryTable -> getAdapter();
		$db -> beginTransaction();

		try {
			$category -> title = $values['label'];
			$category -> save();

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
	}
}