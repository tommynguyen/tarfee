<?php

class Ynevent_Widget_ProfileCoverController extends Engine_Content_Widget_Abstract {

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
          $this->view->event = $event = $subject;
          $category = null;
          if ($event->category_id)
          {	
          		$category = Engine_Api::_()->getItem('ynevent_category', $event->category_id);
          }
          
          $this->view->category = $category;
          $this->view->user = $user = $event->getOwner();
          $this->view->canComment = $canComment = $event->authorization()->isAllowed($viewer, 'comment');
          $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($event);
          
          $followTable = Engine_Api::_()->getDbTable('follow','ynevent');
          $row = $followTable->getFollowEvent($subject->getIdentity(),$viewer->getIdentity());
          $this->view->follow = $row->follow;
          
          $menu = new Ynevent_Plugin_Menus();
          
          $aJoinButton = $menu->onMenuInitialize_YneventProfileMember();
          $this->view->aJoinButton = $aJoinButton;
          
          $aEditButton = $menu->onMenuInitialize_YneventProfileEdit();
          $this->view->aEditButton = $aEditButton;
          
          $aStyleButton = $menu->onMenuInitialize_YneventProfileStyle();
          $this->view->aStyleButton = $aStyleButton;
          
          $aInviteButton = $menu->onMenuInitialize_YneventProfileInvite();
          $this->view->aInviteButton = $aInviteButton;
          
          $aTrasferButton = $menu->onMenuInitialize_YneventProfileTransfer();
          $this->view->aTrasferButton = $aTrasferButton;
          
          $aMessageButton = $menu->onMenuInitialize_YneventProfileMessage();
          $this->view->aMessageButton = $aMessageButton;
          
          $aDeleteButton = $menu->onMenuInitialize_YneventProfileDelete();
          $this->view->aDeleteButton = $aDeleteButton;
          
          $aInviteGroupButton = $menu->onMenuInitialize_YneventProfileInviteGroup();
          $this->view->aInviteGroupButton = $aInviteGroupButton;
          
          $aPromoteButton = $menu->onMenuInitialize_YneventProfilePromote();
          $this->view->aPromoteButton = $aPromoteButton;
     }

}