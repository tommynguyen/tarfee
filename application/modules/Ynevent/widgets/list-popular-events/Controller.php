<?php

class Ynevent_Widget_ListPopularEventsController extends Engine_Content_Widget_Abstract {

     public function indexAction() {
          // Should we consider views or comments popular?
          $popularType = $this->_getParam('popularType', 'view');
          if (!in_array($popularType, array('view', 'member'))) {
               $popularType = 'view';
          }
          $this->view->popularType = $popularType;
          $this->view->popularCol = $popularCol = $popularType . '_count';

          // Get paginator
          $table = Engine_Api::_()->getItemTable('event');
          $select = $table->select()
                  ->where('search = ?', 1)
                  ->order($popularCol . ' DESC');
          $events = $table->fetchAll($select);
          $itemCountPerPage = $this->_getParam('itemCountPerPage', 5);
          $showedEvents = Ynevent_Plugin_Utilities::getListOfEvents($events, empty($itemCountPerPage)?5:$itemCountPerPage);
          $this->view->paginator = $paginator = Zend_Paginator::factory($showedEvents);

          // Hide if nothing to show
          if ($paginator->getTotalItemCount() <= 0) {
               return $this->setNoRender();
          }
     }

}