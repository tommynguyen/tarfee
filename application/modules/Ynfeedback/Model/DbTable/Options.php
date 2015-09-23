<?php
class Ynfeedback_Model_DbTable_Options extends Engine_Db_Table
{
  	protected $_name = 'ynfeedback_polls_options';
	
	public function getOptionsOfPoll($poll)
	{
		$select = $this -> select()
						-> where('poll_id = ?', $poll -> getIdentity());
		$rows = $this -> fetchAll($select);
		return $rows;
	}		
}