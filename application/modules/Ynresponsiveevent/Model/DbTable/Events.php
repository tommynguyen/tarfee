<?php
class Ynresponsiveevent_Model_DbTable_Events extends Engine_Db_Table
{
	protected $_rowClass = "Ynresponsiveevent_Model_Event";
	protected $_name = 'ynresponsive1_events';

	public function getEventPaginator($params = array())
	{
		$select = $this -> getEventSelect($params);
		$events = $this -> fetchAll($select);
		return Zend_Paginator::factory($events);
	}

	public function getEventSelect($params = array())
	{
		$tableName = $this -> info('name');
		$eventTableName = Engine_Api::_() -> getItemTable('event') -> info('name');
		$select = $this -> select() -> from($tableName) -> join($eventTableName, "$eventTableName.event_id = $tableName.event_id", "");

		//Admin form search
		if (isset($params['key_search']) && !empty($params['key_search']))
		{
			$key = $params['key_search'];
			$select -> where("$tableName.title like ?", '%' . $key . '%');
		}
		// From date
		if (!empty($params['start_date']) && empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynresponsive1() -> getFromDaySearch($params['start_date']);
			if (!$fromdate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectEventsFromDate($select, $fromdate, $params['date_search']);
		}
		// To date
		if (!empty($params['end_date']) && empty($params['start_date']))
		{
			$todate = Engine_Api::_() -> ynresponsiveevent() -> getToDaySearch($params['end_date']);
			if (!$todate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectEventsToDate($select, $todate, $params['date_search']);
		}
		if (!empty($params['start_date']) && !empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynresponsiveevent() -> getFromDaySearch($params['start_date']);
			$todate = Engine_Api::_() -> ynresponsiveevent() -> getToDaySearch($params['end_date']);
			$select = $this -> _appendEventSelectInRange($select, $fromdate, $todate, $params['date_search']);
		}

		if (isset($params['order']) && !empty($params['order']))
		{
			$direction = ($params['direction'] != "") ? $params['direction'] : "DESC";
			$select -> order($params['order'] . " " . $direction);
		}
		else
		{
			$select -> order('starttime DESC');
		}
		return $select;
	}

	public function getEventById($event_id)
	{
		$select = $this -> select();
		$select -> where("event_id=?", $event_id);
		return $this -> fetchRow($select);
	}
	
	private function _appendEventSelectInRange($select, $from, $to, $date_search)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');
		$eventName = $this -> info('name');
		$select -> joinLeft($table, "$eventTableName.event_id = $eventName.event_id", "");
		$select -> where(sprintf("$eventTableName.$date_search between '$from' and '$to'"));
		return $select;
	}

	private function _selectEventsFromDate($select, $from, $date_search)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');
		$eventName = $this -> info('name');
		$select -> joinLeft($table, "$eventTableName.event_id = $eventName.event_id", "");
		$select -> where("($eventTableName.$date_search >= ?)", $from);
		return $select;
	}

	private function _selectEventsToDate($select, $todate, $date_search)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');
		$eventName = $this -> info('name');
		$select -> joinLeft($table, "$eventTableName.event_id = $eventName.event_id", "");
		$select -> where("($eventTableName.$date_search <= ?)", $todate);
		return $select;
	}
}
