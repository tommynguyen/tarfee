<?php
class User_Model_DbTable_Sportmaps extends Engine_Db_Table {
	protected $_primary = 'map_id';
	
	public function getSportsOfUser($user_id, $limit = null) {
		$tblName = $this->info('name');
		$sportTbl = Engine_Api::_()->getItemTable('user_sportcategory');
		$sportTblName = $sportTbl->info('name');
		$select = $sportTbl->select()->setIntegrityCheck(false);
		$select -> from("$sportTblName as sport", "sport.*");
		$select -> joinLeft("$tblName as map", "map.sport_id = sport.sportcategory_id", "");
		$select 
			-> where("sport.parent_id = ?", '1')
			-> where("map.user_id = ?", $user_id)
			-> order("sport.title ASC");
		if($limit != 0) {
			$select -> limit($limit);
		}
		return $sportTbl->fetchAll($select);
	}
	
	public function getRow($user_id, $sport_id){
		$select = $this -> select() 
						-> where('user_id = ?', $user_id)
						-> where('sport_id = ?', $sport_id)
						-> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function deleteAllRows($user_id) {
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$db->delete($tableName, array(
			    'user_id = ?' => $user_id
			));
			$db -> commit();
			
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return $e;
		}
	}
	
	public function getAllUserHaveSport($sport_id) {
		$select = $this->select()->from($this->info('name'), 'user_id');
		$select->where('sport_id = ?', $sport_id);
		return $select->query()->fetchAll(FETCH_ASSOC, 0);
	}
}
