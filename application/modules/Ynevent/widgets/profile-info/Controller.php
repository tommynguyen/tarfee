<?php

class Ynevent_Widget_ProfileInfoController extends Engine_Content_Widget_Abstract {

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
          if ($subject->url)
          {
          		$pos = strpos($subject->url, "http");
		  		if ($pos === false){
				  	$subject->url = "http://" . $subject->url;
				}	
          }
          $view = $this->view;
          $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		  $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
          $this->view->subject = $subject;
     }

}