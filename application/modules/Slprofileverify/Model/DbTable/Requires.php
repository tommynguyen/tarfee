<?php
class Slprofileverify_Model_DbTable_Requires extends Engine_Db_Table {

    protected $_rowClass = 'Slprofileverify_Model_Require';
    
    public function getRequireRow($option_id){
        if( empty($option_id) ) { return null;}
        $select = $this->select()->from($this->info('name'))->where('option_id = ?', $option_id);
        $result = $this->fetchRow($select);
        if(!$result){ return null; }
        return $result;        
    }
}