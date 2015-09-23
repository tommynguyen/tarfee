<?php
class User_Model_DbTable_Playercards extends Engine_Db_Table 
{
	protected $_rowClass = 'User_Model_Playercard';
	
	public function getAllPlayers() {
		$select = $this -> select();
		return $this -> fetchAll($select);
	}
	
	public function getPlayersPaginator($user_id = 0, $onlyUser = false)
	{
		$select = $this -> select();
		$select -> where('user_id = ?', $user_id)
				-> order('creation_date DESC');
		if ($onlyUser) {
			$select -> where('parent_type = ?', 'user');
		}
		$paginator = Zend_Paginator::factory($select);
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	
	public function getClubPlayersPaginator($club) {
		$select = $this -> select();
		$select -> where('parent_id = ?', $club->getIdentity())
				-> where('parent_type = ?', 'group')
				-> order('creation_date DESC');
				
		$paginator = Zend_Paginator::factory($select);
	    if( !empty($params['page']) )
	    {
	      $paginator->setCurrentPageNumber($params['page']);
	    }
	    if( !empty($params['limit']) )
	    {
	      $paginator->setItemCountPerPage($params['limit']);
	    }
	    return $paginator;
	}
	
	public function getAllPlayerCard($user_id) {
		$select = $this -> select();
		$select -> where('user_id = ?', $user_id)
				-> order('creation_date DESC');
		return $this -> fetchAll($select);
	}
	
	public function getTotal($user_id = 0)
	{
    	$select = new Zend_Db_Select($this->getAdapter());
    	$select -> from($this->info('name'), 'COUNT(*) AS count')
				-> where('user_id = ?', $user_id);
    	return $select->query()->fetchColumn(0);
	}
}
