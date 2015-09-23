<?php
class Slprofileverify_Model_DbTable_Customs extends Engine_Db_Table {

    protected $_rowClass = 'Slprofileverify_Model_Custom';
    
    public function getCustomRow($option_id){
        if( empty($option_id) ) { return null;}
        $select = $this->select()->from($this->info('name'))->where('option_id = ?', $option_id);
        $result = $this->fetchRow($select);
        if(!$result){ return null; }
        return $result;        
    }
    
    public function getAllCustom(){
        $select = $this->select()->from($this->info('name'));
        $result = $this->fetchAll($select);
        if(!$result){ return null; }
        return $result;        
    }
}