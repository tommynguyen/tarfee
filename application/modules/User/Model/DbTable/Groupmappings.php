<?php
class User_Model_DbTable_GroupMappings extends Engine_Db_Table
{
  	protected $_name = 'user_groupmappings';
	
	public function getRow($user_id, $group_id){
		$select = $this -> select() 
						-> where('user_id = ?', $user_id)
						-> where('group_id = ?', $group_id)
						-> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getGroupByUser($user_id, $limit = 0) {
		$select = $this -> select() 
						-> where('user_id = ?', $user_id);
		if($limit)
		{
			$select -> limit($limit);
		}
		return $this -> fetchALl($select);
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
}