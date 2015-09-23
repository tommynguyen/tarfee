<?php

class User_Model_DbTable_Services extends Engine_Db_Table {
	protected $_rowClass = 'User_Model_Service';
	
	public function getAllServices() {
		return $this -> fetchAll($this -> select() -> order('title'));
	}
	
    public function getServiceById($id = 0) {
        $select = $this->select()->where('service_id = ?', $id) -> order('title');
        return $this->fetchRow($select);
    }
}
