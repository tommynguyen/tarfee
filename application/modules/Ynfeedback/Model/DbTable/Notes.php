<?php
class Ynfeedback_Model_DbTable_Notes extends Engine_Db_Table 
{
	protected $_rowClass = 'Ynfeedback_Model_Note';
	
	public function getNoteByFeedback($feedback)
	{
		$select = $this -> select()->where("idea_id = ?", $feedback -> getIdentity());
		return $this -> fetchAll($select);
	}
	
	public function getNoteByFeedbackId($feedbackId)
	{
		$select = $this -> select()->where("idea_id = ?", $feedbackId);
		return $this -> fetchAll($select);
	}
	
	public function getNote($noteId)
	{
		$select = $this -> select()->where("note_id = ?", $noteId)->limit(1);
		return $this -> fetchRow($select);
	}
    
    public function deleteNotesByIdea($idea_id) {
        $where = $this->getAdapter()->quoteInto('idea_id = ?', $idea_id);
        $this->delete($where);
    }
}
