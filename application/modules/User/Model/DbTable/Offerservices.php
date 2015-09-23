<?php
class User_Model_DbTable_Offerservices extends Engine_Db_Table 
{
    protected $_rowClass = 'User_Model_Offerservice';
    
    public function getAllOfferServicesOfUser($user_id) {
        $select = $this->select()->where('user_id = ?', $user_id)
        ->order('offerservice_id ASC');
        return $this->fetchAll($select);
    }
	
	public function getAllUserHaveService($service_id) {
		$select = $this->select()->from($this->info('name'), 'user_id');
		$select->where('service_id = ?', $service_id);
		return $select->query()->fetchAll(FETCH_ASSOC, 0);
	}
}
