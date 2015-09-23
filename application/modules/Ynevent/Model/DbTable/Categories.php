<?php

class Ynevent_Model_DbTable_Categories extends Engine_Db_Table {

	protected $_rowClass = 'Ynevent_Model_Category';
	protected $_name = 'event_categories';
	
	public function getMaxLevel(){
		return 5;
	}

	public function getCategoriesAssoc() {
		$stmt = $this -> select() -> from($this, array('category_id', 'title')) -> order('title ASC') -> query();

		$data = array();
		foreach ($stmt->fetchAll() as $category) {
			$data[$category['category_id']] = $category['title'];
		}

		return $data;
	}
	
	public function getChildrenCategories($parent_id = 0){
		$select = $this->select()->where('parent_id=?',(int)$parent_id)->order('title');
		return $this->fetchAll($select);
	}

	public function getCategoriesParent() {
		$stmt = $this -> select() -> from($this, array('category_id', 'title')) -> where('parent_id=?', 0) -> order('title ASC') -> query();

		$data = array();
		foreach ($stmt->fetchAll() as $category) {
			$data[$category['category_id']] = $category['title'];
		}

		return $data;
	}

	public function getNode($id) {
		return $this -> find((int)$id) -> current();
	}

	public function addChild($parent_id, $data) {
		$node = $this -> fetchNew();
		$node -> setFromArray($data);
		$node -> parent_id = (int)$parent_id;
		$node -> save();
		return $node;
	}

	public function deleteNode($node, $moveToId = 0) {
		$moveToId = (int)$moveToId;
		$db = Engine_Db_Table::getDefaultAdapter();
		$ids = $node->getAllChildrenIds();
			
		$db -> update('engine4_event_events', array('category_id' => $moveToId), 'category_id IN('.implode(',', $ids).')');
		$db -> delete('engine4_event_categories', 'category_id IN('.implode(',', $ids).')');
		
	}

	public function getDeleteOptions($id) {
		return array();
	}

	public function getMultiOptions($parent_id = 0) 
	{
		$select = $this -> select() -> where('parent_id=?', (int)$parent_id)->order('title');
		$result = array();
		$translate = Zend_Registry::get('Zend_Translate');
		$result[''] =  $translate -> translate('All categories');	
		foreach ($this->fetchAll($select) as $item) {
			$result[$item -> getIdentity()] = $item -> shortTitle();
		}		
		return $result;
	}
	
	public function getPreMultiOptions($options, $pid = 0) {
				
		$select = $this -> select() -> where('parent_id=?', $pid);
		foreach ($this->fetchAll($select) as $item)
		{
			if($options[$item -> parent_id])
			{
				$position = strripos($options[$item -> parent_id],'=>');
				$prefix = substr($options[$item -> parent_id], 0, $position) . '=>=>';
			}
			else {
				$prefix = '';
			}
			$options[$item -> getIdentity()] = $prefix.$item -> shortTitle();
			$options = $this->getPreMultiOptions($options, $item->getIdentity());
		}
		return $options;
	}
	
	public function getCategoryById($category_id) {
        $select = $this->select();
        $select->where("category_id=?", $category_id);
        return $this->fetchRow($select);
    }

}
