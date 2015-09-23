<?php

class Ynevent_Api_Core extends Core_Api_Abstract
{

	public function subPhrase($string, $length = 0)
	{
		if (strlen($string) <= $length)
			return $string;
		$pos = $length;
		for ($i = $length - 1; $i >= 0; $i--)
		{
			if ($string[$i] == " ")
			{
				$pos = $i + 1;
				break;
			}
		}
		return substr($string, 0, $pos) . "...";
	}

	public function getItemTable($type)
	{
		if ($type == 'event_album')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Albums');
		}
		else
		if ($type == 'event_category')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Categories');
		}
		else
		if ($type == 'event_post')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Posts');
		}
		else
		if ($type == 'event_topic')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Topics');
		}
		else
		if ($type == 'event_photo')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Photos');
		}
		else
		if ($type == 'event_sponsor')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Sponsors');
		}
		else
		if ($type == 'event_agent')
		{
			return Engine_Loader::getInstance() -> load('Ynevent_Model_DbTable_Agents');
		}
		else
		{
			$class = Engine_Api::_() -> getItemTableClass($type);
			return Engine_Api::_() -> loadClass($class);
		}
	}

	public function checkRated($event_id, $user_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynevent');

		$rName = $table -> info('name');
		$select = $table -> select() -> setIntegrityCheck(false) -> where('event_id = ?', $event_id) -> where('user_id = ?', $user_id) -> limit(1);
		$row = $table -> fetchAll($select);

		if (count($row) > 0)
			return true;
		return false;
	}

	public function setRating($event_id, $user_id, $rating)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynevent');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.event_id = ?', $event_id) -> where($rName . '.user_id = ?', $user_id);
		$row = $table -> fetchRow($select);
		if (empty($row))
		{
			// create rating
			Engine_Api::_() -> getDbTable('ratings', 'ynevent') -> insert(array(
				'event_id' => $event_id,
				'user_id' => $user_id,
				'rating' => $rating
			));
		}
		/*
		 $select = $table->select()
		 //->setIntegrityCheck(false)
		 ->from($rName)
		 ->where($rName.'.video_id = ?', $video_id);

		 $row = $table->fetchAll($select);
		 $total = count($row);
		 foreach( $row as $item )
		 {
		 $rating += $item->rating;
		 }
		 $video = Engine_Api::_()->getItem('video', $video_id);
		 $video->rating = $rating/$total;
		 $video->save(); */
	}

	public function ratingCount($event_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynevent');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.event_id = ?', $event_id);
		$row = $table -> fetchAll($select);
		$total = count($row);
		return $total;
	}

	public function getRating($event_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynevent');
		$rating_sum = $table -> select() -> from($table -> info('name'), new Zend_Db_Expr('SUM(rating)')) -> group('event_id') -> where('event_id = ?', $event_id) -> query() -> fetchColumn(0);

		$total = $this -> ratingCount($event_id);
		if ($total)
			$rating = $rating_sum / $this -> ratingCount($event_id);
		else
			$rating = 0;

		return $rating;
	}

	public function getRatings($event_id)
	{
		$table = Engine_Api::_() -> getDbTable('ratings', 'ynevent');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.event_id = ?', $event_id);
		$row = $table -> fetchAll($select);
		return $row;
	}

	public function setEventFollow($subject, $viewer)
	{
		$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
		$row = $table -> getFollowEvent($subject -> event_id, $viewer -> getIdentity());
		if (!$row)
		{
			$values = array(
				'resource_id' => $subject -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'follow' => 1
			);
			$row = $table -> createRow();
			$row -> setFromArray($values);
			$row -> save();
			/*
			 $db = $table->getAdapter();
			 $db->beginTransaction();
			 try {
			 $row->save();
			 $db->commit();
			 } catch (Exception $e) {
			 $db->rollBack();
			 throw $e;
			 }
			 */
		}
	}

	public function chkEventFollow($event_id)
	{
		$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
		$rName = $table -> info('name');
		$select = $table -> select() -> from($rName) -> where($rName . '.resource_id = ?', $event_id);

		$row = $table -> fetchAll($select);
		$total = count($row);
		return $total;
	}

	public function getToDaySearch($day)
	{
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();
		//user time zone
		date_default_timezone_set($user_tz);
		$d_temp = strtotime($day);
		if ($d_temp == false)
		{
			return null;
		}
		$toDateObject = new Zend_Date(strtotime($day));

		$toDateObject -> add('1', Zend_Date::DAY);
		$toDateObject -> sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);
		$toDateObject -> setTimezone(date_default_timezone_get());
		return $todate = $toDateObject -> get('yyyy-MM-dd HH:mm:ss');
	}

	public function getFromDaySearch($day)
	{
		$day = $day . " 00:00:00";
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;

		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$start = strtotime($day);
		date_default_timezone_set($oldTz);
		$fromdate = date('Y-m-d H:i:s', $start);
		return $fromdate;
	}
	
	public function getDateSearch($month, $year)
	{

		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($user_tz);
		$first_date = $year . '-' . $month . '-01';
		$firstDateObject = new Zend_Date(strtotime($first_date));
		$lastDateObject = new Zend_Date(strtotime($first_date));
		$lastDateObject -> add('1', Zend_Date::MONTH);
		$lastDateObject -> sub('1', Zend_Date::SECOND);
		date_default_timezone_set($oldTz);

		// convert to server time zone to search in database
		$firstDateObject -> setTimezone(date_default_timezone_get());
		$lastDateObject -> setTimezone(date_default_timezone_get());
		$first_date = $firstDateObject -> get('yyyy-MM-dd HH:mm:ss');
		$last_date = $lastDateObject -> get('yyyy-MM-dd HH:mm:ss');
		$date_search = array(
			$first_date,
			$last_date
		);
		return $date_search;
	}

	public function checkYouNetPlugin($name)
	{
		$table = Engine_Api::_() -> getDbTable('modules', 'core');
		$select = $table -> select() -> where('name = ?', $name) -> where('enabled  = 1');
		$result = $table -> fetchRow($select);
		if ($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function getCurrentHost()
	{
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$parts = parse_url($currentUrl);
		// use port if non default
		$port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';
		$path = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'default');
		$path = str_replace("index.php/", "", $path);
		$currentHostSite = $protocol . $parts['host'] . $port;
		return $currentHostSite;
	}

	public function getPositionsAction($zipcode)
	{
		$lat = $lng = 0;
		// Get lat long from google app
		$url = (_ENGINE_SSL) ? "https://" : "http://";
		$url .= "maps.googleapis.com/maps/api/geocode/json?address=$zipcode&sensor=true";

		$data = array(
			"lang" => "en-GB",
			"fhrsid" => "80928"
		);
		$data_string = json_encode($data);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string)
		));

		$latlng = curl_exec($ch);
		curl_close($ch);
		$latlng = Zend_Json::decode($latlng);

		if (!empty($latlng) && $latlng["status"] == "OK")
		{
			$lat = (float)$latlng["results"][0]["geometry"]["location"]["lat"];
			$lng = (float)$latlng["results"][0]["geometry"]["location"]["lng"];
		}

		// the lattitude and longitude are not found from the zipcode
		return array(
			$lat,
			$lng
		);
	}

	function dateDiff($start, $end)
	{
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);
		$diff = $end_ts - $start_ts;
		return round($diff / 86400);
	}

	function dateDiffBySec($start, $end)
	{
		$start_ts = strtotime($start);
		$end_ts = strtotime($end);
		return $end_ts - $start_ts;
	}

	function dateAdd($date, $day)
	{
		$date = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " +$day day");
		return date('Y-m-d H:i:s', $date);
	}

	function dateAddBySec($date, $sec)
	{
		$date = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " +$sec sec");
		return date('Y-m-d H:i:s', $date);
	}

	function monthAdd($date, $month)
	{
		$date = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " +$month month");
		return date('Y-m-d H:i:s', $date);
	}

	function dateValidate($date)
	{
		return true;
	}

	public function getGroupMember($member_id, $text = '')
	{
		$select = NULL;
		if ($this -> getPlugins() == 'advgroup')
		{
			$table = Engine_Api::_() -> getDbTable('membership', 'advgroup');
			$select = $table -> getMembershipsOfSelect(Engine_Api::_() -> user() -> getUser($member_id));
		}
		elseif ($this -> getPlugins() == 'group')
		{
			$table = Engine_Api::_() -> getDbTable('membership', 'group');
			$select = $table -> getMembershipsOfSelect(Engine_Api::_() -> user() -> getUser($member_id));
		}
		$groupTbl = Engine_Api::_() -> getItemTable('group');
		if (!empty($text))
		{

			$groupTblName = $groupTbl -> info('name');
			$select -> where("$groupTblName.title LIKE ?", "%$text%");
		}
		if ($select)
			return $groupTbl -> fetchAll($select);
		return null;
	}

	public $arrPlugins = array(
		'advgroup' => 'advgroup',
		'group' => 'group',
	);
	private $arrYNPlugins = array('advgroup' => 'advgroup', );

	public function getPlugins()
	{
		$table = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $table -> select() -> where('enabled = ?', 1) -> where('name in (?)', array_keys($this -> arrPlugins));
		$results = $table -> fetchAll($mselect);
		$arr = array();

		$arrayNoplugins = array();
		foreach ($results as $result)
		{
			if (array_key_exists($result -> name, $this -> arrYNPlugins))
			{

				$arr = $this -> arrPlugins[$result -> name];
			}
			else
				$arr = $this -> arrPlugins[$result -> name];
		}
		return $arr;
	}

	public function getDistance($lat1, $lon1, $lat2, $lon2)
	{
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		return $miles;
	}

	public function changeDateFormat($date, $format)
	{
		if (preg_match('/^(\d+)-(\d+)-(\d+)$/', $date, $m))
		{
			array_shift($m);
			$year = $m[stripos($format, 'y')];
			$month = $m[stripos($format, 'm')];
			$day = $m[stripos($format, 'd')];
			$formatString = '%1$04d-%3$02d-%2$02d';
			$valueString = sprintf($formatString, $year, $day, $month);
			return $valueString;
		}
		if (preg_match('/^(\d+)-(\d+)-(\d+)( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $date, $m))
		{
			array_shift($m);
			$year = $m[stripos($format, 'y')];
			$month = $m[stripos($format, 'm')];
			$day = $m[stripos($format, 'd')];
			$hour = @$m[4];
			$minute = @$m[5];
			$second = $m[6];

			$formatString = '%1$04d-%2$02d-%3$02d';
			if (null !== $hour && null !== $minute)
			{
				$formatString .= ' %4$02d:%5$02d:%6$02d';
			}
			$valueString = sprintf($formatString, $year, $month, $day, $hour, $minute, $second);

			return $valueString;
		}
	}

	/**
	 * get timezone offset from current viewer and UTC
	 * @return int [-12, +12]
	 */
	public function getTimezoneOffset()
	{
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		//user time zone
		date_default_timezone_set($user_tz);

		$t1 = strtotime('2010-10-10 00:00:00');
		date_default_timezone_set('UTC');
		$t2 = strtotime('2010-10-10 00:00:00');
		return (int)(($t2 - $t1) / 3600);
	}

	protected static $_log;

	/**
	 * get main logger
	 * @return Zend_Log
	 */
	public function getLog()
	{
		if (self::$_log == null)
		{
			self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/event.txt'));
		}
		return self::$_log;
	}

	/**
	 * write log to temporary/ynevent.log
	 * @param string $intro
	 * @param string $message
	 * @param int   $type
	 * @return Zend_Log
	 */
	public function log($message, $intro = null, $type = NULL)
	{
		if (is_string($message))
		{

		}
		else
		if (is_object($message))
		{
			$message = (string)$message;
		}
		else
		{
			$message = var_export($message, true);
		}

		if ($type == null)
		{
			$type = Zend_Log::INFO;
		}

		if ($intro == NULL)
		{
			$intro = 'Event';
		}
		return $this -> getLog() -> log(PHP_EOL . $intro . PHP_EOL . $message, $type);
	}
	public function getVideosPaginator($params = array())
    {
	    $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
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
	public function getVideosSelect($params = array())
    {
	    $table = Engine_Api::_()->getItemTable('video');
	    $rName = $table->info('name');
	    
	    $select = $table->select()->from($table->info('name'))-> setIntegrityCheck(false);
		$select -> joinLeft("engine4_ynevent_highlights", "engine4_ynevent_highlights.item_id = $rName.video_id  AND engine4_ynevent_highlights.type = 'video'" , "engine4_ynevent_highlights.highlight");
	    $select->order("engine4_ynevent_highlights.highlight DESC"  );
	   
	    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
	    {
	      $select->where("$rName.owner_id = ?", $params['user_id']);
	    }
		if (!empty($params['parent_type'])) {
            $select->where("$rName.parent_type = ?", $params['parent_type']);
        }

        if (!empty($params['parent_id'])) {
            $select->where("$rName.parent_id = ?", $params['parent_id']);
        }
        
	    return $select;
    }
    
    public function getLocaleNumber($localeStr)
    {
    	$locale = array(
    			'US/Pacific'  => '-08:00',
    			'US/Mountain' => '-07:00',
    			'US/Central'  => '-06:00',
    			'US/Eastern'  => '-05:00',
    			'America/Halifax'   => '-04:00',
    			'America/Anchorage' => '-09:00',
    			'Pacific/Honolulu'  => '-10:00',
    			'Pacific/Samoa'     => '-11:00',
    			'Etc/GMT-12' => '-12:00',
    			'Canada/Newfoundland' => '-03:30',
    			'America/Buenos_Aires' => '-03:00',
    			'Atlantic/South_Georgia' => '-02:00',
    			'Atlantic/Azores' => '-01:00',
    			'Europe/London' => '+00:00',
    			'Europe/Berlin' => '+01:00',
    			'Europe/Athens' => '+02:00',
    			'Europe/Moscow' => '+03:00',
    			'Iran' => '+03:30',
    			'Asia/Dubai' => '+04:00',
    			'Asia/Kabul' => '+04:30',
    			'Asia/Yekaterinburg' => '+05:00',
    			'Asia/Calcutta' => '+05:30',
    			'Asia/Katmandu' => '+05:45',
    			'Asia/Omsk' => '+06:00',
    			'India/Cocos' => '+06:30',
    			'Asia/Krasnoyarsk' => '+07:00',
    			'Asia/Hong_Kong' => '+08:00',
    			'Asia/Tokyo' => '+09:00',
    			'Australia/Adelaide' => '+09:30',
    			'Australia/Sydney' => '+10:00',
    			'Asia/Magadan' => '+11:00',
    			'Pacific/Auckland' => '+12:00',
    	);
    	if (isset($locale[$localeStr]))
    	{
    		return $locale[$localeStr];
    	}
    	else
    	{
    		return '+00:00';
    	}
    		 
    }
	
    public function getDateStringForCalendar($strDatetime)
    {
    	$dateObject = new Zend_Date(strtotime($strDatetime));
    	$tz = date_default_timezone_get();
    	$viewer = Engine_Api::_() -> user() -> getViewer();
    	if( $viewer->getIdentity() ) {
    		$tz = $viewer->timezone;
    	}
    	$tzNumber = Engine_Api::_()->ynevent()->getLocaleNumber($tz);
    	$dateObject->setTimezone($tz);
    	$view = Zend_Registry::get("Zend_View");
    	
    	//will return with format 2014-03-06T14:04:00+07:00
    	$str = $view->locale()->toDateTime($dateObject, array('format' => 'Y-MM-dTHH:mm:ss')) . $tzNumber;
    	return $str;
    }
    
    public function getCurrentSiteUrl()
    {
    	// init page url
    	$pageURL = 'http';
    	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
    	{
    		$pageURL .= "s";
    	}
    	$pageURL .= "://";
    	if ($_SERVER["SERVER_PORT"] != "80")
    	{
    		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["PHP_SELF"];
    	}
    	else
    	{
    		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
    	}
    	
    	return $pageURL;
    }
    
}
