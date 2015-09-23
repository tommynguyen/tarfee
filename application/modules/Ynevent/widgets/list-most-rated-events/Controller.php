<?php

class Ynevent_Widget_ListMostRatedEventsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
//        $viewer = Engine_Api::_()->user()->getViewer();
//        if (!$viewer->getIdentity()) {
//            return $this->setNoRender();
//        }
        // Should we consider views or comments popular?
//          $popularType = $this->_getParam('popularType', 'view');
//          if (!in_array($popularType, array('view', 'member'))) {
//               $popularType = 'view';
//          }
//          $this->view->popularType = $popularType;
        $this->view->popularCol = $popularCol = 'rating';
        // $event = Engine_Api::_()->getItemTable('event');
        // Get paginator
//          $table = Engine_Api::_()->getItemTable('event');
//          $select = $table->select()
//                  ->where('search = ?', 1)
//                  ->order($popularCol . ' DESC');
        $table = Engine_Api::_()->getDbTable('events', 'ynevent');
        $select = $table->select();
        $tableEventName = $table->info('name');
        $tableRating = Engine_Api::_()->getDbTable('ratings', 'ynevent');
        $tableRatingName = $tableRating->info('name');
        $select->setIntegrityCheck(false)
                ->from($tableEventName, array("$tableEventName.*", "count($tableRatingName.event_id)as rating_count"))
                ->join($tableRatingName, "$tableRatingName.event_id=$tableEventName.event_id", '')
                ->where("$tableEventName.search = ?", 1)
                ->group("$tableRatingName.event_id")
                ->order("$tableEventName.rating DESC");
        // echo $select; die();
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Hide if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
    }

}