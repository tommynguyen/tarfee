<?php
class Ynfeedback_Model_DbTable_Severities extends Engine_Db_Table {
		
	public function getAllSeverities() {
		return $this -> fetchAll($this -> select());
	}
	
	public function getSeverityArray()
	{
		$typeArray = array();
		$select = $this -> select();
		$types = $this -> fetchAll($select);
		foreach($types as $type)
		{
			$typeArray[$type -> severity_id] = $type -> title;  
		}
		return $typeArray;
	}
}
