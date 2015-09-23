<?php
class Ynevent_Model_DbTable_Mappings extends Engine_Db_Table
{
  	protected $_name = 'ynevent_mappings';
	
	public function getAlbumsPaginator($params = array())
    {
   	 	return Zend_Paginator::factory($this->getAlbumsSelect($params));
    }
  	
	public function getVideoIdsMapping()
	{
		$select = $this -> select() -> from($this, new Zend_Db_Expr("`item_id`"));
		$select -> where("type = 'video'");
		return $this->fetchAll($select);
	}
	
	public function deleteItem($params = array()){
		$table = Engine_Api::_()->getItemTable('ynevent_mapping');
		$tableName = $table->info('name');
		$db = Engine_Api::_() -> getDbtable('mappings', 'ynevent') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$db->delete($tableName, array(
			    'type = ?' => $params['type'],
			    'item_id = ?' => $params['item_id']
			));
			$db -> commit();
			
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return $e;
		}
		return "true";
	}
}