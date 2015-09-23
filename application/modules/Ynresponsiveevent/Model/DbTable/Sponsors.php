<?php
class Ynresponsiveevent_Model_DbTable_Sponsors extends Engine_Db_Table
{
	protected $_rowClass = "Ynresponsiveevent_Model_Sponsor";
	protected $_name = 'ynresponsive1_sponsors';

	public function getSponsorPaginator($params = array())
	{
		$select = $this -> getSponsorSelect($params);
		return Zend_Paginator::factory($select);
	}

	public function getSponsorSelect($params = array())
	{
		$tableName = $this -> info('name');
		$select = $this -> select();
		$select -> from("$tableName", array("$tableName.*"));
		return $select;
	}
	public function checkEventById($event_id)
	{
		$select = $this -> select() -> where("event_id = ?", $event_id) -> limit(1);
		return $this -> fetchRow($select);
	}
}
