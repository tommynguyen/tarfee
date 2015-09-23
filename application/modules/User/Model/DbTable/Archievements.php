<?php
class User_Model_DbTable_Archievements extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_Archievement';
    
    public function getAllArchievementsOfUser($user_id, $type = false, $limit = false) {
        $select = $this->select()->where('user_id = ?', $user_id)
        ->order('year DESC');
        
        if ($type) {
            $select->where('type = ?', $type);
        }
        
        if ($limit) {
            $select->limit($limit);
        }
        
		return $this->fetchAll($select);
    }
}
