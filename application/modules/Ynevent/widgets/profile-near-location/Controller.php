<?php

class Ynevent_Widget_ProfileNearLocationController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
    	// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		
		// Prepare data
		$this->view->event = $event = $subject;
        $lat = $event->latitude;
        $lon =$event->longitude;
        $limit =$this->_getParam('max',5);
        $radius =$this->_getParam('radius', 500);
        $currentDay = date('Y') . '-' . date('m') . '-' . date('d');
        
    	if (!$lon || !$lon) {
			return $this->setNoRender();
		} 
        
        $sql = "
SELECT evt.*,
    3956 * 2 *  
                ASIN(SQRT(
                POWER(SIN(({$lat} -ABS(evt.latitude)) * PI()/180 / 2),2) + 
                COS({$lon} * PI()/180 )*
                COS(ABS({$lat}) *  PI()/180)*
                POWER(SIN(({$lon} - evt.longitude)*PI()/180/2), 2)
                ) ) AS distance 
FROM 
    engine4_event_events AS evt 
WHERE 
    ({$radius} = 0  OR (3956 * 2 *  
                ASIN(SQRT( 
                POWER(SIN(({$lat} -ABS(evt.latitude)) * PI()/180 / 2),2) + 
                COS({$lon} * PI()/180 )* 
                COS(ABS({$lat}) *  PI()/180)* 
                POWER(SIN(({$lon} - evt.longitude)*PI()/180/2), 2) 
                ) ) < {$radius} 
            ) 
        ) AND evt.`event_id` <> {$event->getIdentity()}  
GROUP BY evt.repeat_group 
ORDER BY distance  
LIMIT $limit ";
		
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
       	$rows = $db -> fetchAll($sql);
		$showedEvents = array(); 
		$disArr = array();
		
		$eventTbl = Engine_Api::_()->getItemTable('event');
		foreach ($rows as $row){
       		$event = $eventTbl->createRow($row);
       		$showedEvents[] = $event;
       		$disArr[$row['event_id']] = $row['distance'];
		}
		
	    // Hide if nothing to show
	    if( count($rows) <= 0 ) {
	      return $this->setNoRender();
	    }
	    
	    $this->view->showedEvents = $showedEvents;
	    $this->view->disArr = $disArr;
    }
}   