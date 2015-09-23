<?php
class User_Model_DbTable_Recommendations extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_Recommendation';
	
    public function getReceivedRecommendations($user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
        ->where('approved = ?', 1)
        ->order('given_date DESC');
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}
		
        return $this->fetchAll($select);
    }
	
	public function getShowRecommendations($user_id) {
        $select = $this->select()->where('receiver_id = ?', $user_id)
        ->where('approved = ?', 1)
		->where('`show` = ?', 1)
        ->order('given_date DESC');
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}
		
        return $this->fetchAll($select);
    }

	public function removeRecommendationsOfReceiver($receiver_id) {
		$where = $this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id);
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}
		
        $this->delete($where);
	}
	
	public function getRequestRecommendations($giver_id) {
		$select = $this->select()->where('giver_id = ?', $giver_id)
        ->where('request = ?', 1)
        ->where('approved = ?', 0)
        ->order('creation_date DESC');
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}
		
        return $this->fetchAll($select);
	}
	
	public function getPendingRecommendations($receiver_id) {
		$select = $this->select()->where('receiver_id = ?', $receiver_id)
        ->where('request = ?', 0)
        ->where('approved = ?', 0)
        ->order('given_date DESC');
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}
		
        return $this->fetchAll($select);
	}
	
	public function getRecommendation($receiver_id, $giver_id) {
		$select = $this->select()->where('receiver_id = ?', $receiver_id)
		->where('giver_id = ?', $giver_id);
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select->where('receiver_id NOT IN (?)', $deactiveIds)->where('giver_id NOT IN (?)', $deactiveIds);
		}

        return $this->fetchRow($select);
	}

	public function approveRecommendations($receiver_id, $ids) {
		$data = array(
			'approved' => 1,
			'approved_date' => date('Y-m-d H:i:s')
		);
		$where = array(
			$this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id),
			$this->getAdapter()->quoteInto('recommendation_id IN (?)', $ids)
		);
		$this->update($data, $where);
	}
	
	public function showRecommendations($receiver_id, $ids) {
		$where = array(
			$this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id),
			$this->getAdapter()->quoteInto('approved = ?', 1)
		);
		$this->update(array('show' => 0), $where);
		if (count($ids)) {
			$where = array(
				$this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id),
				$this->getAdapter()->quoteInto('recommendation_id IN (?)', $ids)
			);
			$this->update(array('show' => 1), $where);
		}
	}
	
	public function deleteRecommendations($receiver_id, $ids) {
		$where = array(
			$this->getAdapter()->quoteInto('receiver_id = ?', $receiver_id),
			$this->getAdapter()->quoteInto('recommendation_id IN (?)', $ids)
		);
		$this->delete($where);
	}
	
	public function ignoreRecommendations($giver_id, $ids) {
		$where = array(
			$this->getAdapter()->quoteInto('giver_id = ?', $giver_id),
			$this->getAdapter()->quoteInto('recommendation_id IN (?)', $ids)
		);
		$this->delete($where);
	}
}
