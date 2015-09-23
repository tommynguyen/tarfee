<?php

class Ynevent_Widget_ProfileMembersController extends Engine_Content_Widget_Abstract {

     protected $_childCount;

     public function indexAction() {

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
		  $this -> getElement() -> removeDecorator('Title');
          // Get params
          $this->view->page = $page = $this->_getParam('page', 1);
          $this->view->search = $search = $this->_getParam('search');
          $this->view->waiting = $waiting = $this->_getParam('waiting', false);

          // Prepare data
          $this->view->event = $event = Engine_Api::_()->core()->getSubject();

          $this->view->isOwner = $event->isOwner($viewer);
          
          $members = null;
          $viewer = Engine_Api::_()->user()->getViewer();
          if ($viewer->getIdentity() && $event->isOwner($viewer)) {
               $this->view->waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
               if ($waiting) {
                    $this->view->members = $members = $this->view->waitingMembers;
               }
          }

          if (!$members) {
               $select = $event->membership()->getMembersObjectSelect();
               if ($search) {
                         $select->where('displayname LIKE ?', '%' . $search . '%');
               }

               $this->view->filter=$filter = $this->_getParam('filter');
              
               if (isset($filter) && $filter >= 0) {
                  
                    $membershipTable = Engine_Api::_()->getDbTable("membership", "ynevent");
                    $membershipTableName = $membershipTable->info('name');
                    $select->where("$membershipTableName.rsvp=?", $filter);
                                  
               }
            
               $this->view->members = $members = Zend_Paginator::factory($select);
          }

          $paginator = $members;

          // Set item count per page and current page number
          $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
          $paginator->setCurrentPageNumber($this->_getParam('page', $page));

          // Do not render if nothing to show
//          if ($paginator->getTotalItemCount() <= 0 && '' == $search) {
//               return $this->setNoRender();
//          }

          // Add count to title if configured
          if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
               $this->_childCount = $paginator->getTotalItemCount();
          }
     }

     public function getChildCount() {
          return $this->_childCount;
     }

}