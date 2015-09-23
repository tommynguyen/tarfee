<?php
class Tfcampaign_Model_DbTable_Submissions extends Engine_Db_Table {
	
	protected $_rowClass = 'Tfcampaign_Model_Submission';
	
	public function getCampaignIdsSubmitted($user) {
		$select = $this -> select()
						-> distinct()
						-> from($this -> info('name'), 'campaign_id')
						-> where('user_id = ?', $user -> getIdentity());
		return $select->query()->fetchAll(FETCH_ASSOC, 0);
	}					
	
	public function getSubmissionIdsSubmitted($user, $campaign = null) {
		$select = $this -> select()
						-> distinct()
						-> from($this -> info('name'), 'submission_id')
						-> where('user_id = ?', $user -> getIdentity());
		if(!empty($campaign)) {
			$select -> where("campaign_id = ?", $campaign -> getIdentity());
		}				
		return $select->query()->fetchAll(FETCH_ASSOC, 0);
	}		
	
	public function getSubmissionsPaginator($params = array()) 
    {
        return Zend_Paginator::factory($this->getSubmissionsSelect($params));
    }
  	
    public function getSubmissionsSelect($params = array()) {
    	
		$submissionTbl = $this;
		$submissionTblName = $submissionTbl -> info('name');

		$playerTbl = Engine_Api::_() -> getItemTable('user_playercard');
		$playerTblName = $playerTbl -> info('name');
		
		$locationTbl = Engine_Api::_() -> getDbTable('locations', 'user');
		$locationTblName = $locationTbl -> info('name');
		
		$select = $submissionTbl -> select();
		$select -> setIntegrityCheck(false);

		$select -> from("$submissionTblName as submission", "submission.*");
		
		$select -> joinLeft("$playerTblName as player", "submission.player_id = player.playercard_id", null) ;
		$select -> joinLeft("$locationTblName as location", "location.location_id = player.country_id", null) ;
        $select -> group("submission.submission_id");
        
		if(isset($params['campaign_id'])) {
			$select -> where('submission.campaign_id = ?', $params['campaign_id']);
		}
		
		if (isset($params['order'])) {
	        if (empty($params['direction'])) {
	            $params['direction'] = ($params['order'] == 'submission.submission_id') ? 'DESC' : 'ASC';
	        }
            $select->order($params['order'].' '.$params['direction']);
		}
		else {
	        if (!empty($params['direction'])) {
	            $select->order('submission.submission_id'.' '.$params['direction']);
	        }
			else{
				$select->order('submission.submission_id DESC');
			}
	    }
		if(isset($params['hided'])) {
			$select -> where('hided = ?', $params['hided']);
		} else {
			$select -> where('hided = 0');
		}
    	return $select;
    }
	
}
