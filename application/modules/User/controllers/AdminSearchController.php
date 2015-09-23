<?php
class User_AdminSearchController extends Core_Controller_Action_Admin {
	//HOANGND setting for profile sections
	public function indexAction() {
		if (null !== ($id = $this -> _getParam('level_id'))) {
			$level = Engine_Api::_() -> getItem('authorization_level', $id);
		}
		else {
			$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
		}

		if (!$level instanceof Authorization_Model_Level) {
			throw new Engine_Exception('missing level');
		}

		$id = $level -> level_id;

		// Make form
		$this -> view -> form = $form = new User_Form_Admin_Search( array(
			'public' => ( in_array($level -> type, array('public'))),
			'moderator' => ( in_array($level -> type, array(
				'admin',
				'moderator'
			))),
		));
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$form -> level_id -> setValue($id);

		$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');

		$form -> populate($permissionsTable -> getAllowed('user', $id, array_keys($form -> getValues())));
		
		 $numberFieldArr = Array('max_result', 'max_keyword');
        foreach ($numberFieldArr as $numberField) {
            if ($permissionsTable->getAllowed('user', $id, $numberField) == null) {
                $row = $permissionsTable->fetchRow($permissionsTable->select()
                ->where('level_id = ?', $id)
                ->where('type = ?', 'user')
                ->where('name = ?', $numberField));
                if ($row) {
                    $form->$numberField->setValue($row->value);
                }
            }
        } 
		// Check post
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		// Check validitiy
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$values = $form -> getValues();
		$db = $permissionsTable -> getAdapter();
		$db -> beginTransaction();
		// Process
		try
		{

			$permissionsTable -> setAllowed('user', $id, $values);
			$db -> commit();
		}

		catch(Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$form -> addNotice('Your changes have been saved.');
	}

}
