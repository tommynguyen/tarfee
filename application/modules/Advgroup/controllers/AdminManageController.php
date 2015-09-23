<?php
class Advgroup_AdminManageController extends Core_Controller_Action_Admin
{

	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_manage');

		$this -> view -> form = $form = new Advgroup_Form_Admin_Search();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = $params;

		switch ($params['filter'])
		{
			case '0' :
				$params['featured'] = 1;
				break;
			case '1' :
				$params['featured'] = 0;
				break;
			case '2' :
				$params['is_subgroup'] = 1;
				break;
			case '3' :
				$params['is_subgroup'] = 0;
				break;
		}

		//Get Data
		$groupTable = Engine_Api::_() -> getItemTable('group');
		$select = $groupTable -> getGroupSelect($params);

		$userTable = Engine_Api::_() -> getDbtable('users', 'user');
		$groupMembershipTable = Engine_Api::_() -> getDbtable('membership', 'advgroup');
		$userTableName = $userTable -> info('name');
		$groupMembershipTableName = $groupMembershipTable -> info('name');
		$groupTableName = $groupTable -> info('name');

		$userSelect = $userTable -> select() -> setIntegrityCheck(false);
		$userSelect -> from($userTableName, "COUNT(*)");
		$userSelect -> joinRight($groupMembershipTableName, "$groupMembershipTableName.user_id = $userTableName.user_id", null);
		$userSelect -> where("$groupMembershipTableName.resource_id = $groupTableName.group_id");
		$select -> columns(array("($userSelect) AS number_group_member"));
		if ($active !== null)
		{
			$select -> where("$subtableName.active = ?", (bool)$active);
		}

		$this -> view -> paginator = Zend_Paginator::factory($select);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($params['page']);
	}

	public function deleteSelectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Check post
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			//Process delete
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();
			try
			{
				$ids_array = explode(",", $ids);
				foreach ($ids_array as $id)
				{
					$group = Engine_Api::_() -> getItem('group', $id);
					if ($group)
					{
						$group -> delete();
					}
				}
				$db -> commit();
			}
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => ''));
		}
	}

	public function deleteAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> group_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$group = Engine_Api::_() -> getItem('group', $id);
				$group -> delete();
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		// Output
		$this -> renderScript('admin-manage/delete.tpl');
	}

}
