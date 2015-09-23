<?php

class QuestionAnswer_Api_Core extends Core_Api_Abstract
{
  public function checkVersionSE()
  {
      $c_table  = Engine_Api::_()->getDbTable('modules', 'core');
      $c_name   = $c_table->info('name');
      $select   = $c_table->select()
                        ->where("$c_name.name LIKE ?",'core')->limit(1);
      
      $row = $c_table->fetchRow($select)->toArray();
      $strVersion = $row['version'];
      $intVersion = (int)str_replace('.','',$strVersion);
      return  $intVersion >= 410?true:false;
  }  
}