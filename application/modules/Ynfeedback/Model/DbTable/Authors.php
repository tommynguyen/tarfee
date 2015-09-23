<?php
class Ynfeedback_Model_DbTable_Authors extends Engine_Db_Table {
    
	public function isAuthor($idea_id, $user_id)
	{
		$select = $this -> select() -> where('idea_id = ?', $idea_id) -> where('user_id = ?', $user_id) -> limit(1);
		$row =  $this -> fetchRow($select);
		if($row)
			return true;
		else
			return false;
	}
	
	public function getAuthorsByIdeaId($idea_id)
	{
		$select = $this -> select() -> where('idea_id = ?', $idea_id);
		return $this -> fetchAll($select);
	}
	
	public function deleteAllAuthorsByIdeaId($idea_id)
	{
		$select = $this->select()->where('idea_id = ?', $idea_id);
		$rows =  $this -> fetchAll($select);
		foreach ($rows as $row) {
			$row -> delete();
		}
	}
}	