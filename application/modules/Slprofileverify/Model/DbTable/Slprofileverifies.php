<?php
class Slprofileverify_Model_DbTable_Slprofileverifies extends Engine_Db_Table
{
    protected $_rowClass = "Slprofileverify_Model_Slprofileverify";
    
    public function getVerifyInfor($user_id){
        if( empty($user_id) ) { return null;}
        $select = $this->select()->from($this->info('name'))->where('user_id = ?', $user_id);
        $result = $this->fetchRow($select);
        if( !$result ) { return null;}
        return $result;
    }
}