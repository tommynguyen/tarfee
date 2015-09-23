<?php

class Advgroup_Model_DbTable_Requests extends Engine_Db_Table {
	protected $_name = 'group_requests';
	protected $_rowClass = 'Advgroup_Model_Request';
	
	public function getRequestPaginator($params = array()) {
		return Zend_Paginator::factory($this -> getRequestSelect($params));
	}

	public function getRequestSelect($params = array()) {
		
		$requestTable = $this;
		$requestName = $requestTable -> info('name');
		
		$groupTable = Engine_Api::_() -> getItemTable('group');
		$groupName = $groupTable -> info('name');
		
		$select = $this -> select();	
		$select -> from("$requestName as request", "request.*");	
		$select -> setIntegrityCheck(false) 
				-> joinLeft("$groupName as group", "group.group_id = request.group_id", null);
		
		if (isset($params['title']) && !empty($params['title'])) {
			$select -> where("group.title LIKE ?", '%' . $params['title'] . '%');
		}
		
		if ($params['status'] != "") {
			$select -> where("request.status = ?",  $params['status']);
		}
		
		// Order
		if (!empty($params['order'])) {
			$select -> order($params['order'] . ' ' . $params['direction']);
		} else {
			$select -> order('request.request_id DESC');
		}
		
		return $select;
	}
	
	
}