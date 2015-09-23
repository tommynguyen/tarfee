<?php
class User_Model_DbTable_Educations extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_Education';
    
    public function getAllEducationsOfUser($user_id, $limit = false) {
        $select = $this->select()->where('user_id = ?', $user_id)
        ->order('attend_from DESC')
        ->order('attend_to DESC');
        
        if ($limit) {
            $select->limit($limit);
        }
        
		return $this->fetchAll($select);
    }
}
