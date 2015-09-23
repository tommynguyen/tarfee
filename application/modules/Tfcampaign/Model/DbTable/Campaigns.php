<?php
class Tfcampaign_Model_DbTable_Campaigns extends Engine_Db_Table {
	
	protected $_rowClass = 'Tfcampaign_Model_Campaign';
	
	public function getCampaignsByUser($user, $limit = null, $onlyUser = false) {
		$select = $this -> select();
		$select -> where('user_id = ?', $user -> getIdentity())
				-> where('deleted <> ?', '1');
		if(isset($limit))
			$select -> limit($limit);
		if ($onlyUser) {
			$select->where('parent_type = ?', 'user');
		}
		return $this -> fetchAll($select);
	}
	
	public function getCampaignsByClub($club, $limit = null) {
		$select = $this -> select();
		$select -> where('parent_id = ?', $club -> getIdentity())
				-> where('parent_type = ?', 'group')
				-> where('deleted <> ?', '1');
		if(isset($limit))
			$select -> limit($limit);
		return $this -> fetchAll($select);
	}
	
	public function getCampaignsTotal($user) {
		$select = $this -> select();
    	$select -> from($this->info('name'), 'COUNT(*) AS count')
				-> where('user_id = ?', $user -> getIdentity())
				-> where('deleted <> ?', '1');
    	return $select->query()->fetchColumn(0);
	}
	
	public function getLastestCampaign($user) {
		$select = $this -> select() 
						-> where('user_id = ?', $user -> getIdentity())
						-> order('campaign_id DESC')
						-> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getCampaignsPaginator($params = array()) 
    {
        return Zend_Paginator::factory($this->getCampaignsSelect($params));
    }
  
    public function getCampaignsSelect($params = array()) {
    	$campaignTbl = Engine_Api::_() -> getItemTable('tfcampaign_campaign');
    	$campaignTblName = $campaignTbl -> info('name');


    	$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
    	$userTblName = $userTbl -> info('name');

    	$categoryTbl = Engine_Api::_() -> getItemTable('user_sportcategory');
    	$categoryTblName = $categoryTbl -> info('name');

		$select = $this -> select();
		$select -> from("$campaignTblName as campaign", "campaign.*");
		
    	$select
    	-> joinLeft("$userTblName as user", "user.user_id = campaign.user_id", "")
    	-> joinLeft("$categoryTblName as category", "category.sportcategory_id = campaign.category_id", "");

    	$select->group("campaign.campaign_id");
    	$tmp = array();

    	if (isset($params['title']) && $params['title'] != '') 
    	{
    		$select->where('campaign.title LIKE ?', '%'.$params['title'].'%');
    	}
    	
    	if (isset($params['owner']) && $params['owner'] != '') 
    	{
    		$select->where('user.displayname LIKE ?', '%'.$params['owner'].'%');
    	}
    	
		if (isset($params['from_age']) && $params['from_age'] != '' && isset($params['to_age']) && $params['to_age'] != '') {
			if(is_numeric($params['to_age']) && is_numeric($params['from_age'])) {
				$sql = "			
	    			campaign.from_age between '".$params['from_age']."' and '".$params['to_age']."'
	    			OR campaign.to_age between '".$params['from_age']."' and '".$params['to_age']."'
					OR  (campaign.from_age < '".$params['from_age']."' AND campaign.to_age > '".$params['to_age']."')
				";
				$select -> where($sql);
			} else {
				$select -> where("1 = 0");
			}
		}
		
		$sysTimezone = date_default_timezone_get();
        if (isset($params['start_date_from']) && $params['start_date_from'] != '') 
        {
            $from_date = new Zend_Date(strtotime($params['start_date_from']));
			$from_date->setTimezone($sysTimezone);
			$select->where('campaign.start_date >= ?', $from_date->get('yyyy-MM-dd'));
        }
		
		if (isset($params['start_date_to']) && $params['start_date_to'] != '') 
        {
            $to_date = new Zend_Date(strtotime($params['start_date_to']));
			$to_date->setTimezone($sysTimezone);
			$select->where('campaign.start_date <= ?', $to_date->get('yyyy-MM-dd'));
        }
		
    	if (!empty($params['category_id']) && $params['category_id'] != 'all') 
    	{
			$select->where('campaign.category_id IN (?)', $params['category_id']);
    	}
		
    	if(isset($params['user_id'])) 
    	{
    		$select->where('campaign.user_id = ?', $params['user_id']);
    	}
		
		if(isset($params['country_id']) && $params['country_id'] != '0') 
    	{
    		$select->where('campaign.country_id = ?', $params['country_id']);
    	}
		
		if(isset($params['province_id']) && $params['province_id'] != '0') 
    	{
    		$select->where('campaign.province_id = ?', $params['province_id']);
    	}
		
		if(isset($params['city_id']) && $params['city_id'] != '0') 
    	{
    		$select->where('campaign.city_id = ?', $params['city_id']);
    	}

		if (!empty($params['gender'])) {
			$select->where('campaign.gender = ?', $params['gender']);
		}
    	
		$select -> where('campaign.deleted <> 1');
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where('campaign.user_id NOT IN (?)', $deactiveIds);
		}
		
		if (isset($params['order'])) 
    	{
    		if (isset($params['direction'])) 
    		{
    			$select->order($params['order'].' '.$params['direction']);
    		}
    		else 
    		{
    			$select->order($params['order'].' '.'DESC');
    		}
    	}
    	else 
    	{
    		if (isset($params['direction'])) 
    		{
    			$select->order('campaign.campaign_id'.' '.$params['direction']);
    		}
			else 
			{
				$select->order('campaign.campaign_id ASC');
			}
    	}
    	return $select;
    }
}
