<?php
class Contactimporter_Model_DbTable_Stats extends Engine_Db_Table
{
  protected $_rowClass = 'Contactimporter_Model_Stats';

  public static function getInstance()
    {
        if(null == self::$_inst){
            self::$_inst = new self();
        }
        return self::$_inst;    
    }
}