<?php

class Tfcampaign_Model_DbTable_Saves extends Engine_Db_Table {
	
	public function getSaveRow($user_id, $campaign_id) {
		$select = $this -> select() 
						-> where('user_id = ?', $user_id)
						-> where('campaign_id = ?', $campaign_id)
						-> limit(1);
		return $this -> fetchRow($select);
	}
    
    public function getSavedCampaigns($uid) {
        $select = $this->select();
        $select -> where("user_id = ?", $uid)
				-> where("active = 1");
        return $this->fetchAll($select);
    }
}
