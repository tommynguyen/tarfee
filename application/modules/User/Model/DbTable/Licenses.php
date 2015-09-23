<?php
class User_Model_DbTable_Licenses extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_License';
    
    public function getAllLicensesOfUser($user_id, $type = false, $limit = false) {
        $select = $this->select()->where('user_id = ?', $user_id)
        ->order('year DESC')
        ->order('IF(ISNULL(month),1,0), month DESC');
        
        if ($type) {
            $select->where('type = ?', $type);
        }
        
        if ($limit) {
            $select->limit($limit);
        }
        
		return $this->fetchAll($select);
    }
}
