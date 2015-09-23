<?php
class User_Model_DbTable_Experiences extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_Experience';
    
    public function getAllExperiencesOfUser($user_id, $current = false, $limit = false) {
        $select = $this->select()->where('user_id = ?', $user_id)
        ->order('IF(ISNULL(end_year),0,1), end_year DESC')
        ->order('IF(ISNULL(end_month),0,1), end_month DESC')
        ->order('start_year DESC')
        ->order('IF(ISNULL(start_month),1,0), start_month DESC');
        
        if ($current) {
            $select->where('ISNULL(end_year)');
        }else{
        	if($limit) //section Experience does not send limit param
        		$select->where('end_year IS NOT NULL');
        }
        
        if ($limit) {
            $select->limit($limit);
        }
        
		return $this->fetchAll($select);
    }
}
