<?php

class Ynevent_WidgetController extends Core_Controller_Action_Standard {

    public function profileInfoAction() {

        // Don't render this if not authorized
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
            return $this->_helper->viewRenderer->setNoRender(true);
    }

    public function profileRsvpAction() {

        $this->view->form = new Ynevent_Form_Rsvp();
        $event = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$event->membership()->isMember($viewer, true)) {
            return;
        }
        $row = $event->membership()->getRow($viewer);
        $this->view->viewer_id = $viewer->getIdentity();
        if ($row) {
            $this->view->rsvp = $row->rsvp;
        } else {
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        if ($this->getRequest()->isPost()) {
            $option_id = $this->getRequest()->getParam('option_id');

            $row->rsvp = $option_id;
            $row->save();
        }
        if ($option_id == 2) {
            $table = Engine_Api::_()->getDbTable('follow', 'ynevent');
            $table->setOptionFollowEvent($event->getIdentity(), $viewer->getIdentity(), 1);
        }
    }

    public function profileFollowAction() 
    {
        $event = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbTable('follow', 'ynevent');
        $row = $followTable->getFollowEvent($event->getIdentity(), $viewer->getIdentity());
        $this->view->viewer_id = $viewer->getIdentity();
        if ($row) 
        {
            $this->view->follow = $row->follow;
        } 
        else if($this->getRequest()->isPost())
		{
			$row = $followTable->createRow();
		    $row->resource_id = $event->getIdentity();
		    $row->user_id = $viewer->getIdentity();
		}
		else
        {
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        $option_id = $this->getRequest()->getParam('option_id');
        $row->follow = $option_id;
        $row->save();
    }

    public function requestEventAction() {
    	$this->view->viewer = Engine_Api::_()->user()->getViewer();    	
        $this->view->notification = $notification = $this->_getParam('notification');
    }

    public function eventCalendarAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        //Get user month
        $oldTz = date_default_timezone_get();
        date_default_timezone_set($viewer->timezone);
        $month = $this->_getParam('month', date('m'));
        $year = $this->_getParam('year', date('y'));
        date_default_timezone_set($oldTz);
        $search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);
        $eventTable = Engine_Api::_()->getItemTable('event');
        //Get first date and last day in month server time zone
        $events = $eventTable->getAllEventsInMonth($search[0], $search[1]);

        // var_dump($events);die;
        $event_count = array();
        $i = 0;
        if (count($events)) {
            $eventDates = array();

            foreach ($events as $event) {
                //convert start time to user time zone
                $dateObject = new Zend_Date(strtotime($event->starttime));
                $dateObject->setTimezone($viewer->timezone);
                $event_count[$dateObject->toString('yyyy-MM-dd')][] = $event->event_id;
            }
            date_default_timezone_set($oldTz);
            foreach ($event_count as $index => $evt) {
                $eventDates[$i]['day'] = $index;
                $eventDates[$i]['event_count'] = count($evt);
                $i++;
            }
            $this->view->eventDates = $eventDates;
        }
    }
	public function listRsvpAction() 
	{
		$event = Engine_Api::_() -> getItem('event', $this -> _getParam('id'));
		$this -> view -> event = $event;
		$this -> view -> widget = $this -> _getParam('widget', '');
		$this -> renderScript('widget/list-rsvp.tpl');
	}
	public function rsvpAction()
	{
        $event = Engine_Api::_() -> getItem('event', $this -> _getParam('id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$event->membership()->isMember($viewer, true)) 
        {
            return;
        }
        $row = $event->membership()->getRow($viewer);
		$option_id = 0;
        if ($row) 
        {
            $option_id = $this->getRequest()->getParam('option_id');
            $row->rsvp = $option_id;
            $row->save();
        }
        if ($option_id == 2) {
            $table = Engine_Api::_()->getDbTable('follow', 'ynevent');
            $table->setOptionFollowEvent($event->getIdentity(), $viewer->getIdentity(), 1);
        }
		$this -> view -> status = true;
        $this -> view -> body = $this -> view -> action('list-rsvp', 'widget', 'ynevent', array( 'id' => $event -> getIdentity(), 'widget' => $this -> _getParam('widget', ''), 'format' => 'html'));
        $this -> _helper -> contextSwitch -> initContext();
	}
}