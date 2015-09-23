<?php

class Ynevent_Widget_EventsCalendarController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //$this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/application/modules/Ynevent/externals/scripts/jquery-ui-1.8.17.custom.min.js');
        $this->view->headScript()->appendScript('jQuery.noConflict()');

       
        $viewer = Engine_Api::_()->user()->getViewer();
        $oldTz = date_default_timezone_get();
        $userTz = $oldTz;
        if ($viewer->getIdentity()) {
            $userTz = $viewer->timezone;
        }
        //Get user month

        date_default_timezone_set($userTz);
//        $month = date('m');
//        $year = date('y');
        $month = $this->_getParam('month', date('m'));
        $year = $this->_getParam('year', date('y'));
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
        
        //var_dump($events);die;
        $event_count = array();
        $i = 0;
        if (count($showedEvents)) {
            $eventDates = array();
            foreach ($showedEvents as $event) {
                //convert start time to user time zone
                //echo ($event->starttime)."<br>";
                $t_day = strtotime($event->starttime);
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($userTz);
                $dateObject = date('Y-m-d', $t_day);
                //echo $dateObject;die();
                date_default_timezone_set($oldTz);
                $event_count[$dateObject][] = $event->event_id;
//           
            }
            // date_default_timezone_set($oldTz);
            foreach ($event_count as $index => $evt) {
                $eventDates[$i]['day'] = $index;
                $eventDates[$i]['event_count'] = count($evt);
                $i++;
            }
            $this->view->eventDates = $eventDates;
          
        }

        
       
    }

}