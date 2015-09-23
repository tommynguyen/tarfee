<?php

class Ynvideo_Model_DbTable_Ratingtypes extends Engine_Db_Table
{
    protected $_rowClass = 'Ynvideo_Model_Ratingtype';
	protected $_name = 'ynvideo_ratingtypes';
	
	public function getAllRatingTypes(){
		$select = $this -> select();
		return $this -> fetchAll($select);
	}
	
}
