<?php
class Ynfeed_Model_List extends Core_Model_List {

	protected $_searchTriggers = false;
	protected $_modifiedTriggers = false;
	protected $_owner_type = 'user';
	public $ignorePermCheck = true;

	public function getListItemTable() {
		return Engine_Api::_() -> getItemTable('ynfeed_list_item');
	}

	public function count() 
	{
		$enableResource = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getEnableCustomType(array('enabled' => 1));
		if (empty($enableResource))
			return 0;
		$listItemTable = $this -> getListItemTable();
		return $listItemTable -> select() -> from($listItemTable -> info('name'), new Zend_Db_Expr('COUNT(listitem_id)')) -> where('list_id = ?', $this -> list_id) -> where("child_type in(?)", (array)$enableResource) -> limit(1) -> query() -> fetchColumn();
	}

	public function getListItems() {
		$enableResource = Engine_Api::_() -> getDbtable('customtypes', 'ynfeed') -> getEnableCustomType(array('enabled' => 1));
		if (empty($enableResource))
			return;
		$listItemTable = $this -> getListItemTable();
		$select = $listItemTable -> select() -> where("child_type in(?)", (array)$enableResource) -> where('list_id = ?', $this -> list_id);
		return $listItemTable -> fetchAll($select);
	}

	public function setListItems($selected_resources) {
		$listItemTable = $this -> getListItemTable();
		$listItemTable -> delete(array("list_id = ? " => $this -> list_id));
		if (!empty($selected_resources)) {
			$selectedResourcesArray = explode(',', $selected_resources);
			foreach ($selectedResourcesArray as $value) {
				$resource = explode('-', $value);
				$resource_type = $resource[0];
				$resource_id = $resource[1];
				// enter list item into listitem table for list
				if (!empty($resource_type) && $resource_id) {
					$listItem = $listItemTable -> createRow();
					$listItem -> setFromArray(array('list_id' => $this -> list_id, 'child_type' => $resource_type, 'child_id' => $resource_id));
					$listItem -> save();
				}
			}
		}
	}

}
