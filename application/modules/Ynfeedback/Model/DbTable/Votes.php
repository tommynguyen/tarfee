<?php

class Ynfeedback_Model_DbTable_Votes extends Engine_Db_Table
{
	protected $_rowClass = 'Ynfeedback_Model_Vote';
	protected $_name = 'ynfeedback_votes';
	
	public function getAllVotes($ideaId)
	{
		$select =  $this->select()->where("idea_id = ?", $ideaId);
		return $this->fetchAll($select);
	}
	
	public function getVote($ideaId, $userId)
	{
		$select =  $this->select()->where("user_id = ?", $userId)->where("idea_id = ?", $ideaId)->limit(1);
		return $this->fetchRow($select);
	}
	
	public function add(Core_Model_Item_Abstract $idea, Core_Model_Item_Abstract $poster)
	{
		$row = $this->getVote($idea->getIdentity(), $poster->getIdentity());
		if( null == $row )
		{
			$row = $this->createRow();
		}
		$row -> setFromArray(array(
			'creation_date' => date('Y-m-d H:i:s'),
			'user_id' => $poster -> getIdentity(),
			'value' => 1,
			'idea_id' => $idea->getIdentity(),
		));
		$row->save();

		if( isset($idea->vote_count) )
		{
			$idea->vote_count++;
			$idea->save();
		}
		return $row;
	}
	
	public function remove(Core_Model_Item_Abstract $idea, Core_Model_Item_Abstract $poster)
	{
		$row = $this->getVote($idea->getIdentity(), $poster->getIdentity());
		if( null !== $row )
		{
			$row->delete();
	
			if( isset($idea->vote_count) )
			{
				$idea->vote_count--;
				$idea->save();
			}
		}
		return $this;
	}
	
	public function isVoted(Core_Model_Item_Abstract $idea, Core_Model_Item_Abstract $poster = null)
	{
		if (is_null($poster))
		{
			$poster = Engine_Api::_()->user()->getViewer();
		}
		if (!$poster -> getIdentity())
		{
			return false;
		}
		$row = $this->getVote($idea->getIdentity(), $poster->getIdentity());
		if (is_null($row) || $row -> value == '0')
		{
			return false;
		}
		return true;
	}
	
	public function getVoteCount(Core_Model_Item_Abstract $resource)
	{
		if( isset($resource->vote_count) )
		{
			return $resource->vote_count;
		}
	}
    
    public function deleteVotesByIdea($idea_id) {
        $where = $this->getAdapter()->quoteInto('idea_id = ?', $idea_id);
        $this->delete($where);
    }
}
