<?php
class User_Model_DbTable_Mappings extends Engine_Db_Table
{
  	protected $_name = 'user_mappings';
	
	public function getVideosPaginator($params = array()) {
        $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
	
	public function getRow($owner_id, $owner_type, $item_id, $item_type) {
		$select = $this -> select();
        $select -> where("owner_id = ?", $owner_id);
        $select -> where("owner_type = ?", $owner_type);
        $select -> where("item_id = ?", $item_id);
        $select -> where("item_type = ?", $item_type);
        $select -> limit(1);
		return $this->fetchRow($select);
	}
	
	public function getItemsMapping($type, $params = array())
	{
		$select = $this -> select();
		$select -> where("item_type = ?", $type);
        if (isset($params['owner_id'])) {
            $select -> where("owner_id = ?", $params['owner_id']);
        }
        if (isset($params['owner_type'])) {
            $select -> where("owner_type = ?", $params['owner_type']);
        }
        if (isset($params['user_id'])) {
            $select -> where("user_id = ?", $params['user_id']);
        }
        if (isset($params['parent_id'])) {
            $select -> where("parent_id = ?", $params['parent_id']);
        }
        $select -> order("creation_date DESC");
		$mappings = $this->fetchAll($select);
		return $mappings;
	}
	
	public function getItemIdsMapping($type, $params = array())
	{
		$select = $this -> select() -> from($this, new Zend_Db_Expr("`item_id`"));
		$select -> where("item_type = ?", $type);
        if (isset($params['owner_id'])) {
            $select -> where("owner_id = ?", $params['owner_id']);
        }
		if (isset($params['owner_ids'])) {
            $select -> where("owner_id IN (?)", $params['owner_ids']);
        }
        if (isset($params['owner_type'])) {
            $select -> where("owner_type = ?", $params['owner_type']);
        }
        if (isset($params['user_id'])) {
            $select -> where("user_id = ?", $params['user_id']);
        }
        $select -> order("creation_date DESC");
		$mapping_ids = $this->fetchAll($select);
		$ids = array();
		foreach($mapping_ids as $mapping_id)
		{
			$ids[] = $mapping_id -> item_id;
		}
		return $ids;
	}
	
    public function getVideosSelect($params = array()) {
        $table = Engine_Api::_()->getItemTable('video');
        $rName = $table->info('name');
        $select = $table->select()->from($rName)->setIntegrityCheck(false);
		
		$mappings_p = $params;
        if (isset($mappings_p['user_id'])) unset($mappings_p['user_id']);
		$ids = $this -> getItemIdsMapping('video', $mappings_p);
		if (!empty($ids) && count($ids) > 0) {
            $select->where('video_id IN (?)', $ids);
            
			if (!empty($params['user_only'])) {
				 $select->where('parent_type <> ?', 'group');
			}
        }
		else {
			$select->where('video_id = 0');
		}
		
        $select->order("$rName.creation_date DESC");
        return $select;
    }
	
	public function deleteItem($params = array()){
		$tableName = $this -> info('name');
		$db = $this -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$row = $this -> getRow($params['owner_id'], $params['owner_type'], $params['item_id'], $params['item_type']);
			if($row) {
				$row -> delete();
			}
			$db -> commit();
			
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			return $e;
		}
		return "true";
	}
	public function getTotalVideo($params = array())
	{
	    $select = new Zend_Db_Select($this->getAdapter());
	    $select->from($this->info('name'), 'COUNT(*) AS count');
			
		$select -> where("item_type = ?", 'video');
        if (isset($params['owner_id'])) {
            $select -> where("owner_id = ?", $params['owner_id']);
        }
        if (isset($params['owner_type'])) {
            $select -> where("owner_type = ?", $params['owner_type']);
        }
		$select -> where("`item_id` IN (SELECT `video_id` FROM `engine4_video_videos`)");
	    return $select->query()->fetchColumn(0);
	}
	
	public function getTotalVideoComment($params = array())
	{
		$table = Engine_Api::_()->getItemTable('video');
        $rName = $table->info('name');
	    $select = new Zend_Db_Select($table->getAdapter());
	    $select->from($rName, "SUM($rName.comment_count) AS count");
		$ids = $this -> getItemIdsMapping('video', $params);
		if (!empty($ids) && count($ids) > 0) {
            $select->where('video_id IN (?)', $ids);
        }
		else {
			$select->where('video_id = 0');
		}
	    return $select->query()->fetchColumn(0);
	}
	
	public function getTotalVideoView($params = array())
	{
		$table = Engine_Api::_()->getItemTable('video');
        $rName = $table->info('name');
	    $select = new Zend_Db_Select($table->getAdapter());
	    $select->from($rName, "SUM($rName.view_count) AS count");
		$ids = $this -> getItemIdsMapping('video', $params);
		if (!empty($ids) && count($ids) > 0) {
            $select->where('video_id IN (?)', $ids);
        }
		else {
			$select->where('video_id = 0');
		}
	    return $select->query()->fetchColumn(0);
	}
}