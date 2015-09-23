<?php
class Contactimporter_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_admin_main', array(), 'contactimporter_admin_main_settings');
		$this -> view -> form = $form = new Contactimporter_Form_Admin_Global();
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$this -> view -> form -> saveValues();
				$db -> commit();
				$form -> addNotice('Your changes have been saved.');
			}
			catch (Exception $e)
			{
				$db -> rollback();
				throw $e;
			}
		}
	}

	public function levelAction()
	{

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_admin_main', array(), 'contactimporter_admin_main_level');

		// Get level id
		if (null !== ($id = $this -> _getParam('id')))
		{
			$level = Engine_Api::_() -> getItem('authorization_level', $id);
		}
		else
		{
			$level = Engine_Api::_() -> getItemTable('authorization_level') -> getDefaultLevel();
		}

		if (!$level instanceof Authorization_Model_Level)
		{
			throw new Engine_Exception('missing level');
		}

		$level_id = $id = $level -> level_id;

		// Make form
		$this -> view -> form = $form = new Contactimporter_Form_Admin_Level();

		$form -> level_id -> setValue($level_id);
		$permissionsTable = Engine_Api::_() -> getDbtable('permissions', 'authorization');

		$form -> populate($permissionsTable -> getAllowed('contactimporter', $id, array_keys($form -> getValues())));

		$maxselect = $permissionsTable -> select() -> where("type = 'contactimporter'") -> where("level_id = ?", $level_id) -> where("name = 'max'");

		$mallow = $permissionsTable -> fetchRow($maxselect);
		if (!empty($mallow))
			$max = $mallow['value'];

		$max_get = $form -> max -> getValue();

		if ($max_get < 1)
			$form -> max -> setValue($max);

		$this -> view -> form = $form;

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

		// Process
		$values = $form -> getValues();
		$db = $permissionsTable -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$permissionsTable -> setAllowed('contactimporter', $level_id, $values);
			// Commit
			$db -> commit();
			$form -> addNotice('Your changes have been saved.');
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

	}

}
