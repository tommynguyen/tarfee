<?php

class Ynevent_Widget_ListMostAttendingEventsController extends Engine_Content_Widget_Abstract {

     public function indexAction() {
          // Get paginator
          $table = Engine_Api::_()->getDbTable('events', 'ynevent');
          $select = $table->select();
          $tableEventName = $table->info('name');
     
          $tableMembership = Engine_Api::_()->getDbTable('membership','ynevent');
          $tableMembershipName = $tableMembership->info('name');
          $select->setIntegrityCheck(false)
                  ->from($tableEventName,array("$tableEventName.*","count($tableMembershipName.resource_id)as member_attending"))
                  ->join($tableMembershipName, "$tableMembershipName.resource_id=$tableEventName.event_id", '')
                  ->where("$tableMembershipName.rsvp = ?", 2)
                  ->group("$tableMembershipName.resource_id")
                  ->order("count($tableMembershipName.resource_id) DESC");
          $this->view->paginator = $paginator = Zend_Paginator::factory($select);
          
          // Set item count per page and current page number
          $itemCount = $this->_getParam('itemCountPerPage', 5);
          $paginator->setItemCountPerPage((!empty($itemCount))?$itemCount:5);
          $paginator->setCurrentPageNumber($this->_getParam('page', 1));

          // Hide if nothing to show
          if ($paginator->getTotalItemCount() <= 0) {
               return $this->setNoRender();
          }
     }
}