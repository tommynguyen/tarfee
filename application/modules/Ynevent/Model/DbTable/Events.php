<?php
class Ynevent_Model_DbTable_Events extends Engine_Db_Table
{
	protected $_rowClass = "Ynevent_Model_Event";
	protected $_name = 'event_events';

	public function getEventPaginator($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$select = $this -> getEventSelect($params);
		$events = $this -> fetchAll($select);
		$showedEvents = array();
		$auth = Engine_Api::_() -> authorization() -> context;
		foreach ($events as $event)
		{
			if ($auth -> isAllowed($event, $viewer, 'view'))
			{
				array_push($showedEvents, $event);
			}
		}
		return Zend_Paginator::factory($showedEvents);
	}

	public function getEventSelect($params = array())
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$viewer_id = $viewer -> getIdentity();
		$search = false;
		if (isset($params['search']))
			$search = true;

		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');
		
		$tags_table = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tags_name = $tags_table->info('name');
		
		$select = $table -> select();
		$userTable = Engine_Api::_() -> getDbTable("users", "user");
		$userTableName = $userTable -> info('name');

		//Get your location
		$target_distance = $base_lat = $base_lng = "";
		if(isset($params['lat']))
			$base_lat = $params['lat'];
		if(isset($params['long']))
			$base_lng = $params['long'];

		//Get target distance in miles
		if(isset($params['within']))
			$target_distance = $params['within'];

		$select -> setIntegrityCheck(false);
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance))
		{
			$select -> from("$eventTableName", array(
				"$eventTableName.*",
				"( 3959 * acos( cos( radians('$base_lat')) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( latitude ) ) ) ) AS distance"
			));
			$select -> where("latitude <> ''");
			$select -> where("longitude <> ''");
		}
		else
		{
			$select -> from("$eventTableName", array("$eventTableName.*"));
		}
		
		if( !empty($params['tag']) )
        {
          $select
            ->joinLeft($tags_name, "$tags_name.resource_id = $eventTableName.event_id","")
            ->where($tags_name.'.resource_type = ?', 'event')
            ->where($tags_name.'.tag_id = ?', $params['tag']);
        }
		$select -> join("$userTableName", "$userTableName.user_id = $eventTableName.user_id", '');
		if (isset($params['search']))
		{
			$select -> where("$eventTableName.search = $search OR ($eventTableName.parent_id = $viewer_id AND $eventTableName.parent_type = 'user')");
		}

		if (isset($params['owner']) && $params['owner'] instanceof Core_Model_Item_Abstract)
		{
			$select -> where("$eventTableName.user_id = ?", $params['owner'] -> getIdentity());
		}
		else
		if (isset($params['user_id']) && !empty($params['user_id']))
		{
			$select -> where("$eventTableName.user_id = ?", $params['user_id']);
		}
		else
		if (isset($params['users']) && is_array($params['users']) && $params['users'])
		{
			$users = array();
			foreach ($params['users'] as $user_id)
			{
				if (is_int($user_id) && $user_id > 0)
				{
					$users[] = $user_id;
				}
			}

			// if users is set yet there are none, $select will always return an empty rowset
			if (empty($users))
			{
				return $select -> where('1 != 1');
			}
			else
			{
				$select -> where("$eventTableName.user_id IN (?)", $users);
			}
		}
		//RSvp
		if (isset($params['events']) && is_array($params['events']))
		{
			$events = array();
			foreach ($params['events'] as $event_id)
			{
				if (is_int($event_id) && $event_id > 0)
				{
					$events[] = $event_id;
				}
			}
			if (!empty($events))
			{
				$select -> where("event_id IN (?)", $events);
			}
			else
			{
				$select -> where("1 = 0");
			}
		}
		// Category
		if (isset($params['arrayCat']) && !empty($params['arrayCat']))
		{
			$categories = array();
			foreach ($params['arrayCat'] as $category_id)
			{
				if (is_int($category_id) && $category_id > 0)
				{
					$categories[] = $category_id;
				}
			}
			if (!empty($categories))
			{
				$select -> where("category_id IN (?)", $categories);
			}
		}
		else if (isset($params['categories']) && !empty($params['categories']))
		{
			$categories = $params['categories'];
			if (!empty($categories))
			{
				$select -> where("category_id IN (?)", $categories);
			}
		}
		else
		{
			if (isset($params['category_id']) && !empty($params['category_id']))
			{
				$select -> where('category_id = ?', $params['category_id']);
			}
		}
		// Endtime
		if(empty($params['tag']))
		{
			if (!empty($params['past']) && (!isset($params['start_date']) || empty($params['start_date'])))
			{
				$select -> where("endtime <= FROM_UNIXTIME(?)", time());
			}
			elseif (!empty($params['future']) && (!isset($params['to_date']) || empty($params['to_date'])))
			{
				$select -> where("endtime > FROM_UNIXTIME(?)", time());				
			}
		}
		
		// Day selected
		if (isset($params['selected_day']) && !empty($params['selected_day']))
		{
			$fromdate = Engine_Api::_() -> ynevent() -> getFromDaySearch($params['selected_day']);
			$todate = Engine_Api::_() -> ynevent() -> getToDaySearch($params['selected_day']);
			$select -> where("starttime between '$fromdate' and '$todate' ");
		}

		//Admin form search
		if (isset($params['key_search']) && !empty($params['key_search']))
		{
			$key = $params['key_search'];
			$type = $params['type_search'];
			if ($type == 'owner')
			{
				$select -> where("$userTableName.displayname like ?", '%' . $key . '%');
			}
			else
			{
				$select -> where("$type like ?", '%' . $key . '%');
			}
		}

		// From date
		if (!empty($params['start_date']) && empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynevent() -> getFromDaySearch($params['start_date']);
			if (!$fromdate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectEventsFromDate($select, $fromdate);
		}

		// To date
		if (!empty($params['end_date']) && empty($params['start_date']))
		{
			$todate = Engine_Api::_() -> ynevent() -> getToDaySearch($params['end_date']);
			if (!$todate)
			{
				$select -> where("false");
				return $select;
			}
			$select = $this -> _selectEventsToDate($select, $todate);
		}

		if (!empty($params['start_date']) && !empty($params['end_date']))
		{
			$fromdate = Engine_Api::_() -> ynevent() -> getFromDaySearch($params['start_date']);
			$todate = Engine_Api::_() -> ynevent() -> getToDaySearch($params['end_date']);
			$select = $this -> _appendEventSelectInRange($select, $fromdate, $todate);
		}
		//Keywork
		if (isset($params['keyword']) && !empty($params['keyword']))
		{
			$select -> where("title like ?", '%' . $params['keyword'] . '%');
		}

		//Address
		if (isset($params['address']) && !empty($params['address']))
		{
			$select -> where("address like ?", '%' . $params['address'] . '%');
		}

		//Country
		if (isset($params['country']) && !empty($params['country']))
		{
			$select -> where("country like ?", '%' . $params['country'] . '%');
		}

		//City
		if (isset($params['city']) && !empty($params['city']))
		{
			$select -> where("city like ?", '%' . $params['city'] . '%');
		}

		//Zip code
		if (isset($params['zipcode']) && !empty($params['zipcode']) && empty($params['mile_of']))
		{
			$select -> where("zip_code = ?", $params['zipcode']);
		}

		if (isset($params['zipcode']) && !empty($params['zipcode']) && !empty($params['mile_of']))
		{
			$posArr = Engine_Api::_() -> ynevent() -> getPositionsAction($params['zipcode']);
			$whereClause = "(({$params['mile_of']} = 0 OR (3956 * 2 * 
                ASIN(SQRT(
		    POWER(SIN(({$posArr[0]} -ABS(latitude)) * PI()/180 / 2),2) +
		    COS({$posArr[0]} * PI()/180 )*
		    COS(ABS(latitude) *  PI()/180)*
		    POWER(SIN(({$posArr[1]} - longitude)*PI()/180/2), 2)
		    ) ) < {$params['mile_of']} ) ))";
			$select -> where($whereClause);
		}
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("$eventTableName.user_id NOT IN (?)", $deactiveIds);
		}
		
		// Order
		if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance))
		{
			$select -> having("distance <= $target_distance");
			$select -> order("distance ASC");
		}
		else
		{
			if (isset($params['order']) && !empty($params['order']))
			{
				if (isset($params['past']) && $params['past'] == 1)
				{
					$select -> order($params['order']);
				}
				else
				{
					$direction = ($params['direction'] != "") ? $params['direction'] : "DESC";
					$select -> order($params['order'] . " " . $direction);
				}
			}
			else
			{
				$select -> order('starttime DESC');
			}
		}
		if (!isset($params['isAdmin']))
		{
			$select -> group('repeat_group');
		}
		if (!empty($params['parent_type']))
		{
			$select -> where('parent_type = ?', $params['parent_type']);
		}

		if (!empty($params['parent_id']))
		{
			$select -> where('parent_id = ?', $params['parent_id']);
		}
		return $select;
	}

	public function getAllEventsInMonth($from, $to)
	{

		$tableName = $this -> info('name');
		$select = $this -> select() -> from($tableName, "$tableName.*") -> where("$tableName.starttime between '$from' and '$to'");
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("$tableName.user_id NOT IN (?)", $deactiveIds);
		}
		
		return $this -> fetchAll($select);
	}

	public function getEventDatesInMonth($select)
	{
		$this -> fetchAll($select);
	}

	public function getEventById($event_id)
	{
		$select = $this -> select();
		$select -> where("event_id=?", $event_id);
		return $this -> fetchRow($select);
	}

	public function getMyEventsInMonth($user_id, $from, $to)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');
		$select = $this -> select();

		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"date($eventTableName.starttime) as event_day",
			"date_format($eventTableName.starttime,'%h:%i %p') as event_time"
		)) -> join($membershipTableName, "$eventTableName.event_id = $membershipTableName.resource_id", '') -> where("$membershipTableName.user_id = ?", $user_id) -> where("$eventTableName.starttime between '$from' and '$to'") -> where("$membershipTableName.active = 1 and $membershipTableName.rsvp <> 0") -> order("$eventTableName.starttime");

		return $this -> fetchAll($select);
	}
	
	public function getAllEventsStartInDay($from, $to)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');
		$select = $this -> select();
		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"$membershipTableName.user_id as member_id"
		))
		-> join($membershipTableName, "$eventTableName.event_id = $membershipTableName.resource_id", '') 
		-> where("$membershipTableName.active = 1 and $membershipTableName.rsvp <> 0");
		$select -> where("$eventTableName.starttime between '$from' and '$to' ");
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("$eventTableName.user_id NOT IN (?)", $deactiveIds);
		}
		
		return $this -> fetchAll($select);
	}
	
	public function getAllEventsEndInDay($from, $to)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');
		$select = $this -> select();
		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"$membershipTableName.user_id as member_id"
		))
		-> join($membershipTableName, "$eventTableName.event_id = $membershipTableName.resource_id", '') 
		-> where("$membershipTableName.active = 1 and $membershipTableName.rsvp <> 0");
		$select -> where("$eventTableName.endtime between '$from' and '$to' ");
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("$eventTableName.user_id NOT IN (?)", $deactiveIds);
		}
		
		return $this -> fetchAll($select);
	}

	public function getRecurrenceInMonth($user_id, $event, $from, $to)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');
		$select = $this -> select();
		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"date($eventTableName.starttime) as event_day",
			"date_format($eventTableName.starttime,'%h:%i %p') as event_time"
		)) -> where("$eventTableName.starttime between '$from' and '$to'") -> order("$eventTableName.starttime");
		$select -> where("repeat_group=?", $event -> repeat_group);
		return $this -> fetchAll($select);
	}

	public function getRecurrence($user_id, $event)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');
		$select = $this -> select();
		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
				"$eventTableName.*",
				"date($eventTableName.starttime) as event_day",
				"date_format($eventTableName.starttime,'%h:%i %p') as event_time"
		))-> order("$eventTableName.starttime");
		$select -> where("repeat_group=?", $event -> repeat_group);
		return $this -> fetchAll($select);
	}
	
	private function _convertDayOfWeekToDB($num)
	{
		$val = $num + 1;
		if ($val > 7)
		{
			$val -= 7;
		}
		return $val;
	}

	private function _buildArray($from, $to, $limit)
	{
		$arr = array();
		for ($i = $from; $i <= $to; $i++)
		{
			array_push($arr, $i);
		}
		if ($to < $from)
		{
			for ($i = $to; $i <= $limit; $i++)
			{
				array_push($arr, $i);
			}
			for ($i = 1; $i <= $to; $i++)
			{
				array_push($arr, $i);
			}
		}

		return $arr;
	}

	private function _getDateDiff($date1, $date2)
	{
		$diff = abs($date2 - $date1);
		$days = ($diff) / (60 * 60 * 24);

		return intval(ceil($days));
	}

	private function _appendEventSelectInRange($select, $from, $to)
	{
		$eventTableName = $this -> info('name');
		$select -> where(sprintf("			
    			$eventTableName.starttime between '$from' and '$to'
    			OR ($eventTableName.endtime between '$from' and '$to'
    			OR ($eventTableName.starttime <= '$from' AND $eventTableName.endtime >= '$to'))"));

		return $select;
	}

	private function _selectEventsFromDate($select, $from)
	{
		$eventTableName = $this -> info('name');

		$select -> where("($eventTableName.endtime >= ?)", $from);

		return $select;
	}

	private function _selectEventsToDate($select, $todate)
	{
		$eventTableName = $this -> info('name');

		$select -> where("($eventTableName.starttime <= ?)", $todate);

		return $select;
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $from
	 * @param string $to
	 * @return array
	 */
	private function _buildDayOfWeekArray($from, $to)
	{
		$dateDiff = $this -> _getDateDiff(strtotime($from), strtotime($to));

		if ($dateDiff < 7)
		{
			$dayOfWeekBegin = intval(date('w', strtotime($from)));
			$dayOfWeekEnd = intval(date('w', strtotime($to)));
			$arr = $this -> _buildArray($dayOfWeekBegin + 1, $dayOfWeekEnd + 1, 7);
		}
		else
		{
			$arr = $this -> _buildArray(1, 7, 7);
		}
		return $arr;
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $from
	 * @param string $to
	 * @return array
	 */
	private function _buildDayOfMonthArray($from, $to)
	{
		$dateDiff = $this -> _getDateDiff(strtotime($from), strtotime($to));

		if ($dateDiff < intval(date('t', strtotime($from))))
		{
			$dayOfMonthBegin = intval(date('d', strtotime($from)));
			$dayOfMonthEnd = intval(date('d', strtotime($to)));
			$arr = $this -> _buildArray($dayOfMonthBegin, $dayOfMonthEnd, intval(date('t', strtotime($from))));
		}
		else
		{
			$arr = $this -> _buildArray(1, intval(date('t', strtotime($from))), intval(date('t', strtotime($from))));
		}
		return $arr;
	}

	public function getEvents($user_id, $from, $to)
	{
		$eventTableName = $this -> info('name');
		$membershipTable = Engine_Api::_() -> getDbTable("membership", "ynevent");
		$membershipTableName = $membershipTable -> info('name');

		$addedTable = Engine_Api::_() -> getDbTable("added", "ynevent");
		$addedTableName = $addedTable -> info('name');

		$select = $this -> select();
		$select -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"date($eventTableName.starttime) as event_day",
			"date_format($eventTableName.starttime,'%h:%i %p') as event_time"
		)) -> join($membershipTableName, "$eventTableName.event_id = $membershipTableName.resource_id", '') -> where("$membershipTableName.user_id = ?", $user_id) -> where("$membershipTableName.active = 1 and $membershipTableName.rsvp <> 0") -> order("$eventTableName.starttime");
		$this -> _appendEventSelectInRange($select, $from, $to);

		$select2 = $addedTable -> select();
		$select2 -> setIntegrityCheck(false) -> from($eventTableName, array(
			"$eventTableName.*",
			"date($eventTableName.starttime) as event_day",
			"date_format($eventTableName.starttime,'%h:%i %p') as event_time"
		)) -> join($addedTableName, "$eventTableName.event_id = $addedTableName.event_id", '') -> where("$addedTableName.user_id = ?", $user_id);
		$this -> _appendEventSelectInRange($select2, $from, $to);

		$allEvent = array();
		foreach ($this->fetchAll($select) as $event)
		{
			$allEvent[] = $event;
		}
		foreach ($this->fetchAll($select2) as $event)
		{
			$allEvent[] = $event;
		}

		return $allEvent;
	}

	public function getEventByLocation($location)
	{
		$select = $this -> select();
		$select -> where("city=?", $location);
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("user_id NOT IN (?)", $deactiveIds);
		}
		
		return $this -> fetchRow($select);
	}

	public function getRepeatEvent($repeat_groups)
	{
		$select = $this -> select();
		$select -> where("repeat_group=?", $repeat_groups);
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("user_id NOT IN (?)", $deactiveIds);
		}
		
		return $this -> fetchAll($select);
	}

	public function getGeneralCalendar($fromdate, $todate)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');

		// selector
		$select = $table -> select();

		// engine4_users
		$userTable = Engine_Api::_() -> getDbTable("users", "user");
		$userTableName = $userTable -> info('name');

		// define today
		$today = date('Y-m-d H:i:s');

		$select -> setIntegrityCheck(false) -> from("$eventTableName", array(
			"$eventTableName.event_id",
			"$eventTableName.starttime",
			"$eventTableName.endtime"
		)) -> join("$userTableName", "$userTableName.user_id = $eventTableName.user_id", array());
		$select -> where("$eventTableName.starttime between '$fromdate' and '$todate' ") -> where("$eventTableName.search = 1");
		Engine_Api::_() -> ynevent() -> log((string)$select, "get general event in month  $fromdate to $todate");
		return $this -> fetchAll($select);
	}

}
