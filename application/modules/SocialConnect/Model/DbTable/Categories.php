<?php
class SocialConnect_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'SocialConnect_Model_Category';
  public function getAllCategories()
  {
  	$select = $this -> select() -> order('order');
	return $this -> fetchAll($select);
  }
}