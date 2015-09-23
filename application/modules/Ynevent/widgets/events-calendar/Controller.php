<?php

class Ynevent_Widget_EventsCalendarController extends Engine_Content_Widget_Abstract
{
	
    public function indexAction()
    {
        // process timezone
        $user_tz = date_default_timezone_get();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer -> getIdentity())
        {
            $user_tz = $viewer -> timezone;
        }
        $oldTz = date_default_timezone_get();
        
        //user time zone
        date_default_timezone_set($user_tz);
        
        $paramKey = 'selected_day';
        $request = Zend_Controller_Front::getInstance() -> getRequest();
        $date = $request -> getParam($paramKey, null);
        if ($date == null)
        {
            $date = date('Y-m-d');
        }
        $arr = explode('-', $date);

        $day = 0;
        $month = 0;
        $year = 0;

        if (count($arr) == 3)
        {
            $day = $arr[2];
            $month = $arr[1];
            $year = $arr[0];
        }

        if ($day > 31 || $day < 1)
        {
            $day = date('d');
        }

        if ($month < 1 || $month > 12)
        {
            $month = date('m');
        }

        $thisYear = (int)date('Y');

        if ($year < $thisYear - 9 || $year > $thisYear + 9)
        {
            $year = date('Y');
        }

        $this -> view -> day = $day;
        $this -> view -> month = $month;
        $this -> view -> year = $year;
        
        date_default_timezone_set($oldTz);
        
        $search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);
        $eventTable = Engine_Api::_()->getItemTable('event');
        //Get first date and last day in month server time zone
        $events = $eventTable->getAllEventsInMonth($search[0], $search[1]);
        
        $showedEvents = array();        
        $auth = Engine_Api::_()->authorization()->context;
        foreach($events as $event) {
        	if ($auth->isAllowed($event, $viewer, 'view')) {
        		array_push($showedEvents, $event);
        	}
        }

    	$event_count = array();
        $i = 0;
        if (count($showedEvents)) {
            $eventDates = array();
            foreach ($showedEvents as $event) {
                //convert start time to user time zone
                //echo ($event->starttime)."<br>"; 
                $t_day = strtotime($event->starttime);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($user_tz);
                $dateObject = date('Y-n-j', $t_day);
                date_default_timezone_set($oldTz);
                $event_count[$dateObject][] = $event->event_id;
            }
            //print_r($event_count); exit;
            // date_default_timezone_set($oldTz);
            foreach ($event_count as $index => $evt) {
                $eventDates[$i]['day'] = $index;
                $eventDates[$i]['event_count'] = count($evt);
                $i++;
            }
            
            //print_r($eventDates); exit;
            $this->view->eventDates = json_encode($eventDates);
        }
    }

}
