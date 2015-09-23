<?php
class Contactimporter_Model_DbTable_Apisettings extends Engine_Db_Table
{
    protected $_rowClass = 'Contactimporter_Model_Apisettings';

    public static function getInstance()
    {
        if(null == self::$_inst){
            self::$_inst = new self();
        }
        return self::$_inst;    
    }
    
}