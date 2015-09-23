<?php
class Contactimporter_Model_DbTable_Configs extends Engine_Db_Table
{
    protected $_rowClass = 'Contactimporter_Model_Configs';

    public static function getInstance()
    {
        if(null == self::$_inst){
            self::$_inst = new self();
        }
        return self::$_inst;    
    }

    public static function setHideLoginInviteStatus($data)
    {
        if(empty($data['user_id']) || empty($data['enabled'])){
            throw new Exception('Invalid paramater for setting config');
        }

        $inst =  self::getInstance();
        return $inst->insert($data);
    }
}