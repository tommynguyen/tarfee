<?php
class Ynresponsiveevent_Api_Core extends Core_Api_Abstract
{
    public function getEventPaginator($params = array())
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $select = $this -> getEventSelect($params);
        $table = Engine_Api::_() -> getItemTable('event');
        $events = $table -> fetchAll($select);
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
        // Organizing
        if (isset($params['owner']) && $params['owner'])
        {
            $select -> where("$eventTableName.user_id = ?", $params['owner']);
        }
        // view modes
        else if(isset($params['type']))
        {
            if (Engine_Api::_()->hasModuleBootstrap('ynevent'))
                $membership = Engine_Api::_()->getDbtable('membership', 'Ynevent');
            else
                $membership = Engine_Api::_()->getDbtable('membership', 'event');
            switch ($params['type']) {
                case 'attending':
                    $select = $membership->getMembershipsOfSelect($viewer, true);
                    $select -> where('rsvp = 2');
                    break;
                case 'maybe-attending':
                    $select = $membership->getMembershipsOfSelect($viewer, true);
                    $select -> where('rsvp = 1');
                    break;
                case 'invited':
                    $select = $membership->getMembershipsOfSelect($viewer, false);
                    $select -> where('user_approved = 0');
                    $select -> where('resource_approved  = 1');
                    break;
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
        else
        {
            if (isset($params['category_id']) && !empty($params['category_id']))
            {
                $select -> where('category_id = ?', $params['category_id']);
            }
        }

        if(empty($params['start_date']) && empty($params['type']) && empty($params['owner']))
        {
            $select -> where("endtime > FROM_UNIXTIME(?)", time()); 
        }
        
        // From date
        if (!empty($params['start_date']) && empty($params['end_date']))
        {
            $fromdate = $this -> getFromDaySearch($params['start_date']);
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
            $todate = $this -> getToDaySearch($params['end_date']);
            if (!$todate)
            {
                $select -> where("false");
                return $select;
            }
            $select = $this -> _selectEventsToDate($select, $todate);
        }

        if (!empty($params['start_date']) && !empty($params['end_date']))
        {
            $fromdate = $this -> getFromDaySearch($params['start_date']);
            $todate = $this -> getToDaySearch($params['end_date']);
            $select = $this -> _appendEventSelectInRange($select, $fromdate, $todate);
        }
        //Keywork
        if (isset($params['keyword']) && !empty($params['keyword']))
        {
            $select -> where("title like ?", '%' . $params['keyword'] . '%');
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
                $select -> order($params['order']);
            }
            else
            {
                $select -> order('starttime DESC');
            }
        }
        if (Engine_Api::_()->hasModuleBootstrap('ynevent'))
        {
            $select -> group('repeat_group');
        }
        return $select;
    }
    // get categories
    public function getCategories($module = 'event')
    {
        $table = Engine_Api::_()->getDbtable('categories', $module);
        $select = $table -> select();
        if($module == 'ynevent')
        {
            $select -> where ('parent_id = 0');
        }
        return $table -> fetchAll($select);
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
    
    private function _appendEventSelectInRange($select, $from, $to)
    {
        $table = Engine_Api::_() -> getItemTable('event');
        $eventTableName = $table -> info('name');
        $select -> where(sprintf("          
                $eventTableName.starttime between '$from' and '$to'
                OR ($eventTableName.endtime between '$from' and '$to'
                OR ($eventTableName.starttime <= '$from' AND $eventTableName.endtime >= '$to'))"));

        return $select;
    }

    private function _selectEventsFromDate($select, $from)
    {
        $table = Engine_Api::_() -> getItemTable('event');
        $eventTableName = $table -> info('name');
        $select -> where("($eventTableName.endtime >= ?)", $from);

        return $select;
    }

    private function _selectEventsToDate($select, $todate)
    {
        $table = Engine_Api::_() -> getItemTable('event');
        $eventTableName = $table -> info('name');
        $select -> where("($eventTableName.starttime <= ?)", $todate);
        return $select;
    }
}