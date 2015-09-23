<?php
class Ynfeedback_Model_DbTable_Follows extends Engine_Db_Table
{
	public function getAllFollow($idea_id)
	{
		$select = $this->select();
        $select->where("idea_id = ?", $idea_id);
        return $this->fetchAll($select);
	}
	
	public function getFollowIdea($idea_id, $uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid)
                ->where('idea_id = ?', $idea_id) -> limit(1);
        return $this->fetchRow($select);
    }
	
	public function getFollowIdeas($uid) {
        $select = $this->select();
        $select->where("user_id = ?", $uid);
        return $this->fetchAll($select);
    }

    public function getUsersFollow($idea_id) {
        $select = $this->select() -> from($this -> info('name'), 'user_id');
        $select->where('idea_id= ?', $idea_id);
		$userIds = array();
        foreach($this->fetchAll($select) as $userId)
		{
			$userIds[] = $userId['user_id'];
		}
		if(!$userIds)
		{
			return NULL;
		}
		return Engine_Api::_() -> user() -> getUserMulti($userIds);
    }
}
