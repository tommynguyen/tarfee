<?php

class Ynevent_Widget_ProfileAddRatesController extends Engine_Content_Widget_Abstract {

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

          $this->view->subject = $subject;
          $this->view->event=$subject;
          $this->view->viewer_id = $viewer->getIdentity();
          $this->view->rating_count = Engine_Api::_()->ynevent()->ratingCount($subject->getIdentity());
          
          $this->view->rated = Engine_Api::_()->ynevent()->checkRated($subject->getIdentity(), $viewer->getIdentity());
     }

}