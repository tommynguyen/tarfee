<?php

class User_Model_DbTable_UserItemView extends Engine_Db_Table {

    protected $_name = 'user_itemviews';

    public function getRow($user_id, $item_id, $item_type){
		$select = $this -> select() 
						-> where('user_id = ?', $user_id)
						-> where('item_id = ?', $item_id)
						-> where('item_type = ?', $item_type)
						-> limit(1);
		return $this -> fetchRow($select);
	}
	
	public function getUserByItem($item, $limit = 0) {
		$select = $this -> select() 
						-> where('item_id = ?', $item -> getIdentity())
						-> where('item_type = ?', $item -> getType());
		if($limit)
		{
			$select -> limit($limit);
		}
		return $this -> fetchALl($select);
	}
	
	public function deleteAllRows($item) {
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$db->delete($tableName, array(
			    'item_id = ?' => $item -> getIdentity(),
			    'item_type = ?' => $item -> getType(),
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