<?php
class SocialConnect_Model_DbTable_Pages extends Engine_Db_Table {
    protected $_rowClass = 'SocialConnect_Model_Page';
	public function getAllPages()
  	{
	  	$select = $this -> select() -> order('order');
		return $this -> fetchAll($select);
  	}
}