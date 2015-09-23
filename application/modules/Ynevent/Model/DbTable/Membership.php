<?php

class Ynevent_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

     protected $_type = 'event';
     protected $_name = 'event_membership';

     // Configuration

     /**
      * Does membership require approval of the resource?
      *
      * @param Core_Model_Item_Abstract $resource
      * @return bool
      */
     public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource) {
          return $resource->approval;
     }

     public function getMemberEvents($uid, $rsvp) {
          $select = $this->select();
          $select->where("user_id=?", $uid)
                  ->where("rsvp=?", $rsvp);
          return $this->fetchAll($select);
     }
     
     public function getMembershipsOfEvent($event_id)
     {
          $select = $this->select();
          $select->where("resource_id=?", $event_id);
          return $this->fetchAll($select);
     }
	  

}