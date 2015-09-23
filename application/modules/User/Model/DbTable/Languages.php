<?php
class User_Model_DbTable_Languages extends Engine_Db_Table {
		
	public function getAllLanguages() {
		return $this -> fetchAll($this -> select());
	}
	
	public function getLanguagesArray()
	{
		$typeArray = array();
		$select = $this -> select() -> order("title");
		$types = $this -> fetchAll($select);
		foreach($types as $type)
		{
			$typeArray[$type -> language_id] = $type -> title;  
		}
		return $typeArray;
	}
}
