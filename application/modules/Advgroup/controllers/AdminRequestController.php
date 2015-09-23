<?php
class Advgroup_AdminRequestController extends Core_Controller_Action_Admin
{

	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_request');

		$this -> view -> form = $form = new Advgroup_Form_Admin_RequestFilter();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = $params;


		//Get Data
		$requestTable = Engine_Api::_() -> getDbTable('requests', 'advgroup');
		$this -> view -> paginator = $requestTable -> getRequestPaginator($params);

		$this -> view -> paginator -> setItemCountPerPage(10);
		$this -> view -> paginator -> setCurrentPageNumber($this ->_getParam('page'), 1);
	}


	public function acceptAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$request =  Engine_Api::_() -> getItem('advgroup_request', $this ->_getParam('req_id'));
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
				if($group) {
					$group -> verified = true;
					$group -> save();
				}
				if($request) {
					$request -> status = 1;
					$request -> save();
				}
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
			
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($group -> getOwner(), $group, $group, 'advgroup_request_accepted');
			$notifyApi -> addNotification($group -> getOwner(), $group, $group, 'advgroup_group_verified');
			
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		// Output
		$this -> renderScript('admin-request/accept.tpl');
	}
	
	public function denyAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$request =  Engine_Api::_() -> getItem('advgroup_request', $this ->_getParam('req_id'));
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
				if($group) {
					$group -> requested = false;
					$group -> save();
				}
				if($request) {
					$request -> status = 2;
					$request -> save();
				}
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
			
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($group -> getOwner(), $group, $group, 'advgroup_request_denied');
			
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}
		// Output
		$this -> renderScript('admin-request/deny.tpl');
	}
	
}
