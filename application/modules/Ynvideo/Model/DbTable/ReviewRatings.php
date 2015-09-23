<?php

class Ynvideo_Model_DbTable_ReviewRatings extends Engine_Db_Table
{
    protected $_rowClass = 'Ynvideo_Model_ReviewRating';
	protected $_name = 'ynvideo_reviewratings';
	
	public function getRowRatingThisType($rating_type, $resource_id, $user_id)
	{
		$select = $this -> select() -> where('resource_id = ?', $resource_id) 
									-> where('user_id = ?', $user_id) 
									-> where('rating_type = ?', $rating_type)
									-> limit(1);
		$row = $this -> fetchRow($select);
		if($row)
		{
			return $row;
		}
		return false;
	}
	
	public function getUserRatingByResource($resource_id) {
		$select = $this -> select() -> where('resource_id = ?', $resource_id);
		$userIds = array();
		$rows = $this -> fetchAll($select);
		foreach($rows as $row) {
			if(!in_array($row -> user_id, $userIds)) {
				$userIds[] = $row -> user_id;
			}
		}
		return $userIds;
	}
	
	public function getRatingsByResource($resource_id) {
		$select = $this -> select() -> where('resource_id = ?', $resource_id);
		return $this -> fetchAll($select);
	}
	
	public function getRatingsBy($params = array()) {
		$select = $this -> select();
		if(isset($params['resource_id']) && !empty($params['resource_id'])) {
			$select -> where('resource_id = ?', $params['resource_id']);
		}
		if(isset($params['user_id']) && !empty($params['user_id'])) {
			$select -> where('user_id = ?', $params['user_id']);
		}
		return $this -> fetchAll($select);
	}
		
	public function getRatingOfType($type_id, $resource_id)
	{
		$select = $this -> select() -> where('resource_id = ?', $resource_id) 
									-> where('rating_type = ?', $type_id);
		$rows = $this -> fetchAll($select);
		$count = 0;
		$total = 0;
		foreach($rows as $row)
		{
			$count++;
			$total += $row -> rating;
		}
		if($total ==0 && $count == 0){
			return 0;
		}
		$rate = round(($total/$count), 1);
		return $rate;
	}
}
