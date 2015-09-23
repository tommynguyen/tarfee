<?php

class Ynevent_Widget_ProfileStatusController extends Engine_Content_Widget_Abstract {

     public function indexAction() {
          // Don't render this if not authorized
          $viewer = Engine_Api::_()->user()->getViewer();
          if (!Engine_Api::_()->core()->hasSubject()) {
               return $this->setNoRender();
          }

          // Get subject and check auth
          $subject = Engine_Api::_()->core()->getSubject('event');
          //if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
          //  return $this->setNoRender();
          //}
          $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
         // var_dump($canComment);die();
          $this->view->subject = $subject;
     }

}