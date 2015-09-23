<?php
class User_Model_DbTable_Eyeons extends Engine_Db_Table {
    public function isEyeOn($user_id, $player_id) {
        $select = $this->select()->where('user_id = ?', $user_id)->where('player_id = ?', $player_id);
        $row = $this->fetchRow($select);
        return ($row) ? true : false;
    }
	
	public function getUserEyeOns($user_id) {
        $select = $this->select()->where('user_id = ?', $user_id);
		$rows = $this->fetchAll($select);
		$players = array();
		foreach ($rows as $row) {
			$player = Engine_Api::_()->getItem('user_playercard', $row->player_id);
			if ($player) $players[] = $player;
		}
        return $players;
    }
	
	public function getPlayerEyeOns($player_id) {
		$select = $this->select()->from($this->info('name'), 'user_id');
		$select -> where('player_id = ?', $player_id);
		$rows = $select->query()->fetchAll();
		$userIds = array();
		foreach($rows as $row)
		{
			$userIds[] = $row['user_id'];
		}
		if (empty($userIds)) 
			return array();
		
		return Engine_Api::_()->user()->getUserMulti($userIds);
	}
}