<?php
class User_Model_DbTable_Sportcategories extends User_Model_DbTable_Nodes {

	protected $_rowClass = 'User_Model_Sportcategory';
	public function deleteNode(User_Model_Node $node, $node_id = NULL) {
		$result = $node -> getDescendent(true);
		$db = $this -> getAdapter();
		parent::deleteNode($node);
	}
	
	public function getCategoriesLevel1($params = array()) {
		$select = $this->select();
		$select -> order('title ASC');
		$select -> where('level = ?', 1);
		
		if (!empty($params['title'])) {
			$select->where('title LIKE ?', '%'.$params['title'].'%');
		}
		
		if (!empty($params['limit'])) {
			$select->limit($params['limit']);
		} 
		return $this->fetchAll($select);
	}
	
	public function getCategoriesLevel1Assoc($params = array()) {
		$result = array();
		$rows = $this->getCategoriesLevel1($params);
		foreach ($rows as $row) {
			$result[$row->getIdentity()] = $row->getTitle();
		}
		return $result;
	}
}
