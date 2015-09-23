<?php
class User_Model_DbTable_Relations extends Engine_Db_Table {
		
	public function getAllRelations() {
		return $this -> fetchAll($this -> select() -> order("title"));
	}
	
	public function getRelationArray()
	{
		$typeArray = array();
		$select = $this -> select()-> order("title");
		$types = $this -> fetchAll($select);
		foreach($types as $type)
		{
			$typeArray[$type -> relation_id] = $type -> title;  
		}
		return $typeArray;
	}
	
	public function getRelationSearchArray()
	{
		$typeArray = array();
		$select = $this -> select()-> order("search_title");
		$types = $this -> fetchAll($select);
		foreach($types as $type)
		{
			$typeArray[$type -> relation_id] = $type -> search_title;  
		}
		return $typeArray;
	}
}
